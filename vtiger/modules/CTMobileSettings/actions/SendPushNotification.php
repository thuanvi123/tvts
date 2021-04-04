<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
include_once 'data/CRMEntity.php';
class CTMobileSettings_SendPushNotification_Action extends Vtiger_Save_Action {
    public function process(Vtiger_Request $request) {
        global $adb;
        $UsersList=$request->get("Users");
        $title = $request->get('title');
        $message = $request->get('message');
        $type = $request->get('type');
        $url = $request->get('notification_url');

        $allGroups = array_keys(Settings_Groups_Record_Model::getAll());
        // Save selected fields
        $groupUsers = array();
        if(is_array($UsersList)) {
            foreach($UsersList as $key => $userid) {
                if(in_array($userid,$allGroups)){
                    $groupuser = Users_Record_Model::getAccessibleGroupUsers($userid);
                    $groupUsers = array_merge($groupUsers,$groupuser);
                    unset($UsersList[$key]);
                }
            }
        }
        
        foreach($groupUsers as $key => $user){
            if(!in_array($user,$UsersList)){
                $UsersList[] = $user;
            }
        }

        foreach($UsersList as $key => $userid){
            $get_token = $adb->pquery("SELECT devicetoken,device_type FROM ctmobile_userdevicetoken WHERE userid = ?",array($userid));
            if($adb->num_rows($get_token)){
                 $devicetoken = $adb->query_result($get_token,0,'devicetoken');
                 $device_type = $adb->query_result($get_token,0,'device_type');
                 if($devicetoken && $device_type){
                   if($type == 'link'){
                        $result = CTMobileSettings_Module_Model::sendLinkPushnotification($message,$devicetoken,$device_type,$title,$url);
                        $linktoids = "";
                        $moduleName = 'CTPushNotification';
                        $focus = CRMEntity::getInstance($moduleName);
                        $focus->column_fields['description'] = $message;
                        $focus->column_fields['assigned_user_id'] = $userid;
                        $focus->column_fields['pn_related'] = $linktoids;
                        $focus->column_fields['pushnotificationstatus'] = 'Send';
                        $focus->column_fields['devicekey'] = $devicekey;
                        $focus->column_fields['pn_title'] =  $title;
                        $focus->column_fields['notification_url'] =  $url;
                        $focus->column_fields['pushnotification_response'] =  $result;
                        $focus->save($moduleName);
                   }else{
                        $result = CTMobileSettings_Module_Model::sendpushnotification($message,$devicetoken,$device_type,$title);
                        $linktoids = "";
                        $moduleName = 'CTPushNotification';
                        $focus = CRMEntity::getInstance($moduleName);
                        $focus->column_fields['description'] = $message;
                        $focus->column_fields['assigned_user_id'] = $userid;
                        $focus->column_fields['pn_related'] = $linktoids;
                        $focus->column_fields['pushnotificationstatus'] = 'Send';
                        $focus->column_fields['devicekey'] = $devicekey;
                        $focus->column_fields['pn_title'] =  $title;
                        $focus->column_fields['pushnotification_response'] =  $result;
                        $focus->save($moduleName);
                   }
                 }
            }
        }

        $Detail_Url = CTMobileSettings_Module_Model::$CTMOBILE_DETAILVIEW_URL;
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(true);
        $response->emit();
    }
}
