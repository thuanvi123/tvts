<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
class CTMobileSettings_SaveVCardAjax_Action extends Vtiger_Save_Action {
    public function process(Vtiger_Request $request) {
        global $adb;
        $search_module=$request->get("vcard_module");
        $active=$request->get("active");
        $fields=$request->get("fields");
        // Check exist
        $rs=$adb->pquery("SELECT * FROM `ctmobile_address_modules` WHERE module=?",array($search_module));
        if($adb->num_rows($rs)>0) {
            $adb->pquery("UPDATE `ctmobile_address_modules` SET `active`=? WHERE (`module`=?)", array($active,$search_module));
        }else {
            $adb->pquery("INSERT INTO `ctmobile_address_modules` (`module`, `active`) VALUES (?, ?)",array($search_module,$active));
        }

        // Clear data
        $adb->pquery("DELETE FROM `ctmobile_vcard_fields` WHERE (`module`=?)",array($search_module));
        // Save selected fields
        if(is_array($fields)) {
            foreach($fields as $field) {
                $adb->pquery("INSERT INTO `ctmobile_vcard_fields` (`module`, `fieldname`) VALUES (?, ?)",array($search_module,$field));
            }
        }
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array('_module'=>$search_module));
        $response->emit();
    }
}
