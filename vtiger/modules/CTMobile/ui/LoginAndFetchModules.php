<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
include_once dirname(__FILE__) . '/../api/ws/LoginAndFetchModules.php';

class CTMobile_UI_LoginAndFetchModules extends CTMobile_WS_LoginAndFetchModules {
	
	protected function cacheModules($modules) {
		$this->sessionSet("_MODULES", $modules);
	}
	
	function process(CTMobile_API_Request $request) {
		$wsResponse = parent::process($request);
		
		$response = false;
		if($wsResponse->hasError()) {
			$response = $wsResponse;
		} else {
			$wsResponseResult = $wsResponse->getResult();
			
			$modules = CTMobile_UI_ModuleModel::buildModelsFromResponse($wsResponseResult['modules']);
			$this->cacheModules($modules);

			$viewer = new CTMobile_UI_Viewer();
			$viewer->assign('_MODULES', $modules);

			$response = $viewer->process('generic/Home.tpl');
		}
		return $response;
	}

}
