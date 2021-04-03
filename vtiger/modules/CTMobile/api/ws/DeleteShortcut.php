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

class CTMobile_WS_DeleteShortcut extends CTMobile_WS_Controller {
	
	function process(CTMobile_API_Request $request) {
		global $adb,$current_user;
		$current_user = $this->getActiveUser();
		$shortcutid = trim($request->get('shortcutid'));
		$shortcutType = trim($request->get('shortcutType'));
		if(empty($shortcutid)){
			$message = $this->CTTranslate('Required fields not found');
			throw new WebServiceException(404,$message);
		}
		if(empty($shortcutType)){
			$message = $this->CTTranslate('Required fields not found');
			throw new WebServiceException(404,$message);
		}
		if (!empty($shortcutid)) {
			if($shortcutType == 'filter'){
				$result = $adb->pquery("DELETE FROM ctmobile_filter_shortcut WHERE shortcutid = ?",array($shortcutid));
			}
			if($shortcutType == 'record'){
				$result = $adb->pquery("DELETE FROM ctmobile_record_shortcut WHERE shortcutid = ?",array($shortcutid));
			}
		}
		if($result){
			$response = new CTMobile_API_Response();
			$message = $this->CTTranslate('Shortcut has been deleted');
			$response->setResult(array('deleted' => $shortcutid,"message"=>$message));
		}else{
			$response = new CTMobile_API_Response();
			$message = $this->CTTranslate('Something went wrong - Try again later');
			$response->setError(0,$message);
		}
		
		
		return $response;
	}
}
