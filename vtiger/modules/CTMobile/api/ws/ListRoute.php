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

class CTMobile_WS_ListRoute extends CTMobile_WS_Controller {

	function process(CTMobile_API_Request $request) {
		$default_charset = VTWS_PreserveGlobal::getGlobal('default_charset');
		global $current_user, $adb; // Few core API assumes this variable availability
		$current_user = $this->getActiveUser();
		$roleid = $current_user->roleid;
		$time_zone = $current_user->time_zone;
		$presence = array('0', '2');
		$index = trim($request->get('index'));
		$size = trim($request->get('size'));
		$date_start = trim($request->get('date_start'));
		$date_end = trim($request->get('date_end'));
		$assigned_user = trim($request->get('assigned_users'));
		if($assigned_user == ''){
			//$assigned_user = vtws_getWebserviceEntityId('Users', $current_user->id);
			$assigned_user = 'all';
		}
		$latitude = trim($request->get('latitude'));
		$longitude = trim($request->get('longitude'));
		$module = 'CTRoutePlanning';
		
		$morefields = array('id','ctroutename', 'ctroute_date', 'ctroute_status','createdtime', 'modifiedtime','assigned_user_id');	
			
		$customView = new CustomView();
		$filterid = $customView->getViewId($module);
		$filterOrAlertInstance = CTMobile_WS_FilterModel::modelWithId($module, $filterid);

		$generator = new QueryGenerator($module, $current_user);
		$generator->setFields($morefields);
		$query = $generator->getQuery();
		if($assigned_user != '' && $assigned_user != 'all'){
			$assigned_user_id = explode('x', $assigned_user);
			$user_id = $assigned_user_id[1];
			$query.= " AND vtiger_crmentity.smownerid = '$user_id' ";
		}
		if($date_start != '' && $date_end != ''){
			$date1 = Vtiger_Date_UIType::getDBInsertedValue($date_start);
			$date2 = Vtiger_Date_UIType::getDBInsertedValue($date_end);
			$query.= " AND DATE(vtiger_ctrouteplanning.ctroute_date) BETWEEN '".$date1."' AND '".$date2."' ";
		}
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
			$recordid = $record['ctrouteplanningid'];
			$deleteAction = Users_Privileges_Model::isPermitted('CTRoutePlanning', 'Delete', $recordid);
			$editAction = Users_Privileges_Model::isPermitted('CTRoutePlanning', 'EditView', $recordid);
			$routeRecord['id'] = vtws_getWebserviceEntityId($module, $recordid);
			$routeRecord['ctroutename'] = $record['ctroutename'];
			$routeRecord['ctroute_date'] = $record['ctroute_date'];
			$routeRecord['ctroute_status'] =  !empty($record['ctroute_status']) ? array('value'=>$record['ctroute_status'],'label'=>cttranslate($record['ctroute_status'])) : array('value'=>"",'label'=>"");
			$userRecordModel = Vtiger_Record_Model::getInstanceById($record['smownerid'],'Users');
			if(!empty($userRecordModel->get('user_name'))){
				$routeRecord['assigned_user_id'] = array('value'=>vtws_getWebserviceEntityId('Users', $record['smownerid']),'label'=>html_entity_decode($userRecordModel->get('first_name').' '.$userRecordModel->get('last_name'),ENT_QUOTES,$default_charset));
			}else{
				$query = "SELECT groupname FROM vtiger_groups WHERE groupid = ?";
				$groupResults = $adb->pquery($query,array($record['smownerid']));
				$routeRecord['assigned_user_id'] = array('value'=>vtws_getWebserviceEntityId('Groups', $record['smownerid']),'label'=>html_entity_decode($adb->query_result($groupResults,0,'groupname'),ENT_QUOTES,$default_charset));
			}
			$routeRecord['ctroute_date'] = Vtiger_Date_UIType::getDisplayDateValue($record['ctroute_date']);
			$routeRecord['createdtime'] = Vtiger_Datetime_UIType::getDisplayDateTimeValue($record['createdtime']);
			$routeRecord['modifiedtime'] = Vtiger_Datetime_UIType::getDisplayDateTimeValue($record['modifiedtime']);
			$distanceData = $this->getTotalDistanceOfRoute($recordid,$latitude,$longitude);
			$routeRecord['distance'] = $distanceData['distance'];
			$routeRecord['duration'] = $distanceData['duration'];
			$routeRecord['deleteAction'] = $deleteAction;
			$routeRecord['editAction'] = $editAction;
			$routeRecord['allCheckout'] = $this->getStatusCompleted($recordid);
			if(Users_Privileges_Model::isPermitted('CTRoutePlanning', 'DetailView', $recordid)){
				$modifiedRecords[] = $routeRecord;
			}
		}

