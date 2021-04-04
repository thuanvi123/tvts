<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
include_once dirname(__FILE__) . '/models/Alert.php';
include_once dirname(__FILE__) . '/models/SearchFilter.php';
include_once dirname(__FILE__) . '/models/Paging.php';

class CTMobile_WS_SearchRecord extends CTMobile_WS_Controller {
	
	
	function getSearchFilterModel($module, $search) {
		return CTMobile_WS_SearchFilterModel::modelWithCriterias($module, Zend_JSON::decode($search));
	}
	
	function getPagingModel(CTMobile_API_Request $request) {
		$page = $request->get('page', 0);
		return CTMobile_WS_PagingModel::modelWithPageStart($page);
	}
	
	function process(CTMobile_API_Request $request) {
		global $adb, $site_URL;
		
		$moduleName = trim($request->get('module'));
		$searchString = trim($request->get('searchString'));
		$searchQuery = $adb->pquery("SELECT * from vtiger_crmentity where deleted = 0 and setype = ? and label LIKE '%$searchString%'", array($moduleName));
		$countSearch = $adb->num_rows($searchQuery);
		
		for($i=0;$i<$countSearch;$i++) {
			$crmid = $adb->query_result($searchQuery, $i, 'crmid');
			$getWsModuleId = $adb->pquery("SELECT id from vtiger_ws_entity where name = ?", array($moduleName));
			$moduleId = $adb->query_result($getWsModuleId, 0, 'id');
			$label = trim($adb->query_result($searchQuery, $i, 'label'));
			$setype = $adb->query_result($searchQuery, $i, 'setype');
			$createdtime = $adb->query_result($searchQuery, $i, 'createdtime');
			$modifiedtime = $adb->query_result($searchQuery, $i, 'modifiedtime');
			
			$searchData[$setype][] = array("id"=>$moduleId.'x'.$crmid, "label"=>$label, 'setype'=>$setype, 'createdtime'=>$createdtime, 'modifiedtime'=>$modifiedtime);
		}
		
		$data = array('records' => $searchData);
		$response = new CTMobile_API_Response();
		$response->setResult($searchData);
		
		if ($countSearch == 0) {
			$message = $this->CTTranslate('No records found');
			$response->setResult(array('code'=>404,'message'=>$message));
		}
		
		return $response;
	}
}
