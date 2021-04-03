<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

include_once dirname(__FILE__) . '/../api/ws/FetchRecordWithGrouping.php';

class CTMobile_UI_FetchRecordWithGrouping extends CTMobile_WS_FetchRecordWithGrouping {
	
	function cachedModuleLookupWithRecordId($recordId) {
		$recordIdComponents = explode('x', $recordId);
		$modules = $this->sessionGet('_MODULES'); // Should be available post login
		foreach($modules as $module) {
			if ($module->id() == $recordIdComponents[0]) { return $module; };
		}
		return false;
	}
	
	function process(CTMobile_API_Request $request) {
		$wsResponse = parent::process($request);
		
		$response = false;
		if($wsResponse->hasError()) {
			$response = $wsResponse;
		} else {
			$wsResponseResult = $wsResponse->getResult();

			$module = $this->cachedModuleLookupWithRecordId($wsResponseResult['record']['id']);
			$record = CTMobile_UI_ModuleRecordModel::buildModelFromResponse($wsResponseResult['record']);
			$record->setId($wsResponseResult['record']['id']);
			
			$viewer = new CTMobile_UI_Viewer();
			$viewer->assign('_MODULE', $module);
			$viewer->assign('_RECORD', $record);

			$response = $viewer->process('generic/Detail.tpl');
		}
		return $response;
	}

}
