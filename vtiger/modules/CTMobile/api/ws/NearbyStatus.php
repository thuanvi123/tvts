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

class CTMobile_WS_NearbyStatus extends CTMobile_WS_Controller {
	function process(CTMobile_API_Request $request) {
		global $adb, $site_URL;
		$query = "SELECT * FROM vtiger_cron_task WHERE module = 'CTUserFilterView' AND status='1'";
		$result = $adb->pquery($query,array());
		if($adb->num_rows($result) > 0){
			$response = new CTMobile_API_Response();
			$message = vtranslate('CTLatLongScheduler is Enabled FROM CRM','CTMobile');
			$response->setResult(array('code'=>1,'message'=>$message));
			return $response; 
		}else{
			$response = new CTMobile_API_Response();
			$message = vtranslate('Please Enable CTLatLongScheduler FROM Scheduler','CTMobile');
			$response->setResult(array('code'=>0,'message'=>$message));
			return $response; 
		}
	}
}
