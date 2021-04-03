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

class CTMobile_WS_UpdateLocation extends CTMobile_WS_Controller {
	
	
	function getSearchFilterModel($module, $search) {
		return CTMobile_WS_SearchFilterModel::modelWithCriterias($module, Zend_JSON::decode($search));
	}
	
	function getPagingModel(CTMobile_API_Request $request) {
		$page = $request->get('page', 0);
		return CTMobile_WS_PagingModel::modelWithPageStart($page);
	}
	
	function process(CTMobile_API_Request $request) {
		global $adb;
		
		$userId = trim($request->get('userid'));
		if($userId == ''){
			$message = $this->CTTranslate('Userid cannot be blank');
			throw new WebServiceException(404,$message);
		}
		$latitude = trim($request->get('latitude'));
		$longitude = trim($request->get('longitude'));
		$userId = substr($userId, stripos($userId, 'x')+1);
		$date_var = date("Y-m-d H:i:s");
		$createdtime = $adb->formatDate($date_var, true);
		$selectQuery = $adb->pquery("SELECT * FROM ctmobile_userdevicetoken where userid = ?", array($userId));								
		$selectQueryCount = $adb->num_rows($selectQuery);
		if($latitude!='' && $longitude!=''){
			if($selectQueryCount > 0) {
			$query = $adb->pquery("UPDATE ctmobile_userdevicetoken SET latitude = ?, longitude = ? WHERE userid = ?", array($latitude, $longitude, $userId));
			$query = $adb->pquery("INSERT INTO ctmobile_userderoute (userid, latitude, longitude, createdtime) VALUES (?,?,?,?)", array($userId, $latitude, $longitude, $createdtime));
			} else {
				$query = $adb->pquery("INSERT INTO ctmobile_userdevicetoken (userid, latitude, longitude) VALUES (?,?,?)", array($userId, $latitude, $longitude));
				
				$query = $adb->pquery("INSERT INTO ctmobile_userderoute (userid, latitude, longitude, createdtime) VALUES (?,?,?,?)", array($userId, $latitude, $longitude, $createdtime));
			}
			
			if($query) {
				$message = $this->CTTranslate('User Location Updated Successfully');
				$userData[] = array('code'=>1,'message'=>$message);
			} else {
				$message = $this->CTTranslate('User Location not Updated Successfully');
				$userData[] = array('code'=>0,'message'=>$message);
			}
			
		}else{
			$message = $this->CTTranslate('User Location not Updated Successfully');
			$userData[] = array('code'=>0,'message'=>$message);
		}
		
		$response = new CTMobile_API_Response();
		$response->setResult($userData);
		return $response;
	}
}

?>
