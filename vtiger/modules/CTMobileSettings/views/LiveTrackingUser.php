<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

class CTMobileSettings_LiveTrackingUser_View extends Settings_Vtiger_Index_View {

    public function process(Vtiger_Request $request) {
        $adb = PearDatabase::getInstance();
        $module = $request->getModule();
        $viewer = $this->getViewer($request);
        $viewer->assign('MODULES', $module);
        $viewer->assign('LICENSE_DATA', CTMobileSettings_Module_Model::getLicenseData());
        $users = CTMobileSettings_Module_Model::getCTRouteUser();
        $viewer->assign('ROUTE_USER', $users);
        $Users =$adb->pquery("SELECT * FROM `vtiger_users` WHERE deleted = 0 AND status = ?",array('Active'));
		$Api = '';
		$userArray = array();
		for($i=0;$i<($adb->num_rows($Users));$i++){
			$id = $adb->query_result($Users,$i,'id');
			$name = $adb->query_result($Users,$i,'first_name').' '.$adb->query_result($Users,$i,'last_name');
			$userArray[] = array('userid'=>$id,'username'=>$name);
		}
		
		$selected = $adb->pquery("SELECT * FROM ctmobile_livetracking_users",array());
		$selectedUsers = array();
		for($i=0;$i<($adb->num_rows($selected));$i++){
			$selectedUsers[] = $adb->query_result($selected,$i,'userid');
		}
        $viewer->assign('USER_MODEL', $userArray);
        $viewer->assign('SELECTED_FIELDS', $selectedUsers);
        echo $viewer->view('CTMobileLiveTrackingUser.tpl',$module,true); 
    }   

   

    /**
     * Function to get the list of Script models to be included
     * @param Vtiger_Request $request
     * @return <Array> - List of Vtiger_JsScript_Model instances
     */
    function getHeaderScripts(Vtiger_Request $request) {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName = $request->getModule();

        $jsFileNames = array(
            "modules.CTMobileSettings.resources.OtherSettings",
            "modules.CTMobileSettings.resources.LiveTrackingUser",
        );

        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }
}
