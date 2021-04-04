<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
class CTMobileSettings_SaveBarcodeAjax_Action extends Vtiger_Save_Action {
    public function process(Vtiger_Request $request) {
        global $adb;
        $search_module=$request->get("barcode_module");
        $active=$request->get("active");
        $field=$request->get("barcode_fields");
       
        // Clear data
        $adb->pquery("DELETE FROM `ctmobile_barcode_fields` WHERE (`module`=?)",array($search_module));
        // Save selected fields
        
        $adb->pquery("INSERT INTO `ctmobile_barcode_fields` (`module`, `fieldname`) VALUES (?, ?)",array($search_module,$field));
           
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array('_module'=>$search_module));
        $response->emit();
    }
}
