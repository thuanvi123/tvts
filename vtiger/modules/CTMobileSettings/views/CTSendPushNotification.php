<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

class CTMobileSettings_CTSendPushNotification_View extends Settings_Vtiger_Index_View {

    public function checkPermission(Vtiger_Request $request) {
        $license_data = CTMobileSettings_Module_Model::getLicenseData();
        if(strtolower($license_data['Plan']) === 'free'){
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED', 'Vtiger'));
        }else{
            return true;
        }
    }

    public function process(Vtiger_Request $request) {
        $adb = PearDatabase::getInstance();
        $module = $request->getModule();
        $viewer = $this->getViewer($request);
        $viewer->assign('MODULES', $module);

        $UsersModel = Users_Record_Model::getCurrentUserModel();
        $accesUsers = $UsersModel->getAccessibleUsers();
        $userArray =  array();
        foreach($accesUsers as $key => $value){
            $userArray[] = array('userid'=>$key,'username'=>$value);
        }
		$groups = $UsersModel->getAccessibleGroups();
        $groupsArray = array();
        foreach ($groups as $key => $value) {
            $groupsArray[] = array('userid'=>$key,'username'=>$value);
        }
        $viewer->assign('USER_MODEL', $userArray);
        $viewer->assign('GROUPS_MODEL', $groupsArray);
        echo $viewer->view('CTSendPushNotification.tpl',$module,true); 
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
            "modules.CTMobileSettings.resources.CTSendPushNotification",
        );

        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }
}
