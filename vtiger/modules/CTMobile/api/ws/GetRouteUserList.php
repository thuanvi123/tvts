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

class CTMobile_WS_GetRouteUserList extends CTMobile_WS_Controller {
	
	function getSearchFilterModel($module, $search) {
		return CTMobile_WS_SearchFilterModel::modelWithCriterias($module, Zend_JSON::decode($search));
	}
	
	function getPagingModel(CTMobile_API_Request $request) {
		$page = $request->get('page', 0);
		return CTMobile_WS_PagingModel::modelWithPageStart($page);
	}
	
	function process(CTMobile_API_Request $request) {
		$default_charset = VTWS_PreserveGlobal::getGlobal('default_charset');
		global $adb, $current_user;
		$current_user = $this->getActiveUser();
		$userid = $current_user->id;
		$roleid = $current_user->roleid;
		
		require_once('include/utils/UserInfoUtil.php');
		$now_rs_users = getRoleAndSubordinateUsers($roleid);
		foreach ($now_rs_users as $now_rs_userid => $now_rs_username) {
			
			$userRecordModel = Vtiger_Record_Model::getInstanceById($now_rs_userid, 'Users');
			$first_name = trim($userRecordModel->get('first_name'));
			$last_name = trim($userRecordModel->get('last_name'));
			$userName = html_entity_decode($first_name." ".$last_name, ENT_QUOTES, $default_charset);
			$userData[] =  array('userid'=>$now_rs_userid, 'username'=>$userName);		
		}
		
		if(count($userData) == 0) {
			$message = $this->CTTranslate('No records found');
			$response->setResult(array('code'=>404,'message'=>$message));
		}
		$response = new CTMobile_API_Response();
		$response->setResult($userData);
		return $response;				
	}
}

?>
