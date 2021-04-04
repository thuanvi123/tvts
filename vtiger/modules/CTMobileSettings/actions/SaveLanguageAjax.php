<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
class CTMobileSettings_SaveLanguageAjax_Action extends Vtiger_Save_Action {
    public function process(Vtiger_Request $request) {
        global $adb;
        $ctlanguage = $request->get('ctlanguage');
        $ctlanguage_section = $request->get('ctlanguage_section');
        $LanguageFields = CTMobileSettings_Module_Model::getLanguageFields($ctlanguage,$ctlanguage_section);
        if($LanguageFields){
            foreach($LanguageFields as $key => $languageField){
                $keyword_id = $languageField['keyword_id'];
                $language_keyword = $request->get('field_'.$keyword_id);
                $adb->pquery("UPDATE ctmobile_language_keyword SET language_keyword = ? WHERE keyword_id = ?",array($language_keyword,$keyword_id));
            }
        }
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array('ctlanguage'=>$ctlanguage,'ctlanguage_section'=>$ctlanguage_section));
        $response->emit();
    }
}
