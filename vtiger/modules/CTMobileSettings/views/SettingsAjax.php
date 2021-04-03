<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

Class CTMobileSettings_SettingsAjax_View extends Vtiger_IndexAjax_View {

    public function process(Vtiger_Request $request) {
        global $adb;
        $viewer = $this->getViewer ($request);
        $moduleName = $request->get('search_module');
        $module = $request->getModule();
        $mode = $request->get("mode");
        
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        $recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($moduleModel);

        $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
        $recordStructure = $recordStructureInstance->getStructure();
        // for Inventory module we should now allow item details block
        if(in_array($moduleName, getInventoryModules())){
            $itemsBlock = "LBL_ITEM_DETAILS";
            unset($recordStructure[$itemsBlock]);
        }
        $viewer->assign('RECORD_STRUCTURE', $recordStructure);
        // Added to show event module custom fields
        if($moduleName == 'Calendar'){
            $relatedModuleName = 'Events';
            $relatedModuleModel = Vtiger_Module_Model::getInstance($relatedModuleName);
            $relatedRecordStructureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($relatedModuleModel);
            $eventBlocksFields = $relatedRecordStructureInstance->getStructure();
            $viewer->assign('EVENT_RECORD_STRUCTURE_MODEL', $relatedRecordStructureInstance);
            $viewer->assign('EVENT_RECORD_STRUCTURE', $eventBlocksFields);
        }

        $viewer->assign('SOURCE_MODULE', $moduleName);

        if($mode == "autoAddressField"){
            
            //Static GEO API Fields
            $arrAutoAddressFields = array('Auto-Search','Street','City','State','PostalCode','Country');
            $viewer->assign('SELECTED_ADDRESS_FIELDS', $arrAutoAddressFields);    

            //Get selected auto address field by module 
            $autoaddresss_field=$adb->pquery("SELECT * FROM `ctmobile_address_autofields` WHERE module=?",array($moduleName));
            if($adb->num_rows($autoaddresss_field) > 0) {
                $selectedFields['Auto-Search'] = $adb->query_result($autoaddresss_field,0,'auto_search');
                $selectedFields['Street']  = $adb->query_result($autoaddresss_field,0,'street');
                $selectedFields['City'] = $adb->query_result($autoaddresss_field,0,'city');    
                $selectedFields['State'] = $adb->query_result($autoaddresss_field,0,'state');  
                $selectedFields['PostalCode'] = $adb->query_result($autoaddresss_field,0,'postalcode');  
                $selectedFields['Country'] = $adb->query_result($autoaddresss_field,0,'country');  
                
            }
            
            $viewer->assign('SELECTED_FIELDS', $selectedFields);
            echo $viewer->view('autoAddressFields.tpl', $module, true);

        }else{

            //to get filter according module
            $viewer->assign('CUSTOM_VIEWS', CustomView_Record_Model::getAllByGroup($moduleName));
            $customView = new CustomView();
            $defaultViewId = $customView->getViewId($moduleName);
            $viewer->assign('VIEWID', $defaultViewId);
            
            $listViewModel = Vtiger_ListView_Model::getInstance($moduleName, $defaultViewId);
            $noOfEntries = $listViewModel->getListViewCount();
            $viewer->assign('noOfEntries', $noOfEntries);


            //Get selected module data
            $selectedFields=array();
            $rs=$adb->pquery("SELECT * FROM `ctmobile_address_modules` WHERE module=?",array($moduleName));
            if($adb->num_rows($rs) >0) {
                $viewer->assign('ACTIVE', $adb->query_result($rs,0,'active'));
                // Get selected fields
                $rs_field=$adb->pquery("SELECT * FROM `ctmobile_address_fields` WHERE module=?",array($moduleName));
                if($adb->num_rows($rs_field) > 0) {
                    while($row=$adb->fetch_array($rs_field)) {
                        $selectedFields[]=$row['fieldname'];
                    }
                }
            }
            $viewer->assign('SELECTED_FIELDS', $selectedFields);

            $AddressRecords = CTMobileSettings_Module_Model::getAddressRecords($moduleName,$defaultViewId);
            $nonAddressRecords = $noOfEntries - $AddressRecords;
            $viewer->assign('AddressRecords', $AddressRecords);
            $viewer->assign('nonAddressRecords', $nonAddressRecords);

            $nonAddressRecordList =  CTMobileSettings_Module_Model::getNonAddressRecordsList($moduleName,$defaultViewId);

            $viewer->assign('selected_module', $moduleName);
            $viewer->assign('nonAddressRecordList', $nonAddressRecordList);
            
            $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
            $viewer->assign('SOURCE_MODULE', $moduleName);
            echo $viewer->view('SelectedFields.tpl', $module, true);
        }
    }
}
