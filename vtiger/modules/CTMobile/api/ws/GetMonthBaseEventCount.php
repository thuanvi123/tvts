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

class CTMobile_WS_GetMonthBaseEventCount extends CTMobile_WS_Controller {
	
	function process(CTMobile_API_Request $request) {
		global $current_user,$adb, $site_URL;
		$current_user = $this->getActiveUser();
		$moduleModel = Vtiger_Module_Model::getInstance('Calendar');
		if(!in_array($moduleModel->get('presence'), array('0','2'))){
			$message = vtranslate('Calendar','Calendar')." ".$this->CTTranslate('Module is Disabled');
			throw new WebServiceException(404,$message);
		}
		$userid = trim($request->get('userid'));
		$month = trim($request->get('month'));
		$year = trim($request->get('year'));
		$response = new CTMobile_API_Response();
		$recentEvent_data = array();
		$generator = new QueryGenerator('Events', $current_user);
		$generator->setFields(array('subject','activitytype','location','date_start','due_date','time_start','time_end','location','createdtime','modifiedtime','id'));
		$eventQuery = $generator->getQuery();
		if ($month == '') {
			$message = $this->CTTranslate('Month cannot be empty');
			$response->setError(1501, $message);
			return $response;
		}
		$year = $request->get('year');
		if ($year == '') {
			$message = $this->CTTranslate('Year cannot be empty');
			$response->setError(1501, $message);
			return $response;
		}
		if ($userid == '') {
			$message = $this->CTTranslate('Userid cannot be empty');
			$response->setError(1501, $message);
			return $response;
		}
		if($current_user->date_format == 'dd-mm-yyyy'){
			$format = 'd-m-Y';
		}else if($current_user->date_format == 'mm-dd-yyyy'){
			$format = 'm-d-Y';
		}else{
			$format = 'Y-m-d';
		}
		$startdate = date($format,strtotime(date($year.'-'.$month.'-01')));
		$enddate = date($format,strtotime(date($year.'-'.$month.'-t')));
		
		$startDateTime = Vtiger_Datetime_UIType::getDBDateTimeValue($startdate . ' 00:00:00');
		$endDateTime = Vtiger_Datetime_UIType::getDBDateTimeValue($enddate . ' 23:59:00');

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
			$type = $value['module'];
			if($type == 'Events' || $type == 'Calendar'){
				$start = $startDateTime;
				$end = $endDateTime;
			}else{
				$start = $startdate;
				$end = $enddate;
			}
			$userid = $current_user->id;
			$color = $value['color'];
			$targetModule = $value['targetModule'];
			$fieldName = $value['fieldname'];
			$isGroupId = false;
			$mapping = $value['mapping'];
			$conditions = $value['conditions']['rules'];
			$result = array(); 
			switch ($type) {
				case 'Events'			:	if($fieldName == 'date_start,due_date' || $userid) {
												$this->pullEvents($start, $end, $result,$userid,$color,$isGroupId,$conditions,$year,$month);
											} else {
												$this->pullDetails($start, $end, $result, $type, $fieldName, $color, $conditions);
											}
											break;
				case 'Calendar'			:	if($fieldName == 'date_start,due_date') {
												$this->pullTasks($start, $end, $result,$color,$year,$month);
											} else {
												$this->pullDetails($start, $end, $result, $type, $fieldName, $color);
											}
											break;
				case 'MultipleEvents'	:	$this->pullMultipleEvents($start,$end, $result,$mapping);break;
				case $type				:	$this->pullDetails($start, $end, $result, $type, $fieldName, $color,$conditions,$year,$month);break;
			}
			if(!empty($result)){
				$recentEvent_data = array_merge($recentEvent_data,$result);
			}
		}
										  