		$picklistValues1[] = array('value'=>"", 'label'=>vtranslate('LBL_SELECT_STATUS',$module));
		$picklistValues = Vtiger_Util_Helper::getRoleBasedPicklistValues('ctroute_status',$roleid);
		foreach($picklistValues as $pvalue){
			$picklistValues1[] = array('value'=>$pvalue, 'label'=>cttranslate($pvalue));
			$field['type']['picklistValues'] = $picklistValues1;
		}

		$currentUser = Users_Record_Model::getCurrentUserModel();
        $users = $currentUser->getAccessibleUsers();
        $usersWSId = CTMobile_WS_Utils::getEntityModuleWSId('Users');
        $assigned_users =  array();
        $all_is_selected = false;
        if($assigned_user == 'all'){
        	$all_is_selected = true;
        }
        $assigned_users[] = array('value'=>'all','label'=>vtranslate('LBL_ALL'),'selected'=>$all_is_selected);
        foreach ($users as $id => $name) {
            unset($users[$id]);
            $is_selected =  false;
            if($assigned_user == $usersWSId.'x'.$id){
            	$is_selected = true;
            }
            $assigned_users[] =  array('value'=>$usersWSId.'x'.$id,'label'=> decode_html(decode_html($name)),'selected'=>$is_selected); 
        }
        
        $groups = $currentUser->getAccessibleGroups();
        $groupsWSId = CTMobile_WS_Utils::getEntityModuleWSId('Groups');
        foreach ($groups as $id => $name) {
            unset($groups[$id]);
            $groups[$groupsWSId.'x'.$id] = $name; 
        }
		$response = new CTMobile_API_Response();
		$moduleLabel = vtranslate($module,$module);

		$isModuleDisabled = false;
		$message = '';
		
		$CTRoutePlanningModuleModel = Vtiger_Module_Model::getInstance('CTRoutePlanning');
		$userPrivilegesModel = Users_Privileges_Model::getInstanceById($currentUser->getId());
		$permission = $userPrivilegesModel->hasModulePermission($CTRoutePlanningModuleModel->getId());
		if(!in_array($CTRoutePlanningModuleModel->get('presence'), $presence)){
			$message = vtranslate('CTRoutePlanning','CTRoutePlanning')." ".$this->CTTranslate('Module is Disabled');
			$isModuleDisabled = true;
		}else if(!$permission){
			$message = vtranslate('CTRoutePlanning','CTRoutePlanning')." ".vtranslate('LBL_NOT_ACCESSIBLE');
			$isModuleDisabled = true;
		}
		$userid = vtws_getWebserviceEntityId('Users', $currentUser->getId());
		$username = decode_html($currentUser->get('first_name')).' '.decode_html($currentUser->get('last_name'));
		$assigned_user_id = array('label'=>$username,'value'=>$userid);

		$createAction = $userPrivilegesModel->hasModuleActionPermission($CTRoutePlanningModuleModel->getId(), 'CreateView');

