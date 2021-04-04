<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
class CTMobileSettings_SaveDisplayFieldAjax_Action extends Vtiger_Save_Action {
    public function process(Vtiger_Request $request) {
        global $adb;
        $display_field_module=$request->get("display_field_module");
        $active = $request->get("active");
        $userid = $request->get("userid");
        $first_field=$request->get("first_field");
        $second_field=$request->get("second_field");
        $third_field=$request->get("third_field");
       
        // Clear data
        $adb->pquery("DELETE FROM `ctmobile_display_fields` WHERE `module` = ? AND userid = ?",array($display_field_module,$userid));
        // Save selected fields
        
        $adb->pquery("INSERT INTO `ctmobile_display_fields` (`userid`,`module`, `fieldname`,`fieldtype`) VALUES (?, ?, ?, ?)",array($userid,$display_field_module,$first_field,'First Field'));
        $adb->pquery("INSERT INTO `ctmobile_display_fields` (`userid`,`module`, `fieldname`,`fieldtype`) VALUES (?, ?, ?, ?)",array($userid,$display_field_module,$second_field,'Second Field'));
        $adb->pquery("INSERT INTO `ctmobile_display_fields` (`userid`,`module`, `fieldname`,`fieldtype`) VALUES (?, ?, ?, ?)",array($userid,$display_field_module,$third_field,'Third Field'));
           
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array('_module'=>$display_field_module));
        $response->emit();
    }
}
