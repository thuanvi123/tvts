<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

vimport ('~~/include/Webservices/Query.php');

class CTMobile_WS_GetDateBaseSharedEventList extends CTMobile_WS_Controller {
	
	function process(CTMobile_API_Request $request) {
		global $current_user,$adb, $site_URL;
		$current_user = $this->getActiveUser();
		$roleid = $current_user->roleid;
		$mode = $request->get('mode');
		if($mode == 'getActivityData'){

			$assignedUsers = Users_Record_Model::getAll();
			$USER_MODEL = Users_Record_Model::getCurrentUserModel();
			$AccessibleUsers = array_keys($USER_MODEL->getAccessibleUsers());
			$assignedTo = array();
			foreach ($assignedUsers as $userid => $users) {
				if(in_array($userid, $AccessibleUsers)){
					$assignedTo[] = array("value"=>$userid,"label"=>decode_html(decode_html($users->get('first_name')))." ".decode_html(decode_html($users->get('last_name'))));
				}
			}

			$eventstatus = array();
			$eventPicklistValues = Vtiger_Util_Helper::getRoleBasedPicklistValues('eventstatus',$roleid);
			foreach($eventPicklistValues as $epvalue){
				$eventstatus[] = array('value'=>$epvalue, 'label'=>vtranslate($epvalue,'Events'));
			}

			$activitytype = array();
			$actPicklistValues = Vtiger_Util_Helper::getRoleBasedPicklistValues('activitytype',$roleid);
			foreach($actPicklistValues as $epvalue){
				$activitytype[] = array('value'=>$epvalue, 'label'=>vtranslate($epvalue,'Events'));
			}

			$priority = array();
			$priorityPicklistValues = Vtiger_Util_Helper::getRoleBasedPicklistValues('taskpriority',$roleid);
			foreach($priorityPicklistValues as $epvalue){
				$priority[] = array('value'=>$epvalue, 'label'=>vtranslate($epvalue,'Calendar'));
			}

			$response = new CTMobile_API_Response();
			$response->setResult(array('users_label'=>vtranslate('Assigned To','Vtiger'),'status_label'=>vtranslate('LBL_STATUS','Calendar'),'activitytype_label'=>vtranslate('Activity Type','Events'),'priority_label'=>vtranslate('Priority','Events'),'users'=>$assignedTo,'status'=>$eventstatus,'activitytype'=>$activitytype,'priority'=>$priority));
			return $response;

		}else{

			$moduleModel = Vtiger_Module_Model::getInstance('Calendar');
			if(!in_array($moduleModel->get('presence'), array('0','2'))){
				$message = vtranslate('Calendar','Calendar')." ".$this->CTTranslate('Module is disabled');
				throw new WebServiceException(404,$message);
			}

			$userid = trim($request->get('userid'));
			$status = trim($request->get('status'));
			$activitytype = trim($request->get('activitytype'));
			$priority = trim($request->get('priority'));
			$startdate = trim($request->get('startdate'));
			$enddate = trim($request->get('enddate'));
			$recentEvent_data = array();
			$default_charset = VTWS_PreserveGlobal::getGlobal('default_charset');

			$start = $startdate;
			$end = $enddate;

			$getColorSql = $adb->pquery("SELECT * FROM `vtiger_calendar_default_activitytypes` WHERE module = 'Events'",array());
			$color = $adb->query_result($getColorSql,0,'defaultcolor');
			//$color = "";
			$conditions = "";
			$recentEvent_data = $this->pullEvents($start, $end,$userid,$color,$isGroupId,$conditions,$status,$activitytype,$priority);
			//$recentEvent_data = $this->pullTasks($start, $end, $result,$color);							
			if(!empty($result)){
				$recentEvent_data = array_merge($recentEvent_data,$result);
			}
			
			$picklistValues1 = array();
			$eventPicklistValues = Vtiger_Util_Helper::getRoleBasedPicklistValues('eventstatus',$roleid);
			foreach($eventPicklistValues as $epvalue){
				$picklistValues1[] = array('value'=>$epvalue, 'label'=>vtranslate($epvalue,'Events'));
			}
			$picklistValues2 = array();
			$taskPicklistValues = Vtiger_Util_Helper::getRoleBasedPicklistValues('taskstatus',$roleid);
			foreach($taskPicklistValues as $tpvalue){
				$picklistValues2[] = array('value'=>$tpvalue, 'label'=>vtranslate($tpvalue,'Calendar'));
			}
			
			$response = new CTMobile_API_Response();					  

			if(count($recentEvent_data) == 0){
				$message =  $this->CTTranslate('No event or task for this date');
				$response->setResult(array('GetEventList'=>[],'code'=>404,'message'=>$message,'eventstatus'=>$picklistValues1,'taskstatus'=>$picklistValues2));
			} else {
				$response->setResult(array('GetEventList'=>$recentEvent_data,'message'=>'','eventstatus'=>$picklistValues1,'taskstatus'=>$picklistValues2));
			}
			return $response;
		}
	}

