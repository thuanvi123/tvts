<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

class CTMobile_WS_UpdateSequenceDashboard extends CTMobile_WS_Controller {
	function process(CTMobile_API_Request $request) {
		global $adb,$current_user; 
		$current_user = $this->getActiveUser();
		
		$userid = $current_user->id;
		$sequence = $request->get('sequence');
		if($sequence != ""){
			$sequence = Zend_Json::decode($sequence);
			$deleteSql = $adb->pquery("DELETE FROM ctmobile_dashboard_sequence WHERE userid = ?",array($userid));
			foreach($sequence as $key => $value){
				$type = $value['type'];
				$id = $value['id'];
				$insertedResult = $adb->pquery("INSERT INTO ctmobile_dashboard_sequence (id,userid,type) VALUES(?,?,?)",array($id,$userid,$type));
				
			}
			$response = new CTMobile_API_Response();
			$message = $this->CTTranslate('sequence updated successfully');
			$response->setResult(array('message' => $message));
			return $response;
		}else{
			$message = $this->CTTranslate('sequence cannot be empty');
			$response->setError(404, $message);
			return $response;
		}

		
	}
}
