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

class CTMobile_WS_AttendanceUserHours extends CTMobile_WS_Controller {
	
		function process(CTMobile_API_Request $request) {
		global $current_user,$adb;
		$current_user = $this->getActiveUser();
		$time_zone = $current_user->time_zone;
		$user =  Users::getActiveAdminUser();
		$employee_name = trim($request->get('userid'));
		
		$response = new CTMobile_API_Response();
		if (empty($employee_name)) {
			$message = $this->CTTranslate('User cannot be empty');
			$response->setError(1501, $message);
			return $response;
		}
		$date = date('Y-m-d');
		$startDateTime = new DateTimeField($date . ' ' . date('H:i:s'));
		$userStartDate = $startDateTime->getDisplayDate();
		$userStartDateTime = new DateTimeField($userStartDate . ' 00:00:00');
		$startDateTime = $userStartDateTime->getDBInsertDateTimeValue();
		$endDateTime = new DateTimeField($date . ' ' . date('H:i:s'));
		$userEndDate = $endDateTime->getDisplayDate();
		$userEndDateTime = new DateTimeField($userEndDate . ' 23:59:00');
		$endDateTime = $userEndDateTime->getDBInsertDateTimeValue();
		
		$recentEvent_data = array();
		$generator = new QueryGenerator('CTAttendance', $user);
		$generator->setFields(array('employee_name','attendance_status','createdtime','modifiedtime','id'));
		$eventQuery = $generator->getQuery();
		$eventQuery .= " and vtiger_ctattendance.employee_name = '$employee_name' ";
		$queryForToday = $eventQuery." AND modifiedtime BETWEEN '$startDateTime' AND '$endDateTime' ";
		$query = $adb->pquery($queryForToday);
		$querycheckin = $adb->pquery($eventQuery);
		$record = '';
		for($i=0; $i<$adb->num_rows($querycheckin); $i++) {
				$id = $adb->query_result($querycheckin, $i, 'ctattendanceid');
				$recordid = vtws_getWebserviceEntityId('CTAttendance',$id);
				$attendance_status = $adb->query_result($querycheckin, $i, 'attendance_status');
				if($attendance_status == 'check_in'){
					$record = $recordid;
				}
		}
		if($adb->num_rows($query) == 0){
			$response->setResult(array('attendance_data'=>$recentEvent_data,'total_hours'=>'0 hrs 0 min','record'=>$record, 'module'=>'CTAttendance', 'message'=>$this->CTTranslate('No records found')));
			//throw new WebServiceException(404,vtranslate('LBL_NO_RECORDS_FOUND','Vtiger'));
		} else {
			$total_hours = 0;
			for($i=0; $i<$adb->num_rows($query); $i++) {
				
				define("SECONDS_PER_HOUR", 60*60);
				$id = $adb->query_result($query, $i, 'ctattendanceid');
				$recordid = vtws_getWebserviceEntityId('CTAttendance',$id);
				
				
				$check_in1 = $adb->query_result($query, $i, 'createdtime');
				$date = new DateTime($check_in1, new DateTimeZone('UTC'));
				$date->setTimezone(new DateTimeZone($time_zone));
				$check_in = $date->format('Y-m-d H:i:s');
				$check_in = Vtiger_Util_Helper::convertTimeIntoUsersDisplayFormat(date('H:i:s',strtotime($check_in)));
				
				$attendance_status = $adb->query_result($query, $i, 'attendance_status');
				if($attendance_status == 'check_in'){
					$record = $recordid;
					$check_out1 = date('Y-m-d H:i:s');
					$date = new DateTime($check_out1, new DateTimeZone('UTC'));
					$date->setTimezone(new DateTimeZone($time_zone));
					$check_out = $date->format('Y-m-d H:i:s');
					$check_out = Vtiger_Util_Helper::convertTimeIntoUsersDisplayFormat(date('H:i:s',strtotime($check_out)));

				}else{
					$check_out1 = $adb->query_result($query, $i, 'modifiedtime');
					$date = new DateTime($check_out1, new DateTimeZone('UTC'));
					$date->setTimezone(new DateTimeZone($time_zone));
					$check_out = $date->format('Y-m-d H:i:s');
					$check_out = Vtiger_Util_Helper::convertTimeIntoUsersDisplayFormat(date('H:i:s',strtotime($check_out)));
				}
				
			    $startdatetime = strtotime($check_in1);
			    // calculate the end timestamp
			    $enddatetime = strtotime($check_out1);
			    // calulate the difference in seconds
			    $difference = $enddatetime - $startdatetime;
			    $total_hours = $difference + $total_hours;
			    $hours = round($difference / SECONDS_PER_HOUR, 0, PHP_ROUND_HALF_DOWN);
				$minutes = round(($difference % SECONDS_PER_HOUR) / 60, 0, PHP_ROUND_HALF_DOWN);
			    // output the result
			    $diffrent = $hours . " hr " . $minutes . " min";
				$recentEvent_data[] = array('id'=> $recordid, 'check_in' => $check_in, 'check_out' => $check_out,'attendance_status' => $attendance_status,'total_hours' => $diffrent);	
			}	
			
			$f_hours = round($total_hours / SECONDS_PER_HOUR, 0, PHP_ROUND_HALF_DOWN);
			$f_minutes = round(($total_hours % SECONDS_PER_HOUR) / 60, 0, PHP_ROUND_HALF_DOWN);
				   // output the result
			$f_diffrent = $f_hours . " hrs " . $f_minutes . " min";
			
			$response->setResult(array('attendance_data'=>$recentEvent_data,'total_hours'=>$f_diffrent,'record'=>$record, 'module'=>'CTAttendance', 'message'=>''));
		}
		return $response;
	}
	
}
