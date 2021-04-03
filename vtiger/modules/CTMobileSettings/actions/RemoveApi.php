<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
class CTMobileSettings_RemoveApi_Action extends Vtiger_Save_Action {
    
public function process(Vtiger_Request $request) {
	global $adb,$site_URL;
	
	$deleteLicenseQuery=$adb->pquery("DELETE FROM ctmobile_api_settings");

	$result = array('code'=>1, 'msg'=>vtranslate('Google Api Key deleted Successfully','CTMobileSettings'));
	$response = new Vtiger_Response();
	$response->setEmitType(Vtiger_Response::$EMIT_JSON);
	$response->setResult($result);
	$response->emit();
}
}
?>
