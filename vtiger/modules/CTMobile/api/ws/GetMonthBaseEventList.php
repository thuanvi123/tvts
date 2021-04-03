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
include_once 'include/QueryGenerator/QueryGenerator.php';

class CTMobile_WS_GetMonthBaseEventList extends CTMobile_WS_Controller {
	
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
		$user = Users::getActiveAdminUser();
		$month = trim($request->get('month'));
		$year = trim($request->get('year'));
		$response = new CTMobile_API_Response();
		$recentEvent_data = array();
		
		$eventQuery = "SELECT vtiger_activity.subject, vtiger_activity.activitytype, vtiger_activity.location, vtiger_activity.date_start, vtiger_activity.time_start, vtiger_activity.location, vtiger_crmentity.createdtime, vtiger_crmentity.modifiedtime, vtiger_activity.activityid, vtiger_crmentity.smownerid FROM vtiger_activity  INNER JOIN vtiger_crmentity ON vtiger_activity.activityid = vtiger_crmentity.crmid LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id LEFT JOIN vtiger_groups ON vtiger_crmentity.smownerid = vtiger_groups.groupid  WHERE vtiger_crmentity.deleted=0 AND vtiger_activity.activityid > 0";
		
		$month = $request->get('month');
		$year = $request->get('year');
		
		
		if($month == '' && $year == ''){
			$startdate = date('Y-m-d');
			$enddate = date('Y-m-d',strtotime("+7 days"));
		}else{
			$startdate = date($year.'-'.$month.'-01');
			$enddate = date($year.'-'.$month.'-t');
		}
		 
		
		$startDateTime = new DateTimeField($startdate . ' ' . date('H:i:s'));
		$userStartDate = $startDateTime->getDisplayDate();
		$userStartDateTime = new DateTimeField($userStartDate . ' 00:00:00');
		$startDateTime = $userStartDateTime->getDBInsertDateTimeValue();
		
		$endDateTime = new DateTimeField($enddate . ' ' . date('H:i:s'));
		$userEndDate = $endDateTime->getDisplayDate();
		$userEndDateTime = new DateTimeField($userEndDate . ' 23:59:00');
		$endDateTime = $userEndDateTime->getDBInsertDateTimeValue();

		$titleMessageEventSQL = $adb->pquery("SELECT notification_title,notification_message FROM ctmobile_notification_settings WHERE notification_type = ?",array('event_reminder'));
		$event_reminder_title = decode_html(decode_html($adb->query_result($titleMessageEventSQL, 0, "notification_title")));
		$event_reminder_message = decode_html(decode_html($adb->query_result($titleMessageEventSQL, 0, "notification_message")));

		$titleMessageTaskSQL = $adb->pquery("SELECT notification_title,notification_message FROM ctmobile_notification_settings WHERE notification_type = ?",array('task_reminder'));
		$task_reminder_title = decode_html(decode_html($adb->query_result($titleMessageTaskSQL, 0, "notification_title")));
		$task_reminder_message = decode_html(decode_html($adb->query_result($titleMessageTaskSQL, 0, "notification_message")));

		$eventQuery .= " AND vtiger_crmentity.setype = 'Calendar' AND CAST((CONCAT(vtiger_activity.date_start,' ',vtiger_activity.time_start)) AS DATETIME) BETWEEN '" . $startDateTime . "' and '" . $endDateTime . "'  AND vtiger_crmentity.deleted =0  ORDER BY vtiger_activity.date_start, time_start DESC";
																																																																																												
