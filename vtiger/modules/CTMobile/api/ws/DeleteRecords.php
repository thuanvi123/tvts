<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
include_once 'include/Webservices/Delete.php';

class CTMobile_WS_DeleteRecords extends CTMobile_WS_Controller {
	
	function process(CTMobile_API_Request $request) {
		global $current_user;
		$current_user = $this->getActiveUser();
		$records = trim($request->get('records'));
		$ClearAllNotification = trim($request->get('ClearAllNotification'));
		if ($records == '') {
			$records = array($request->get('record'));
		} else {
			$records = Zend_Json::decode($records);
		}
		if($ClearAllNotification == true && $ClearAllNotification != 'false'){
			global $adb;
			$results = $adb->pquery("SELECT vtiger_ctpushnotification.ctpushnotificationid FROM vtiger_ctpushnotification INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_ctpushnotification.ctpushnotificationid WHERE vtiger_crmentity.deleted = 0 AND vtiger_crmentity.smownerid = ?",array($current_user->id));
			for($i=0;$i<$adb->num_rows($results);$i++){
				$records[] = CTMobile_WS_Utils::getEntityModuleWSId('CTPushNotification').'x'.$adb->query_result($results,$i,'ctpushnotificationid');
			}
		}
		$deleted = array();
		foreach($records as $record) {
			try {
				vtws_delete($record, $current_user);
				$result = true;
			} catch(Exception $e) {
				$result = false;
			}
			$deleted[$record] = $result;
		}
		$response = new CTMobile_API_Response();
		$response->setResult(array('deleted' => $deleted,'message'=>$this->CTTranslate('Record has been deleted successfully')));
		
		return $response;
	}
}
