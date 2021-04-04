<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
include_once dirname(__FILE__) . '/FetchRecordWithGrouping.php';

include_once 'include/Webservices/Create.php';
include_once 'include/Webservices/Update.php';

class CTMobile_WS_SaveShortcut extends CTMobile_WS_FetchRecordWithGrouping {

	function process(CTMobile_API_Request $request) {
		global $adb,$current_user; // Required for vtws_update API
		$current_user = $this->getActiveUser();
		$module = trim($request->get('module'));
		$shortcutType = trim($request->get('shortcutType'));
		if($shortcutType == ''){
			$message = $this->CTTranslate('Required fields not found');
			throw new WebServiceException(404,$message);
		}
		if($module == ''){
			$message = $this->CTTranslate('Required fields not found');
			throw new WebServiceException(404,$message);
		}
		if($shortcutType == 'filter'){
			$filterid = trim($request->get('filterid'));
			$shortcutname = trim($request->get('shortcutname'));
			if(is_array($fieldname)){
				$fieldname = implode('::',$request->get('fieldname'));
			}else{
				$fieldname = implode('::',Zend_Json::decode($request->get('fieldname')));
			}
			if(is_array($search_value)){
				$search_value = implode('::',$request->get('search_value'));
			}else{
				$search_value = implode('::',Zend_Json::decode($request->get('search_value')));
			}
			$userid = trim($request->get('userid'));
			$module = trim($request->get('module'));
			$createdTime = date('Y-m-d H:i:s');

			$result = $adb->pquery('INSERT INTO ctmobile_filter_shortcut(shortcutname,filterid,fieldname,search_value,userid,module,createdtime) VALUES(?,?,?,?,?,?,?)',array($shortcutname,$filterid,$fieldname,$search_value,$userid,$module,$createdTime));

		}
		if($shortcutType == 'record'){
			$record = explode('x',trim($request->get('recordid')));
			$recordid = $record[1];
			$shortcutname = trim($request->get('shortcutname'));
			$userid = trim($request->get('userid'));
			$module = trim($request->get('module'));
			$createdTime = date('Y-m-d H:i:s');

			$result = $adb->pquery('INSERT INTO ctmobile_record_shortcut(shortcutname,recordid,userid,module,createdtime) VALUES(?,?,?,?,?)',array($shortcutname,$recordid,$userid,$module,$createdTime));

		}
		if($result){
			$message = $this->CTTranslate('Shortcut details saved successfully');
			$code = 1;
			$response = new CTMobile_API_Response();
			$response->setResult(array("code"=>$code,"message"=>$message));
			return $response;
		}else{
			$code = 0;
			$message = $this->CTTranslate('Shortcut details not saved');
			$response = new CTMobile_API_Response();
			$response->setError($code,$message);
			return $response;
		}
		
	}
}