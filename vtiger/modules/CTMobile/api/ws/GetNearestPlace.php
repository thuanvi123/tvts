<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
include_once dirname(__FILE__) . '/models/SearchFilter.php';

class CTMobile_WS_GetNearestPlace extends CTMobile_WS_Controller {
	
	function process(CTMobile_API_Request $request) {
		global $adb,$current_user;
		$current_user = $this->getActiveUser();
		$current_module_name = trim($request->get('module'));
		$current_latitude = trim($request->get('latitude'));
		$current_longitude = trim($request->get('longitude'));
		$radius = trim($request->get('radius'));
		$moduleWSId = CTMobile_WS_Utils::getEntityModuleWSId($current_module_name);
		if($current_module_name == 'Calendar'){
			$otherId[] = $moduleWSId;
			$otherId[] = CTMobile_WS_Utils::getEntityModuleWSId('Events');
			$moduleWSId = implode(",",$otherId);
		}
		$getModuleLatLongQuery = $adb->pquery("SELECT ct_address_lat_long.* from ct_address_lat_long
			INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = ct_address_lat_long.recordid
			WHERE vtiger_crmentity.deleted = 0 AND ct_address_lat_long.moduleid IN (".$moduleWSId.") AND latitude != '' AND longitude != ''", array());
		$countRows = $adb->num_rows($getModuleLatLongQuery);
		$countRows;
		for($i=0;$i<$countRows;$i++) {
			$recordid = trim($adb->query_result($getModuleLatLongQuery, $i, 'recordid'));
			$latitude = trim($adb->query_result($getModuleLatLongQuery, $i, 'latitude'));
			$longitude = trim($adb->query_result($getModuleLatLongQuery, $i, 'longitude'));
			
			$getCRMEntityData = $adb->pquery("SELECT * FROM vtiger_crmentity where deleted = 0 and crmid = ?", array($recordid));
			$seType	= $adb->query_result($getCRMEntityData, 0, 'setype');
			$label = trim($adb->query_result($getCRMEntityData, 0, 'label'));
			$default_charset = VTWS_PreserveGlobal::getGlobal('default_charset');
			$label = html_entity_decode($label, ENT_QUOTES, $default_charset);

			$WSId = CTMobile_WS_Utils::getEntityModuleWSId($seType);
			if($current_module_name == 'Calendar'){
				 $EventTaskQuery = $adb->pquery("SELECT * FROM  `vtiger_activity` WHERE activitytype = ? AND activityid = ?",array('Task',$recordid)); 
			    if($adb->num_rows($EventTaskQuery) > 0){
					$WSId = CTMobile_WS_Utils::getEntityModuleWSId('Calendar');
				}else{
					$WSId = CTMobile_WS_Utils::getEntityModuleWSId('Events');
				}
			}
			$distance = $this->distance($latitude, $longitude, $current_latitude, $current_longitude);
			$address = $this->getAddress($current_module_name,$recordid);
			if($distance < $radius) {
				if($latitude == ''){
					$latitude = '';
				}
				if($longitude == ''){
					$longitude = '';
				}
				if(Users_Privileges_Model::isPermitted($current_module_name, 'DetailView', $recordid)){
					$nearestPlaceData[] = array('recordid'=>$WSId.'x'.$recordid,'label'=>$label,'latitude'=>$latitude, 'longitude'=>$longitude,'address'=>$address);
				}
			}	
		}
		
		$response = new CTMobile_API_Response();
		if(count($nearestPlaceData) == 0) {
			$message = $this->CTTranslate('Nothing around here');
			$response->setResult(array('records'=>[],'code'=>404,'message'=>$message));
		} else {
			$response->setResult(array('records'=>$nearestPlaceData, 'message'=>''));
		}
		
		return $response;
	}
	
	function distance($lat1, $lon1, $lat2, $lon2) {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        return $miles;
    }

    function getAddress($module,$recordid){
    	global $adb,$current_user;
		$current_user = $this->getActiveUser();

    	$resultAddress = $adb->pquery("SELECT * FROM ctmobile_address_fields WHERE module = ?",array($module));
		$count = $adb->num_rows($resultAddress);

		$generator = new QueryGenerator($module, $current_user);
		$addressFields = array();
		$address = '';
		for($j=0;$j<$count;$j++){
		   $fields = $adb->query_result($resultAddress,$j,'fieldname');
		   $test = explode(":",$fields);
		   $addressFields[] = $test[1];
		}
		$addressFields[]='id';
		$generator->setFields($addressFields);
		$query = $generator->getQuery();
		$newquery = explode('WHERE',$query);
		if($newquery[1] != ""){
			$Aquery = $newquery[0]." WHERE vtiger_crmentity.crmid = '".$recordid."' AND ".$newquery[1];
		}else{
			$Aquery = $newquery[0]." WHERE vtiger_crmentity.crmid = '".$recordid."'";
		}
		$Aresult = $adb->pquery($Aquery,array());
		for($i=0;$i<$count;$i++){
		   $fields = $addressFields[$i];
		   $newField = $adb->query_result($Aresult,0, $fields);
		   if($newField != '') {
				if($i+1 == $count){
					$address .= $newField;
				}else{
					$address .= $newField.', ';
				}
		   }
		}
		return $address;
    }
}
