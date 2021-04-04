<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

function AddRecordLatLong($entityData){
	$adb = PearDatabase::getInstance();
	$moduleName = $entityData->getModuleName();
	$wsId = $entityData->getId();
	$parts = explode('x', $wsId);
	$entityId = $parts[1];
	$moduleId = $parts[0];
	if($moduleName == 'Leads'){
		updateLeads($entityId,$moduleId,$entityData);
	}else if($moduleName == 'Contacts'){
		updateContacts($entityId,$moduleId,$entityData);
	}else if($moduleName == 'Accounts'){
		updateAccounts($entityId,$moduleId,$entityData);
	}else if($moduleName == 'Calendar' || $moduleName == 'Events'){
		updateActivity($entityId,$moduleId,$entityData);
	}
}

function updateLeads($entityId,$moduleId,$entityData) {
	   global $adb;
	   $resultAddress = $adb->pquery("SELECT * FROM ctmobile_address_fields WHERE module = ?",array("Leads"));
	   $count = $adb->num_rows($resultAddress);
	   $address = '';
	   for($i=0;$i<$count;$i++){
		   $fields = $adb->query_result($resultAddress,$i,'fieldname');
		   $test = explode(":",$fields);
		   $field = $test[1];
		   $newField = $entityData->get($field);
		   if($newField != '') {
				if($i+1 == $count){
					   $address .= $newField;
			     }else{
					   $address .= $newField.', ';
			     }
		   }
	   }
	   if($address != ''){
		 $leadAddress = getLatAndLong($address);// for retrieve lat long from adress
		 $leadAddress['recordid'] = $entityId;
		 $leadAddress['moduleid'] = $moduleId;
		 if($leadAddress['latitude'] != '' && $leadAddress['longitude'] != ''){
			insertLatLong($leadAddress);// to insert latlong in ct_address_lat_long table
		 }
	   }
}


function updateContacts($entityId,$moduleId,$entityData) {
	   global $adb;
	   $resultAddress = $adb->pquery("SELECT * FROM ctmobile_address_fields WHERE module = ?",array("Contacts"));
	   $count = $adb->num_rows($resultAddress);
	   $address = '';
	   for($i=0;$i<$count;$i++){
		   $fields = $adb->query_result($resultAddress,$i,'fieldname');
		   $test = explode(":",$fields);
		   $field = $test[1];
		   $newField = $entityData->get($field);
		   if($newField != '') {
			  if($i+1 == $count){
				   $address .= $newField;
			  }else{
				   $address .= $newField.', ';
			   }
		   }
	   }
	   if($address != ''){
		  $leadAddress = getLatAndLong($address);// for retrieve lat long from adress
		  $leadAddress['recordid'] = $entityId;
		  $leadAddress['moduleid'] = $moduleId;
		  if($leadAddress['latitude'] != '' && $leadAddress['longitude'] != ''){
		 	insertLatLong($leadAddress);// to insert latlong in ct_address_lat_long table
		  }
	   }	
}


function updateAccounts($entityId,$moduleId,$entityData) {
	   global $adb;
	   $resultAddress = $adb->pquery("SELECT * FROM ctmobile_address_fields WHERE module = ?",array("Accounts"));
	   $count = $adb->num_rows($resultAddress);
	   $address = '';
	   for($i=0;$i<$count;$i++){
		   $fields = $adb->query_result($resultAddress,$i,'fieldname');
		   $test = explode(":",$fields);
		   $field = $test[1];
		   $newField = $entityData->get($field);
		   if($newField != '') {
				 if($i+1 == $count){
					   $address .= $newField;
			     }else{
					   $address .= $newField.', ';
			     }
		   }
	   }
	   if($address != ''){
		 $leadAddress = getLatAndLong($address);// for retrieve lat long from adress
		 $leadAddress['recordid'] = $entityId;
		 $leadAddress['moduleid'] = $moduleId;
		 if($leadAddress['latitude'] != '' && $leadAddress['longitude'] != ''){
			insertLatLong($leadAddress);// to insert latlong in ct_address_lat_long table
		 }
	   }	
}

function updateActivity($entityId,$moduleId,$entityData) {
	   global $adb;
	   $resultAddress = $adb->pquery("SELECT * FROM ctmobile_address_fields WHERE module = ?",array("Calendar"));
	   $count = $adb->num_rows($resultAddress);
	   $address = '';
	   for($i=0;$i<$count;$i++){
		   $fields = $adb->query_result($resultAddress,$i,'fieldname');
		   $test = explode(":",$fields);
		   $field = $test[1];
		   $newField = $entityData->get($field);
		   if($newField != '') {
			   if($i+1 == $count){
				   $address .= $newField;
			   }else{
				   $address .= $newField.', ';
			   }
		   }
	   }
	   if($address != ''){
		$leadAddress = getLatAndLong($address);
	    $leadAddress['recordid'] = $entityId;
        $leadAddress['moduleid'] = $moduleId;
		 if($leadAddress['latitude'] != '' && $leadAddress['longitude'] != ''){
			insertLatLong($leadAddress);
		 }
	   }
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

?>
