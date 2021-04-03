<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

class CTMobile_WS_TaxByType extends CTMobile_WS_Controller{
    
    function process(CTMobile_API_Request $request) {
		global $current_user;
		$response = new CTMobile_API_Response();
		$current_user = $this->getActiveUser();

		$taxType = $request->get('taxType');
        
        	$result = $this->getTaxDetails($taxType);
		$response->setResult($result);

		return $response;
	}
    
    protected function getTaxDetails($taxType){
       global $adb;
       $tableName = $this->getTableName($taxType);
       $result = $adb->pquery("SELECT * FROM $tableName WHERE deleted = 0", array());
       $rowCount =  $adb->num_rows($result);
        if($rowCount){
            for($i = 0; $i < $rowCount; $i++){
                $row = $adb->query_result_rowdata($result, $i);
                $recordDetails[] = $row;
            }
        }
        return $recordDetails;
    }
    
    protected function getTableName($taxType){
        switch($taxType){
            case 'shipping':
                return 'vtiger_shippingtaxinfo';
                break;
            case 'inventory':
                return 'vtiger_inventorytaxinfo';
                break;
        }
    }
}
