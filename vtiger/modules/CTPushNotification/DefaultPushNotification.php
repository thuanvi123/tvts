<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
include_once 'modules/Vtiger/CRMEntity.php';

function SendNotificationFollowRecord($entityData){
	$adb = PearDatabase::getInstance();
	$moduleName = $entityData->getModuleName();
	$wsId = $entityData->getId();
	$parts = explode('x', $wsId);
	$recordId = $parts[1];
	$moduleId = $parts[0];

	$titleMessageSQL = $adb->pquery("SELECT notification_title,notification_message FROM ctmobile_notification_settings WHERE notification_type = ?",array('follow_record'));
	$title = decode_html(decode_html($adb->query_result($titleMessageSQL, 0, "notification_title")));
	$message = decode_html(decode_html($adb->query_result($titleMessageSQL, 0, "notification_message")));
	
	$getFollowUserSql = $adb->pquery("SELECT userid FROM vtiger_crmentity_user_field WHERE recordid = ? AND starred = '1'",array($recordId));
	if($adb->num_rows($getFollowUserSql)){
		for ($i=0; $i < $adb->num_rows($getFollowUserSql); $i++) { 
			$userid = $adb->query_result($getFollowUserSql,$i,'userid');
			if(checkPermission('follow_record',$userid)){
				$perm_qry = "SELECT devicetoken,device_type FROM ctmobile_userdevicetoken  WHERE userid = ? ";
				$perm_result = $adb->pquery($perm_qry, array($userid));
				$perm_rows = $adb->num_rows($perm_result);
				if($perm_rows > 0){
					$devicetoken = $adb->query_result($perm_result, 0, "devicetoken");
					$device_type = $adb->query_result($perm_result,0,'device_type');
					if($devicetoken != '' && $device_type != ''){
						$module_Name = 'CTPushNotification';
						$focus = CRMEntity::getInstance($module_Name);
						$focus->column_fields['description'] = $message;
						$focus->column_fields['assigned_user_id'] = $userid;
						$focus->column_fields['pn_related'] = $recordId;
						$focus->column_fields['pushnotificationstatus'] = 'Draft';
						$focus->column_fields['devicekey'] = $devicetoken;
						$focus->column_fields['pn_title'] =  $title;
						$focus->save($module_Name);
						if($focus->id != ''){
							$record_id = $focus->id;
							$result = sendpushnotification($message,$devicetoken,$device_type,$wsId,$moduleName,$title);
							if($result){
								$recordModel = Vtiger_Record_Model::getInstanceById($record_id, $module_Name);
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

function SendEventNotificationInviteUser($entityData){
	$adb = PearDatabase::getInstance();
	$moduleName = $entityData->getModuleName();
	$wsId = $entityData->getId();
	$parts = explode('x', $wsId);
	$recordId = $parts[1];
	$moduleId = $parts[0];

	$titleMessageSQL = $adb->pquery("SELECT notification_title,notification_message FROM ctmobile_notification_settings WHERE notification_type = ?",array('event_invitation'));
	$title = decode_html(decode_html($adb->query_result($titleMessageSQL, 0, "notification_title")));
	$message = decode_html(decode_html($adb->query_result($titleMessageSQL, 0, "notification_message")));
	$message = getMergedDescription($message, $recordId, 'Events');

	$getInvites = $adb->pquery("SELECT inviteeid FROM vtiger_invitees where activityid = ?", array($recordId));
	if($adb->num_rows($getInvites)){
		for ($i=0; $i < $adb->num_rows($getInvites); $i++) { 
			$inviteId = $adb->query_result($getInvites, $i, 'inviteeid');
			if(checkPermission('event_invitation',$inviteId)){
				$perm_qry = "SELECT devicetoken,device_type FROM ctmobile_userdevicetoken  WHERE userid = ?";
				$perm_result = $adb->pquery($perm_qry, array($inviteId));
				$perm_rows = $adb->num_rows($perm_result);
				if($perm_rows > 0){
					$devicetoken = $adb->query_result($perm_result, 0, "devicetoken");
					$device_type = $adb->query_result($perm_result,0,'device_type');
					if($devicetoken != '' && $device_type != ''){
						$module_Name = 'CTPushNotification';
						$focus = CRMEntity::getInstance($module_Name);
						$focus->column_fields['description'] = $message;
						$focus->column_fields['assigned_user_id'] = $inviteId;
						$focus->column_fields['pn_related'] = $recordId;
						$focus->column_fields['pushnotificationstatus'] = 'Draft';
						$focus->column_fields['devicekey'] = $devicetoken;
						$focus->column_fields['pn_title'] =  $title;
						$focus->save($module_Name);
						if($focus->id != ''){
							$record_id = $focus->id;
							$result = sendpushnotification($message,$devicetoken,$device_type,$wsId,$moduleName,$title);
							if($result){
								$recordModel = Vtiger_Record_Model::getInstanceById($record_id, $module_Name);
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

function SendEventReminderNotification($entityData){
	$adb = PearDatabase::getInstance();
	$moduleName = $entityData->getModuleName();
	$wsId = $entityData->getId();
	$parts = explode('x', $wsId);
	$recordId = $parts[1];
	$moduleId = $parts[0];
	$title = 'event_reminder';
	$message = 'event_reminder';
	$perm_qry = "SELECT devicetoken,device_type FROM ctmobile_userdevicetoken";
	$perm_result = $adb->pquery($perm_qry, array());
	$perm_rows = $adb->num_rows($perm_result);
	if($perm_rows > 0){
		for ($i=0; $i < $perm_rows; $i++) {
			$devicetoken = $adb->query_result($perm_result, $i, "devicetoken");
			$device_type = $adb->query_result($perm_result,$i,'device_type');
			if($devicetoken != '' && $device_type != ''){
				$result = sendpushnotification($message,$devicetoken,$device_type,'','',$title);
			}
		}
	}
}

function SendTaskReminderNotification($entityData){
	$adb = PearDatabase::getInstance();
	$moduleName = $entityData->getModuleName();
	$wsId = $entityData->getId();
	$parts = explode('x', $wsId);
	$recordId = $parts[1];
	$moduleId = $parts[0];
	$title = 'task_reminder';
	$message = 'task_reminder';
	$perm_qry = "SELECT devicetoken,device_type FROM ctmobile_userdevicetoken";
	$perm_result = $adb->pquery($perm_qry, array());
	$perm_rows = $adb->num_rows($perm_result);
	if($perm_rows > 0){
		for ($i=0; $i < $perm_rows; $i++) {
			$devicetoken = $adb->query_result($perm_result, $i, "devicetoken");
			$device_type = $adb->query_result($perm_result,$i,'device_type');
			if($devicetoken != '' && $device_type != ''){
				$result = sendpushnotification($message,$devicetoken,$device_type,'','',$title);
			}
		}
	}
}

function sendpushnotification($message,$devicekey, $device_type, $ws_id, $linktoModule, $title) {
		
	 define( 'API_ACCESS_KEY', 'AAAA_kGRtQ8:APA91bEWdbKg2fAycMdQGfhh6wWgdorH8D4J7lmcKq6tLE8RTKFg6_BKOQLNa_-agDsJugMCM3BrhFIPbvNq6EqW2PKO5E6SN-KwFs4RWRNcfl7TWrbNCkFhuaLtVg9F_FTrHal1tn7t' );
   
	
    $fcmMsg = array(
		 'content_available'=> 'true',
    );
    if($device_type == 'ios'){
    	if($title == 'event_reminder' && $message == 'event_reminder'){
    		$notification = array('alert'=>array('title' =>$title,'content-available'=>1) , 'text' => $message, 'sound' => 'default');
    	}else if($title == 'task_reminder' && $message == 'task_reminder'){
    		$notification = array('alert'=>array('title' =>$title,'content-available'=>1) , 'text' => $message, 'sound' => 'default');
    	}else{
			$notification = array('title' =>$title , 'body' => $message, 'sound' => 'default');
    	}
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

function checkPermission($notification_type,$userid){
	$adb = PearDatabase::getInstance();
	$main_perm_query = "SELECT * FROM ctmobile_notification_settings WHERE notification_type = ? AND notification_enabled = '1'";
	$main_perm_result = $adb->pquery($main_perm_query,array($notification_type));
	if($adb->num_rows($main_perm_result)){
		$notification_id = $adb->query_result($main_perm_result,0,'notification_id');
		$sub_perm_query = "SELECT * FROM ctmobile_notification_restriction WHERE user_id = ? AND notification_id = ?";
		$sub_perm_result = $adb->pquery($sub_perm_query,array($userid,$notification_id));
		if($adb->num_rows($sub_perm_result) == 0){
			return true;
		}else{
			return false;
		}
	}else{
		return false;
	}
}