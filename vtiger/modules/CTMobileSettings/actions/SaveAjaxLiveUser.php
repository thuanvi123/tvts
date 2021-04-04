<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
class CTMobileSettings_SaveAjaxLiveUser_Action extends Vtiger_Save_Action {
    public function process(Vtiger_Request $request) {
        global $adb;
        $fields=$request->get("fields");
        // Clear data
        $adb->pquery("DELETE FROM `ctmobile_livetracking_users`",array());
        // Save selected fields
        if(is_array($fields)) {
            foreach($fields as $field) {
                $adb->pquery("INSERT INTO `ctmobile_livetracking_users` (`userid`) VALUES (?)",array($field));
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
