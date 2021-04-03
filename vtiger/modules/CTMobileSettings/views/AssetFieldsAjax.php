<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

Class CTMobileSettings_AssetFieldsAjax_View extends Vtiger_IndexAjax_View {

    public function process(Vtiger_Request $request) {
        global $adb;
        $viewer = $this->getViewer ($request);

        $moduleName = $request->get('asset_module');
        $module = $request->getModule();


        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        $recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($moduleModel);

        $viewer->assign('ASSET_RECORD_STRUCTURE_MODEL', $recordStructureInstance);
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
        
        $viewer->assign('ASSET_RECORD_STRUCTURE', $recordStructure);
        // Added to show event module custom fields
        if($moduleName == 'Calendar'){
            $relatedModuleName = 'Events';
            $relatedModuleModel = Vtiger_Module_Model::getInstance($relatedModuleName);
            $relatedRecordStructureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($relatedModuleModel);
            $eventBlocksFields = $relatedRecordStructureInstance->getStructure();
            $viewer->assign('EVENT_RECORD_STRUCTURE_MODEL', $relatedRecordStructureInstance);
            $viewer->assign('EVENT_RECORD_STRUCTURE', $eventBlocksFields);
        }

        //Get selected module data
        $selectedFields=array();
        $rs=$adb->pquery("SELECT * FROM `ctmobile_address_modules` WHERE module=?",array($moduleName));
        if($adb->num_rows($rs) >0) {
            $viewer->assign('ACTIVE', $adb->query_result($rs,0,'active'));
            // Get selected fields
            $rs_field=$adb->pquery("SELECT * FROM `ctmobile_asset_field` WHERE module=?",array($moduleName));
            if($adb->num_rows($rs_field) > 0) {
                while($row=$adb->fetch_array($rs_field)) {
                    $selectedFields[]=$row['fieldname'];
                }
            }
        }
        $viewer->assign('ASSET_SELECTED_FIELDS', $selectedFields);
        $viewer->assign('asset_selected_module', $moduleName);
        $viewer->assign('ASSET_SOURCE_MODULE', $moduleName);

        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('SOURCE_MODULE', $moduleName);
        echo $viewer->view('AssetSelectedFields.tpl', $module, true);
    }
}
