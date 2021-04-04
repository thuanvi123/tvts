<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
class CTMobileSettings_SaveAssetAjax_Action extends Vtiger_Save_Action {
    public function process(Vtiger_Request $request) {
        global $adb;
        $asset_module=$request->get("asset_module");
        $active=$request->get("active");
        $field=$request->get("asset_fields");
       
        // Clear data
        $adb->pquery("DELETE FROM `ctmobile_asset_field` WHERE (`module`=?)",array($asset_module));
        // Save selected fields
        
        $adb->pquery("INSERT INTO `ctmobile_asset_field` (`module`, `fieldname`) VALUES (?, ?)",array($asset_module,$field));
           
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array('_module'=>$asset_module));
        $response->emit();
    }
}
