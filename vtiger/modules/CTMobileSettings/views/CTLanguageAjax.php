<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

Class CTMobileSettings_CTLanguageAjax_View extends Vtiger_IndexAjax_View {

    public function process(Vtiger_Request $request) {
        global $adb;
        $viewer = $this->getViewer ($request);
        $ctlanguage = $request->get('ctlanguage');
        $ctlanguage_section = $request->get('ctlanguage_section');
        $module = $request->getModule();
        $LanguageFields = CTMobileSettings_Module_Model::getLanguageFields($ctlanguage,$ctlanguage_section);
        $viewer->assign('CTLANGAUGE', $ctlanguage);
        $viewer->assign('CTLANGUAGE_SECTION', $ctlanguage_section);
        $viewer->assign('LANGUAGE_FIELDS', $LanguageFields);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('SOURCE_MODULE', $module);
        echo $viewer->view('CTLanguageFields.tpl', $module, true);
    }
}
