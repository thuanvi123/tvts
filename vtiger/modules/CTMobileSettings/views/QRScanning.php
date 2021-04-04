<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

class CTMobileSettings_QRScanning_View extends Settings_Vtiger_Index_View {

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
        $this->renderSettingsUI($request);
    }   

    function renderSettingsUI(Vtiger_Request $request) {
        $adb = PearDatabase::getInstance();
        $module = $request->getModule();
        $viewer = $this->getViewer($request);
        $allModules = Vtiger_Module_Model::getSearchableModules();

        $viewer->assign('ALL_MODULE', array_keys($allModules));
        
        // Get search modules from database
        $rsSearch=$adb->pquery("SELECT * FROM `ctmobile_address_modules` ORDER BY sequence",array());
        $searchModules=array();
        if($adb->num_rows($rsSearch)>0) {
            while($row=$adb->fetch_array($rsSearch)) {
				$searchModules[$row['module']]=$row['sequence'];
            }
        }
        $viewer->assign('SEARCH_MODULES', $searchModules);
        echo $viewer->view('QRScanning.tpl',$module,true);
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
            "modules.CTMobileSettings.resources.QRScanning",
        );

        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }
}
