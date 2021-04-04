<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

class CTMobileSettings_RouteAnalytics_View extends Settings_Vtiger_Index_View {

    public function checkPermission(Vtiger_Request $request) {
        $license_data = CTMobileSettings_Module_Model::getLicenseData();
        if(strtolower($license_data['Plan']) === 'free'){
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED', 'Vtiger'));
        }else{
           return true;
        }
    }

    function __construct() {
        parent::__construct();
    }

    public function preProcess(Vtiger_Request $request) {
        parent::preProcess($request);        
    }

    public function process(Vtiger_Request $request) {
        $module = $request->getModule();
        $adb = PearDatabase::getInstance();
        $mode = $request->getMode();
        $viewer = $this->getViewer($request);

        $UsersModel = Users_Record_Model::getCurrentUserModel();
        $users = $UsersModel->getAccessibleUsers();
        $userArray =  array();
        foreach($users as $key => $value){
            $userArray[] = array('userid'=>$key,'username'=>decode_html($value));
        }
        $groups = $UsersModel->getAccessibleGroups();
        $groupsArray = array();
        foreach ($groups as $key => $value) {
            $groupsArray[] = array('userid'=>$key,'username'=>$value);
        }
        $viewer->assign('USER_MODEL', $userArray);
        $viewer->assign('GROUPS_MODEL', $groupsArray);

        /*code by sapna*/
        $viewer->assign('CUSTOM_MODULE_LIST', $this->getModuleList());
        /*code by sapna end*/

        $searchApi=$adb->pquery("SELECT * FROM `ctmobile_api_settings`",array());
        $Api = '';
        if($adb->num_rows($searchApi)>0) {
            $Api = $adb->query_result($searchApi,0,'api_key');
        }
        $viewer->assign('API_KEY', $Api);
        $viewer->assign('CURRENT_USER', $UsersModel);
        
        echo $viewer->view('RouteAnalytics.tpl',$module,true);
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
            "modules.CTMobileSettings.resources.RouteAnalytics",
        );

        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }

     //display module dropdown in list tab  
    function getModuleList(){
        $arrModule = array("all" => vtranslate("All Modules"));
        $array = array('Leads','Contacts','Accounts','HelpDesk','Quotes','Invoice','SalesOrder','PurchaseOrder');
        foreach($array as $module){
            $arrModule[$module] = vtranslate($module,$module);
        }
        return $arrModule;
    }
}
