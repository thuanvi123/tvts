<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

class CTMobileSettings_PremiumFeatureManagement_View extends Settings_Vtiger_Index_View {

    function __construct() {
        parent::__construct();
    }

    public function checkPermission(Vtiger_Request $request) {
        $license_data = CTMobileSettings_Module_Model::getLicenseData();
        if(strtolower($license_data['Plan']) === 'free'){
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED', 'Vtiger'));
        }else{
            $currentUserModel = Users_Record_Model::getCurrentUserModel();
            if(!$currentUserModel->isAdminUser()) {
                throw new AppException(vtranslate('LBL_PERMISSION_DENIED', 'Vtiger'));
            }
        }
    }

    public function preProcess(Vtiger_Request $request) {
        parent::preProcess($request);        
    }

    public function process(Vtiger_Request $request) {
        $module = $request->getModule();
        $adb = PearDatabase::getInstance();
        $mode = $request->getMode();
        $selectedPremiumFeature = array();
        $result = $adb->pquery("SELECT * FROM ctmobile_premium_feature",array());
        for ($i=0; $i < $adb->num_rows($result); $i++) { 
            $feature_name = $adb->query_result($result,$i,'feature_name');
            $feature_enabled = $adb->query_result($result,$i,'feature_enabled');
            $selectedPremiumFeature[$feature_name] = $feature_enabled;
        }
        
        $viewer = $this->getViewer($request);
        $viewer->assign('SELECTED_FEATURE', $selectedPremiumFeature);

        echo $viewer->view('CTMobilePremiumManagement.tpl',$module,true);
    }

    public function getAssignedRecordModules(){
        global $adb;
        $getAssignedRecordModulesQuery = $adb->pquery("SELECT * FROM ctmobile_notification_module_settings INNER JOIN ctmobile_notification_settings ON ctmobile_notification_settings.notification_id =  ctmobile_notification_module_settings.notification_id WHERE ctmobile_notification_settings.notification_type = 'record_assigned'",array());
        $noofTAssignedRecordModulesRows = $adb->num_rows($getAssignedRecordModulesQuery);
        $moduleList = array();
        for ($i=0; $i <$noofTAssignedRecordModulesRows ; $i++) {
            $moduleName = $adb->query_result($getAssignedRecordModulesQuery,$i,'modulename');
            $moduleList[] = $moduleName;
        }
        return $moduleList;
    }

    public function getAssignedRecordCommentsModules(){
        global $adb;
        $getAssignedRecordCommentQuery = $adb->pquery("SELECT * FROM ctmobile_notification_module_settings INNER JOIN ctmobile_notification_settings ON ctmobile_notification_settings.notification_id =  ctmobile_notification_module_settings.notification_id WHERE ctmobile_notification_settings.notification_type = 'comment_assigned'",array());
        $noofTAssignedRecordCommentRows = $adb->num_rows($getAssignedRecordCommentQuery);
        $moduleList = array();
        for ($i=0; $i <$noofTAssignedRecordCommentRows ; $i++) {
            $moduleName = $adb->query_result($getAssignedRecordCommentQuery,$i,'modulename');
            $moduleList[] = $moduleName;
        }
        return $moduleList;
    }

    public function getFollowRecordModules(){
        global $adb;
        $getAssignedRecordCommentQuery = $adb->pquery("SELECT * FROM ctmobile_notification_module_settings INNER JOIN ctmobile_notification_settings ON ctmobile_notification_settings.notification_id =  ctmobile_notification_module_settings.notification_id WHERE ctmobile_notification_settings.notification_type = 'follow_record'",array());
        $noofTAssignedRecordCommentRows = $adb->num_rows($getAssignedRecordCommentQuery);
        $moduleList = array();
        for ($i=0; $i <$noofTAssignedRecordCommentRows ; $i++) {
            $moduleName = $adb->query_result($getAssignedRecordCommentQuery,$i,'modulename');
            $moduleList[] = $moduleName;
        }
        return $moduleList;
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
            "modules.CTMobileSettings.resources.NotificationSettings",
            '~/libraries/jquery/bootstrapswitch/js/bootstrap-switch.min.js'
        );

        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }

    public function getHeaderCss(Vtiger_Request $request) {
        $headerCssInstances = parent::getHeaderCss($request);
        $cssFileNames = array(
            '~/libraries/jquery/bootstrapswitch/css/bootstrap3/bootstrap-switch.min.css'
        );
        $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
        $headerCssInstances = array_merge($headerCssInstances, $cssInstances);
        return $headerCssInstances;
    }
}
