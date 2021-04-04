<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
class CTMobileSettings_addLatlong_Action extends Vtiger_Save_Action {
    
	public function process(Vtiger_Request $request) {
		global $adb;
		$selectLeadQuery = $adb->pquery("SELECT vtiger_leadaddress.*, vtiger_crmentity.setype from vtiger_leadaddress 
		INNER JOIN vtiger_leaddetails ON vtiger_leaddetails.leadid = vtiger_leadaddress.leadaddressid 
		INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_leaddetails.leadid WHERE vtiger_crmentity.deleted = 0 AND vtiger_leaddetails.leadid NOT IN ( SELECT recordid FROM ct_address_lat_long )", array());
		$numOfLeads = $adb->num_rows($selectLeadQuery);
		for($i=0;$i<$numOfLeads;$i++){
			   $setype = $adb->query_result($selectLeadQuery, $i, 'setype');
			   $result = $adb->pquery("SELECT id FROM vtiger_ws_entity WHERE name=?", array($setype));
			   $moduleId = $adb->query_result($result, 0, 'id');
			   $leadid = $adb->query_result($selectLeadQuery, $i, 'leadaddressid');
			   
			   $resultAddress = $adb->pquery("SELECT * FROM ctmobile_address_fields WHERE module = ?",array("Leads"));
			   $count = $adb->num_rows($resultAddress);
			   $address = '';
			   for($j=0;$j<$count;$j++){
				   $fields = $adb->query_result($resultAddress,$j,'fieldname');
				   $test = explode(":",$fields);
				   $field = $test[1];
				   $newField = $adb->query_result($selectLeadQuery, $i, $field);
				   if($newField != '') {
						if($i+1 == $count){
							$address .= $newField;
						}else{
							$address .= $newField.', ';
						}
				   }
			   }
			
			$leadAddress = $this->getLatAndLong($address);
			$leadAddress['recordid'] = $leadid;
			$leadAddress['moduleid'] = $moduleId;
			
			$this->insertLatLong($leadAddress);
		}

		$selectContactQuery = $adb->pquery("SELECT vtiger_contactaddress.*, vtiger_crmentity.setype from vtiger_contactaddress INNER JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_contactaddress.contactaddressid INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_contactdetails.contactid WHERE vtiger_crmentity.deleted = 0 AND vtiger_crmentity.crmid NOT IN ( SELECT recordid FROM ct_address_lat_long )", array());
		$numOfContacts = $adb->num_rows($selectContactQuery);
		for($i=0;$i<$numOfContacts;$i++){
			$setype = $adb->query_result($selectContactQuery, $i, 'setype');
			$result = $adb->pquery("SELECT id FROM vtiger_ws_entity WHERE name=?", array($setype));
		    $moduleId = $adb->query_result($result, 0, 'id');
			$contactid = $adb->query_result($selectContactQuery, $i, 'contactaddressid');
			
			$resultAddress = $adb->pquery("SELECT * FROM ctmobile_address_fields WHERE module = ?",array("Contacts"));
			$count = $adb->num_rows($resultAddress);
			$address = '';
			for($j=0;$j<$count;$j++){
			   $fields = $adb->query_result($resultAddress,$j,'fieldname');
			   $test = explode(":",$fields);
			   $field = $test[1];
			   $newField = $adb->query_result($selectContactQuery, $i, $field);
			   if($newField != '') {
						if($i+1 == $count){
							$address .= $newField;
						}else{
							$address .= $newField.', ';
						}
				   }
			}
			   
			$contactAddress = $this->getLatAndLong($address);
			$contactAddress['recordid'] = $contactid;
			$contactAddress['moduleid'] = $moduleId;
			
			$this->insertLatLong($contactAddress);
		}

		$selectAccountQuery = $adb->pquery("SELECT vtiger_accountbillads.*, vtiger_accountshipads.ship_city, vtiger_accountshipads.ship_code, vtiger_accountshipads.ship_country, vtiger_accountshipads.ship_state, vtiger_accountshipads.ship_pobox, vtiger_accountshipads.ship_street, vtiger_crmentity.setype from vtiger_accountbillads INNER JOIN vtiger_accountshipads ON vtiger_accountshipads.accountaddressid = vtiger_accountbillads.accountaddressid INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_accountbillads.accountaddressid WHERE vtiger_crmentity.deleted = 0 AND vtiger_crmentity.crmid NOT IN ( SELECT recordid FROM ct_address_lat_long )", array());
		$numOfAccount = $adb->num_rows($selectAccountQuery);
		for($i=0;$i<$numOfAccount;$i++) {
			$setype = $adb->query_result($selectAccountQuery, $i, 'setype');
			$result = $adb->pquery("SELECT id FROM vtiger_ws_entity WHERE name=?", array($setype));
		    $moduleId = $adb->query_result($result, 0, 'id');
			$accountid = $adb->query_result($selectAccountQuery, $i, 'accountaddressid');
			$resultAddress = $adb->pquery("SELECT * FROM ctmobile_address_fields WHERE module = ?",array("Accounts"));
			$count = $adb->num_rows($resultAddress);
			$address = '';
			for($j=0;$j<$count;$j++){
			   $fields = $adb->query_result($resultAddress,$j,'fieldname');
			   $test = explode(":",$fields);
			   $field = $test[1];
			   $newField = $adb->query_result($selectAccountQuery, $i, $field);
				if($newField != '') {
						if($i+1 == $count){
							$address .= $newField;
						}else{
							$address .= $newField.', ';
						}
				   }
			}
			
			$accountAddress = $this->getLatAndLong($address);
			$accountAddress['recordid'] = $accountid;
			$accountAddress['moduleid'] = $moduleId;
			
			$this->insertLatLong($accountAddress);
		}
		
		$selectEventQuery = $adb->pquery("SELECT vtiger_activity.*, vtiger_crmentity.setype from vtiger_activity INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_activity.activityid WHERE vtiger_crmentity.deleted = 0 AND vtiger_crmentity.crmid NOT IN ( SELECT recordid FROM ct_address_lat_long )", array());
		$numOfEvent = $adb->num_rows($selectEventQuery);
		for($i=0;$i<$numOfEvent;$i++) {
			$setype = $adb->query_result($selectEventQuery, $i, 'setype');
			$result = $adb->pquery("SELECT id FROM vtiger_ws_entity WHERE name=?", array($setype));
		    $moduleId = $adb->query_result($result, 0, 'id');
			$activityid = $adb->query_result($selectEventQuery, $i, 'activityid');
			$resultAddress = $adb->pquery("SELECT * FROM ctmobile_address_fields WHERE module = ?",array("Calendar"));
			$count = $adb->num_rows($resultAddress);
			$address = '';
			for($j=0;$j<$count;$j++){
			   $fields = $adb->query_result($resultAddress,$j,'fieldname');
			   $test = explode(":",$fields);
			   $field = $test[1];
			   $newField = $adb->query_result($selectEventQuery, $i, $field);
				if($newField != '') {
						if($i+1 == $count){
							$address .= $newField;
						}else{
							$address .= $newField.', ';
						}
				   }
			}
			
			$eventAddress = $this->getLatAndLong($address);
			$eventAddress['recordid'] = $activityid;
			$eventAddress['moduleid'] = $moduleId;
			
			$this->insertLatLong($eventAddress);
		}
		$response = new Vtiger_Response();
		$response->setEmitType(Vtiger_Response::$EMIT_JSON);
		$response->setResult(array("true"=>$numOfEvent));
		$response->emit();

	}
	
	 function getLatAndLong($address) {
		// Get lat and long by address
		$address=urlencode($address);
		$data = array();
		$opts = array('http'=>array('header'=>"User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.146 Safari/537.36\r\n"));
		$context = stream_context_create($opts);
	
		$formattedAddr = str_replace(' ','+',$address);
		global $adb;
		$resultApi = $adb->pquery("SELECT * FROM ctmobile_api_settings",array());
		$apiKey = $adb->query_result($resultApi,0,'api_key');
		if($apiKey != ''){
			//Send request and receive json data by address
			$geocodeFromAddr = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address='.$formattedAddr.'&key='.$apiKey, false, $context);
			$output = json_decode($geocodeFromAddr);
			
			//Get latitude and longitute from json data
			$data['latitude']  = $output->results[0]->geometry->location->lat; 
			$data['longitude'] = $output->results[0]->geometry->location->lng;
		}else{
			//Send request and receive json data by address
			$geocodeFromAddr = file_get_contents('https://nominatim.openstreetmap.org/search?q='.$formattedAddr.'&format=json&polygon=1&addressdetails=1',false, $context);
			$output = json_decode($geocodeFromAddr);
			//Get latitude and longitute from json data
			$data['latitude']  = $output[0]->lat;
			$data['longitude'] = $output[0]->lon;
		}
					
		return $data;
	 }
	 function insertLatLong($recordData) {
		 global $adb;
		 $recordid = $recordData['recordid'];
		 $moduleid = $recordData['moduleid'];
		 $latitude = $recordData['latitude'];
		 $longitude = $recordData['longitude'];
		 
		 $checkRecordExit = $adb->pquery("SELECT * from ct_address_lat_long where recordid = ?", array($recordid));
		 $countRecord = $adb->num_rows($checkRecordExit);
		 
		 if($countRecord > 0) {
			$adb->pquery("UPDATE ct_address_lat_long SET latitude = ?, longitude = ? where recordid = ?", array($latitude, $longitude, $recordid));
		 } else {
			$adb->pquery("INSERT INTO ct_address_lat_long(recordid, moduleid, latitude, longitude) values(?,?,?,?)", array($recordid, $moduleid, $latitude, $longitude)); 
		} 
	}
}
