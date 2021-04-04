<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

class CTMobile_WS_GetRouteDetails extends CTMobile_WS_Controller {

	function process(CTMobile_API_Request $request) {
		$default_charset = VTWS_PreserveGlobal::getGlobal('default_charset');
		global $current_user, $adb; // Few core API assumes this variable availability
		$current_user = $this->getActiveUser();

		$record = trim($request->get('record'));
		$latitude = trim($request->get('latitude'));
		$longitude = trim($request->get('longitude'));
		$radius = trim($request->get('radius'));
		$index = trim($request->get('index'));
		$size = trim($request->get('size'));
		if($record == ""){
			$message =  $this->CTTranslate('Required fields not found');
			throw new WebServiceException(404,$message);
		}
		$record_id = explode('x',$record);
		$recordId = $record_id[1];
		$module = 'CTRoutePlanning';

		$recordPermission = Users_Privileges_Model::isPermitted($module, 'DetailView', $recordId);
		if(!$recordPermission) {
			throw new WebServiceException(403,vtranslate('LBL_PERMISSION_DENIED'));
		}

		$getDistanceUnit = $adb->pquery("SELECT route_distance_unit FROM ctmobile_routegeneralsettings",array());
		$distance_unit = $adb->query_result($getDistanceUnit,0,'route_distance_unit');

		$date_query = 'SELECT * FROM vtiger_ctrouteplanning WHERE ctrouteplanningid = ?';
		$ctroute_result = $adb->pquery($date_query,array($recordId));
		$ctroute_date = $adb->query_result($ctroute_result,0,'ctroute_date');
		$ctroute_date = Vtiger_Date_UIType::getDisplayDateValue($ctroute_date);

		$query = 'SELECT * from vtiger_ctrouteplanrel where ctrouteplanningid=?';
		$isLast = true;
		if($index && $size){
			$totalQuery = $query;
			$totalParams = array($recordId);
			$totalResults = $adb->pquery($totalQuery,$totalParams);
			$totalRecords = $adb->num_rows($totalResults);
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
        $result = $adb->pquery($query, array($recordId));
        $num_rows = $adb->num_rows($result);

        $ctroute_realtedtoList = array();
        for($i=0; $i<$num_rows; $i++) {
            $row = $adb->fetchByAssoc($result, $i);
            $ctroute_realtedto = $row['ctroute_realtedto'];
            $getModuleLabel = $adb->pquery("SELECT * FROM vtiger_crmentity WHERE deleted = 0  AND crmid = ?",array($ctroute_realtedto));
            if($adb->num_rows($getModuleLabel)){
            	$setype = $adb->query_result($getModuleLabel,0,'setype');
            	$label = $adb->query_result($getModuleLabel,0,'label');
            	$Permission = Users_Privileges_Model::isPermitted($setype, 'DetailView', $ctroute_realtedto);
            	if($Permission){
            		$email = "";
            		$phone = "";
            		$mobile = "";
	            	if($setype == 'Accounts'){
	            		$recordModels = Vtiger_Record_Model::getInstanceById($ctroute_realtedto,$setype);
	            		if($recordModels){
		            		$email = $recordModels->get('email1');
		            		$phone = $recordModels->get('phone');
		            		$mobile = $recordModels->get('otherphone');	
	            		}
	            	}else if($setype == 'Contacts' || $setype == 'Leads'){
	            		$recordModels = Vtiger_Record_Model::getInstanceById($ctroute_realtedto,$setype);
	            		if($recordModels){
		            		$email = $recordModels->get('email');
		            		$phone = $recordModels->get('phone');
		            		$mobile = $recordModels->get('mobile');
		            	}
	            	}
	            	$attendance_data = $this->attendance_status($recordId,$ctroute_realtedto);
	            	$id = vtws_getWebserviceEntityId($setype, $ctroute_realtedto);
	            	if(in_array($setype, array('HelpDesk','Invoice','Quotes','SalesOrder','PurchaseOrder'))){
	            		$address = $this->getLatLongFromRelatedRecord($ctroute_realtedto,$setype);
	            		$email = $address['email'];
	            		$phone = $address['phone'];
	            		$mobile = $address['mobile'];
	            	}else{
	            		$address = $this->getLatLongOfRecord($ctroute_realtedto);
	            	}
	            	$distance = $this->distance($address['lat'],$address['long'],$latitude,$longitude);
	            	if($distance < $radius) {
	            		if($distance_unit == 'Miles'){
	            			$distance = number_format($distance,2).' Mi';
	            		}else{
	            			$distance = number_format(($distance*1.609344),2).' KM';
	            		}
	            		$ctroute_realtedtoList[] = array('id'=>$id,'label'=>decode_html(decode_html($label)),'email'=>$email,'phone'=>$phone,'mobile'=>$mobile,'module'=>$setype,'latitude'=>$address['lat'],'longitude'=>$address['long'],'attendance_status'=>$attendance_data['attendance_status'],'ctrouteattendanceid'=>$attendance_data['ctrouteattendanceid'],'ctroute_attendance_status'=>$attendance_data['ctroute_attendance_status'],'check_in_time'=>$attendance_data['check_in_time'],'check_out_time'=>$attendance_data['check_out_time'],'duration'=>$attendance_data['duration'],'distance'=>$distance);
	            	}
            	}
            }
        }
		$response = new CTMobile_API_Response();
		$moduleLabel = vtranslate($module,$module);
		if(count($ctroute_realtedtoList) == 0) {
			$message = $this->CTTranslate('No records found');
			$response->setResult(array('records'=>$ctroute_realtedtoList,'ctroute_date'=>$ctroute_date, 'module'=>$module,'moduleLabel'=>$moduleLabel, 'message'=>$message,'isLast'=>$isLast));
		} else {
			$response->setResult(array('records'=>$ctroute_realtedtoList,'ctroute_date'=>$ctroute_date, 'module'=>$module,'moduleLabel'=>$moduleLabel, 'message'=>'','isLast'=>$isLast));
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
		$data['email'] = "";
		$data['phone'] = "";
		$data['mobile'] = "";
		if($recordid){
			$checkDeleted = $adb->pquery("SELECT * FROM vtiger_crmentity WHERE crmid = ? AND deleted = 0",array($recordid));
			if($adb->num_rows($checkDeleted) > 0 ){
				$recordModel = Vtiger_Record_Model::getInstanceById($recordid,$module);
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
					$checkDeleted1 = $adb->pquery("SELECT * FROM vtiger_crmentity WHERE crmid = ? AND deleted = 0",array($record1));
					if($adb->num_rows($checkDeleted1) > 0 ){
						if($module == 'PurchaseOrder'){
							$relModule = 'Contacts';
						}else{
							$relModule = 'Accounts';
						}
						$recordModels = Vtiger_Record_Model::getInstanceById($record1,$relModule);
						if($recordModels->getModuleName() == 'Accounts'){
							$data['email'] = $recordModels->get('email1');
							$data['phone'] = $recordModels->get('phone');
							$data['mobile'] = $recordModels->get('otherphone');
						}else{
							$data['email'] = $recordModels->get('email');
							$data['phone'] = $recordModels->get('phone');
							$data['mobile'] = $recordModels->get('mobile');
						}
					}
				}
				if($record2 != "" && $data['lat'] == "" && $data['long'] == ""){
					$result  = $adb->pquery("SELECT * FROM `ct_address_lat_long` WHERE recordid = ? ",array($record2));
					if($adb->num_rows($result) > 0){
						$data['lat'] = $adb->query_result($result,0,'latitude');
						$data['long'] = $adb->query_result($result,0,'longitude');
					}
					$checkDeleted2 = $adb->pquery("SELECT * FROM vtiger_crmentity WHERE crmid = ? AND deleted = 0",array($record2));
					if($adb->num_rows($checkDeleted2) > 0 ){
						$recordModels = Vtiger_Record_Model::getInstanceById($record2,'Contacts');
						$data['email'] = $recordModels->get('email');
						$data['phone'] = $recordModels->get('phone');
						$data['mobile'] = $recordModels->get('mobile');
					}
				}
			}
		}

		return $data;
	}

	function attendance_status($ctroute_planning,$ctroute_realtedto){
		global $current_user,$adb;
		$current_user = $this->getActiveUser();
		$employee_name = $current_user->id;
		$user =  Users::getActiveAdminUser();
		$generator = new QueryGenerator('CTRouteAttendance', $user);
		$generator->setFields(array('ctroute_user','ctroute_attendance_status','createdtime','modifiedtime','id','related_to','ctroute_planning'));
		$eventQuery = $generator->getQuery();
		$eventQuery .= " AND vtiger_ctrouteattendance.ctroute_user = '$employee_name' AND vtiger_ctrouteattendance.related_to = '$ctroute_realtedto' AND vtiger_ctrouteattendance.ctroute_planning = '$ctroute_planning'";
		
		$query = $adb->pquery($eventQuery);
		define("SECONDS_PER_HOUR", 60*60);
		$num_rows = $adb->num_rows($query);
		if( $num_rows > 0){
			$ctroute_attendance_status = $adb->query_result($query,0,'ctroute_attendance_status');
			$ctrouteattendanceid = vtws_getWebserviceEntityId('CTRouteAttendance',$adb->query_result($query,0,'ctrouteattendanceid'));
			if($ctroute_attendance_status == 'check_in'){
				$attendance_status = true;
				$check_in = $adb->query_result($query,0,'createdtime');
				$check_in_time = Vtiger_Util_Helper::convertDateTimeIntoUsersDisplayFormat($check_in);
				$check_out = date('Y-m-d H:i:s');
				$check_out_time = '';
				$startdatetime = strtotime($check_in);
			    // calculate the end timestamp
			    $enddatetime = strtotime($check_out);
			    // calulate the difference in seconds
			    $difference = $enddatetime - $startdatetime;
			    $hours = round($difference / SECONDS_PER_HOUR, 0, PHP_ROUND_HALF_DOWN);
				$minutes = round(($difference % SECONDS_PER_HOUR) / 60, 0, PHP_ROUND_HALF_DOWN);
			    // output the result
			    $duration = $hours . " hr " . $minutes . " min";
			}else{
				$attendance_status = false;
				$check_in = $adb->query_result($query,0,'createdtime');
				$check_in_time = Vtiger_Util_Helper::convertDateTimeIntoUsersDisplayFormat($check_in);
				$check_out = $adb->query_result($query,0,'modifiedtime');
				$check_out_time = Vtiger_Util_Helper::convertDateTimeIntoUsersDisplayFormat($check_out);
				$startdatetime = strtotime($check_in);
			    // calculate the end timestamp
			    $enddatetime = strtotime($check_out);
			    // calulate the difference in seconds
			    $difference = $enddatetime - $startdatetime;
			    $hours = round($difference / SECONDS_PER_HOUR, 0, PHP_ROUND_HALF_DOWN);
				$minutes = round(($difference % SECONDS_PER_HOUR) / 60, 0, PHP_ROUND_HALF_DOWN);
			    // output the result
			    $duration = $hours . " hr " . $minutes . " min";
			}
		} else {
			$ctroute_attendance_status = "";
			$attendance_status = false;
			$ctrouteattendanceid = '';
			$check_in_time = '';
			$check_out_time = '';
			$duration = '0 hr 0 min';
		}
		$data = array();
		$data['attendance_status'] = $attendance_status;
		$data['ctrouteattendanceid'] = $ctrouteattendanceid;
		$data['ctroute_attendance_status'] = $ctroute_attendance_status;
		$data['check_in_time'] = $check_in_time;
		$data['check_out_time'] = $check_out_time;
		$data['duration'] = $duration;
		return $data;
	}

	/*function distance($lat1, $lon1, $lat2, $lon2, $unit='') {
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
	}*/

	function distance($lat1, $lon1, $lat2, $lon2) {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        return $miles;
    }

}