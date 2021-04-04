<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
class CTMobileSettings_SaveApi_Action extends Vtiger_Save_Action {
    
public function process(Vtiger_Request $request) {
	global $adb,$site_URL;
	$api_Key = trim($request->get('api_Key'));
	$getLicenseQuery=$adb->pquery("SELECT * FROM ctmobile_api_settings");
	$numOfLicenseCount = $adb->num_rows($getLicenseQuery);
	
	$address = "203 - Prerna Arcade, Parimal Underbridge, Ellisbridge, Tulsibag Society, Ambawadi, Ahmedabad, Gujarat 380006";
	$address=urlencode($address);
	
	$data = array();
	$opts = array('http'=>array('header'=>"User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.146 Safari/537.36\r\n"));
	$context = stream_context_create($opts);
	$formattedAddr = str_replace(' ','+',$address);
	//Send request and receive json data by address
	$geocodeFromAddr = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address='.$formattedAddr.'&key='.$api_Key, false, $context);
	$output = json_decode($geocodeFromAddr);
	$status = $output->status;
	if($status === 'OK'){
		if($numOfLicenseCount > 0){
			$record=$adb->query_result($getLicenseQuery,0,'id');
			$query=$adb->pquery("UPDATE ctmobile_api_settings SET api_key=? WHERE id=?",array($api_Key,$record));
			if($query){
				
				$result = array('code'=>2, 'msg'=>vtranslate('Google Api Key Updated Successfully','CTMobileSettings'));
			}
		}else{
			$query=$adb->pquery("INSERT INTO ctmobile_api_settings (api_key) values(?)",array($api_Key));
			if($query){
				$result = array('code'=>1, 'msg'=>vtranslate('Google Api Key Inserted Successfully','CTMobileSettings'));
			}
		}
	}else{
		$result = array('code'=>100, 'msg'=>vtranslate('Invalid Google Api Key','CTMobileSettings'));
	}
	$response = new Vtiger_Response();
	$response->setEmitType(Vtiger_Response::$EMIT_JSON);
	$response->setResult($result);
	$response->emit();
}
}
?>