	protected function generateCalendarViewConditionQuery($conditions) {
		$conditionQuery = $operator = '';
		switch ($conditions['operator']) {
			case 'e' : $operator = '=';
		}

		if(!empty($operator) && !empty($conditions['fieldname']) && !empty($conditions['value'])) {
			$conditionQuery = ' '.$conditions['fieldname'].$operator.'\'' .$conditions['value'].'\' ';
		}
		return $conditionQuery;
	}


	protected function pullEvents($start, $end,$userid = false, $color = null,$isGroupId = false, $conditions = '',$status,$activitytype,$priority) {
		global $current_user;
		$result = array();
		$current_user = $this->getActiveUser();
		$startDateTime = Vtiger_Datetime_UIType::getDBDateTimeValue($start . ' 00:00:00');
		$endDateTime = Vtiger_Datetime_UIType::getDBDateTimeValue($end . ' 23:59:00');

		$currentDateTime = date("Y-m-d H:i:s");

		$currentUser = Users_Record_Model::getCurrentUserModel();
		$db = PearDatabase::getInstance();
		$groupsIds = Vtiger_Util_Helper::getGroupsIdsForUsers($currentUser->getId());
		require('user_privileges/user_privileges_'.$currentUser->id.'.php');
		require('user_privileges/sharing_privileges_'.$currentUser->id.'.php');

		$moduleModel = Vtiger_Module_Model::getInstance('Events');
		
		$queryGenerator = new QueryGenerator($moduleModel->get('name'), $currentUser);
		

		$queryGenerator->setFields(array('subject', 'eventstatus', 'visibility','date_start','time_start','due_date','time_end','assigned_user_id','id','activitytype','recurringtype'));
		$query = $queryGenerator->getQuery();
        if($activitytype != ''){
        	$activity_type = explode(',',$activitytype);
        	$query.= " AND vtiger_activity.activitytype IN ('".implode("','", $activity_type)."')";
        }else{
			$query.= " AND vtiger_activity.activitytype NOT IN ('Emails','Task') ";
        }
		/*$hideCompleted = $currentUser->get('hidecompletedevents');
		if($hideCompleted)
			$query.= "vtiger_activity.eventstatus != 'Held' AND ";*/
		if($status != ''){
			$eventstatus = explode(',',$status);
			$query .= " AND vtiger_activity.eventstatus IN ('".implode("','", $eventstatus)."')";
		}

		if($priority != ''){
			$eventpriority = explode(',',$priority);
			$query .= " AND vtiger_activity.priority IN ('".implode("','", $eventpriority)."')";
		}

		if(!empty($conditions)) {
			$conditions = Zend_Json::decode(Zend_Json::decode($conditions));
			$query .=  $this->generateCalendarViewConditionQuery($conditions).'AND ';
		}
		if($start != '' && $end != ''){
			$query.= " AND CONCAT(date_start,' ',time_start) <= '".$endDateTime."' AND CONCAT(due_date,' ',time_end) >= '".$startDateTime."' ";
		}

		$params = array();
		if(!empty($userid)){
			$assignUsers = explode(',',$userid);
			$query.= " AND vtiger_crmentity.smownerid IN ('".implode("','", $assignUsers)."')";
			//$params = array_merge(array($eventUserId), $this->getGroupsIdsForUsers($eventUserId));
		}
		
		$queryResult = $db->pquery($query, $params);

		while($record = $db->fetchByAssoc($queryResult)){
			$item = array();
			$crmid = $record['activityid'];
			$visibility = $record['visibility'];
			$activitytype = $record['activitytype'];
			$status = vtranslate($record['eventstatus'],'Events');
			$ownerId = $record['smownerid'];
			//$item['id'] = $crmid;
			$item['id'] = vtws_getWebserviceEntityId('Events',$crmid);
			$item['visibility'] = $visibility;
			$item['activitytype'] = $activitytype;
			$item['status'] = $status;
			$recordBusy = true;
			if(in_array($ownerId, $groupsIds)) {
				$recordBusy = false;
			} else if($ownerId == $currentUser->getId()){
				$recordBusy = false;
			}
			// if the user is having view all permission then it should show the record
			// as we are showing in detail view
			if($profileGlobalPermission[1] ==0 || $profileGlobalPermission[2] ==0) {
				$recordBusy = false;
			}

			if(!$currentUser->isAdminUser() && $visibility == 'Private' && $userid && $userid != $currentUser->getId() && $recordBusy) {
				$item['title'] = decode_html($userName).' - '.decode_html(vtranslate('Busy','Events')).'*';
				//$item['url']   = '';
			} else {
				$item['title'] = decode_html($record['subject']);
				//$item['url']   = sprintf('index.php?module=Calendar&view=Detail&record=%s', $crmid);
			}

			$isFutureEvents = false;
           	$startDateTimes = $record['date_start'].' '.$record['time_start'];
            if(strtotime($startDateTimes) > strtotime($currentDateTime)){
            	$isFutureEvents = true;
            }
			$item['start'] = Vtiger_Util_Helper::convertDateTimeIntoUsersDisplayFormat($record['date_start'].' '.$record['time_start']);
			$item['end'] = Vtiger_Util_Helper::convertDateTimeIntoUsersDisplayFormat($record['due_date'].' '.$record['time_end']);

			$item['color'] = $color;
			$item['module'] = $moduleModel->getName();
			$item['modulelabel'] = vtranslate($moduleModel->getName(),$moduleModel->getName());
			$item['moduleicon'] = CTMobile_WS_Utils::getModuleURL($moduleModel->getName());
			$item['isShowStatus'] = true;
			$item['isShowCheckin'] = true;
			if($record['eventstatus'] == 'Held'){
				$item['isShowCheckin'] = false;
			}
			$attendance_data = $this->attendance_status($crmid);
			$item['ctattendance_status'] = $attendance_data['ctattendance_status'];
			$item['attendance_status'] = $attendance_data['attendance_status'];
			if($attendance_data['ctattendanceid'] != ''){
				$item['ctattendanceid'] = CTMobile_WS_Utils::getEntityModuleWSId('CTAttendance').'x'.$attendance_data['ctattendanceid'];
			}else{
				$item['ctattendanceid'] = $attendance_data['ctattendanceid'];
			}
			$item['isFutureEvents'] = $isFutureEvents;
			$item['hour_format'] = $currentUser->get('hour_format');
			if(Users_Privileges_Model::isPermitted('Calendar', 'DetailView', $crmid)){
				$result[] = $item;
			}
		}
		return $result;
	}

