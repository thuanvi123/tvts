<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

class CTMobileSettings_Settings_View extends Settings_Vtiger_Index_View {

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
        if($mode == 'GoogleMap'){
            $this->settingsGoogleMap($request);
        }else{
            $this->settingsOpenStreetMap($request);
        }
    } 

    function settingsGoogleMap(Vtiger_Request $request) {
        $adb = PearDatabase::getInstance();
        $module = $request->getModule();
        $viewer = $this->getViewer($request);
        $allModules = Vtiger_Module_Model::getSearchableModules();
        $AddressModules =  array("Contacts","Leads","Accounts","Calendar","Events");
        foreach($allModules as $key => $value){
            if(in_array($key,$AddressModules)){
                $allModule[$key] = $value;
            }
        }
        $viewer->assign('ALL_MODULE', array_keys($allModule));
        
        //get Google Api key
        $searchApi=$adb->pquery("SELECT * FROM `ctmobile_api_settings`",array());
        $Api = '';
        if($adb->num_rows($searchApi)>0) {
            $Api = $adb->query_result($searchApi,0,'api_key');
        }
        $viewer->assign('API_KEY', $Api);
        // Get search modules from database
        $rsSearch=$adb->pquery("SELECT * FROM `ctmobile_address_modules` ORDER BY sequence",array());
        $searchModules=array();
        if($adb->num_rows($rsSearch)>0) {
            while($row=$adb->fetch_array($rsSearch)) {
                $AddressModules =  array("Contacts","Leads","Accounts","Calendar","Events");
                if(in_array($row['module'],$AddressModules)){
                    $searchModules[$row['module']]=$row['sequence'];
                }  
            }
        }
        $viewer->assign('SEARCH_MODULES', $searchModules);
        echo $viewer->view('GoogleMapSettings.tpl',$module,true);
    }

    function settingsOpenStreetMap(Vtiger_Request $request) {
        $adb = PearDatabase::getInstance();
        $module = $request->getModule();
        $viewer = $this->getViewer($request);
        $allModules = Vtiger_Module_Model::getSearchableModules();
        $AddressModules =  array("Contacts","Leads","Accounts","Calendar","Events");
        foreach($allModules as $key => $value){
            if(in_array($key,$AddressModules)){
                $allModule[$key] = $value;
            }
        }
        $viewer->assign('ALL_MODULE', array_keys($allModule));
        
        //get Google Api key
        $searchApi=$adb->pquery("SELECT * FROM `ctmobile_api_settings`",array());
        $Api = '';
        if($adb->num_rows($searchApi)>0) {
            $Api = $adb->query_result($searchApi,0,'api_key');
        }
        $viewer->assign('API_KEY', $Api);
        // Get search modules from database
        $rsSearch=$adb->pquery("SELECT * FROM `ctmobile_address_modules` ORDER BY sequence",array());
        $searchModules=array();
        if($adb->num_rows($rsSearch)>0) {
            while($row=$adb->fetch_array($rsSearch)) {
                $AddressModules =  array("Contacts","Leads","Accounts","Calendar","Events");
                if(in_array($row['module'],$AddressModules)){
                    $searchModules[$row['module']]=$row['sequence'];
                }  
            }
        }
        $viewer->assign('SEARCH_MODULES', $searchModules);
        echo $viewer->view('OpenMapSettings.tpl',$module,true);
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
            "modules.CTMobileSettings.resources.Settings",
        );

        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }
}