		$query = $adb->pquery($eventQuery);
		for($i=0; $i<$adb->num_rows($query); $i++) {
			$activityid = $adb->query_result($query, $i, 'activityid');

			$EventTaskQuery = $adb->pquery("SELECT * FROM  `vtiger_activity` WHERE activitytype = ? AND activityid = ?",array('Task',$activityid)); 
			if($adb->num_rows($EventTaskQuery) > 0){
				$wsid = CTMobile_WS_Utils::getEntityModuleWSId('Calendar');
				$recordId = $wsid.'x'.$activityid;
				$recordModule = 'Calendar';
				$invite_user = array();
				$notification_title = $task_reminder_title;
				$notification_message = getMergedDescription($task_reminder_message, $activityid, 'Calendar');
			}else{
				$wsid = CTMobile_WS_Utils::getEntityModuleWSId('Events');
				$recordId = $wsid.'x'.$activityid;
				$recordModule = 'Events';
				$recordModel =  Vtiger_Record_Model::getInstanceById($activityid,'Events');
				$invite_user = $recordModel->getInvities();
				$notification_title = $event_reminder_title;
				$notification_message = getMergedDescription($event_reminder_message, $activityid, 'Events');
			}

			$query_exist = "SELECT activity_id,reminder_time FROM vtiger_activity_reminder WHERE activity_id = ?";
			$result_exist = $adb->pquery($query_exist, array($activityid));
			$num_rows = $adb->num_rows($result_exist);

			if ($num_rows > 0) {
				$reminder_time = $adb->query_result($result_exist,0,'reminder_time');
				if($reminder_time == 0){
					if($recordModule == 'Calendar'){
						$query_exist = "SELECT notification_type,reminder_time FROM ctmobile_notification_settings WHERE notification_type = ?";
						$result_exist = $adb->pquery($query_exist, array('task_reminder'));
						$reminder_time = $adb->query_result($result_exist,0,'reminder_time');
					}else{
						$query_exist = "SELECT notification_type,reminder_time FROM ctmobile_notification_settings WHERE notification_type = ?";
						$result_exist = $adb->pquery($query_exist, array('event_reminder'));
						$reminder_time = $adb->query_result($result_exist,0,'reminder_time');
					}
				}
			}else{
				if($recordModule == 'Calendar'){
					$query_exist = "SELECT notification_type,reminder_time FROM ctmobile_notification_settings WHERE notification_type = ?";
					$result_exist = $adb->pquery($query_exist, array('task_reminder'));
					$reminder_time = $adb->query_result($result_exist,0,'reminder_time');
				}else{
					$query_exist = "SELECT notification_type,reminder_time FROM ctmobile_notification_settings WHERE notification_type = ?";
					$result_exist = $adb->pquery($query_exist, array('event_reminder'));
					$reminder_time = $adb->query_result($result_exist,0,'reminder_time');
				}
			}
			$eventSubject = $adb->query_result($query, $i, 'subject');
			$eventSubject = html_entity_decode($eventSubject, ENT_QUOTES, $default_charset);
			$eventtype = $adb->query_result($query, $i, 'activitytype');
			$eventtype = html_entity_decode($eventtype, ENT_QUOTES, $default_charset);
			$startDate = $adb->query_result($query, $i, 'date_start');
			$startTime = $adb->query_result($query, $i, 'time_start');

			$assigned_user_id = $adb->query_result($query, $i, 'smownerid');
			
			$startDateTime = Vtiger_Util_Helper::convertDateTimeIntoUsersDisplayFormat($startDate.' '.$startTime);
			list($startDate,$startTime,$ampm) = explode(' ', $startDateTime);
			if($ampm != ''){
				$startTime = $startTime.' '.$ampm;
			}
			$createdTime = $adb->query_result($query, $i, 'createdtime');
			if($createdTime!=''){
				$dateTimeFieldInstance = new DateTimeField($createdTime);
				$createdTime = $dateTimeFieldInstance->getDisplayDateTimeValue($current_user);
			}
			
			$modifiedtime = $adb->query_result($query, $i, 'modifiedtime');
			if($modifiedtime!=''){
				$dateTimeFieldInstance = new DateTimeField($modifiedtime);
				$modifiedtime = $dateTimeFieldInstance->getDisplayDateTimeValue($current_user);
			}
			
			$recentEvent_data[] = array('activityid'=> $recordId, 'module'=>$recordModule, 'eventSubject' => $eventSubject, 'activitytype' => $eventtype,'startDate' => $startDate,'startTime' => $startTime, 'startDateTime' => $startDateTime,'createdTime' => $createdTime, 'modifiedtime' => $modifiedtime,'reminder_time'=>$reminder_time,'assigned_user_id'=>$assigned_user_id,'invite_user'=>$invite_user,'notification_title'=>$notification_title,'notification_message'=>$notification_message);
			
		}
		
										  
		if(count($recentEvent_data) == 0){
			$message = vtranslate('No event for this month','CTMobile'); 
			$response->setResult(array('GetEvents'=>[],'date_format'=>$current_user->date_format,'hour_format'=>$current_user->hour_format,'module'=>'Events','code'=>404,'message'=>$message));
		} else {
			$response->setResult(array('GetEvents'=>$recentEvent_data,'date_format'=>$current_user->date_format,'hour_format'=>$current_user->hour_format,'module'=>'Events', 'message'=>''));
		}
		
		return $response;
	}
}
