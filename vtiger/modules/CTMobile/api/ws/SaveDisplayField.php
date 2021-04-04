<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

class CTMobile_WS_SaveDisplayField extends CTMobile_WS_Controller {
	
    function process(CTMobile_API_Request $request) {
	global $adb,$current_user;
	$current_user = $this->getActiveUser();

	$module=$request->get("module");
        $userid = $request->get("userid");
        $first_field=$request->get("first_field");
        $second_field=$request->get("second_field");
        $third_field=$request->get("third_field");

        // Clear data
        $adb->pquery("DELETE FROM `ctmobile_display_fields` WHERE `module` = ? AND userid = ?",array($module,$userid));
        // Save selected fields
        
        $adb->pquery("INSERT INTO `ctmobile_display_fields` (`userid`,`module`, `fieldname`,`fieldtype`) VALUES (?, ?, ?, ?)",array($userid,$module,$first_field,'First Field'));
        $adb->pquery("INSERT INTO `ctmobile_display_fields` (`userid`,`module`, `fieldname`,`fieldtype`) VALUES (?, ?, ?, ?)",array($userid,$module,$second_field,'Second Field'));
        $adb->pquery("INSERT INTO `ctmobile_display_fields` (`userid`,`module`, `fieldname`,`fieldtype`) VALUES (?, ?, ?, ?)",array($userid,$module,$third_field,'Third Field'));

        $response = new CTMobile_API_Response();
        $message = $this->CTTranslate('Display Field save successfully');
        $response->setResult($message);
        return $response;

    }

}