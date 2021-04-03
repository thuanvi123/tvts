<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

class CTMobile_WS_NotificationSettings extends CTMobile_WS_Controller {
	
	function process(CTMobile_API_Request $request) {
		global $adb,$current_user; // Required for vtws_update API
		$current_user = $this->getActiveUser();
		$mode = $request->get('mode');
		$response = new CTMobile_API_Response();
		if($mode == 'ViewDetails'){
			$result = $adb->pquery("SELECT * FROM ctmobile_notification_settings",array());
	        for ($i=0; $i < $adb->num_rows($result); $i++) { 
	        	$notification_id = $adb->query_result($result,$i,'notification_id');
	            $notification_type = $adb->query_result($result,$i,'notification_type');
	            $notification_enabled = $adb->query_result($result,$i,'notification_enabled');
	            if($notification_enabled == '1'){
	            	$result2 = $adb->pquery("SELECT * FROM ctmobile_notification_restriction WHERE user_id = ? AND notification_id = ?",array($current_user->id,$notification_id));
	            	if($adb->num_rows($result2)){
	            		$notification_enabled = '0';
	            	}
	            }
	            if($notification_type == 'event_invitation' || $notification_type == 'event_reminder'){
	            	$notification_heading = $this->CTTranslate('Events');
	            	$count = 0;
	            }else if($notification_type == 'record_assigned' || $notification_type == 'comment_mentioned' || $notification_type == 'comment_assigned'){
	            	$notification_heading = $this->CTTranslate('Conversions');
	            	$count = 1;
	            }else if($notification_type == 'task_reminder' || $notification_type == 'task_assigned'){
	            	$notification_heading = $this->CTTranslate('Task');
	            	$count = 2;
	            }else if($notification_type == 'follow_record'){
	            	$notification_heading = $this->CTTranslate('Follow record');
	            	$count = 3;
	            }

	            if($notification_type == 'event_invitation'){
	            	$notification_label = $this->CTTranslate('Event Invitation');
	            }else if($notification_type == 'event_reminder'){
	            	$notification_label = $this->CTTranslate('Event Reminder');
	            }else if($notification_type == 'record_assigned'){
	            	$notification_label = $this->CTTranslate('When Record Assigned');
	            }else if($notification_type == 'comment_assigned'){
	            	$notification_label = $this->CTTranslate('Comments has been added to record assigned to you');
	            }else if($notification_type == 'comment_mentioned'){
	            	$notification_label = $this->CTTranslate('You were mentioned in comments');
	            }else if($notification_type == 'task_reminder'){
	            	$notification_label = $this->CTTranslate('Task Reminder');
	            }else if($notification_type == 'task_assigned'){
	            	$notification_label = $this->CTTranslate('Task assigned to you');
	            }else if($notification_type == 'follow_record'){
	            	$notification_label = $this->CTTranslate('Notify when any updates to the record you\\\'re following');
	            }
	            $selectedNotification[$count]['notification_heading'] = $notification_heading;
	            $selectedNotification[$count]['notification_fields'][] = array('notification_type'=>$notification_type,'notification_label'=>$notification_label,'notification_value'=>$notification_enabled);
	        }

	        $response->setResult(array('notificationResult'=>$selectedNotification));
	        return $response;
		}else if($mode == 'SaveDetails'){
			$notification_type = $request->get('notification_type');
			$notification_value = $request->get('notification_value');
			if($notification_type != '' && $notification_value != ''){
				$result = $adb->pquery("SELECT * FROM ctmobile_notification_settings WHERE notification_type = ?",array($notification_type));
				$notification_id = $adb->query_result($result,0,'notification_id');
	            $notification_type = $adb->query_result($result,0,'notification_type');
	            $notification_enabled = $adb->query_result($result,0,'notification_enabled');
	            if($notification_enabled == '0' && $notification_value == '1'){
	            	$message = $this->CTTranslate('This Notification has been disabled by admin');
	            	$response->setError('',$message);
	        		return $response;
	            }else{
	            	if($notification_value == '1'){
		            	$result2 = $adb->pquery("DELETE FROM ctmobile_notification_restriction WHERE user_id = ? AND notification_id = ?",array($current_user->id,$notification_id));
	            	}else{
	            		$result2 = $adb->pquery("INSERT INTO ctmobile_notification_restriction(user_id,notification_id) VALUES(?,?)",array($current_user->id,$notification_id));
	            	}
		            $message = $this->CTTranslate('User Notification Settings saved successfully');
		            $response->setResult(array('message'=>$message));
	        		return $response;
	            }
			}
		}

	}
}