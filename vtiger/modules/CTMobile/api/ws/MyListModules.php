<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

class CTMobile_WS_MyListModules extends CTMobile_WS_Controller {
	
	function process(CTMobile_API_Request $request) {
		$current_user = $this->getActiveUser();
		$listresult = vtws_listtypes(null,$current_user);
		$menuModelsList = Vtiger_Menu_Model::getAll(true);
		$presence = array('0', '2');
		$modules = array();
		$userPrivModel = Users_Privileges_Model::getInstanceById($current_user->id);
		$restrictedModule = array('CTMobile','Rss','Portal','RecycleBin','ExtensionStore','CTPushNotification','EmailTemplates','CTAttendance','CTTimeTracker','CTRoutePlanning','CTTimeControl','CTRouteAttendance','CTUserFilterView','CTMessageTemplate','CTMobileSettings');
		foreach($menuModelsList as $moduleName => $moduleModel){
			if (empty($moduleModel))
					continue;
			if (in_array($moduleModel->get('name'),$restrictedModule))
					continue;
			if (($userPrivModel->isAdminUser() ||
						$userPrivModel->hasGlobalReadPermission() ||
						$userPrivModel->hasModulePermission($moduleModel->getId())) && in_array($moduleModel->get('presence'), $presence)) {
				$modules[] = array('value'=>trim($moduleName),'label'=>vtranslate($moduleModel->get('label'),$moduleName));
			}

		}
		$response = new CTMobile_API_Response();
		$response->setResult($modules);
		return $response;
	}
}
