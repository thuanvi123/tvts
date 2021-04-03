<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
include_once dirname(__FILE__) . '/SaveRecord.php';
include_once 'modules/Vtiger/CRMEntity.php';

class CTMobile_WS_AddRecordComment extends CTMobile_WS_SaveRecord {
	
	function process(CTMobile_API_Request $request) {
		global $current_user,$adb, $site_URL;
		$values = Zend_Json::decode($request->get('values'));
		$relatedTo = trim($values['related_to']);
		$commentContent = $values['commentcontent'];
		$mentioned_users = explode(',',$request->get('mentioned_user_id'));
		$request->set('mentioned_user_id','');
		if($commentContent == ''){
			$commentContent = $request->get('commentcontent');
		}
		
		if($relatedTo == ''){		
			$message = $this->CTTranslate('Required fields not found');		
			throw new WebServiceException(404,$message);		
		}		
		if($commentContent == ''){		
			$message = $this->CTTranslate('Required fields not found');		
			throw new WebServiceException(404,$message);		
		}

		$user = $this->getActiveUser();
		$targetModule = 'ModComments';
		$response = false;
		if (vtlib_isModuleActive($targetModule)) {
			$request->set('module', $targetModule);
			$values['assigned_user_id'] = sprintf('%sx%s', CTMobile_WS_Utils::getEntityModuleWSId('Users'), $user->id);
			//$values['userid'] = $values['assigned_user_id'];
			$request->set('values', Zend_Json::encode($values) );
			
			$response = parent::process($request);
			$id = $response->result['id'];

			//code start for notification of mentioned in comment by suresh
			if(!empty($mentioned_users)){
				$main_perm_query = "SELECT * FROM ctmobile_notification_settings WHERE notification_type = 'comment_mentioned' AND notification_enabled = '1'";
				$main_perm_result = $adb->pquery($main_perm_query,array());
				if($adb->num_rows($main_perm_result)){
					$notification_id = $adb->query_result($main_perm_result,0,'notification_id');
					$title = decode_html(decode_html($adb->query_result($main_perm_result,0,'notification_title')));
					$message = decode_html(decode_html($adb->query_result($main_perm_result,0,'notification_message')));
					$commmentid = explode('x', $id);
					$message = getMergedDescription($message, $commmentid[1], 'ModComments');
					$assigned_user_id = $user->id;
					$userRecordModel = Users_Record_Model::getInstanceById($assigned_user_id,'Users');
					if($userRecordModel->get('user_name') == ''){
						$query = "SELECT groupname FROM vtiger_groups WHERE groupid = ?";
						$groupResults = $adb->pquery($query,array($assigned_user_id));
						$user_name = decode_html(html_entity_decode($adb->query_result($groupResults,0,'groupname'),ENT_QUOTES,$default_charset));
					}else{
						$user_name = decode_html(html_entity_decode($userRecordModel->get('first_name').' '.$userRecordModel->get('last_name'),ENT_QUOTES,$default_charset));
					}
					//$title = $user_name.' '.$this->CTTranslate('mentioned you');
					foreach ($mentioned_users as $key => $mentioned_user) {
						list($user_ws_id,$mentioned_user_id) = explode('x', $mentioned_user);
						$sub_perm_query = "SELECT * FROM ctmobile_notification_restriction WHERE user_id = ? AND notification_id = ?";
						$sub_perm_result = $adb->pquery($sub_perm_query,array($mentioned_user_id,$notification_id));
						if($adb->num_rows($sub_perm_result) == 0){
							$perm_qry = "SELECT devicetoken,device_type FROM ctmobile_userdevicetoken WHERE userid = ?";
							$perm_result = $adb->pquery($perm_qry, array($mentioned_user_id));
							$perm_rows = $adb->num_rows($perm_result);
							if($perm_rows > 0){
								$devicetoken = $adb->query_result($perm_result,0,'devicetoken');
								$device_type = $adb->query_result($perm_result,0,'device_type');
								if($devicetoken && $device_type){
									$module_Name = 'CTPushNotification';
									$relatedCRMid = substr($relatedTo, stripos($relatedTo, 'x')+1);
									$focus = CRMEntity::getInstance($module_Name);
									$focus->column_fields['description'] = $message;
									$focus->column_fields['assigned_user_id'] = $mentioned_user_id;
									$focus->column_fields['pn_related'] = $relatedCRMid;
									$focus->column_fields['pushnotificationstatus'] = 'Draft';
									$focus->column_fields['devicekey'] = $devicetoken;
									$focus->column_fields['pn_title'] =  $title;
									$focus->save($module_Name);
									if($focus->id != ''){
										$record_id = $focus->id;
										$result = CTMobileSettings_Module_Model::sendpushnotification($message,$devicetoken,$device_type,$title,'record',$relatedTo,CTMobile_WS_Utils::detectModulenameFromRecordId($relatedTo));
										if($result){
											$recordModel = Vtiger_Record_Model::getInstanceById($record_id, $module_Name);
											$modelData = $recordModel->getData();
											$recordModel->set('mode', 'edit');
											$recordModel->set('pushnotification_response', $result);
											$recordModel->set('pushnotificationstatus', 'Send');
											$recordModel->save();
										}
									} 
									
								}
							}
						}
					}
				}
			}
			//code end for notification of mentioned in comment

			if(!empty($id)){

				$record = explode('x',$id);
				$modcommentsid = $record[1];
				$adb->pquery("UPDATE vtiger_modcomments SET userid = ? WHERE modcommentsid = ?",array($user->id,$modcommentsid));
				if($request->get('reasontoedit') != ''){
					$adb->pquery("UPDATE vtiger_modcomments SET reasontoedit = ? WHERE modcommentsid = ?",array($request->get('reasontoedit'),$modcommentsid));
				}
				$query = "SELECT vtiger_modcomments.*, vtiger_crmentity.createdtime,vtiger_crmentity.modifiedtime, vtiger_crmentity.smownerid from vtiger_modcomments INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_modcomments.modcommentsid where vtiger_crmentity.deleted = 0 and vtiger_modcomments.modcommentsid = ? ";

				$getCommentQuery = $adb->pquery($query, array($modcommentsid));
				$countComment = $adb->num_rows($getCommentQuery);

				$modcommentId = $adb->query_result($getCommentQuery, 0, 'modcommentsid');
				$commentcontent = $adb->query_result($getCommentQuery, 0, 'commentcontent');
				$reasontoedit = $adb->query_result($getCommentQuery,0,'reasontoedit');
				$default_charset = VTWS_PreserveGlobal::getGlobal('default_charset');
				$commentcontent = html_entity_decode($commentcontent, ENT_QUOTES, $default_charset);
				$reasontoedit = html_entity_decode($reasontoedit, ENT_QUOTES, $default_charset);
				$relatedTo = $adb->query_result($getCommentQuery, 0, 'related_to');
				$filenames = $adb->query_result($getCommentQuery, $i, 'filename');
				if($filenames != '' && $filenames != '0'){
					$files = explode(',',$filenames);
				}else{
					$files = array();
				}
				$Attachments = array();
				foreach ($files as $key => $fileid) {
					$filename = "";
					$file_URL = "";
					$fileAccess =  true;
					$AccessMessage = "";
					if($fileid != '' && $fileid != 0){
						$fileDetails = CTMobile_WS_Utils::getAttachments($fileid,$modcommentId);
						$filename = $fileDetails['filename'];
						$file_URL = $fileDetails['file_URL'];
						$file_URL = $site_URL.'modules/CTMobile/api/ws/DownloadUrl.php?record='.$fileid;
						$ext = pathinfo($fileDetails['file_URL'], PATHINFO_EXTENSION);
						if(file_get_contents($file_URL) == ""){
							$fileAccess = false;
							$AccessMessage = vtranslate("You don't have permission to access this resource",'CTMobile');
						}
					}
					$Attachments[] = array('filename'=>$filename,'file_URL'=>$file_URL,'fileAccess'=>$fileAccess,'AccessMessage'=>$AccessMessage,'extension'=>$ext);
				}
				$parent_comments = $adb->query_result($getCommentQuery, 0, 'parent_comments');
				$userId = $adb->query_result($getCommentQuery, 0, 'smownerid');
				$createdtime = $adb->query_result($getCommentQuery, 0, 'createdtime');
				$modifiedtime = $adb->query_result($getCommentQuery, 0, 'modifiedtime');
				$isModified = false;
				$modifiedText = "";
				if($createdtime != $modifiedtime){
					$isModified = true;
					$modifiedtime = Vtiger_Util_Helper::formatDateDiffInStrings($modifiedtime);
					$modifiedText = vtranslate('LBL_COMMENT','ModComments').' '.strtolower(vtranslate('LBL_MODIFIED','ModComments')).' '.$modifiedtime;
				}
				
				$commentedtime = Vtiger_Util_Helper::formatDateDiffInStrings($createdtime);
				if($userId) {
					$userRecordModel = Vtiger_Record_Model::getInstanceById($userId, 'Users');
					$firstname = $userRecordModel->get('first_name');
					$firstname = html_entity_decode($firstname, ENT_QUOTES, $default_charset);
					$lastname = $userRecordModel->get('last_name');
					$lastname = html_entity_decode($lastname, ENT_QUOTES, $default_charset);
					$userImage = CTMobile_WS_Utils::getUserImage($userId);
				}
				$isEdit = false;
				if(Users_Privileges_Model::isPermitted('ModComments', 'EditView')){
					if($userId == $current_user->id){
						$isEdit = true;
					}
				}

				$commentsWSid = CTMobile_WS_Utils::getEntityModuleWSId('ModComments');
				$modcommentsData = array('modcommentId'=>$commentsWSid.'x'.$modcommentId, 'commentcontent'=>$commentcontent, 'relatedTo' => $relatedTo,'parent_comments'=>$commentsWSid.'x'.$parent_comments,'reasontoedit'=>$reasontoedit, 'userid'=>$userId,'attachments'=>$Attachments,'userName'=>$firstname." ".$lastname,'userImage'=>$userImage, 'createdtime'=>$createdtime,'ModifiedTime'=>$commentedtime,'isEdit'=>$isEdit,'isModified'=>$isModified,'modifiedText'=>$modifiedText);
				$response = new CTMobile_API_Response();
				$response->setResult(array('record'=>$modcommentsData,'message'=>$this->CTTranslate('Comment saved successfully')));
			}else{
				$response = new CTMobile_API_Response();
				$response->setResult(array('record'=>array(),'message'=>$this->CTTranslate('Comment not saved')));
			}
		
		}else{
			$response = new CTMobile_API_Response();
			$message = $this->CTTranslate('Comment module is not active');
			$response->setError(403,$message);
		}
		return $response;
	}
}
