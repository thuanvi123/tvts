<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

class CTMobileSettings_LicenseDetail_View extends Settings_Vtiger_Index_View {
    
    public function process(Vtiger_Request $request){
		global $adb;
        $viewer = $this->getViewer($request);
        $qualifiedName = $request->getModule(false);
		$getLicenseQuery=$adb->pquery("SELECT * FROM ctmobile_license_settings");
		$numOfLicense = $adb->num_rows($getLicenseQuery);
        if($numOfLicense > 0){
			$license_key = $adb->query_result($getLicenseQuery,0,'license_key');
		}else{
			$license_key = '';
		}
        $viewer->assign('LICENCE_KEY',$license_key);
        $viewer->assign('MODULE','CTMobileSettings');
        $viewer->view('CTMobileLicenseDetail.tpl',$qualifiedName);
    }
		
   function getHeaderScripts(Vtiger_Request $request) {
		$headerScriptInstances = parent::getHeaderScripts($request);
		$moduleName = $request->getModule();

		$jsFileNames = array(
			"modules.$moduleName.resources.OtherSettings",
		);

		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
    }
}
    
