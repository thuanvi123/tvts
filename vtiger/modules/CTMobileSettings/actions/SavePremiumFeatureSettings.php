<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

class CTMobileSettings_SavePremiumFeatureSettings_Action extends Vtiger_Save_Action {

    public function process(Vtiger_Request $request) {
        global $adb;
        $meeting_checkin=$request->get("meeting_checkin");
        if($meeting_checkin == ''){
            $meeting_checkin = '0';
        }
        $adb->pquery('UPDATE ctmobile_premium_feature SET feature_enabled = ? WHERE feature_name = ?',array($meeting_checkin,'meeting_checkin'));

        $attendance_checkin=$request->get("attendance_checkin");
        if($attendance_checkin == ''){
            $attendance_checkin = '0';
        }
        $adb->pquery('UPDATE ctmobile_premium_feature SET feature_enabled = ? WHERE feature_name = ?',array($attendance_checkin,'attendance_checkin'));


        $qr_code_scanner=$request->get("qr_code_scanner");
        if($qr_code_scanner == ''){
            $qr_code_scanner = '0';
        }
        $adb->pquery('UPDATE ctmobile_premium_feature SET feature_enabled = ? WHERE feature_name = ?',array($qr_code_scanner,'qr_code_scanner'));

        $business_card_scanner=$request->get("business_card_scanner");
        if($business_card_scanner == ''){
            $business_card_scanner = '0';
        }
        $adb->pquery('UPDATE ctmobile_premium_feature SET feature_enabled = ? WHERE feature_name = ?',array($business_card_scanner,'business_card_scanner'));

        $asset_tracking=$request->get("asset_tracking");
        if($asset_tracking == ''){
            $asset_tracking = '0';
        }
        $adb->pquery('UPDATE ctmobile_premium_feature SET feature_enabled = ? WHERE feature_name = ?',array($asset_tracking,'asset_tracking'));

        $nearby_customer=$request->get("nearby_customer");
        if($nearby_customer == ''){
            $nearby_customer = '0';
        }
        $adb->pquery('UPDATE ctmobile_premium_feature SET feature_enabled = ? WHERE feature_name = ?',array($nearby_customer,'nearby_customer'));

        $record_map_view=$request->get("record_map_view");
        if($record_map_view == ''){
            $record_map_view = '0';
        }
        $adb->pquery('UPDATE ctmobile_premium_feature SET feature_enabled = ? WHERE feature_name = ?',array($record_map_view,'record_map_view'));

        $address_autofinder=$request->get("address_autofinder");
        if($address_autofinder == ''){
            $address_autofinder = '0';
        }
        $adb->pquery('UPDATE ctmobile_premium_feature SET feature_enabled = ? WHERE feature_name = ?',array($address_autofinder,'address_autofinder'));

        $call_from_app=$request->get("call_from_app");
        if($call_from_app == ''){
            $call_from_app = '0';
        }
        $adb->pquery('UPDATE ctmobile_premium_feature SET feature_enabled = ? WHERE feature_name = ?',array($call_from_app,'call_from_app'));

        $email_from_app=$request->get("email_from_app");
        if($email_from_app == ''){
            $email_from_app = '0';
        }
        $adb->pquery('UPDATE ctmobile_premium_feature SET feature_enabled = ? WHERE feature_name = ?',array($email_from_app,'email_from_app'));

        $sms_from_app=$request->get("sms_from_app");
        if($sms_from_app == ''){
            $sms_from_app = '0';
        }
        $adb->pquery('UPDATE ctmobile_premium_feature SET feature_enabled = ? WHERE feature_name = ?',array($sms_from_app,'sms_from_app'));

        $whatsapp_from_app=$request->get("whatsapp_from_app");
        if($whatsapp_from_app == ''){
            $whatsapp_from_app = '0';
        }
        $adb->pquery('UPDATE ctmobile_premium_feature SET feature_enabled = ? WHERE feature_name = ?',array($whatsapp_from_app,'whatsapp_from_app'));

        $livetracking_users=$request->get("livetracking_users");
        // Clear data
        $adb->pquery("DELETE FROM `ctmobile_livetracking_users`",array());
        // Save selected fields
        if(!empty($livetracking_users)){
            if(is_array($livetracking_users)) {
                foreach($livetracking_users as $field) {
                    $adb->pquery("INSERT INTO `ctmobile_livetracking_users` (`userid`) VALUES (?)",array($field));
                }
            }
            $title = 'logout';
            $message = 'logout';
            CTMobileSettings_Module_Model::sendpushnotificationAll($message,$title);  
        }
        

        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array('_module'=>true));
        $response->emit();
    }
}
