<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

class CTMobileSettings_Uninstall_Action extends Vtiger_Save_Action {
    public function process(Vtiger_Request $request) {
        global $adb;
        global $site_URL;
        $Vtiger_Utils_Log = true;
		include_once('vtlib/Vtiger/Module.php');

        $array = array('CTAttendance','CTMessageTemplate','CTMobile','CTPushNotification','CTUserFilterView','CTMobileSettings','CTRoutePlanning','CTRouteAttendance','CTTimeTracker','CTTimeControl');

        foreach ($array as $key => $value) {
            $module = Vtiger_Module::getInstance($value);
    		if($module) {
    		    $module->delete();
    		}
        }
        $getLicenseQuery = $adb->pquery("SELECT * FROM ctmobile_license_settings",array());
        $numOfLicenseCount = $adb->num_rows($getLicenseQuery);
		if($numOfLicenseCount > 0){
			$license_key=$adb->query_result($getLicenseQuery,0,'license_key');
			$domain=$adb->query_result($getLicenseQuery,0,'domain');
			$url = CTMobileSettings_Module_Model::$CTMOBILE_CHECKLICENSE_URL;
			$ch = curl_init($url);
			$data = array( "license_key"=>$license_key,"domain"=>$domain,"action"=>"deactivate");
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			// Send request.
			$result = curl_exec($ch);
			curl_close($ch);
			if($result == 'Deactivated'){
				$deleteLicense = $adb->pquery("DELETE FROM ctmobile_license_settings",array());
			}
		}
		
		$query ="DELETE FROM vtiger_settings_field WHERE name = 'CTMobileSettings'";
        $results = $adb->pquery($query);
        if ($results) {
            $result = $site_URL;
        }
        
        $title = 'logout';
		$message = 'logout';
		CTMobileSettings_Module_Model::sendpushnotificationAll($message,$title);
		$adb->pquery('DROP TABLE ctmobile_api_settings',array());
        $adb->pquery('DROP TABLE ctmobile_license_settings',array());
        $adb->pquery('DROP TABLE ctmobile_livetracking_users',array());
        $adb->pquery('DROP TABLE ctmobile_userderoute',array());
        $adb->pquery('DROP TABLE ctmobile_userdevicetoken',array());
        $adb->pquery('DROP TABLE ct_address_lat_long',array());
		$adb->pquery('DROP TABLE cte_modules',array());
		$adb->pquery('DROP TABLE ctmobile_access_users',array());
		$adb->pquery('DROP TABLE ctmobile_address_fields',array());
		$adb->pquery('DROP TABLE ctmobile_address_modules',array());
		$adb->pquery('DROP TABLE ctmobile_record_shortcut',array());
		$adb->pquery('DROP TABLE ctmobile_filter_shortcut',array());
		$adb->pquery('DROP TABLE ctmobile_session_expire',array());
		$adb->pquery('DROP TABLE ctmobile_language_keyword',array());
		$adb->pquery('DROP TABLE ctmobile_language_section',array());
		$adb->pquery('DROP TABLE ctmobile_timetracking_modules',array());
		$adb->pquery('DROP TABLE ctmobile_routestatus',array());
		$adb->pquery('DROP TABLE ctmobile_routegeneralsettings',array());
		$adb->pquery('DROP TABLE ctmobile_signature_fields',array());
		
        $array = array('CTAttendance','CTMessageTemplate','CTMobile','CTPushNotification','CTUserFilterView','CTRoutePlanning','CTRouteAttendance','CTTimeTracker','CTTimeControl');
		foreach ($array as $key => $value) {
			$path = $root_directory.'modules/'.$value;
			self::deleteAll($path);
			$path = $root_directory.'layouts/v7/modules/'.$value;
			self::deleteAll($path); 
		}
		
		
        $query ="DELETE FROM vtiger_settings_field WHERE name = 'CTMobileSettings'";
        $results = $adb->pquery($query);
        if ($results) {
            $result = $site_URL;
        }
		
		$array = array('CTMobileSettings');
		foreach ($array as $key => $value) {
			$path = $root_directory.'modules/'.$value;
			self::deleteAll($path);
			$path = $root_directory.'layouts/v7/modules/'.$value;
			self::deleteAll($path); 
		}
		
		
		
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array($result));
        $response->emit();
    }
	
	static function deleteAll($str) {
		//It it's a file.
		if (is_file($str)) {
			//Attempt to delete it.
			return unlink($str);
		}
		//If it's a directory.
		elseif (is_dir($str)) {
			//Get a list of the files in this directory.
			$scan = glob(rtrim($str,'/').'/*');
			//Loop through the list of files.
			foreach($scan as $index=>$path) {
				//Call our recursive function.
				self::deleteAll($path); 
			}
			//Remove the directory itself.
			return @rmdir($str);
		}
	} 
	
	
}

?>