	protected function pullTasks($start, $end, &$result, $color = null) {
		global $current_user;
		$current_user = $this->getActiveUser();
		$startDateTime = Vtiger_Datetime_UIType::getDBDateTimeValue($start . ' 00:00:00');
		$endDateTime = Vtiger_Datetime_UIType::getDBDateTimeValue($end . ' 23:59:00');

		$dbStartDateTimeComponents = explode(' ', $startDateTime);
		$dbStartDate = $dbStartDateTimeComponents[0];

		$dbEndDateTimeComponents = explode(' ', $endDateTime);
		$dbEndDate = $dbEndDateTimeComponents[0];

		$db = PearDatabase::getInstance();
		$currentUser = Users_Record_Model::getCurrentUserModel();
		$moduleModel = Vtiger_Module_Model::getInstance('Calendar');
		$userAndGroupIds = array_merge(array($currentUser->getId()),$this->getGroupsIdsForUsers($currentUser->getId()));
		$queryGenerator = new QueryGenerator($moduleModel->get('name'), $currentUser);

		$queryGenerator->setFields(array('activityid','subject', 'taskstatus','activitytype', 'date_start','time_start','due_date','time_end','id'));
		$query = $queryGenerator->getQuery();

		$query.= " AND vtiger_activity.activitytype = 'Task' AND ";
		$hideCompleted = $currentUser->get('hidecompletedevents');
		if($hideCompleted)
			$query.= "vtiger_activity.status != 'Completed' AND ";

		//$query.= " date_start >= '".$dbStartDate."' AND date_start <= '".$dbEndDate."' ";
		$query.= " CONCAT(date_start,' ',time_start) <= '".$endDateTime."' AND date_start >= '".$dbStartDate."' ";
		//$query.= " ((date_start >= '$start' AND due_date < '$end') OR ( due_date >= '$start'))";
		$params = array($currentUser->getId());
		$query.= " AND vtiger_crmentity.smownerid IN (".generateQuestionMarks($params).")";
		$queryResult = $db->pquery($query,$params);

		while($record = $db->fetchByAssoc($queryResult)){
			$item = array();
			$crmid = $record['activityid'];
			$item['title'] = decode_html($record['subject']);
			$item['status'] = vtranslate($record['status'],'Calendar');
			$item['activitytype'] = $record['activitytype'];
			//$item['id'] = $crmid;
			$item['id'] = vtws_getWebserviceEntityId('Calendar',$crmid);

			$item['start'] = Vtiger_Util_Helper::convertDateTimeIntoUsersDisplayFormat($record['date_start'].' '.$record['time_start']);
			$item['end'] =  Vtiger_Date_UIType::getDisplayDateValue($record['due_date']);

			$item['color'] = $color;
			$item['module'] = $moduleModel->getName();
			$item['modulelabel'] = vtranslate($moduleModel->getName(),$moduleModel->getName());
			$item['moduleicon'] = CTMobile_WS_Utils::getModuleURL($moduleModel->getName());
			$item['isShowStatus'] = true;
			$item['isShowCheckin'] = false;
			$item['hour_format'] = $currentUser->get('hour_format');
			if(Users_Privileges_Model::isPermitted('Calendar', 'DetailView', $crmid)){
				$result[] = $item;
			}
		}
	}

