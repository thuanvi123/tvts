<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
include_once dirname(__FILE__) . '/models/Alert.php';
include_once dirname(__FILE__) . '/models/SearchFilter.php';
include_once dirname(__FILE__) . '/models/Paging.php';

class CTMobile_WS_ListRecordComment extends CTMobile_WS_Controller {
	
	
	function getSearchFilterModel($module, $search) {
		return CTMobile_WS_SearchFilterModel::modelWithCriterias($module, Zend_JSON::decode($search));
	}
	
	function getPagingModel(CTMobile_API_Request $request) {
		$page = $request->get('page', 0);
		return CTMobile_WS_PagingModel::modelWithPageStart($page);
	}
	
	function process(CTMobile_API_Request $request) {
		global $current_user,$adb, $site_URL;
		$current_user = $this->getActiveUser();

		$default_charset = VTWS_PreserveGlobal::getGlobal('default_charset');
		$moduleName = CTMobile_WS_Utils::detectModulenameFromRecordId($request->get('record'));
		$recordid = explode('x',trim($request->get('record')));

		$record = $recordid[1];
		$index = trim($request->get('index'));
		$size = trim($request->get('size'));
		$limit = ($index*$size) - $size;
		if($moduleName == 'ModComments'){
			$query = "SELECT vtiger_modcomments.*, vtiger_crmentity.createdtime,vtiger_crmentity.modifiedtime, vtiger_crmentity.smownerid from vtiger_modcomments INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_modcomments.modcommentsid where vtiger_crmentity.deleted = 0 AND vtiger_modcomments.parent_comments = ? ORDER BY vtiger_modcomments.modcommentsid DESC";
		}else{
			$query = "SELECT vtiger_modcomments.*, vtiger_crmentity.createdtime,vtiger_crmentity.modifiedtime, vtiger_crmentity.smownerid from vtiger_modcomments INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_modcomments.modcommentsid where vtiger_crmentity.deleted = 0 and vtiger_modcomments.related_to = ? AND vtiger_modcomments.parent_comments = 0 ORDER BY vtiger_modcomments.modcommentsid DESC";
		}
		
		if(!empty($index) && !empty($size)){
			$query .= sprintf(" LIMIT %s, %s", $limit, $size);
		}
		$getCommentQuery = $adb->pquery($query, array($record));
		$countComment = $adb->num_rows($getCommentQuery);
		
		$modcommentsData = array();
		for($i=0;$i<$countComment;$i++) {
			$modcommentId = $adb->query_result($getCommentQuery, $i, 'modcommentsid');
			$commentcontent = $adb->query_result($getCommentQuery, $i, 'commentcontent');
			$reasontoedit = $adb->query_result($getCommentQuery,$i,'reasontoedit');
			$commentcontent = html_entity_decode($commentcontent, ENT_QUOTES, $default_charset);
			$reasontoedit = html_entity_decode($reasontoedit, ENT_QUOTES, $default_charset);
			$relatedTo = $adb->query_result($getCommentQuery, $i, 'related_to');
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
						$AccessMessage = $this->CTTranslate('You don\'t have permission to access this resource');
					}
				}
				$Attachments[] = array('filename'=>$filename,'file_URL'=>$file_URL,'fileAccess'=>$fileAccess,'AccessMessage'=>$AccessMessage,'extension'=>$ext);
			}

			$recordModel = Vtiger_Record_Model::getInstanceById($relatedTo);
			$relatedWSId = CTMobile_WS_Utils::getEntityModuleWSId($recordModel->getModuleName());
			$parent_comments = $adb->query_result($getCommentQuery, $i, 'parent_comments');
			$userId = $adb->query_result($getCommentQuery, $i, 'smownerid');
			$createdtime = $adb->query_result($getCommentQuery, $i, 'createdtime');
			$commentedtime = Vtiger_Util_Helper::formatDateDiffInStrings($createdtime);
			$modifiedtime = $adb->query_result($getCommentQuery, $i, 'modifiedtime');
			$isModified = false;
			$modifiedText = "";
			if($createdtime != $modifiedtime){
				$isModified = true;
				$modifiedtime = Vtiger_Util_Helper::formatDateDiffInStrings($modifiedtime);
				$modifiedText = vtranslate('LBL_COMMENT','ModComments').' '.strtolower(vtranslate('LBL_MODIFIED','ModComments')).' '.$modifiedtime;
			}

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
			$isReply = false;
			if(Users_Privileges_Model::isPermitted('ModComments', 'EditView')){
				$isReply = true;
			}
			$commentsWSid = CTMobile_WS_Utils::getEntityModuleWSId('ModComments');
			$childCommentsData = $this->getCommentsData($modcommentId,$recordid);
			$modcommentsData[] = array('modcommentId'=>$commentsWSid.'x'.$modcommentId, 'commentcontent'=>decode_html($commentcontent), 'relatedTo' => $relatedWSId.'x'.$relatedTo,'parent_comments'=>$parent_comments,'reasontoedit'=>$reasontoedit,'userid'=>$userId,'attachments'=>$Attachments,'userName'=>$firstname." ".$lastname,'userImage'=>$userImage, 'createdtime'=>$createdtime,'ModifiedTime'=>$commentedtime,'isEdit'=>$isEdit,'isModified'=>$isModified,'modifiedText'=>$modifiedText,'isReply'=>$isReply,'childCommentsData'=>$childCommentsData);

		}
		$response = new CTMobile_API_Response();
		if(count($modcommentsData) == 0){
			$message = $this->CTTranslate('No records found');
			$response->setResult(array('CommentsData'=>array(),'code'=>404,'message'=>$message));
		}else{
			$response->setResult(array('CommentsData'=>$modcommentsData));
		}
		return $response;
	}

	public function getCommentsData($modcommentId,$related_to){
		$adb = PearDatabase::getInstance();
		global $current_user;
		global $site_URL;
		$current_user = $this->getActiveUser();
		$query = "SELECT vtiger_modcomments.*, vtiger_crmentity.createdtime,vtiger_crmentity.modifiedtime, vtiger_crmentity.smownerid from vtiger_modcomments INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_modcomments.modcommentsid where vtiger_crmentity.deleted = 0 AND vtiger_modcomments.related_to = ? AND vtiger_modcomments.parent_comments = ? ORDER BY vtiger_modcomments.modcommentsid DESC";
		$getCommentQuery = $adb->pquery($query, array($related_to[1],$modcommentId));
		$countComment = $adb->num_rows($getCommentQuery);
		$modcommentsData = array();
		for($i=0;$i<$countComment;$i++) {
			$modcommentId = $adb->query_result($getCommentQuery, $i, 'modcommentsid');
			$commentcontent = $adb->query_result($getCommentQuery, $i, 'commentcontent');
			$reasontoedit = $adb->query_result($getCommentQuery,$i,'reasontoedit');
			$commentcontent = decode_html(decode_html($commentcontent));
			$reasontoedit = decode_html(decode_html($reasontoedit));
			$relatedTo = $adb->query_result($getCommentQuery, $i, 'related_to');
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
						$AccessMessage = $this->CTTranslate('You don\'t have permission to access this resource');
					}
				}
				$Attachments[] = array('filename'=>$filename,'file_URL'=>$file_URL,'fileAccess'=>$fileAccess,'AccessMessage'=>$AccessMessage,'extension'=>$ext);
			}
			$parent_comments = $adb->query_result($getCommentQuery, $i, 'parent_comments');
			$userId = $adb->query_result($getCommentQuery, $i, 'smownerid');
			$createdtime = $adb->query_result($getCommentQuery, $i, 'createdtime');
			$commentedtime = Vtiger_Util_Helper::formatDateDiffInStrings($createdtime);
			$modifiedtime = $adb->query_result($getCommentQuery, $i, 'modifiedtime');
			$isModified = false;
			$modifiedText = "";
			if($createdtime != $modifiedtime){
				$isModified = true;
				$modifiedtime = Vtiger_Util_Helper::formatDateDiffInStrings($modifiedtime);
				$modifiedText = vtranslate('LBL_COMMENT','ModComments').' '.strtolower(vtranslate('LBL_MODIFIED','ModComments')).' '.$modifiedtime;
			}
			
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
			$isReply = false;
			if(Users_Privileges_Model::isPermitted('ModComments', 'EditView')){
				$isReply = true;
			}
			$commentsWSid = CTMobile_WS_Utils::getEntityModuleWSId('ModComments');
			$modcommentsData[] = array('modcommentId'=>$commentsWSid.'x'.$modcommentId, 'commentcontent'=>$commentcontent, 'relatedTo' => $related_to[0].'x'.$relatedTo,'parent_comments'=>$commentsWSid.'x'.$parent_comments,'reasontoedit'=>$reasontoedit,'userid'=>$userId,'attachments'=>$Attachments, 'userName'=>$firstname." ".$lastname,'userImage'=>$userImage, 'createdtime'=>$createdtime,'ModifiedTime'=>$commentedtime,'isModified'=>$isModified,'modifiedText'=>$modifiedText,'isReply'=>$isReply);
		}
		return array('countComment'=>$countComment,'comments'=>$modcommentsData);
	}
	
}
