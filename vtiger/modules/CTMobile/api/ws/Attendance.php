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

class CTMobile_WS_Attendance extends CTMobile_WS_FetchRecordWithGrouping {
	protected $recordValues = false;
	
	// Avoid retrieve and return the value obtained after Create or Update
	protected function processRetrieve(CTMobile_API_Request $request) {
		return $this->recordValues;
	}
	
	function process(CTMobile_API_Request $request) {
		global $current_user; // Required for vtws_update API
		$current_user = $this->getActiveUser();
		$user = Users::getActiveAdminUser();
		$module = 'CTAttendance';
		$eventid = trim($request->get('eventid'));
		$recordid = trim($request->get('record'));
		$attendance_status = trim($request->get('attendance_status'));
		$employee_name = trim($request->get('userid'));
		$latitude = trim($request->get('latitude'));
		$longitude = trim($request->get('longitude'));
		
		$response = new CTMobile_API_Response();
		
		if (empty($attendance_status)) {
			$message = $this->CTTranslate('Status cannot be empty');
			$response->setError(404, $message);
			return $response;
		}
		if (empty($employee_name)) {
			$message = $this->CTTranslate('User cannot be empty');
			$response->setError(404, $message);
			return $response;
		}
		if (empty($latitude)) {
			$message = $this->CTTranslate('Latitude cannot be empty');
			$response->setError(404, $message);
			return $response;
		}	
		if (empty($longitude)) {
			$message = $this->CTTranslate('Longitude cannot be empty');
			$response->setError(404, $message);
			return $response;
		}
		try {
			if($eventid != ''){
				// Retrieve or Initalize
				if (!empty($recordid) && !$this->isTemplateRecordRequest($request)) {
					$this->recordValues = vtws_retrieve($recordid, $user);
				} else {
					$this->recordValues = array();
				}
				
				// Set the modified values
				$checkin_status = false;
				$this->recordValues['attendance_status'] = trim($attendance_status);
				$this->recordValues['employee_name'] = CTMobile_WS_Utils::getEntityModuleWSId('Users').'x'.$employee_name;
				$this->recordValues['assigned_user_id'] = CTMobile_WS_Utils::getEntityModuleWSId('Users').'x'.$current_user->id;
				$calendarid = explode('x',$eventid);
				$eventid = CTMobile_WS_Utils::getEntityModuleWSId('Calendar').'x'.$calendarid[1];
				$this->recordValues['eventid'] = $eventid;
				if($attendance_status == 'check_in'){
					$this->recordValues['check_in_location'] = "$latitude,$longitude";
					$this->recordValues['check_in_address'] = trim($request->get('check_in_address'));
					$checkin_status = true;
				}elseif($attendance_status == 'check_out'){
					$this->recordValues['check_out_location'] = "$latitude,$longitude";
					$this->recordValues['check_out_address'] = trim($request->get('check_out_address'));
					$checkin_status = false;
				}
				// Update or Create
				if (isset($this->recordValues['id'])) {
					if($attendance_status == 'check_out') {
						$recordId = explode('x',$this->recordValues['id']);
						$attendanceRecordModel = Vtiger_Record_Model::getInstanceById($recordId[1], $module);
						$attendanceRecordModel->set('mode','edit');
						$attendanceRecordModel->set('check_out_location',"$latitude,$longitude");
						$attendanceRecordModel->set('check_out_address',trim($request->get('check_out_address')));
						$attendanceRecordModel->set('attendance_status',$attendance_status);
						$attendanceRecordModel->set('assigned_user_id',$current_user->id);
						$attendanceRecordModel->save();
						$message = $this->CTTranslate('Shift ended successfully');
					}else{
						$this->recordValues = vtws_update($this->recordValues, $user);
						$message = $this->CTTranslate('Shift ended successfully');
					}
				} else {
					$attendanceRecordModel = Vtiger_Record_Model::getCleanInstance($module);
					$attendanceRecordModel->set('mode','');
					$attendanceRecordModel->set('employee_name',$employee_name);
					$attendanceRecordModel->set('assigned_user_id',$current_user->id);
					$attendanceRecordModel->set('eventid',$calendarid[1]);
					$attendanceRecordModel->set('check_in_location',"$latitude,$longitude");
					$attendanceRecordModel->set('check_in_address',trim($request->get('check_in_address')));
					$attendanceRecordModel->set('attendance_status',$attendance_status);
					$attendanceRecordModel->save();
					$moduleWSId = CTMobile_WS_Utils::getEntityModuleWSId($module);
					$recordId = $attendanceRecordModel->getId();
					$this->recordValues['id'] = $moduleWSId.'x'.$recordId;
					//$this->recordValues = vtws_create($module, $this->recordValues, $user);
					$message = $this->CTTranslate('Shift started successfully');
					
				}
				$response->setResult(array('id'=>$this->recordValues['id'],'ctattendance_status'=>$checkin_status,'attendance_status'=>vtranslate($attendance_status,'CTAttendance'),'isShowCheckin'=>true,'message'=>$message));
			}else{
				// Retrieve or Initalize
				if (!empty($recordid) && !$this->isTemplateRecordRequest($request)) {
					$this->recordValues = vtws_retrieve($recordid, $user);
				} else {
					$this->recordValues = array();
				}
				
				// Set the modified values
				
					$this->recordValues['attendance_status'] = trim($attendance_status);
					$this->recordValues['employee_name'] = CTMobile_WS_Utils::getEntityModuleWSId('Users').'x'.$employee_name;
					$this->recordValues['assigned_user_id'] = CTMobile_WS_Utils::getEntityModuleWSId('Users').'x'.$current_user->id;
					
					if($attendance_status == 'check_in'){
						$this->recordValues['check_in_location'] = "$latitude,$longitude";
						$this->recordValues['check_in_address'] = trim($request->get('check_in_address'));
					}elseif($attendance_status == 'check_out'){
						$this->recordValues['check_out_location'] = "$latitude,$longitude";
						$this->recordValues['check_out_address'] = trim($request->get('check_out_address'));
					}
				// Update or Create
				if (isset($this->recordValues['id'])) {
					if($attendance_status == 'check_out') {
						$recordId = explode('x',$this->recordValues['id']);
						$attendanceRecordModel = Vtiger_Record_Model::getInstanceById($recordId[1], $module);
						$attendanceRecordModel->set('mode','edit');
						$attendanceRecordModel->set('check_out_location',"$latitude,$longitude");
						$attendanceRecordModel->set('check_out_address',trim($request->get('check_out_address')));
						$attendanceRecordModel->set('attendance_status',$attendance_status);
						$attendanceRecordModel->set('assigned_user_id',$current_user->id);
						$attendanceRecordModel->save();
						$message = $this->CTTranslate('Shift ended successfully');
						
					}else{
						$this->recordValues = vtws_update($this->recordValues, $user);
						$message = $this->CTTranslate('Shift ended successfully');
					}
				} else {
					$attendanceRecordModel = Vtiger_Record_Model::getCleanInstance($module);
					$attendanceRecordModel->set('mode','');
					$attendanceRecordModel->set('employee_name',$employee_name);
					$attendanceRecordModel->set('assigned_user_id',$current_user->id);
					//$attendanceRecordModel->set('eventid',$calendarid[1]);
					$attendanceRecordModel->set('check_in_location',"$latitude,$longitude");
					$attendanceRecordModel->set('check_in_address',trim($request->get('check_in_address')));
					$attendanceRecordModel->set('attendance_status',$attendance_status);
					$attendanceRecordModel->save();
					$moduleWSId = CTMobile_WS_Utils::getEntityModuleWSId($module);
					$recordId = $attendanceRecordModel->getId();
					$this->recordValues['id'] = $moduleWSId.'x'.$recordId;
					//$this->recordValues = vtws_create($module, $this->recordValues, $user);
					$message = $this->CTTranslate('Shift started successfully');
				}

				$result = array('record'=>array('id'=>$this->recordValues['id'],'module'=>$module),'message'=>$message);
				$response->setResult($result);
			}
			
			
		} catch(Exception $e) {
			$response->setError($e->getCode(), $e->getMessage());
		}
		return $response;
	}
	
}
