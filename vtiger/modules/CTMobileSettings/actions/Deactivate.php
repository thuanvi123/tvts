<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

class CTMobileSettings_Deactivate_Action extends Vtiger_Save_Action {
	
    public function process(Vtiger_Request $request) {
        global $adb;
        global $site_URL;
        $license_key = $request->get('license_key');
        $domain = $request->get('domain');
        $getLicenseQuery = $adb->pquery("SELECT * FROM ctmobile_license_settings",array());
        $numOfLicenseCount = $adb->num_rows($getLicenseQuery);
		if($numOfLicenseCount > 0){
			if($license_key == "" && $domain == ""){
				$license_key=$adb->query_result($getLicenseQuery,0,'license_key');
				$domain=$adb->query_result($getLicenseQuery,0,'domain');
			}
			$url = CTMobileSettings_Module_Model::$CTMOBILE_CHECKLICENSE_URL;
			$ch = curl_init($url);
			$data = array( "license_key"=>$license_key,"domain"=>$domain,"action"=>"deactivate");
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			// Send request.
			$result = curl_exec($ch);
			curl_close($ch);
			$result_response = json_decode($result,true);
			if($result_response['message'] == 'Deactivated'){
				$deleteLicense = $adb->pquery("DELETE FROM ctmobile_license_settings",array());
				//pushnotification for logout
				$title = 'logout';
				$message = 'logout';
				CTMobileSettings_Module_Model::sendpushnotificationAll($message,$title);		 		
				$response = new Vtiger_Response();
				$response->setEmitType(Vtiger_Response::$EMIT_JSON);
				$response->setResult(array("message"=>vtranslate('MSG_DEACTIVATE_LICENSE','CTMobileSettings')));
				$response->emit();
			}else{
				$response = new Vtiger_Response();
				$response->setEmitType(Vtiger_Response::$EMIT_JSON);
				$response->setResult(array("message"=>vtranslate('License Deactivation Failed','CTMobileSettings')));
				$response->emit();
			}
		}
       
    }
}

?>
