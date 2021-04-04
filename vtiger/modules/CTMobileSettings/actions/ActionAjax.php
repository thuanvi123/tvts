<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

class CTMobileSettings_ActionAjax_Action extends Vtiger_Save_Action {
    function checkPermission(Vtiger_Request $request) {
        return;
    }

    function __construct() {
        parent::__construct();
        $this->exposeMethod('updateSequenceNumber');
    }

    function process(Vtiger_Request $request) {
        $mode = $request->get('mode');
        if(!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
    }

    function updateSequenceNumber(Vtiger_Request $request) {
        $response = new Vtiger_Response();

        try{
            $db = PearDatabase::getInstance();
            $sequenceList = $request->get('sequence');
            $query = 'UPDATE ctmobile_address_modules SET sequence = CASE module ';
            foreach ($sequenceList as $blockId => $sequence){
                $query .=" WHEN '$blockId' THEN ".$sequence;
            }
            $query .=' END ';
            $query .= ' WHERE module IN ('.generateQuestionMarks($sequenceList).')';

            $db->pquery($query, array_keys($sequenceList));
            $response->setResult(array('success'=>true));
        }catch(Exception $e) {
            $response->setError($e->getCode(),$e->getMessage());
        }
        $response->emit();
    }
}
