<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
include_once 'vtlib/Vtiger/Module.php';

class CTMobileSettings_SaveSignatureAjax_Action extends Vtiger_Save_Action {
    public function process(Vtiger_Request $request) {
        global $adb;
        $signature_module=$request->get("signature_module");
        $fields=$request->get("fields");
        $doc_type=$request->get("doc_type");
        $defaultDocumentsModules = array('Accounts','Leads','Contacts','Potentials','Products','Emails','HelpDesk','Quotes','PurchaseOrder','SalesOrder','Invoice','Faq','Services','ServiceContracts','Assets','ProjectTask','Project');
        // Clear data
        $adb->pquery("DELETE FROM `ctmobile_signature_fields` WHERE module = ? AND fieldname = ? ",array($signature_module,$fields));
        // Save selected fields
        if($fields != '') {
            $adb->pquery("INSERT INTO `ctmobile_signature_fields` (module, fieldname,doc_type) VALUES (?,?,?)",array($signature_module,$fields,$doc_type));

            $result = $adb->pquery('SELECT 1 FROM vtiger_relatedlists where tabid=? AND related_tabid=? AND presence = 0', array(getTabid($signature_module), getTabid('Documents')));
            if ($adb->num_rows($result) == 0) {
                $DocumentsModuleModel = Vtiger_Module_Model::getInstance('Documents');
                $RelatedModuleModel = Vtiger_Module_Model::getInstance($signature_module);
                $RelatedModuleModel->setRelatedList($DocumentsModuleModel, 'Documents', array('ADD','SELECT'), 'get_attachments');
            }
        }
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array('_module'=>$signature_module));
        $response->emit();
    }
}
