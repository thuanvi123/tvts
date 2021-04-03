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

class CTMobile_WS_CTTimeTracker extends CTMobile_WS_FetchRecordWithGrouping {
	protected $recordValues = false;
	protected $TimeControlValues = false;
	
	// Avoid retrieve and return the value obtained after Create or Update
	protected function processRetrieve(CTMobile_API_Request $request) {
		return $this->recordValues;
	}
	
	function process(CTMobile_API_Request $request) {
		global $adb,$current_user; // Required for vtws_update API
		$current_user = $this->getActiveUser();
		$user = Users::getActiveAdminUser();
		$module = 'CTTimeTracker';
		$recordid = trim($request->get('record'));
		$tracking_title = trim($request->get('tracking_title'));
		$tracking_notes = trim($request->get('tracking_notes'));
		$module_name = trim($request->get('module_name'));
		$related_to = trim($request->get('related_to'));
		$tracking_user = trim($request->get('tracking_user'));
		$is_start_tracking = trim($request->get('is_start_tracking'));
		$old_record = trim($request->get('old_record'));
		$is_edit = trim($request->get('is_edit'));
		$latitude = trim($request->get('latitude'));
		$longitude = trim($request->get('longitude'));
		$check_in_address = trim($request->get('check_in_address'));
		$check_out_address = trim($request->get('check_out_address'));
		
		$response = new CTMobile_API_Response();

		try {
			$nowInDBFormat = date('Y-m-d H:i:s');
			// Set the initial values
			$checkin_status = false;
			define("SECONDS_PER_HOUR", 60*60);
			// Retrieve or Initalize
			if (!empty($recordid)) {
				$this->recordValues = vtws_retrieve($recordid, $user);
				if($is_start_tracking == true && $is_start_tracking != 'false'){
					list($date_start, $time_start) = explode(' ',  $nowInDBFormat);

					$this->TimeControlValues['date_start'] = $date_start;
					$this->TimeControlValues['time_start'] = $time_start;
					$this->TimeControlValues['related_tracking'] = $recordid;
					$this->TimeControlValues['assigned_user_id'] = vtws_getWebserviceEntityId('Users',$current_user->id);
					$this->TimeControlValues['check_in_location'] = "$latitude,$longitude";
					$this->TimeControlValues['check_in_address'] = $check_in_address;
					$this->TimeControlValues = vtws_create('CTTimeControl', $this->TimeControlValues, $user);
					$this->recordValues['tracking_status'] = 'Start';
					$checkin_status = true;
				}else{
					if($is_edit != true){
						list($date_end, $time_end) = explode(' ', $nowInDBFormat);
						$this->recordValues['tracking_status'] = 'End';
						$checkin_status = false;

						$record =  explode('x', $recordid);
						$timeControlQuery = "SELECT * FROM vtiger_cttimecontrol INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_cttimecontrol.cttimecontrolid WHERE vtiger_crmentity.deleted = 0 AND vtiger_cttimecontrol.related_tracking = ? ";
						$timeControlResult = $adb->pquery($timeControlQuery,array($record[1]));
						$num_rows = $adb->num_rows($timeControlResult);
						
						$difference = 0;
						for($i=0;$i<$num_rows;$i++) {
							$start_date = $adb->query_result($timeControlResult,$i,'date_start');
							$start_time = $adb->query_result($timeControlResult,$i,'time_start');
							$end_date = $adb->query_result($timeControlResult,$i,'date_end');
							$end_time = $adb->query_result($timeControlResult,$i,'time_end');
							if($end_date == '' && $end_time == ''){
								$cttimecontrolid = $adb->query_result($timeControlResult,$i,'cttimecontrolid');
								$end_date = $date_end;
								$end_time = $time_end;
							}

							$startdatetime = strtotime($start_date.' '.$start_time);
						    // calculate the end timestamp
						    $enddatetime = strtotime($end_date.' '.$end_time);
						    // calulate the difference in seconds
						    $difference = $difference + ($enddatetime - $startdatetime);
						}
						
						if($cttimecontrolid != ''){
							$timecontrolid = vtws_getWebserviceEntityId('CTTimeControl',$cttimecontrolid);
							
							$this->TimeControlValues = vtws_retrieve($timecontrolid, $user);
							
							$this->TimeControlValues['date_end'] = $date_end;
							$this->TimeControlValues['time_end'] = $time_end;
							$this->TimeControlValues['check_out_location'] = "$latitude,$longitude";
							$this->TimeControlValues['check_out_address'] = $check_out_address;

							$this->TimeControlValues = vtws_update($this->TimeControlValues, $user);
						}
						
					    $totalTime = $difference;

					    $hours = gmdate("H", $totalTime);
						$minutes = gmdate("i", $totalTime);
						$seconds = gmdate("s", $totalTime);

					    // output the result
					    $duration = $hours . " hr " . $minutes . " min";
					    $this->recordValues['total_time'] = $totalTime;
					    $this->recordValues['total_hour'] = $hours;
					    $this->recordValues['total_min'] = $minutes;
					    $this->recordValues['total_seconds'] = $seconds;
					}
				}
				if($tracking_title != ''){
					$this->recordValues['tracking_title'] = $tracking_title;
				}
				if($tracking_notes != ''){
					$this->recordValues['tracking_notes'] = $tracking_notes;
				}
				$check_in_time = "";
				$check_out_time = "";
				if($is_start_tracking == true && $is_start_tracking != 'false'){
					if($this->recordValues['check_in_location'] == '' && $this->recordValues['check_in_address'] == ''){
						$this->recordValues['check_in_location'] = "$latitude,$longitude";
						$this->recordValues['check_in_address'] = $check_in_address;
					}
				}else{
					$this->recordValues['check_out_location'] = "$latitude,$longitude";
					$this->recordValues['check_out_address'] = $check_out_address;
				}
				$this->recordValues = vtws_update($this->recordValues, $user);
				if($is_start_tracking == true && $is_start_tracking != 'false'){
					
					$message = $this->CTTranslate('Time Tracking started successfully');
					$duration = "0 hr 0 min";
					$check_in_time = Vtiger_Datetime_UIType::getDisplayDateTimeValue($this->recordValues['date_start'].' '.$this->recordValues['time_start']);
				}else{
					if($is_edit != true){
						$message = $this->CTTranslate('Time Tracking ended successfully');
						$check_in_time = Vtiger_Datetime_UIType::getDisplayDateTimeValue($this->recordValues['date_start'].' '.$this->recordValues['time_start']);
						$check_out_time = Vtiger_Datetime_UIType::getDisplayDateTimeValue($this->recordValues['date_end'].' '.$this->recordValues['time_end']);
					}else{
						$message = $this->CTTranslate('Time Tracking updated successfully');
					}
				}
			} else {
				
				/*if(!empty($old_record)){
					$this->recordValues = vtws_retrieve($old_record, $user);
					list($date_end, $time_end) = explode(' ', $nowInDBFormat);
					$this->recordValues['date_end'] = $date_end;
					$this->recordValues['time_end'] = $time_end;
					$this->recordValues['tracking_status'] = 'End';

					$startdatetime = strtotime($this->recordValues['date_start'].' '.$this->recordValues['time_start']);
				    // calculate the end timestamp
				    $enddatetime = strtotime($this->recordValues['date_end'].' '.$this->recordValues['time_end']);
				    // calulate the difference in seconds
				    $difference = $enddatetime - $startdatetime;
				    $totalTime = $difference;
				    $hours = round($difference / SECONDS_PER_HOUR, 0, PHP_ROUND_HALF_DOWN);
					$minutes = round(($difference % SECONDS_PER_HOUR) / 60, 0, PHP_ROUND_HALF_DOWN);
				    // output the result
				    $this->recordValues['total_time'] = $totalTime;
				    $this->recordValues['total_hour'] = $hours;
				    $this->recordValues['total_min'] = $minutes;
				    $this->recordValues = vtws_update($this->recordValues, $user);

				}*/
				list($date_start, $time_start) = explode(' ',  $nowInDBFormat);
				$this->recordValues = array();
				$this->recordValues['tracking_title'] = $tracking_title;
				//$this->recordValues['tracking_notes'] = $tracking_notes;
				$this->recordValues['tracking_user'] = $tracking_user;
				$this->recordValues['assigned_user_id'] = vtws_getWebserviceEntityId('Users',$current_user->id);
				$this->recordValues['related_to'] = $related_to;
				if($is_start_tracking == true && $is_start_tracking != 'false'){
					$this->recordValues['tracking_status'] = 'Start';
					$this->recordValues['check_in_location'] = "$latitude,$longitude";
					$this->recordValues['check_in_address'] = $check_in_address;
					$checkin_status = true;
				}else{
					$checkin_status = false;
				}
				$this->recordValues = vtws_create($module, $this->recordValues, $user);
				if($is_start_tracking == true && $is_start_tracking != 'false'){
					$this->TimeControlValues['date_start'] = $date_start;
					$this->TimeControlValues['time_start'] = $time_start;
					$this->TimeControlValues['check_in_location'] = "$latitude,$longitude";
					$this->TimeControlValues['check_in_address'] = $check_in_address;
					$this->TimeControlValues['related_tracking'] = $this->recordValues['id'];
					$this->TimeControlValues['assigned_user_id'] = vtws_getWebserviceEntityId('Users',$current_user->id);
					$this->TimeControlValues = vtws_create('CTTimeControl', $this->TimeControlValues, $user);
				}
				$check_in_time = "";
				if($is_start_tracking == true && $is_start_tracking != 'false'){
					$message = $this->CTTranslate('Time Tracking started successfully');
					$check_in_time = Vtiger_Datetime_UIType::getDisplayDateTimeValue($date_start.' '.$time_start);
				}else{
					$message = $this->CTTranslate('Time Tracking saved successfully');
				}
				$check_out_time = "";
			    $duration = "0 hr 0 min";
			}
			

			if($is_edit != true){
				$response->setResult(array('id'=>$this->recordValues['id'],'tracking_status'=>$checkin_status,'message'=>$message,'track_in_time'=>$check_in_time,'track_out_time'=>$check_out_time,'duration'=>$duration));
			}else{
				$response->setResult(array('id'=>$this->recordValues['id'],'message'=>$message));
			}
			
		} catch(Exception $e) {
			$response->setError($e->getCode(), $e->getMessage());
		}
		return $response;
	}
	
}
