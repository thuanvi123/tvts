<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
include_once dirname(__FILE__) . '/../api/ws/ListModuleRecords.php';
include_once dirname(__FILE__) . '/models/SearchFilter.php';

class CTMobile_UI_ListModuleRecords extends CTMobile_WS_ListModuleRecords {
	
	function cachedModule($moduleName) {
		$modules = $this->sessionGet('_MODULES'); // Should be available post login
		foreach($modules as $module) {
			if ($module->name() == $moduleName) return $module;
		}
		return false;
	}
	
	function getPagingModel(CTMobile_API_Request $request) {
		$pagingModel =  CTMobile_WS_PagingModel::modelWithPageStart($request->get('page'));
		$pagingModel->setLimit(CTMobile::config('Navigation.Limit', 100));
		return $pagingModel;
	}
	
	/** For search capability */
	function cachedSearchFields($module) {
		$cachekey = "_MODULE.{$module}.SEARCHFIELDS";
		return $this->sessionGet($cachekey, false);
	}
	
	function getSearchFilterModel($module, $search) {
		$searchFilter = false;
		if (!empty($search)) {
			$criterias = array('search' => $search, 'fieldnames' => $this->cachedSearchFields($module));
			$searchFilter = CTMobile_UI_SearchFilterModel::modelWithCriterias($module, $criterias);
			return $searchFilter;
		}
		return $searchFilter;
	}
	/** END */
	
	function process(CTMobile_API_Request $request) {
		$wsResponse = parent::process($request);

		$response = false;
		if($wsResponse->hasError()) {
			$response = $wsResponse;
		} else {
			$wsResponseResult = $wsResponse->getResult();

			$viewer = new CTMobile_UI_Viewer();
			$viewer->assign('_MODULE', $this->cachedModule($wsResponseResult['module']) );
			$viewer->assign('_RECORDS', CTMobile_UI_ModuleRecordModel::buildModelsFromResponse($wsResponseResult['records']) );
			$viewer->assign('_MODE', $request->get('mode'));
			$viewer->assign('_PAGER', $this->getPagingModel($request));
			$viewer->assign('_SEARCH', $request->get('search'));

			$response = $viewer->process('generic/List.tpl');
		}
		return $response;
	}

}
