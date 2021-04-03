<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
class CTMobile_UI_Logout extends CTMobile_WS_Controller {
	
	function process(CTMobile_API_Request $request) {
		HTTP_Session2::destroy(HTTP_Session2::detectId());
		header('Location: index.php');
		exit;
	}

}
