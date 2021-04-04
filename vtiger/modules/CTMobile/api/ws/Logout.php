<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

class CTMobile_WS_Logout extends CTMobile_WS_Controller {
	
	function process(CTMobile_API_Request $request) {
		global $adb, $current_user;
		$current_user = $this->getActiveUser();
		$userid = $current_user->id;
		// devicetoken will be blank of current user
		$query = "UPDATE ctmobile_userdevicetoken SET devicetoken = '', sessionid = '' WHERE userid = ?";
		$adb->pquery($query,array($userid));
		$response = new CTMobile_API_Response();
		session_regenerate_id(true);
		Vtiger_Session::destroy();
		
		//Track the logout History
		$moduleName = 'Users';
		$moduleModel = Users_Module_Model::getInstance($moduleName);
		$moduleModel->saveLogoutHistory();
		$message =  $this->CTTranslate('Logout Successfully');
		$result =  array('code' => 1,'message' => $message);
		$response->setResult($result);
		return $response;
	}
}