	function attendance_status($recordid){
		global $current_user,$adb, $site_URL;
		$current_user = $this->getActiveUser();
		$employee_name = $current_user->id;

		$user =  Users::getActiveAdminUser();
		$recentEvent_data = array();
		$generator = new QueryGenerator('CTAttendance', $user);
		$generator->setFields(array('employee_name','attendance_status','createdtime','modifiedtime','id'));
		//$generator->addCondition('attendance_status', 'check_in', 'e');
		$eventQuery = $generator->getQuery();
		$eventQuery .= " AND vtiger_ctattendance.employee_name = '$employee_name' AND vtiger_ctattendance.eventid = '$recordid'";
		
		$query = $adb->pquery($eventQuery);
		$num_rows = $adb->num_rows($query);
		if( $num_rows > 0){
			$ctattendanceid = $adb->query_result($query,$num_rows-1,'ctattendanceid');
			$ctattendance_status = $adb->query_result($query,$num_rows-1,'attendance_status');
			$attendance_status = true;
		} else {
			$ctattendance_status = "";
			$attendance_status = false;
			$ctattendanceid = '';
		}
		$data = array();
		$data['attendance_status'] = vtranslate($ctattendance_status,'CTAttendance');
		$data['ctattendance_status'] = $attendance_status;
		$data['ctattendanceid'] = $ctattendanceid;
		if($ctattendance_status == 'check_out'){
			$data['ctattendance_status'] = false;
			$data['ctattendanceid'] = "";
		}
		return $data;
	}
}
