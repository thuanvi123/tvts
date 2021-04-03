<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
include_once dirname(__FILE__) . '/FetchRecordWithGrouping.php';
include_once 'include/Webservices/Update.php';

class CTMobile_WS_ListNotification extends CTMobile_WS_Controller {
	protected $recordValues = false;
	function process(CTMobile_API_Request $request) {
		global $current_user,$adb;
		$current_user = $this->getActiveUser();
		$default_charset = VTWS_PreserveGlobal::getGlobal('default_charset');
		$response = new CTMobile_API_Response();
		$mode = $request->get('mode');
		$search = $request->get('search');
		if($mode == 'readNotification'){
			$record = trim($request->get('record'));
			if($record != ''){
				$this->recordValues = vtws_retrieve($record, $current_user);
				$this->recordValues['read_status'] = 'Read';
				$this->recordValues = vtws_update($this->recordValues, $current_user);
				$message = $this->CTTranslate('Notification Read successfully');
				$result = array('id'=>$this->recordValues['id'],'module'=>'CTPushNotification','message'=>$message);
				$response->setResult($result);
			}else{
				$message = $this->CTTranslate('Record Id is required');
				$response->setError(404,$message);
			}

		}else{
			$index = trim($request->get('index'));
			$size = trim($request->get('size'));
			$limit = ($index*$size) - $size;
		
			$query = "SELECT vtiger_ctpushnotification.*, vtiger_crmentity.createdtime,vtiger_crmentity.modifiedtime, vtiger_crmentity.smownerid from vtiger_ctpushnotification INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_ctpushnotification.ctpushnotificationid where vtiger_crmentity.deleted = 0 AND vtiger_crmentity.smownerid = ? ";
			if($search != ''){
				$query.= " AND vtiger_ctpushnotification.description LIKE '%".$search."%' ";
			}
			
			$isLast = true;
			$totalQuery = $query;
			$totalParams = array($current_user->id);
			if($totalQuery != ""){
				$totalResults = $adb->pquery($totalQuery,$totalParams);
				$totalRecords = $adb->num_rows($totalResults);
				if($index && $size){
					if($totalRecords > $index*$size){
						$isLast = false;	
						$pagesize = $index-1;
						$startRange = $pagesize*$size+1;
						$lastRange = $index*$size;	
					}else{
						$isLast = true;
						$pagesize = $index-1;
						$startRange = $pagesize*$size+1;
						$lastRange = $totalRecords;
					}
				}else{
					$isLast = true;
					$startRange = 1;
					$lastRange = $totalRecords;
				}
			}
			
			$query.= " ORDER BY vtiger_crmentity.modifiedtime DESC ";
			//AND (vtiger_ctpushnotification.read_status != 'Read' )
			if(!empty($index) && !empty($size)){
				$query .= sprintf(" LIMIT %s, %s", $limit, $size);
			}

			$getNotificationQuery = $adb->pquery($query, array($current_user->id));
			$countNotification = $adb->num_rows($getNotificationQuery);
			
			$NotificationData = array();
			for($i=0;$i<$countNotification;$i++) {
				$ctpushnotificationid = $adb->query_result($getNotificationQuery, $i, 'ctpushnotificationid');
				$pn_title = $adb->query_result($getNotificationQuery, $i, 'pn_title');
				$pn_title = decode_html(decode_html($pn_title));
				$description = $adb->query_result($getNotificationQuery, $i, 'description');
				$description = decode_html(decode_html($description));
				$notification_url = $adb->query_result($getNotificationQuery, $i, 'notification_url');
				$pn_related = $adb->query_result($getNotificationQuery, $i, 'pn_related');
				$createdtime = $adb->query_result($getNotificationQuery, $i, 'createdtime');
				$datetime = Vtiger_Util_Helper::formatDateDiffInStrings($createdtime);

				$setype = "";
				$icon = "";
				$record = "";
				if($pn_related != ''){
					$notification_type = 'record';
					$getrelated = $adb->pquery("SELECT * FROM vtiger_crmentity WHERE crmid = ?",array($pn_related));
					if($adb->num_rows($getrelated) > 0){
						$setype = $adb->query_result($getrelated, 0, 'setype');
						
						/*code added by sapna start*/ 
						if($setype == 'ModComments')
						{
							$queryComment = $adb->pquery("SELECT vtiger_modcomments.related_to FROM vtiger_modcomments INNER JOIN vtiger_crmentity ON vtiger_modcomments.modcommentsid = vtiger_crmentity.crmid WHERE vtiger_modcomments.modcommentsid = ?",array($pn_related));
							if($adb->num_rows($queryComment) > 0){
								$pn_related = $adb->query_result($queryComment, 0, 'related_to');	
							}

						}
						/*code end*/
						if($setype == 'Calendar' || $setype == 'Events'){
							$recordId = $pn_related;
						    $EventTaskQuery = $adb->pquery("SELECT * FROM  `vtiger_activity` WHERE activitytype = ? AND activityid = ?",array('Task',$recordId)); 
						    if($adb->num_rows($EventTaskQuery) > 0){
								$wsid = CTMobile_WS_Utils::getEntityModuleWSId('Calendar');
								$record = $wsid.'x'.$recordId;
								$setype = 'Calendar';
							}else{
								$wsid = CTMobile_WS_Utils::getEntityModuleWSId('Events');
								$record = $wsid.'x'.$recordId;
								$setype = 'Events';
							}
							$icon = CTMobile_WS_Utils::getModuleURL($setype);
							//$record = CTMobile_WS_Utils::getEntityModuleWSId($setype).'x'.$pn_related;
						}else{
							$icon = CTMobile_WS_Utils::getModuleURL($setype);
							$record = CTMobile_WS_Utils::getEntityModuleWSId($setype).'x'.$pn_related;
						}

					}
				}else if($notification_url != ''){
					$notification_type = 'link';
				}else{
					$notification_type = 'normal';
				}

				$NotificationWSid = CTMobile_WS_Utils::getEntityModuleWSId('CTPushNotification');
				$NotificationData[] = array('modcommentId'=>$NotificationWSid.'x'.$ctpushnotificationid,'pn_title'=>$pn_title,'description'=>$description,'notification_url'=>$notification_url,'icon'=>$icon,'datetime'=>$datetime,'module'=>$setype,'record'=>$record,'notification_type'=>$notification_type);

			}
			if(count($NotificationData) == 0){
				$message = $this->CTTranslate('No records found');
				$response->setResult(array('NotificationData'=>array(),'code'=>404,'message'=>$message,'isLast'=>$isLast));
			}else{
				$response->setResult(array('NotificationData'=>$NotificationData,'isLast'=>$isLast));
			}
		}
		return $response;
	}
	
}
