<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
include_once dirname(__FILE__) . '/models/SearchFilter.php';

class CTMobile_WS_ListTimeTracking extends CTMobile_WS_Controller {

	function process(CTMobile_API_Request $request) {
		$default_charset = VTWS_PreserveGlobal::getGlobal('default_charset');
		define("SECONDS_PER_HOUR", 60*60);
		global $current_user, $adb; // Few core API assumes this variable availability
		$current_user = $this->getActiveUser();
		$roleid = $current_user->roleid;
		$time_zone = $current_user->time_zone;
		$presence = array('0', '2');
		$index = trim($request->get('index'));
		$size = trim($request->get('size'));
		$tracking_date = trim($request->get('tracking_date'));
		$tracking_users = trim($request->get('tracking_user'));
		$module = 'CTTimeTracker';
		$TimeTrackerModuleModel = Vtiger_Module_Model::getInstance('CTTimeTracker');
		
	    $nowInDBFormat = date('Y-m-d H:i:s');
	    // calulate the difference in seconds
	    list($date_end, $time_end) = explode(' ', $nowInDBFormat);
		
		$morefields = array('id','tracking_title','related_to','total_time','total_hour','total_min','tracking_user','assigned_user_id','tracking_notes','tracking_status');	
			
		$customView = new CustomView();
		$filterid = $customView->getViewId($module);
		$filterOrAlertInstance = CTMobile_WS_FilterModel::modelWithId($module, $filterid);

		$generator = new QueryGenerator($module, $current_user);
		$generator->setFields($morefields);
		$query = $generator->getQuery();
		$total_time_sql = $query;
		if($tracking_users != '' && $tracking_users != 'all'){
			$tracking_user_id = explode('x', $tracking_users);
			$user_id = $tracking_user_id[1];
			$query.= " AND vtiger_cttimetracker.tracking_user = '$user_id' ";
			$total_time_sql.= " AND vtiger_cttimetracker.tracking_user = '$user_id' ";
		}
		if($tracking_date != ''){
			$tracking_date = Vtiger_Date_UIType::getDBInsertedValue($tracking_date);
			$date = new DateTime($tracking_date, new DateTimeZone($time_zone));
			$date->setTimezone(new DateTimeZone('UTC'));
			$startdate = $date->format('Y-m-d H:i:s');
			$date = new DateTime($tracking_date.' 23:59:00', new DateTimeZone($time_zone));
			$date->setTimezone(new DateTimeZone('UTC'));
			$enddate = $date->format('Y-m-d H:i:s');
			$query.= " AND vtiger_crmentity.createdtime BETWEEN '".$startdate."' AND '".$enddate."' ";
			$total_time_sql.= " AND vtiger_crmentity.createdtime BETWEEN '".$startdate."' AND '".$enddate."' ";
		}
		$query.= " ORDER BY vtiger_crmentity.modifiedtime DESC ";
		$totalQuery = $query;
		$totalParams = $filterOrAlertInstance->queryParameters();
		$totalResults = $adb->pquery($totalQuery,$totalParams);
		$totalRecords = $adb->num_rows($totalResults);
		if($index && $size){
			$limit = ($index*$size) - $size;
			$query .= sprintf(" LIMIT %s, %s", $limit, $size);
			if($totalRecords > ($index*$size)){
					$isLast = false;
			}else{
				$isLast = true;
			}	
		}else{
			$isLast = true;
		}
		$prequeryResult = $adb->pquery($query, $filterOrAlertInstance->queryParameters());
		$records = new SqlResultIterator($adb, $prequeryResult);
		$modifiedRecords = array();

		foreach($records as $record) {
			if ($record instanceof SqlResultIteratorRow) {
				$record = $record->data;
				// Remove all integer indexed mappings
				for($index = count($record); $index > -1; --$index) {
					if(isset($record[$index])) {
						unset($record[$index]);
					}
				}
			}
			$routeRecord = array();
			$recordid = $record['cttimetrackerid'];
			$deleteAction = Users_Privileges_Model::isPermitted('CTTimeTracker', 'Delete', $recordid);
			$routeRecord['id'] = vtws_getWebserviceEntityId($module, $recordid);

			$routeRecord['tracking_title'] = decode_html(decode_html($record['tracking_title']));
			$routeRecord['tracking_notes'] = decode_html(decode_html($record['tracking_notes']));

			$routeRecord['tracking_status'] = $record['tracking_status'];
			if($routeRecord['tracking_status'] == null){
				$routeRecord['tracking_status'] = "";
			}
			$routeRecord['timetracking_status'] = false;
			$cttimetrackerid = "";
			$isTimeTrackingSameRecord = false;
			$tracking_user = $current_user->id;
			$getTimeQuery = "SELECT * FROM vtiger_cttimetracker INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_cttimetracker.cttimetrackerid WHERE vtiger_crmentity.deleted = 0 AND vtiger_cttimetracker.tracking_user = '$tracking_user' AND vtiger_cttimetracker.tracking_status = 'Start' ";
			$resultTime = $adb->pquery($getTimeQuery,array());
			if($adb->num_rows($resultTime) > 0){
				$cttimetrackerid = $adb->query_result($resultTime,0,'cttimetrackerid');
				$cttimetrackerid = vtws_getWebserviceEntityId('CTTimeTracker',$cttimetrackerid);
				if($cttimetrackerid == $routeRecord['id']){
					$isTimeTrackingSameRecord = true;
				}
				$routeRecord['cttimetrackerid'] = $cttimetrackerid;
				$routeRecord['timetracking_status'] = true;
			}
			$routeRecord['cttimetrackerid'] = $cttimetrackerid;
			$routeRecord['isTimeTrackingSameRecord'] = $isTimeTrackingSameRecord;

			$userRecordModel = Vtiger_Record_Model::getInstanceById($record['smownerid'],'Users');
			if(!empty($userRecordModel->get('user_name'))){
				$routeRecord['assigned_user_id'] = array('value'=>vtws_getWebserviceEntityId('Users', $record['smownerid']),'label'=>html_entity_decode($userRecordModel->get('first_name').' '.$userRecordModel->get('last_name'),ENT_QUOTES,$default_charset));
			}else{
				$query = "SELECT groupname FROM vtiger_groups WHERE groupid = ?";
				$groupResults = $adb->pquery($query,array($record['smownerid']));
				$routeRecord['assigned_user_id'] = array('value'=>vtws_getWebserviceEntityId('Groups', $record['smownerid']),'label'=>html_entity_decode($adb->query_result($groupResults,0,'groupname'),ENT_QUOTES,$default_charset));
			}

			$userRecordModel = Vtiger_Record_Model::getInstanceById($record['tracking_user'],'Users');
			if(!empty($userRecordModel->get('user_name'))){
				$routeRecord['tracking_user'] = array('value'=>vtws_getWebserviceEntityId('Users', $record['tracking_user']),'label'=>html_entity_decode($userRecordModel->get('first_name').' '.$userRecordModel->get('last_name'),ENT_QUOTES,$default_charset));
			}else{
				$query = "SELECT groupname FROM vtiger_groups WHERE groupid = ?";
				$groupResults = $adb->pquery($query,array($record['tracking_user']));
				$routeRecord['tracking_user'] = array('value'=>vtws_getWebserviceEntityId('Groups', $record['tracking_user']),'label'=>html_entity_decode($adb->query_result($groupResults,0,'groupname'),ENT_QUOTES,$default_charset));
			}
			$related_to = $record['related_to'];
			$setype = '';
			$modulelabel = '';
			if($related_to != ''){
				$rel_query = "SELECT * FROM vtiger_crmentity WHERE crmid = ?";
				$rel_Results = $adb->pquery($rel_query,array($related_to));
				$setype = $adb->query_result($rel_Results,0,'setype');
				if($setype == 'Events'){
					$moduleModels = Vtiger_Module_Model::getInstance('Calendar');
				}else{
					$moduleModels = Vtiger_Module_Model::getInstance($setype);
				}
				if(!in_array($moduleModels->get('presence'), $presence)){
					continue;
				}
				$label = $adb->query_result($rel_Results,0,'label');
				if($setype == 'Events' || $setype == 'Calendar'){
					$calendarRecordModel = Vtiger_Record_Model::getInstanceById($related_to);
					if($calendarRecordModel->get('activitytype') == 'Task'){
						$setype = 'Calendar';
					}else{
						$setype = 'Events';
					}
				}
				$routeRecord['related_to'] = array('value'=>vtws_getWebserviceEntityId($setype, $related_to),'label'=>html_entity_decode($label,ENT_QUOTES,$default_charset));
			}else{
				$routeRecord['related_to'] = array('value'=>'','label'=>'');
			}
			$routeRecord['modulename'] = $setype;
			$modulelabel = vtranslate($setype,$setype);
			$routeRecord['modulelabel'] = $modulelabel;
			$routeRecord['total_time'] = $record['total_time'];
			if($routeRecord['tracking_status'] == 'Start'){
				$timeControlQuery = "SELECT * FROM vtiger_cttimecontrol INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_cttimecontrol.cttimecontrolid WHERE vtiger_crmentity.deleted = 0 AND vtiger_cttimecontrol.related_tracking = ? ";
				$timeControlResult = $adb->pquery($timeControlQuery,array($recordid));
				$num_rows = $adb->num_rows($timeControlResult);
				
				$difference = 0;
				for($i=0;$i<$num_rows;$i++) {
					$start_date = $adb->query_result($timeControlResult,$i,'date_start');
					$start_time = $adb->query_result($timeControlResult,$i,'time_start');
					$end_date = $adb->query_result($timeControlResult,$i,'date_end');
					$end_time = $adb->query_result($timeControlResult,$i,'time_end');
					if($end_date == '' && $end_time == ''){
						$end_date = $date_end;
						$end_time = $time_end;
					}

					$startdatetime = strtotime($start_date.' '.$start_time);
				    // calculate the end timestamp
				    $enddatetime = strtotime($end_date.' '.$end_time);
				    // calulate the difference in seconds
				    $difference = $difference + ($enddatetime - $startdatetime);
				}
				$routeRecord['total_time'] = $difference;
			    // calculate the end timestamp
			}

			$routeRecord['duration'] = gmdate("H:i:s", $routeRecord['total_time']);
			$editAction = false;
			if($record['smownerid'] == $current_user->id){
				$editAction = Users_Privileges_Model::isPermitted('CTTimeTracker', 'EditView', $recordid);
			}
			$routeRecord['editAction'] = $editAction;
			$routeRecord['deleteAction'] = $deleteAction;
			if(Users_Privileges_Model::isPermitted('CTTimeTracker', 'DetailView', $recordid)){
				$modifiedRecords[] = $routeRecord;
			}
		}

		$currentUser = Users_Record_Model::getCurrentUserModel();
        $users = $currentUser->getAccessibleUsers();
        $usersWSId = CTMobile_WS_Utils::getEntityModuleWSId('Users');
        $assigned_users =  array();
        $assigned_users[] = array('value'=>'all','label'=>vtranslate('LBL_ALL'));
        foreach ($users as $id => $name) {
            unset($users[$id]);
            $assigned_users[] =  array('value'=>$usersWSId.'x'.$id,'label'=> decode_html(decode_html($name))); 
        }

        $timeTrackerModules = array();
        $getTimeTrackerQuery = $adb->pquery("SELECT * FROM ctmobile_timetracking_modules");
		$timeTrackerArray = array();
		
		for ($i=0; $i < $adb->num_rows($getTimeTrackerQuery); $i++) {
			$modules = $adb->query_result($getTimeTrackerQuery,$i,'module');
			if($modules == 'Events'){
				$moduleModels = Vtiger_Module_Model::getInstance('Calendar');
			}else{
				$moduleModels = Vtiger_Module_Model::getInstance($modules);
			}
			if(in_array($moduleModels->get('presence'), $presence)){
				$timeTrackerModules[] = array('value'=>$modules,'label'=>vtranslate($modules,$modules));
			}
		}
		
		$total_time_result  = $adb->query($total_time_sql,$filterOrAlertInstance->queryParameters());
		$totalRows = $adb->num_rows($total_time_result);
		$total_time_user = 0;
		for($i=0;$i<$totalRows;$i++) {
			$recordid = $adb->query_result($total_time_result,$i,'cttimetrackerid');
			if(Users_Privileges_Model::isPermitted('CTTimeTracker', 'DetailView', $recordid)){
				$tracking_status = $adb->query_result($total_time_result,$i,'tracking_status');
				if($tracking_status == 'Start'){

					$timeControlQuery = "SELECT * FROM vtiger_cttimecontrol INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_cttimecontrol.cttimecontrolid WHERE vtiger_crmentity.deleted = 0 AND vtiger_cttimecontrol.related_tracking = ? ";
					$timeControlResult = $adb->pquery($timeControlQuery,array($recordid));
					$num_rows = $adb->num_rows($timeControlResult);
					
					$difference = 0;
					for($j=0;$j<$num_rows;$j++) {
						$start_date = $adb->query_result($timeControlResult,$j,'date_start');
						$start_time = $adb->query_result($timeControlResult,$j,'time_start');
						$end_date = $adb->query_result($timeControlResult,$j,'date_end');
						$end_time = $adb->query_result($timeControlResult,$j,'time_end');
						if($end_date == '' && $end_time == ''){
							$end_date = $date_end;
							$end_time = $time_end;
						}

						$startdatetime = strtotime($start_date.' '.$start_time);
					    // calculate the end timestamp
					    $enddatetime = strtotime($end_date.' '.$end_time);
					    // calulate the difference in seconds
					    $difference = $difference + ($enddatetime - $startdatetime);
					}
					$total_time = $difference;
				}else {
					$total_time = $adb->query_result($total_time_result,$i,'total_time');
				}
			    
			    $total_time_user = $total_time_user + $total_time;
			}

		}
		
		$hours = floor($total_time_user / 3600);
		$minutes = floor(($total_time_user / 60) % 60);
		$seconds = $total_time_user % 60;

		//$user_total_time = gmdate("H:i:s", $total_time_user);
		$user_total_time = "$hours:$minutes:$seconds";
        
		$response = new CTMobile_API_Response();
		$moduleLabel = vtranslate($module,$module);
		$current_user_array = array('value'=>CTMobile_WS_Utils::getEntityModuleWSId('Users').'x'.$current_user->id,'label'=>decode_html(decode_html($current_user->first_name)).' '.decode_html(decode_html($current_user->last_name)));

		$isModuleDisabled = false;
		$message = '';
		$userPrivilegesModel = Users_Privileges_Model::getInstanceById($currentUser->getId());
		$permission = $userPrivilegesModel->hasModulePermission($TimeTrackerModuleModel->getId());
		if(!in_array($TimeTrackerModuleModel->get('presence'), $presence)){
			$message = vtranslate('CTTimeTracker','CTTimeTracker')." ".$this->CTTranslate('Module is Disabled');
			$isModuleDisabled = true;
		}else if(!$permission){
			$message = vtranslate('CTTimeTracker','CTTimeTracker')." ".vtranslate('LBL_NOT_ACCESSIBLE');
			$isModuleDisabled = true;
		}
		$createAction = $userPrivilegesModel->hasModuleActionPermission($TimeTrackerModuleModel->getId(), 'CreateView');

		$tracking_user = $current_user->id;
		$getTimeQuery = "SELECT * FROM vtiger_cttimetracker INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_cttimetracker.cttimetrackerid WHERE vtiger_crmentity.deleted = 0 AND vtiger_cttimetracker.tracking_user = '$tracking_user' AND vtiger_cttimetracker.tracking_status = 'Start' ";
		$resultTime = $adb->pquery($getTimeQuery,array());
		$isAlreadyStartedTracking = false;
		if($adb->num_rows($resultTime) > 0){
			$isAlreadyStartedTracking = true;
		}

		if(count($modifiedRecords) == 0) {
			if($message == ''){
				$message = $this->CTTranslate('No records found');
			}
			$response->setResult(array('records'=>$modifiedRecords, 'module'=>$module,'moduleLabel'=>$moduleLabel, 'message'=>$message,'isLast'=>$isLast,'tracking_users'=>$assigned_users,'timeTrackerModules'=>$timeTrackerModules,'current_user'=>$current_user_array,'user_total_time'=>$user_total_time,'isModuleDisabled'=>$isModuleDisabled,'isAlreadyStartedTracking'=>$isAlreadyStartedTracking,'createAction'=>$createAction));
		} else {
			$response->setResult(array('records'=>$modifiedRecords, 'module'=>$module,'moduleLabel'=>$moduleLabel, 'message'=>$message,'isLast'=>$isLast,'tracking_users'=>$assigned_users,'timeTrackerModules'=>$timeTrackerModules,'current_user'=>$current_user_array,'user_total_time'=>$user_total_time,'isModuleDisabled'=>$isModuleDisabled,'isAlreadyStartedTracking'=>$isAlreadyStartedTracking,'createAction'=>$createAction));
		}
		return $response;
	}


}

