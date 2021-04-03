<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

class CTMobileSettings_FieldSettings_View extends Settings_Vtiger_Index_View {

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
        $viewer = $this->getViewer($request);
        $allModules = Vtiger_Module_Model::getSearchableModules();
        $AddressModules = array("Contacts","Leads","Vendors");
        foreach($allModules as $key => $value){
            if(in_array($key,$AddressModules)){
                $allModule[$key] = $value;
            }
        }
        $viewer->assign('ALL_MODULE', array_keys($allModule));
         // Get search modules from database
        $rsSearch=$adb->pquery("SELECT * FROM `ctmobile_address_modules` ORDER BY sequence",array());
        $searchModules=array();
        if($adb->num_rows($rsSearch)>0) {
            while($row=$adb->fetch_array($rsSearch)) {
                $AddressModules =  array("Contacts","Leads","Vendors");
                if(in_array($row['module'],$AddressModules)){
                    $searchModules[$row['module']]=$row['sequence'];
                }  
            }
        }


        $asset_allModules = Vtiger_Module_Model::getSearchableModules();
        $disallowedModules = array('CTMobileSettings','CTMobile','CTAttendance','CTPushNotification','CTUserFilterView','CTTimeTracker','CTMessageTemplate','CTRouteAttendance','CTRoutePlanning','CTTimeControl','SMSNotifier','ModComments','PBXManager','MailManager','Emails','Documents');
        foreach($asset_allModules as $key => $value){
            if(in_array($key,$disallowedModules)){
                unset($asset_allModules[$key]);
            }
        }
        $viewer->assign('ASSET_ALL_MODULE', array_keys($asset_allModules));

   

        $moduleName = 'Products';
        $module = $request->getModule();

        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        $recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($moduleModel);

        $viewer->assign('BARCODE_RECORD_STRUCTURE_MODEL', $recordStructureInstance);
        $recordStructure = $recordStructureInstance->getStructure();
        // for Inventory module we should now allow item details block
        if(in_array($moduleName, getInventoryModules())){
            $itemsBlock = "LBL_ITEM_DETAILS";
            unset($recordStructure[$itemsBlock]);
        }

        foreach ($recordStructure as $blockname => $fields) {
            foreach ($fields as $fieldname => $field) {
                if($field->isReferenceField() || $field->isOwnerField() || $field->getFieldDataType() == 'image'){
                    unset($recordStructure[$blockname][$fieldname]);
                }
            }
        }
        
        $viewer->assign('BARCODE_RECORD_STRUCTURE', $recordStructure);


        //Get selected module data
        $selectedFields=array();
        $rs=$adb->pquery("SELECT * FROM `ctmobile_address_modules` WHERE module=?",array($moduleName));
        if($adb->num_rows($rs) >0) {
            $viewer->assign('ACTIVE', $adb->query_result($rs,0,'active'));
            // Get selected fields
            $rs_field=$adb->pquery("SELECT * FROM `ctmobile_barcode_fields` WHERE module=?",array($moduleName));
            if($adb->num_rows($rs_field) > 0) {
                while($row=$adb->fetch_array($rs_field)) {
                    $selectedFields[]=$row['fieldname'];
                }
            }
        }
        $viewer->assign('BARCODE_SELECTED_FIELDS', $selectedFields);
        $viewer->assign('barcode_selected_module', $moduleName);
        $viewer->assign('BARCODE_SOURCE_MODULE', $moduleName);

        $sign_allModules = Vtiger_Module_Model::getSearchableModules();
        $disallowedModules = array('CTMobileSettings','CTMobile','CTAttendance','CTPushNotification','CTUserFilterView','CTTimeTracker','CTMessageTemplate','CTRouteAttendance','CTRoutePlanning','CTTimeControl','SMSNotifier','ModComments','PBXManager','MailManager','Emails','Documents');
        foreach($sign_allModules as $key => $value){
            if(in_array($key,$disallowedModules)){
                unset($sign_allModules[$key]);
            }
        }
        $viewer->assign('SIGN_ALL_MODULE', array_keys($sign_allModules));

        $Users =$adb->pquery("SELECT * FROM `vtiger_users` WHERE deleted = 0 AND status = ?",array('Active'));
        $userArray = array();
        for($i=0;$i<($adb->num_rows($Users));$i++){
            $id = $adb->query_result($Users,$i,'id');
            $name = $adb->query_result($Users,$i,'first_name').' '.$adb->query_result($Users,$i,'last_name');
            $userArray[] = array('userid'=>$id,'username'=>$name);
        }
        $viewer->assign('DISPLAY_USER_MODEL', $userArray);

        $display_allModules = Vtiger_Module_Model::getSearchableModules();
        $disallowedModules = array('CTMobileSettings','CTMobile','CTAttendance','CTPushNotification','CTUserFilterView','CTTimeTracker','CTMessageTemplate','CTRouteAttendance','CTRoutePlanning','CTTimeControl','SMSNotifier','ModComments','PBXManager','MailManager','Emails','Documents');
        foreach($display_allModules as $key => $value){
            if(in_array($key,$disallowedModules)){
                unset($display_allModules[$key]);
            }
        }
        $viewer->assign('DISPLAY_ALL_MODULE', array_keys($display_allModules));

        $viewer->assign('SEARCH_MODULES', $searchModules);
        echo $viewer->view('FieldSettings.tpl',$module,true);
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
            "modules.CTMobileSettings.resources.FieldSettings",
        );

        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }
}
