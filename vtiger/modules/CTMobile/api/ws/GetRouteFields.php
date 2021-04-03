<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

class CTMobile_WS_GetRouteFields extends CTMobile_WS_Controller {

	function process(CTMobile_API_Request $request) {
		global $adb,$current_user;
		$current_user = $this->getActiveUser();
		$default_charset = VTWS_PreserveGlobal::getGlobal('default_charset');
		$moduleName = 'CTRoutePlanning';
		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
		$fieldModels = $moduleModel->getFields();
		$routeFields = array();

		$refersTo = array();
		$ReferenceList = $fieldModels['ctroute_realtedto']->getReferenceList();
		foreach ($ReferenceList as $key => $modules) {
			$ModuleURL = CTMobile_WS_Utils::getModuleURL($modules);
			$refersTo[] = array('value'=>$modules,'label'=>vtranslate($modules,$modules),'ModuleURL'=>$ModuleURL);
		}

		$usersWSId = CTMobile_WS_Utils::getEntityModuleWSId('Users');
		$defaultValue = array("value"=>$usersWSId.'x'.$current_user->id,"label"=>html_entity_decode($current_user->first_name.' '.$current_user->last_name, ENT_QUOTES, $default_charset));

		$response = new CTMobile_API_Response();
		$result = array('module'=>$moduleName,'modulesList'=>$refersTo,'current_user'=>$defaultValue);
		$response->setResult($result);
		return $response;

	}
}