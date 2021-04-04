<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
class CTMobileSettings_SaveAjaxModuleManagement_Action extends Vtiger_Save_Action {
    public function process(Vtiger_Request $request) {
        global $adb;
        $modules = $request->get("modules_management_module");
        // Clear data
        $adb->pquery("DELETE FROM `ctmobile_modules_management`",array());
        // Save selected fields
        if(is_array($modules)) {
            foreach($modules as $module) {
                $adb->pquery("INSERT INTO `ctmobile_modules_management` (`module`) VALUES (?)",array($module));
            }
        }
		$title = 'logout';
		$message = 'logout';
		CTMobileSettings_Module_Model::sendpushnotificationAll($message,$title);
        $Detail_Url = CTMobileSettings_Module_Model::$CTMOBILE_DETAILVIEW_URL;
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array('Detail_Url'=>$Detail_Url));
        $response->emit();
    }
}