		if(count($modifiedRecords) == 0) {
			if($message == ''){
				$message = $this->CTTranslate('No records found');
			}
			$response->setResult(array('records'=>$modifiedRecords, 'module'=>$module,'moduleLabel'=>$moduleLabel, 'message'=>$message,'isLast'=>$isLast,'ctroute_status'=>$picklistValues1,'assigned_users'=>$assigned_users,'isModuleDisabled'=>$isModuleDisabled,'createAction'=>$createAction,'assigned_user_id'=>$assigned_user_id));
		} else {
			$response->setResult(array('records'=>$modifiedRecords, 'module'=>$module,'moduleLabel'=>$moduleLabel, 'message'=>$message,'isLast'=>$isLast,'ctroute_status'=>$picklistValues1,'assigned_users'=>$assigned_users,'isModuleDisabled'=>$isModuleDisabled,'createAction'=>$createAction,'assigned_user_id'=>$assigned_user_id));
		}
		return $response;
	}

	function getLatLongOfRecord($recordid){
		global $adb;
		$data['lat'] = "";
		$data['long'] = "";
		if($recordid){
			$result  = $adb->pquery("SELECT * FROM `ct_address_lat_long` WHERE recordid = ? ",array($recordid));
			if($adb->num_rows($result) > 0){
				$data['lat'] = $adb->query_result($result,0,'latitude');
				$data['long'] = $adb->query_result($result,0,'longitude');
			}

		}

		return $data;
	}

	function getLatLongFromRelatedRecord($recordid,$module){
		global $adb;
		$data['lat'] = "";
		$data['long'] = "";
		if($recordid){
			$checkDelete = $adb->pquery("SELECT * FROM vtiger_crmentity WHERE crmid = ? AND deleted = 0",array($recordid));
			if($adb->num_rows($checkDelete) > 0){
				$recordModel = Vtiger_Record_Model::getInstanceById($recordid);
				if($module == 'HelpDesk'){
					$record1 = $recordModel->get('parent_id');
					$record2 = $recordModel->get('contact_id');
				}else if($module == 'PurchaseOrder'){
					$record1 = $recordModel->get('contact_id');
				}else{
					$record1 = $recordModel->get('account_id');
					$record2 = $recordModel->get('contact_id');
				}

				if($record1 != ""){
					$result  = $adb->pquery("SELECT * FROM `ct_address_lat_long` WHERE recordid = ? ",array($record1));
					if($adb->num_rows($result) > 0){
						$data['lat'] = $adb->query_result($result,0,'latitude');
						$data['long'] = $adb->query_result($result,0,'longitude');
					}

				}
				if($record2 != "" && $data['lat'] == "" && $data['long'] == ""){
					$result  = $adb->pquery("SELECT * FROM `ct_address_lat_long` WHERE recordid = ? ",array($record2));
					if($adb->num_rows($result) > 0){
						$data['lat'] = $adb->query_result($result,0,'latitude');
						$data['long'] = $adb->query_result($result,0,'longitude');
					}
				}
			}
		}

		return $data;
	}

	function getStatusCompleted($recordId){
		global $adb;
		if($recordId){
			$statusCompleted = true;
			$query = 'SELECT * from vtiger_ctrouteplanrel where ctrouteplanningid=?';
			$result = $adb->pquery($query, array($recordId));
        	$numRows = $adb->num_rows($result);

       		$ctroute_realtedtoList = array();
        	for($i=0; $i<$numRows; $i++) {
        		$row = $adb->fetchByAssoc($result, $i);
	            $ctroute_realtedto = $row['ctroute_realtedto'];

				$checkAllCheckout = $adb->pquery("SELECT ctroute_attendance_status FROM vtiger_ctrouteattendance WHERE ctroute_planning = ? AND related_to = ?",array($recordId,$ctroute_realtedto));
				$num_rows = $adb->num_rows($checkAllCheckout);
				if($num_rows > 0){
	            	$row1 = $adb->fetchByAssoc($checkAllCheckout, 0);
	            	$ctroute_attendance_status = $row1['ctroute_attendance_status'];
	            	if($ctroute_attendance_status == 'check_in' || $ctroute_attendance_status == ''){
	            		$statusCompleted = false;
	            	}
				}else{
					$statusCompleted = false;
				}
			}
			return $statusCompleted;
		}
	}

	function getTotalDistanceOfRoute($ctrouteplanningid,$latitude,$longitude){
		global $adb;
		$getDistanceUnit = $adb->pquery("SELECT route_distance_unit FROM ctmobile_routegeneralsettings",array());
		$distance_unit = $adb->query_result($getDistanceUnit,0,'route_distance_unit');
		$resultApi = $adb->pquery("SELECT * FROM ctmobile_api_settings",array());
		if($adb->num_rows($resultApi)){
			define("SECONDS_PER_HOUR", 60*60);
			$alllatlongData = array();
			$distance = 0;
			$duration = 0;
			$api_key = $adb->query_result($resultApi,0,'api_key');
			$checkQuery = $adb->pquery("SELECT vtiger_ctrouteplanrel.ctroute_realtedto,vtiger_crmentity.setype FROM vtiger_ctrouteplanrel INNER JOIN vtiger_crmentity ON vtiger_ctrouteplanrel.ctroute_realtedto = vtiger_crmentity.crmid WHERE vtiger_ctrouteplanrel.ctrouteplanningid = ? AND vtiger_crmentity.deleted = 0",array($ctrouteplanningid));
			for($i=0; $i<$adb->num_rows($checkQuery);$i++){
				$row = $adb->fetchByAssoc($checkQuery, $i);
				$recordid = $row['ctroute_realtedto'];
				if(in_array($row['setype'],array('HelpDesk','Invoice','Quotes','SalesOrder','PurchaseOrder'))){
					$latlongData = $this->getLatLongFromRelatedRecord($recordid,$row['setype']);
				}else{
					$latlongData = $this->getLatLongOfRecord($recordid);
				}
				if($latlongData['lat'] != '' && $latlongData['long'] != ''){
					$alllatlongData[] = $latlongData['lat'].','. $latlongData['long'];
				}
			}
			$latlongUrl = urlencode(implode('|',$alllatlongData));
			$opts = array('http'=>array('header'=>"User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.146 Safari/537.36\r\n"));
			$context = stream_context_create($opts);
			$geocodeFromAddr = file_get_contents("https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins=$latitude,$longitude&destinations=$latlongUrl&key=$api_key",false, $context);
			$output = json_decode($geocodeFromAddr,true);
			if($output['status'] == 'OK'){
				$elements = $output['rows'][0]['elements'];
				foreach ($elements as $key => $element) {
					if($element['status'] == 'OK'){
						$distance+=$element['distance']['value'];
						$duration+=$element['duration']['value'];
					}
				}
			}
			$distance = $distance/1000;
			if($distance_unit == 'Miles'){
				$distance = $distance / 1.609344;
				$data['distance'] = number_format($distance,2)." Mi";
			}else{
				$data['distance'] = number_format($distance,2)." Km";
			}
			$hours = round($duration / SECONDS_PER_HOUR, 0, PHP_ROUND_HALF_DOWN);
			$minutes = round(($duration % SECONDS_PER_HOUR) / 60, 0, PHP_ROUND_HALF_DOWN);
			if($hours == 0){
				$data['duration'] = $minutes." Mins";
			}else{
				$data['duration'] = $hours." Hours ".$minutes." Mins";
			}
			return $data;
		}else{
			$duration = '0 Hours 0 Mins';
			$distance = 0.00;
			$prev_latitude = 0;
			$prev_longitude = 0;
			$checkQuery = $adb->pquery("SELECT vtiger_ctrouteplanrel.ctroute_realtedto,vtiger_crmentity.setype FROM vtiger_ctrouteplanrel INNER JOIN vtiger_crmentity ON vtiger_ctrouteplanrel.ctroute_realtedto = vtiger_crmentity.crmid WHERE vtiger_ctrouteplanrel.ctrouteplanningid = ? AND vtiger_crmentity.deleted = 0",array($ctrouteplanningid));
			for($i=0; $i<$adb->num_rows($checkQuery);$i++){
				$row = $adb->fetchByAssoc($checkQuery, $i);
				$recordid = $row['ctroute_realtedto'];
				if(in_array($row['setype'],array('HelpDesk','Invoice','Quotes','SalesOrder','PurchaseOrder'))){
					$latlongData = $this->getLatLongFromRelatedRecord($recordid,$row['setype']);
				}else{
					$latlongData = $this->getLatLongOfRecord($recordid);
				}
				if($latlongData['lat'] == '' && $latlongData['long'] == ''){
					continue;
				}
				if($prev_latitude == 0 && $prev_longitude == 0){
					$distance+=$this->distance($latlongData['latitude'],$latlongData['longitude'],$latitude,$longitude,'K');
					$prev_latitude = $latlongData['lat'];
					$prev_longitude = $latlongData['long'];
				}else{
					$distance+=$this->distance($latlongData['latitude'],$latlongData['longitude'],$prev_latitude,$prev_longitude,'K');
				}
			}
			if($distance_unit == 'Miles'){
				$distance = $distance / 1.609344;
				$data['distance'] = number_format($distance,2)." Mi";
			}else{
				$data['distance'] = number_format($distance,2)." Km";
			}
			$data['duration'] = $duration;
			return $data;
		}
	}



	function distance($lat1, $lon1, $lat2, $lon2, $unit) {
	  	$theta = $lon1 - $lon2;
	 	$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
	  	$dist = acos($dist);
	  	$dist = rad2deg($dist);
	  	$miles = $dist * 60 * 1.1515;
	 	$unit = strtoupper($unit);

	  	if ($unit == "K") {
		  return number_format(($miles * 1.609344),2);
	 	} else if ($unit == "N") {
		  return ($miles * 0.8684);
	 	} else {
		  return $miles;
	  	}
	}

}

function cttranslate($pvalue){
	global $adb;
	$selQuery = $adb->pquery("SELECT * FROM ctmobile_routestatus WHERE routestatusname = ?",array($pvalue));
	if($adb->num_rows($selQuery) > 0){
		return decode_html(decode_html($adb->query_result($selQuery,0,'routestatuslabel')));
	}else{
		return $pvalue;
	}
}