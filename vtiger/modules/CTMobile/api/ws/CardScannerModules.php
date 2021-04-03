<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

class CTMobile_WS_CardScannerModules extends CTMobile_WS_Controller {

	function process(CTMobile_API_Request $request) {
		global $current_user, $adb, $site_URL; // Few core API assumes this variable availability
		$current_user = $this->getActiveUser();
		$userPrivModel = Users_Privileges_Model::getInstanceById($current_user->id);
		$presence = array('0', '2');
		$mode = $request->get('mode');
		if($mode == 'EmailsRelated'){
			$module = 'Emails';
			$moduleModel = Vtiger_Module_Model::getInstance($module);
			$relatedModules = $moduleModel->getEmailRelatedModules();
			$emailModules = array();
			foreach ($relatedModules as $key => $emodule) {
				$emailModules[] = array('moduleName'=>$emodule,'moduleLabel'=>vtranslate($emodule,$emodule));
			}
			$response = new CTMobile_API_Response();
			$response->setResult($emailModules);
			return $response;
		}else if($mode == 'vcard'){
			$vcardModules =  array();
			$allowedModules =  array('Leads','Contacts','Vendors');
			foreach($allowedModules as $modules){
				$moduleModel = Vtiger_Module_Model::getInstance($modules);
				if (($userPrivModel->isAdminUser() ||
						$userPrivModel->hasGlobalReadPermission() ||
						$userPrivModel->hasModulePermission($moduleModel->getId())) && in_array($moduleModel->get('presence'), $presence)) {
					$createAction = $userPrivModel->hasModuleActionPermission($moduleModel->getId(), 'CreateView');
					$vcardModules[] = array('moduleName'=>$modules,'moduleLabel'=> vtranslate($moduleModel->get('label'),$modules),'createAction'=>$createAction
								);
				}
			}
			$response = new CTMobile_API_Response();
			$response->setResult($vcardModules);
			return $response;
		}else if($mode == 'assetTracking'){
			$AssetQuery = $adb->pquery("SELECT * FROM ctmobile_asset_field",array());
			$numRows = $adb->num_rows($AssetQuery);
			$assetTrackingModules = array();
			for($i=0;$i<$numRows;$i++){
				$module = $adb->query_result($AssetQuery,$i,'module');
				$moduleModel = Vtiger_Module_Model::getInstance($module);
				if (($userPrivModel->isAdminUser() ||
						$userPrivModel->hasGlobalReadPermission() ||
						$userPrivModel->hasModulePermission($moduleModel->getId())) && in_array($moduleModel->get('presence'), $presence)) {
					$createAction = $userPrivModel->hasModuleActionPermission($moduleModel->getId(), 'CreateView');
					$assetTrackingModules[] = array('moduleName'=>$module,'moduleLabel'=> vtranslate($moduleModel->get('label'),$module),'createAction'=>$createAction
								);
				}
			}

			$response = new CTMobile_API_Response();
			$response->setResult($assetTrackingModules);
			return $response;

		}else{
			$cardScannerModules =  array();
			$allowedModules =  array('Leads','Potentials','Contacts','Accounts','Vendors');
			foreach($allowedModules as $modules){
				$moduleModel = Vtiger_Module_Model::getInstance($modules);
				if (($userPrivModel->isAdminUser() ||
						$userPrivModel->hasGlobalReadPermission() ||
						$userPrivModel->hasModulePermission($moduleModel->getId())) && in_array($moduleModel->get('presence'), $presence)) {
					$createAction = $userPrivModel->hasModuleActionPermission($moduleModel->getId(), 'CreateView');
					$cardScannerModules[] = array('moduleName'=>$modules,'moduleLabel'=> vtranslate($moduleModel->get('label'),$modules),'createAction'=>$createAction
								);
				}
			}
			$response = new CTMobile_API_Response();
			$response->setResult($cardScannerModules);
			return $response;
		}

	}
}