		if(count($recentEvent_data) == 0){
			$message = $this->CTTranslate('No event for this month'); 
			$response->setResult(array('GetEventCount'=>[],'date_format'=>$current_user->date_format,'hour_format'=>$current_user->hour_format,'code'=>404,'message'=>$message));
		} else {
			$recentEvent_data = array_values(array_unique($recentEvent_data));
			sort($recentEvent_data);
			$response->setResult(array('GetEventCount'=>$recentEvent_data,'date_format'=>$current_user->date_format,'hour_format'=>$current_user->hour_format,'message'=>''));
		}
		return $response;
	}

	protected function pullDetails($start, $end, &$result, $type, $fieldName, $color = null, $conditions = '',$year,$month) {
		global $current_user;
		$current_user = $this->getActiveUser();
		$user = Users_Record_Model::getCurrentUserModel();
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
			$userAndGroupIds = array_merge(array($user->getId()),$this->getGroupsIdsForUsers($user->getId()));
			$queryGenerator = new QueryGenerator($moduleModel->get('name'), $user);
			$meta = $queryGenerator->getMeta($moduleModel->get('name'));

			$queryGenerator->setFields(array_merge(array_merge($nameFields, array('id')), $fieldsList));
			$query = $queryGenerator->getQuery();
			$query.= " AND (($fieldsList[0] >= ? AND $fieldsList[1] < ?) OR ($fieldsList[1] >= ?)) ";
			$params = array($start,$end,$start);
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
			list ($modid, $crmid) = vtws_getIdComponents($record['id']);
			if(!empty($record[$fieldsList[0]])) {
				$start = $record[$fieldsList[0]];
			} else {
				$start = $record[$fieldsList[1]];
			}
			if(Users_Privileges_Model::isPermitted($moduleModel->get('name'), 'DetailView', $crmid)){
				if(date('Y',strtotime($start)) == $year && date('m',strtotime($start)) == $month){
					if(count($fieldsList) > 1){
						if($user->get('date_format') == 'dd-mm-yyyy'){
							$format = 'd-m-Y';
						}else if($user->get('date_format') == 'mm-dd-yyyy'){
							$format = 'm-d-Y';
						}else{
							$format = 'Y-m-d';
						}
						$startDate = Vtiger_Date_UIType::getDisplayDateValue($record[$fieldsList[0]]);
						$endDate = Vtiger_Date_UIType::getDisplayDateValue($record[$fieldsList[1]]);
						$dates = dateCustBet($startDate,$endDate,$format,$year,$month);
						if(!empty($dates)){
							$result = array_merge($result,$dates);
						}
					}else{
						$result[] = Vtiger_Date_UIType::getDisplayDateValue($start);
					}
				}
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

	protected function pullEvents($start, $end, &$result, $userid = false, $color = null,$isGroupId = false, $conditions = '',$year,$month) {
		global $current_user;
		$current_user = $this->getActiveUser();
		$dbStartDateTimeComponents = explode(' ', $start);
		$dbStartDate = $dbStartDateTimeComponents[0];

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
			$query.= "vtiger_activity.eventstatus != 'HELD' AND ";

		if(!empty($conditions)) {
			$conditions = Zend_Json::decode(Zend_Json::decode($conditions));
			$query .=  $this->generateCalendarViewConditionQuery($conditions).'AND ';
		}
		$query.= " ((concat(date_start, '', time_start)  >= '$start' AND concat(due_date, '', time_end) < '$end') OR ( due_date >= '$dbStartDate'))";

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
			$activityid = $record['activityid'];
			$dateTimeFieldInstance = new DateTimeField($record['date_start'].' '.$record['time_start']);
			$userDateTimeString = $dateTimeFieldInstance->getDisplayDateTimeValue($currentUser);
			$dateTimeComponents = explode(' ',$userDateTimeString);
			$dateComponent = $dateTimeComponents[0];
			$startDate = $dateComponent;

			$dateTimeFieldInstance = new DateTimeField($record['due_date'].' '.$record['time_end']);
			$userDateTimeString = $dateTimeFieldInstance->getDisplayDateTimeValue($currentUser);
			$dateTimeComponents = explode(' ',$userDateTimeString);
			$dateComponent = $dateTimeComponents[0];
			$endDate = $dateComponent;
			if(Users_Privileges_Model::isPermitted('Calendar', 'DetailView', $activityid)){
				if($currentUser->get('date_format') == 'dd-mm-yyyy'){
					$format = 'd-m-Y';
				}else if($currentUser->get('date_format') == 'mm-dd-yyyy'){
					$format = 'm-d-Y';
				}else{
					$format = 'Y-m-d';
				}
				$dates = dateCustBet($startDate,$endDate,$format,$year,$month);
				if(!empty($dates)){
					$result = array_merge($result,$dates);
				}
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

	protected function pullTasks($start, $end, &$result, $color = null,$year,$month) {
		global $current_user;
		$current_user = $this->getActiveUser();
		$dbStartDateTimeComponents = explode(' ', $start);
		$dbStartDate = $dbStartDateTimeComponents[0];

		$dbEndDateTimeComponents = explode(' ', $end);
		$dbEndDate = $dbEndDateTimeComponents[0];

		$user = Users_Record_Model::getCurrentUserModel();
		$db = PearDatabase::getInstance();

		$moduleModel = Vtiger_Module_Model::getInstance('Calendar');
		$userAndGroupIds = array_merge(array($user->getId()),$this->getGroupsIdsForUsers($user->getId()));
		$queryGenerator = new QueryGenerator($moduleModel->get('name'), $user);

		$queryGenerator->setFields(array('activityid','subject', 'taskstatus','activitytype', 'date_start','time_start','due_date','time_end','id'));
		$query = $queryGenerator->getQuery();

		$query.= " AND vtiger_activity.activitytype = 'Task' AND ";
		$currentUser = Users_Record_Model::getCurrentUserModel();
		$hideCompleted = $currentUser->get('hidecompletedevents');
		if($hideCompleted)
			$query.= "vtiger_activity.status != 'Completed' AND ";
		$query.= " ((date_start >= '$dbStartDate' AND due_date < '$dbEndDate') OR ( due_date >= '$dbStartDate'))";
		$params = $userAndGroupIds;
		$query.= " AND vtiger_crmentity.smownerid IN (".generateQuestionMarks($params).")";
		$queryResult = $db->pquery($query,$params);

		while($record = $db->fetchByAssoc($queryResult)){
			$activityid = $record['activityid'];
			$dateTimeFieldInstance = new DateTimeField($record['date_start'].' '.$record['time_start']);
			$userDateTimeString = $dateTimeFieldInstance->getDisplayDateTimeValue();
			$dateTimeComponents = explode(' ',$userDateTimeString);
			$dateComponent = $dateTimeComponents[0];
			$start = $dateComponent;
			if(Users_Privileges_Model::isPermitted('Calendar', 'DetailView', $activityid)){
				if(date('Y',strtotime($start)) == $year && date('m',strtotime($start)) == $month){
					$result[] = $start;
				}
			}
		}
	}
}

function dateCustBet($date_from,$date_to,$format,$year,$month){
	$date_from = Vtiger_Date_UIType::getDBInsertedValue($date_from);
	$date_to = Vtiger_Date_UIType::getDBInsertedValue($date_to);
	// Specify the start date. This date can be any English textual format  
	$date_from = strtotime($date_from); // Convert date to a UNIX timestamp  
	// Specify the end date. This date can be any English textual format  
	$date_to = strtotime($date_to); // Convert date to a UNIX timestamp    
	// Loop from the start date to end date and output all dates inbetween  
	$Retmasterdates = array();
	for ($i=$date_from; $i<=$date_to; $i+=86400) { 
		if(date('Y', $i) == $year && date('m', $i) == $month){
	    	$Retmasterdates[] = date($format, $i);  
	    }	
	}
	return $Retmasterdates;
}
