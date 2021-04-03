<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

class PUSHNotifier {
	/**
	 * Send SMS (Creates SMS Entity record, links it with related CRM record and triggers provider to send sms)
	 *
	 * @param String $message
	 * @param Array $tonumbers
	 * @param Integer $ownerid User id to assign the SMS record
	 * @param mixed $linktoids List of CRM record id to link SMS record
	 * @param String $linktoModule Modulename of CRM record to link with (if not provided lookup it will be calculated)
	 */
	static function sendnotification($message, $tonumbers, $ownerid = false, $linktoids = false, $linktoModule = false, $title = '', $ws_id = '',$workflowId = '') {
		$license_data = CTMobileSettings_Module_Model::getLicenseData();
        if(strtolower($license_data['Plan']) != 'free'){
			global $current_user, $adb;

			if($ownerid === false) {
				if(isset($current_user) && !empty($current_user)) {
					$ownerid = $current_user->id;
				} else {
					$ownerid = 1;
				}
			}
			$title = html_entity_decode(decode_html($title),ENT_QUOTES,'UTF-8');
			$message = html_entity_decode(decode_html($message),ENT_QUOTES,'UTF-8');
			foreach ($tonumbers as $userid){
				if($userid!=''){
					$checkpermission = self::checkNotificationPermission($workflowId,$userid);
					if($checkpermission){
						$data = self::getdevicekey($userid);
						$devicekey = $data['devicetoken'];
						$device_type = $data['device_type'];
						if($devicekey != '' && $device_type != ''){
							$moduleName = 'CTPushNotification';
							$focus = CRMEntity::getInstance($moduleName);
							$focus->column_fields['description'] = $message;
							$focus->column_fields['assigned_user_id'] = $userid;
							$focus->column_fields['pn_related'] = $linktoids;
							$focus->column_fields['pushnotificationstatus'] = 'Draft';
							$focus->column_fields['devicekey'] = $devicekey;
							$focus->column_fields['pn_title'] =  $title;
							$focus->save($moduleName);
							if($focus->id != ''){
								$record_id = $focus->id;
								$result = self::sendpushnotification($message,$devicekey,$device_type, $ws_id, $linktoModule, $title);
								if($result){
									$recordModel = Vtiger_Record_Model::getInstanceById($record_id, $moduleName);
									$modelData = $recordModel->getData();
									$recordModel->set('mode', 'edit');
									$recordModel->set('pushnotification_response', $result);
									$recordModel->set('pushnotificationstatus', 'Send');
									$recordModel->save();
								}
							}
						}
					}
					
				}
			}
		}	
	}
	
	static function getdevicekey($userid) {
		global $log, $adb;
		
		$perm_qry = "SELECT devicetoken,device_type FROM ctmobile_userdevicetoken  WHERE userid = ? ;";
		$perm_result = $adb->pquery($perm_qry, array($userid));
		$perm_rows = $adb->num_rows($perm_result);
		if($perm_rows > 0){
			$devicetoken = $adb->query_result($perm_result, 0, "devicetoken");
			$device_type = $adb->query_result($perm_result,$i,'device_type');
			$data = array('devicetoken'=>$devicetoken,'device_type'=>$device_type);
			return $data;
		}
		return '';
	}
	
	static function sendpushnotification($message,$devicekey, $device_type, $ws_id, $linktoModule, $title) {
		
		 define( 'API_ACCESS_KEY', 'AAAA_kGRtQ8:APA91bEWdbKg2fAycMdQGfhh6wWgdorH8D4J7lmcKq6tLE8RTKFg6_BKOQLNa_-agDsJugMCM3BrhFIPbvNq6EqW2PKO5E6SN-KwFs4RWRNcfl7TWrbNCkFhuaLtVg9F_FTrHal1tn7t' );
       
		
        $fcmMsg = array(
			 'content_available'=> 'true',
        );
        if($device_type == 'ios'){
			//define( 'API_ACCESS_KEY', 'AIzaSyC9q9_LoSE5_faOalJx_6wl9Q7aeOq584I' );
			$notification = array('title' =>$title , 'text' => $message, 'sound' => 'default');
			$dataPayload = array('type'=>'record','recordId' => $ws_id , 'moduleName' => $linktoModule);
			$fcmFields = array(
				'to' => $devicekey ,
				'priority' => 'high',
				'notification' => $notification,
				'data' => $dataPayload
			);
			
		}else{
			$dataPayload = array('type'=>'record','message' => $message, 'title' => $title, 'recordId' => $ws_id , 'moduleName' => $linktoModule);
			
			$fcmFields = array(
				'to' => $devicekey ,
				'priority' => 'high',
				'notification' => $fcmMsg,
				'data' => $dataPayload

			);
		}
		
        $headers = array(
            'Authorization: key=' .API_ACCESS_KEY ,
            'Content-Type: application/json'
        );
         
        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fcmFields ) );
		
        $result = curl_exec($ch );
        curl_close( $ch );
       
		return $result;
	}

	static function checkNotificationPermission($workflowId,$userid){
		$adb = PearDatabase::getInstance();
		$main_perm_query = "SELECT ctmobile_notification_settings.notification_id,ctmobile_notification_settings.notification_enabled FROM ctmobile_notification_settings INNER JOIN ctmobile_notification_module_settings ON ctmobile_notification_settings.notification_id = ctmobile_notification_module_settings.notification_id WHERE ctmobile_notification_module_settings.workflow_id = ?";
		$main_perm_result = $adb->pquery($main_perm_query,array($workflowId));
		if($adb->num_rows($main_perm_result)){
			$notification_id = $adb->query_result($main_perm_result,0,'notification_id');
			$notification_enabled = $adb->query_result($main_perm_result,0,'notification_enabled');
			if($notification_enabled == '1'){
				$sub_perm_query = "SELECT user_id FROM ctmobile_notification_restriction WHERE user_id = ? AND notification_id = ?";
				$sub_perm_result = $adb->pquery($sub_perm_query,array($userid,$notification_id));
				if($adb->num_rows($sub_perm_result) == 0){
					return true;
				}else{
					return false;
				}
			}else{
				return false;
			}
		}else{
			return true;
		}
	}

	
}
?>
