<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
class CTMobileSettings_SaveAjaxUserSettings_Action extends Vtiger_Save_Action {

    public function process(Vtiger_Request $request) {
        global $adb;
        $fieldname = $request->get("fieldname");
        $fieldvalue = $request->get("fieldvalue");
        if(!empty($fieldname)){
            if($fieldvalue == ''){
                $fieldvalue = '0';
            }
            $adb->pquery("UPDATE `ctmobile_user_settings` SET `user_setting_value` = ? WHERE `user_setting_type` = ?",array($fieldvalue,$fieldname));

            if($fieldname == 'call_logging' && $fieldvalue == '1'){
                $result = $adb->pquery('SELECT 1 FROM vtiger_relatedlists where tabid=? AND related_tabid=? AND presence = 0', array(getTabid('Events'), getTabid('Documents')));
                if (!($adb->num_rows($result))) {
                    $DocModuleModel = Vtiger_Module_Model::getInstance('Documents');
                    $RelatedModuleModel = Vtiger_Module_Model::getInstance('Events');
                    $RelatedModuleModel->setRelatedList($DocModuleModel, 'Documents', array(), 'get_attachments');
                }
            }
        }
        $Detail_Url = CTMobileSettings_Module_Model::$CTMOBILE_DETAILVIEW_URL;
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array('Detail_Url'=>$Detail_Url));
        $response->emit();
    }
}
