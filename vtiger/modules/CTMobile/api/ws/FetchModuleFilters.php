<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
class CTMobile_WS_FetchModuleFilters extends CTMobile_WS_Controller {
	
	function process(CTMobile_API_Request $request) {
		$response = new CTMobile_API_Response();

		$module = trim($request->get('module'));
		if($module == 'Events'){
			$module = 'Calendar';
		}
		global $adb,$current_user;
		$current_user = $this->getActiveUser();
		
		$results = array();
		$filters = array();
		$AllFilters = CustomView_Record_Model::getAll($module);

		$customView = new CustomView();
		$defaultcvid = $customView->getViewId($module);

		foreach ($AllFilters as $key => $filter) {
			$cvid = $filter->get('cvid');
			$viewname = vtranslate($filter->get('viewname'),$module);
			$setdefault = $filter->get('setdefault');
			$setmetrics = $filter->get('setmetrics');
			$moduleName = $filter->get('entitytype');
			$status = $filter->get('status');
			$userid = $filter->get('userid');
			$userRecordModel = Users_Record_Model::getInstanceById($userid,'Users');
			$userName = $userRecordModel->get('first_name').' '.$userRecordModel->get('last_name');
			if($defaultcvid == $cvid){
				$isDefault = 1;
			}else{
				$isDefault = 0;
			}
			$filters[] = array('cvid'=>$cvid,'viewname'=>$viewname,'setdefault'=>$setdefault,'setmetrics'=>$setmetrics,'moduleName'=>$moduleName,'userName'=>$userName,'isDefault'=>$isDefault);
		}

		$results = $filters;
		if(count($filters) == 0){
			$results['code'] = 404;
			$results['message'] = $this->CTTranslate('No records found');
			$response->setResult($results);
		}else{
			$response->setResult(array('filters'=>$results));
		}

		return $response;
	}
}
