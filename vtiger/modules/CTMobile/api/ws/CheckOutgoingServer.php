<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

class CTMobile_WS_CheckOutgoingServer extends CTMobile_WS_Controller {
	
	function process(CTMobile_API_Request $request) {
		global $adb, $current_user;
		$current_user = $this->getActiveUser();
		$response = new CTMobile_API_Response();
		$query = "SELECT id FROM vtiger_systems WHERE server_type='email'";
		$result = $adb->pquery($query,array());
		if($adb->num_rows($result) > 0){
			$EmailModuleModel = Vtiger_Module_Model::getInstance('Emails');
			if(!in_array($EmailModuleModel->get('presence'), array(0,2))){
				$message = vtranslate('Emails','Emails').' '.$this->CTTranslate('Module is disabled');
				$result =  array('code' => 0,'message' => $message);
			}else{
				$currentUserPriviligesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
				if(!$currentUserPriviligesModel->hasModulePermission($EmailModuleModel->getId())) {
					$message = vtranslate('LBL_NOT_ACCESSIBLE');
					$result =  array('code' => 0,'message' => $message);
				}else{
					$message = $this->CTTranslate('Outgoing server is Enabled');
					$result =  array('code' => 1,'message' => $message);
				}
			}
		}else{
			$message = $this->CTTranslate('Outgoing server is not Enabled');
			$result =  array('code' => 0,'message' => $message);
		}
		$response->setResult($result);
		return $response;
	}
}
