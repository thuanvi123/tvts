 <?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

Class CTMobileSettings_DisplayFieldsAjax_View extends Vtiger_IndexAjax_View {

    public function process(Vtiger_Request $request) {
        global $adb;
        $viewer = $this->getViewer ($request);
        $mode = $request->get('mode');
        if($mode == 'get_doc_type'){
            $moduleName = $request->get('signature_module');
            $fields=$request->get("fields");
            $result = $adb->pquery("SELECT doc_type FROM `ctmobile_signature_fields` WHERE module = ? AND fieldname = ? ",array($moduleName,$fields));
            $num_rows = $adb->num_rows($result);
            $selectedField = '';
            if($num_rows>0){
                $selectedField = $adb->query_result($result,0,'doc_type');
            }
            $option= '<option value="">'.vtranslate("Select Documents Type","CTMobileSettings").'</option>';
            $selected = "";
            if($selectedField == 'Signature'){
                $selected = "selected";
            }
            $option.='<option value="Signature" '.$selected.' >'.vtranslate("Signature","CTMobileSettings").'</option>';

            $selected = "";
            if($selectedField == 'Documents'){
                $selected = "selected";
            }
            $option.= '<option value="Documents" '.$selected.' >'.vtranslate("Documents","CTMobileSettings").'</option>';

            echo $option;

        }else{
            $moduleName = $request->get('display_field_module');
            $userid = $request->get('userid');
            $module = $request->getModule();

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

            //Get selected module data
            $selectedFields['first_field']='';
            $selectedFields['second_field'] ='';
            $selectedFields['third_field'] ='';
            // Get selected fields
            $rs_field=$adb->pquery("SELECT * FROM `ctmobile_display_fields` WHERE module=? AND fieldtype = ? AND userid = ?",array($moduleName,'First Field',$userid));
            if($adb->num_rows($rs_field) > 0) {
                while($row=$adb->fetch_array($rs_field)) {
                    $selectedFields['first_field']=$row['fieldname'];
                }
            }

            $rs_field=$adb->pquery("SELECT * FROM `ctmobile_display_fields` WHERE module=? AND fieldtype = ? AND userid = ?",array($moduleName,'Second Field',$userid));
            if($adb->num_rows($rs_field) > 0) {
                while($row=$adb->fetch_array($rs_field)) {
                    $selectedFields['second_field']=$row['fieldname'];
                }
            }

            $rs_field=$adb->pquery("SELECT * FROM `ctmobile_display_fields` WHERE module=? AND fieldtype = ? AND userid = ?",array($moduleName,'Third Field',$userid));
            if($adb->num_rows($rs_field) > 0) {
                while($row=$adb->fetch_array($rs_field)) {
                    $selectedFields['third_field']=$row['fieldname'];
                }
            }

            $viewer->assign('SELECTED_FIELDS', $selectedFields);

            $viewer->assign('selected_module', $moduleName);
            $viewer->assign('USERID', $userid);

            $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
            $viewer->assign('SOURCE_MODULE', $moduleName);
            echo $viewer->view('DisplaySelectedFields.tpl', $module, true);
        }
    }
}
