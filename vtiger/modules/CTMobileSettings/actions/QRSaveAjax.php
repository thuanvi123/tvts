<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
class CTMobileSettings_QRSaveAjax_Action extends Vtiger_Save_Action {
    public function process(Vtiger_Request $request) {
        global $adb;
        $search_module=$request->get("search_module");
        $active=$request->get("active");
        $fields=$request->get("fields");
        $modulefield = $request->get("modulefield");
        // Check exist
        $rs=$adb->pquery("SELECT * FROM `ctmobile_qrscanning_modules` WHERE module=?",array($search_module));
        if($adb->num_rows($rs)>0) {
            $adb->pquery("UPDATE `ctmobile_qrscanning_modules` SET `active`=? WHERE (`module`=?)", array($active,$search_module));
        }else {
            $adb->pquery("INSERT INTO `ctmobile_qrscanning_modules` (`module`, `active`) VALUES (?, ?)",array($search_module,$active));
        }

        $newQuery = $adb->pquery("SELECT * FROM ctmobile_qrscanning WHERE module = ?",array($search_module));
        if($adb->num_rows($newQuery)>0){
            $adb->pquery("UPDATE `ctmobile_qrscanning` SET `modulefield`=? WHERE (`module`=?)", array($modulefield,$search_module));
        }else{
             $adb->pquery("INSERT INTO `ctmobile_qrscanning` (`module`, `modulefield`) VALUES (?, ?)",array($search_module,$modulefield));
        }

        // Clear data
        $adb->pquery("DELETE FROM `ctmobile_qrscanning_fields` WHERE (`module`=?)",array($search_module));
        // Save selected fields
        if(is_array($fields)) {
            foreach($fields as $field) {
                $adb->pquery("INSERT INTO `ctmobile_qrscanning_fields` (`module`, `fieldname`) VALUES (?, ?)",array($search_module,$field));
            }
        }
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array('_module'=>$search_module));
        $response->emit();
    }
}
