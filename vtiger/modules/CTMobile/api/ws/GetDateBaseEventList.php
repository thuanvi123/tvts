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

class CTMobile_WS_GetDateBaseEventList extends CTMobile_WS_Controller {
	
	function process(CTMobile_API_Request $request) {
		global $current_user,$adb, $site_URL;
		$current_user = $this->getActiveUser();
		$roleid = $current_user->roleid;
		$moduleModel = Vtiger_Module_Model::getInstance('Calendar');
		if(!in_array($moduleModel->get('presence'), array('0','2'))){
			$message = vtranslate('Calendar','Calendar')." ".$this->CTTranslate('Module is disabled');
			throw new WebServiceException(404,$message);
		}
		$userid = trim($request->get('userid'));
		$startdate = trim($request->get('startdate'));
		$enddate = trim($request->get('enddate'));
		$recentEvent_data = array();
		$default_charset = VTWS_PreserveGlobal::getGlobal('default_charset');

		$query = "SELECT * FROM vtiger_calendar_user_activitytypes 
			INNER JOIN vtiger_calendar_default_activitytypes on vtiger_calendar_default_activitytypes.id=vtiger_calendar_user_activitytypes.defaultid 
			WHERE vtiger_calendar_user_activitytypes.userid=?";
		$results = $adb->pquery($query, array($current_user->id));
		$rows = $adb->num_rows($results);

		$calendarViewTypes = Array();
		for($i=0; $i<$rows; $i++){
			$activityTypes = $adb->query_result_rowdata($results, $i);
			$moduleInstance = Vtiger_Module_Model::getInstance($activityTypes['module']);
			//If there is no module view permission, should not show in calendar view
			if($moduleInstance === false || !$moduleInstance->isPermitted('Detail')) {
				continue;
			}
			$type = '';
			if(in_array($activityTypes['module'], array('Events','Calendar')) && $activityTypes['isdefault']) {
				$type = $activityTypes['module'].'_'.$activityTypes['isdefault'];
			}
			$fieldNamesList = Zend_Json::decode(html_entity_decode($activityTypes['fieldname']));
			$fieldLabelsList = array();
			foreach ($fieldNamesList as $fieldName) {
				$fieldInstance = Vtiger_Field_Model::getInstance($fieldName, $moduleInstance);
				if ($fieldInstance) {
					//If there is no field view permission, should not show in calendar view
					if (!$type && !$fieldInstance->isViewableInDetailView()) {
						$fieldLabelsList = array();
						break;
					}
					$fieldLabelsList[$fieldName] = $fieldInstance->label;
				}
			}

			$conditionsName = '';
			if (!empty($activityTypes['conditions'])) {
				$conditions = Zend_Json::decode(decode_html($activityTypes['conditions']));
				$conditions = Zend_Json::decode($conditions);
				$conditionsName = $conditions['value'];
			}
			$fieldInfo = array(	'module'	=> $activityTypes['module'],
								'fieldname' => implode(',', array_keys($fieldLabelsList)),
								'fieldlabel'=> implode(',', $fieldLabelsList),
								'visible'	=> $activityTypes['visible'],
								'color'		=> $activityTypes['color'],
								'type'		=> $type,
								'conditions'=> array(
												'name' => $conditionsName,
												'rules' => $activityTypes['conditions']
												)
			);

			if($activityTypes['visible'] == '1') {
				if ($fieldLabelsList) {
					$calendarViewTypes['visible'][] = $fieldInfo;
				}
			} else {
				$calendarViewTypes['invisible'][] = $fieldInfo;
			}
		}

		foreach($calendarViewTypes['visible'] as $key=>$value) {  
			$start = $startdate;
			$end = $enddate;
			$type = $value['module'];
			$userid = $current_user->id;
			$color = $value['color'];
			$targetModule = $value['targetModule'];
			$fieldName = $value['fieldname'];
			$isGroupId = $value['group'];
			$mapping = $value['mapping'];
			$conditions = $value['conditions']['rules'];
			$result = array(); 
			switch ($type) {
				case 'Events'			:	if($fieldName == 'date_start,due_date' || $userid) {
												$this->pullEvents($start, $end, $result,$userid,$color,$isGroupId,$conditions);
											} else {
												$this->pullDetails($start, $end, $result, $type, $fieldName, $color, $conditions);
											}
											break;
				case 'Calendar'			:	if($fieldName == 'date_start,due_date') {
												$this->pullTasks($start, $end, $result,$color);
											} else {
												$this->pullDetails($start, $end, $result, $type, $fieldName, $color);
											}
											break;
				case 'MultipleEvents'	:	$this->pullMultipleEvents($start,$end, $result,$mapping);break;
				case $type				:	$this->pullDetails($start, $end, $result, $type, $fieldName, $color);break;
			}
			if(!empty($result)){
				$recentEvent_data = array_merge($recentEvent_data,$result);
			}
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

	protected function pullDetails($start, $end, &$result, $type, $fieldName, $color = null, $conditions = '') {
		global $current_user;
		$current_user = $this->getActiveUser();
		//$start = Vtiger_Date_UIType::getDBInsertedValue($start);
		//$end = Vtiger_Date_UIType::getDBInsertedValue($end);
		
		$currentUser = Users_Record_Model::getCurrentUserModel();
		$moduleModel = Vtiger_Module_Model::getInstance($type);
		$nameFields = $moduleModel->getNameFields();
		foreach($nameFields as $i => $nameField) {
			$fieldInstance = $moduleModel->getField($nameField);
			if(!$fieldInstance->isViewable()) {
				unset($nameFields[$i]);
			}
		}
		$nameFields = array_values($nameFields);
		$selectFields = implode(',', $nameFields);		
		$fieldsList = explode(',', $fieldName);
		if(count($fieldsList) == 2) {
			$db = PearDatabase::getInstance();
			$user = Users_Record_Model::getCurrentUserModel();
			$userAndGroupIds = array($user->getId());
			$queryGenerator = new QueryGenerator($moduleModel->get('name'), $user);
			$meta = $queryGenerator->getMeta($moduleModel->get('name'));
			
			$queryGenerator->setFields(array_merge(array_merge($nameFields, array('id')), $fieldsList));
			$query = $queryGenerator->getQuery();
			$query.= " AND (($fieldsList[0] >= ? AND $fieldsList[1] < ?) OR ($fieldsList[1] >= ?) AND $fieldsList[0] <= ?) ";
			$params = array($start,$end,$start,$end);
			$query.= " AND vtiger_crmentity.smownerid IN (".generateQuestionMarks($userAndGroupIds).")";
			$params = array_merge($params, $userAndGroupIds);
			$queryResult = $db->pquery($query, $params);

			$records = array();
			while($rowData = $db->fetch_array($queryResult)) {
				$records[] = DataTransform::sanitizeDataWithColumn($rowData, $meta);
			}
		} else {
			if($fieldName == 'birthday') {
				$startDateComponents = explode('-', $start);
				$endDateComponents = explode('-', $end);

				$year = $startDateComponents[0];
				$db = PearDatabase::getInstance();
				$user = Users_Record_Model::getCurrentUserModel();
				$userAndGroupIds = array_merge(array($user->getId()),$this->getGroupsIdsForUsers($user->getId()));
				$queryGenerator = new QueryGenerator($moduleModel->get('name'), $user);
				$meta = $queryGenerator->getMeta($moduleModel->get('name'));

				$queryGenerator->setFields(array_merge(array_merge($nameFields, array('id')), $fieldsList));
				$query = $queryGenerator->getQuery();
				$query.= " AND ((CONCAT('$year-', date_format(birthday,'%m-%d')) >= ? AND CONCAT('$year-', date_format(birthday,'%m-%d')) <= ? )";
				$params = array($start,$end);
				$endDateYear = $endDateComponents[0]; 
				if ($year !== $endDateYear) {
					$query .= " OR (CONCAT('$endDateYear-', date_format(birthday,'%m-%d')) >= ?  AND CONCAT('$endDateYear-', date_format(birthday,'%m-%d')) <= ? )"; 
					$params = array_merge($params,array($start,$end));
				} 
				$query .= ")";
				$query.= " AND vtiger_crmentity.smownerid IN (".  generateQuestionMarks($userAndGroupIds).")";
				$params = array_merge($params,$userAndGroupIds);
				$queryResult = $db->pquery($query, $params);
				$records = array();
				while($rowData = $db->fetch_array($queryResult)) {
					$records[] = DataTransform::sanitizeDataWithColumn($rowData, $meta);
				}
			} else {
				$query = "SELECT $selectFields, $fieldsList[0] FROM $type";
				$query.= " WHERE $fieldsList[0] >= '$start' AND $fieldsList[0] <= '$end' ";

				if(!empty($conditions)) {
					$conditions = Zend_Json::decode(Zend_Json::decode($conditions));
					$query .=  'AND '.$this->generateCalendarViewConditionQuery($conditions);
				}

				if($type == 'PriceBooks') {
					$records = $this->queryForRecords($query, false);
				} else {
					$records = $this->queryForRecords($query);
				}
			}
		}
		foreach ($records as $record) {
			$item = array();
			list ($modid, $crmid) = vtws_getIdComponents($record['id']);
			//$item['id'] = $crmid;
			$item['id'] = $record['id'];
			$item['title'] = decode_html($record[$nameFields[0]]);
			if(count($nameFields) > 1) {
				$item['title'] = decode_html(trim($record[$nameFields[0]].' '.$record[$nameFields[1]]));
			}
			if(!empty($record[$fieldsList[0]])) {
				$item['start'] = $record[$fieldsList[0]];
			} else {
				$item['start'] = $record[$fieldsList[1]];
			}
			if(count($fieldsList) == 2) {
				$item['end'] = $record[$fieldsList[1]];
				$item['end'] = Vtiger_Date_UIType::getDisplayDateValue($item['end']);
			}else{
				$item['end'] = "";
			}
			if($fieldName == 'birthday') {
				$recordDateTime = new DateTime($record[$fieldName]); 

				$calendarYear = $year; 
				if($recordDateTime->format('m') < $startDateComponents[1]) { 
						$calendarYear = $endDateYear; 
				} 
				$recordDateTime->setDate($calendarYear, $recordDateTime->format('m'), $recordDateTime->format('d'));
				$item['start'] = $recordDateTime->format('Y-m-d');
			}

			$item['start'] = Vtiger_Date_UIType::getDisplayDateValue($item['start']);

			$urlModule = $type;
			if ($urlModule === 'Events') {
				$urlModule = 'Calendar';
			}
			$item['status'] = "";
			$item['isShowStatus'] = false;
			$item['color'] = $color;
			$item['module'] = $moduleModel->getName();
			$item['modulelabel'] = vtranslate($moduleModel->getName(),$moduleModel->getName());
			$item['moduleicon'] = CTMobile_WS_Utils::getModuleURL($moduleModel->getName());
			$item['isShowCheckin'] = false;
			$item['hour_format'] = $currentUser->get('hour_format');
			if(Users_Privileges_Model::isPermitted($moduleModel->getName(), 'DetailView', $crmid)){
				$result[] = $item;
			}
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

	protected function getGroupsIdsForUsers($userId) {
		vimport('~~/include/utils/GetUserGroups.php');

		$userGroupInstance = new GetUserGroups();
		$userGroupInstance->getAllUserGroups($userId);
		return $userGroupInstance->user_groups;
	}

	protected function queryForRecords($query, $onlymine=true) {
		$user = Users_Record_Model::getCurrentUserModel();
		if ($onlymine) {
			$groupIds = $this->getGroupsIdsForUsers($user->getId());
			$groupWsIds = array();
			foreach($groupIds as $groupId) {
				$groupWsIds[] = vtws_getWebserviceEntityId('Groups', $groupId);
			}
			$userwsid = vtws_getWebserviceEntityId('Users', $user->getId());
			$userAndGroupIds = array_merge(array($userwsid),$groupWsIds);
			$query .= " AND assigned_user_id IN ('".implode("','",$userAndGroupIds)."')";
		}
		// TODO take care of pulling 100+ records
		return vtws_query($query.';', $user);
	}

	protected function pullEvents($start, $end, &$result, $userid = false, $color = null,$isGroupId = false, $conditions = '') {
		global $current_user;
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
		if($userid && !$isGroupId){
			$focus = new Users();
			$focus->id = $userid;
			$focus->retrieve_entity_info($userid, 'Users');
			$user = Users_Record_Model::getInstanceFromUserObject($focus);
			$userName = $user->getName();
			$queryGenerator = new QueryGenerator($moduleModel->get('name'), $user);
		}else{
			$queryGenerator = new QueryGenerator($moduleModel->get('name'), $currentUser);
		}

		$queryGenerator->setFields(array('subject', 'eventstatus', 'visibility','date_start','time_start','due_date','time_end','assigned_user_id','id','activitytype','recurringtype'));
		$query = $queryGenerator->getQuery();

		$query.= " AND vtiger_activity.activitytype NOT IN ('Emails','Task') AND ";
		$hideCompleted = $currentUser->get('hidecompletedevents');
		if($hideCompleted)
			$query.= "vtiger_activity.eventstatus != 'Held' AND ";

		if(!empty($conditions)) {
			$conditions = Zend_Json::decode(Zend_Json::decode($conditions));
			$query .=  $this->generateCalendarViewConditionQuery($conditions).'AND ';
		}
		$query.= " CONCAT(date_start,' ',time_start) <= '".$endDateTime."' AND CONCAT(due_date,' ',time_end) >= '".$startDateTime."' ";

		$params = array();
		if(empty($userid)){
			$eventUserId  = $currentUser->getId();
			$params = array_merge(array($eventUserId), $this->getGroupsIdsForUsers($eventUserId));
		}else{
			$eventUserId = $userid;
			$params = array($eventUserId);
		}

		$query.= " AND vtiger_crmentity.smownerid IN (".  generateQuestionMarks($params).")";
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
	}

	protected function pullMultipleEvents($start, $end, &$result, $data) {

		foreach ($data as $id=>$backgroundColorAndTextColor) {
			$userEvents = array();
			$colorComponents = explode(',',$backgroundColorAndTextColor);
			$this->pullEvents($start, $end, $userEvents ,$id, $colorComponents[0], $colorComponents[2]);
			$result[$id] = $userEvents;
		}
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
