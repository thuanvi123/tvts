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

include_once 'include/Webservices/Create.php';
include_once 'include/Webservices/Update.php';

class CTMobile_WS_CTRouteAttendance extends CTMobile_WS_FetchRecordWithGrouping {
	protected $recordValues = false;
	
	// Avoid retrieve and return the value obtained after Create or Update
	protected function processRetrieve(CTMobile_API_Request $request) {
		return $this->recordValues;
	}
	
	function process(CTMobile_API_Request $request) {
		global $current_user; // Required for vtws_update API
		$current_user = $this->getActiveUser();
		$user =  Users::getActiveAdminUser();
		$module = 'CTRouteAttendance';
		$ctroute_planning = trim($request->get('ctroute_planning'));
		$related_to = trim($request->get('ctroute_realtedto'));
		$recordid = trim($request->get('record'));
		$ctroute_attendance_status = trim($request->get('ctroute_attendance_status'));
		$employee_name = trim($request->get('ctroute_user'));
		$latitude = trim($request->get('latitude'));
		$longitude = trim($request->get('longitude'));
		
		$response = new CTMobile_API_Response();
		
		if ($ctroute_attendance_status == '') {
			$message = $this->CTTranslate('Status cannot be empty');
			$response->setError(404, $message);
			return $response;
		}
		if ($employee_name == '') {
			$message = $this->CTTranslate('User cannot be empty');
			$response->setError(404, $message);
			return $response;
		}
		if ($latitude == '') {
			$message = $this->CTTranslate('Latitude cannot be empty');
			$response->setError(404, $message);
			return $response;
		}	
		if ($longitude == '') {
			$message = $this->CTTranslate('Longitude cannot be empty');
			$response->setError(404, $message);
			return $response;
		}
		if ($ctroute_planning == '') {
			$message = $this->CTTranslate('ctroute_planning cannot be empty');
			$response->setError(404, $message);
			return $response;
		}
		if ($related_to == '') {
			$message = $this->CTTranslate('ctroute_realtedto cannot be empty');
			$response->setError(1501, $message);
			return $response;
		}
		try {
			// Retrieve or Initalize
			if (!empty($recordid) && !$this->isTemplateRecordRequest($request)) {
				$this->recordValues = vtws_retrieve($recordid, $user);
			} else {
				$this->recordValues = array();
			}
			
			// Set the modified values
			$checkin_status = false;
			$this->recordValues['ctroute_attendance_status'] = trim($ctroute_attendance_status);
			$this->recordValues['ctroute_user'] = $employee_name;
			$this->recordValues['assigned_user_id'] = vtws_getWebserviceEntityId('Users',$current_user->id);
			
			if($ctroute_attendance_status == 'check_in'){
				$this->recordValues['related_to'] = $related_to;
				$this->recordValues['ctroute_planning'] = $ctroute_planning;
				$this->recordValues['check_in_location'] = "$latitude,$longitude";
				$this->recordValues['check_in_address'] = trim($request->get('check_in_address'));
				$checkin_status = true;
				$update_status = $this->changeStatusInprogress($ctroute_planning);
			}elseif($ctroute_attendance_status == 'check_out'){
				$this->recordValues['check_out_location'] = "$latitude,$longitude";
				$this->recordValues['check_out_address'] = trim($request->get('check_out_address'));
				$checkin_status = false;
			}
			define("SECONDS_PER_HOUR", 60*60);
			// Update or Create
			if (isset($this->recordValues['id'])) {
				$this->recordValues = vtws_update($this->recordValues, $user);
				$message = $this->CTTranslate('Shift ended successfully');
				$check_in_time = Vtiger_Util_Helper::convertDateTimeIntoUsersDisplayFormat($this->recordValues['createdtime']);
				$check_out_time = Vtiger_Util_Helper::convertDateTimeIntoUsersDisplayFormat($this->recordValues['modifiedtime']);
				$startdatetime = strtotime($this->recordValues['createdtime']);
			    // calculate the end timestamp
			    $enddatetime = strtotime($this->recordValues['modifiedtime']);
			    // calulate the difference in seconds
			    $difference = $enddatetime - $startdatetime;
			    $hours = round($difference / SECONDS_PER_HOUR, 0, PHP_ROUND_HALF_DOWN);
				$minutes = round(($difference % SECONDS_PER_HOUR) / 60, 0, PHP_ROUND_HALF_DOWN);
			    // output the result
			    $duration = $hours . " hr " . $minutes . " min";
			} else {
				$this->recordValues = vtws_create($module, $this->recordValues, $user);
				$message = $this->CTTranslate('Shift started successfully');
				$check_in_time = Vtiger_Util_Helper::convertDateTimeIntoUsersDisplayFormat($this->recordValues['createdtime']);
				$check_out_time = '';
				$startdatetime = strtotime($this->recordValues['createdtime']);
			    // calculate the end timestamp
			    $enddatetime = strtotime($this->recordValues['modifiedtime']);
			    // calulate the difference in seconds
			    $difference = $enddatetime - $startdatetime;
			    $hours = round($difference / SECONDS_PER_HOUR, 0, PHP_ROUND_HALF_DOWN);
				$minutes = round(($difference % SECONDS_PER_HOUR) / 60, 0, PHP_ROUND_HALF_DOWN);
			    // output the result
			    $duration = $hours . " hr " . $minutes . " min";
				
			}

			if($ctroute_attendance_status == 'check_out'){
				$update_status = $this->changeStatusCompleted($ctroute_planning);
			}

			$response->setResult(array('id'=>$this->recordValues['id'],'attendance_status'=>$checkin_status,'message'=>$message,'check_in_time'=>$check_in_time,'check_out_time'=>$check_out_time,'duration'=>$duration));
			
		} catch(Exception $e) {
			$response->setError($e->getCode(), $e->getMessage());
		}
		return $response;
	}

	function changeStatusInprogress($ctroute_planning){
		if($ctroute_planning){
			$ctroute_planning_id = explode('x',$ctroute_planning);
			$recordId = $ctroute_planning_id[1];
			$moduleName = 'CTRoutePlanning';
			$ctroute_status = 'In Progress';
			$recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
			$recordModel->set('id', $recordId);
			$recordModel->set('mode', 'edit');
			$recordModel->set('ctroute_status',$ctroute_status);
			$recordModel->save();
		}
	}

	function changeStatusCompleted($ctroute_planning){
		global $adb;
		if($ctroute_planning){
			$ctroute_planning_id = explode('x',$ctroute_planning);
			$recordId = $ctroute_planning_id[1];
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
			if($statusCompleted == true){
				$moduleName = 'CTRoutePlanning';
				$ctroute_status = 'Completed';
				$recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
				$recordModel->set('id', $recordId);
				$recordModel->set('mode', 'edit');
				$recordModel->set('ctroute_status',$ctroute_status);
				$recordModel->save();
			}
		}
	}
	
}
