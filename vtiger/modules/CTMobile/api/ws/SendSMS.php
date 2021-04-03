<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
 
class CTMobile_WS_SendSMS extends CTMobile_WS_Controller {
	
	function process(CTMobile_API_Request $request) {
		global $adb;
		global $current_user;
		$current_user = $this->getActiveUser();
		$getSMSNotifier = $adb->pquery("SELECT * from vtiger_smsnotifier_servers where isactive = 1 order by id LIMIT 0,1");
		$countSMSNotifier = $adb->num_rows($getSMSNotifier);
		$response = new CTMobile_API_Response();
		if($countSMSNotifier > 0) {
		
			$valuesJSONString =  $request->get('values');
			$values = Zend_Json::decode($valuesJSONString);
			
			//Multiple mobiles numbers separated by comma
			$mobileNumber = trim($values['mobiles']);

			$currentUserModel = Users_Record_Model::getCurrentUserModel();

			//Your message to send, Add URL encoding here.
			$message = $values['message'];
			$recordIds = array();
			$toNumbers = array();
			$recordIds[] = trim($values['recordIds']);
			$toNumbers[] = $mobileNumber;
			$moduleName = trim($request->get('module'));
			if(!empty($toNumbers)) {
				$id = SMSNotifier_Record_Model::SendSMS($message, $toNumbers, $current_user->id, $recordIds, $moduleName);
				$statusDetails = SMSNotifier::getSMSStatusInfo($id);
				$response->setResult(array('id' => $id, 'statusdetails' => $statusDetails[0]));
			}
			
		} else {
			$message = $this->CTTranslate('SMSNotifier is not enable in CRM. Please enable it first');
			$response->setError(503, $message);
		}
		return $response;
	}
}
		
