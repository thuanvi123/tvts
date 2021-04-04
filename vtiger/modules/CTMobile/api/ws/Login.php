<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
class CTMobile_WS_Login extends CTMobile_WS_Controller {

	function requireLogin() {
		return false;
	}

	function process(CTMobile_API_Request $request) {
		$default_charset = VTWS_PreserveGlobal::getGlobal('default_charset');
		$response = new CTMobile_API_Response();

		$username = trim($request->get('username'));
		$password = trim($request->get('password'));

		$current_user = CRMEntity::getInstance('Users');
		$current_user->column_fields['user_name'] = $username;

		

		if(!$current_user->doLogin($password)) {
			$message = $this->CTTranslate('Authentication Failed');
			$response->setError(1210, $message);

		} else {
			
			// Start session now
			$sessionid = CTMobile_API_Session::init();

			if($sessionid === false) {
				$message = $this->CTTranslate('Session init failed').' '.$sessionid;
				echo $message;
			}

			$current_user->id = $current_user->retrieve_user_id($username);
			$current_user->retrieveCurrentUserInfoFromFile($current_user->id);
			$this->setActiveUser($current_user);
			$theme = $current_user->theme_config;
			
			if($theme == 'RTL'){	
				$theme = true;
			} else if($theme == 'LTR') {
				$theme = false;
			}else{
				$theme = $current_user->theme;
				$explode_theme = explode('_',$theme);
			
				if(isset($explode_theme[1]) && $explode_theme[1] == 'rtl') {
					$theme = true;
				}else if(isset($explode_theme[1]) && $explode_theme[1] == 'ltr'){
					$theme = false;
				}else{
					$theme = false;
				}
			}

			$device_key = $request->get('device_key');
			$device_type = $request->get('device_type');
			if($device_key!='' && $device_type != '' && $current_user->id != ''){
				global $adb;
				$userId = $current_user->id;
				$selectQuery = $adb->pquery("SELECT * FROM ctmobile_userdevicetoken where userid = ?", array($userId));								
				$selectQueryCount = $adb->num_rows($selectQuery);
				
				if($selectQueryCount > 0) {
					$oldsessionid = $adb->query_result($selectQuery,0,'sessionid');
					if($oldsessionid != ''){
					 	$InsertOldSession = $adb->pquery("INSERT INTO ctmobile_session_expire (userid, sessionid) VALUES(?,?)",array($userId,$oldsessionid));
					}
					$query = $adb->pquery("UPDATE ctmobile_userdevicetoken SET devicetoken = ?, device_type = ?, sessionid = ?, currency_id = ? ,time_zone = ?, date_format = ?, hour_format = ?, language = ? WHERE userid = ?", array($device_key, $device_type, $sessionid,$current_user->currency_id,$current_user->time_zone,$current_user->date_format,$current_user->hour_format,$current_user->language, $userId));
					
				} else {
					$query = $adb->pquery("INSERT INTO ctmobile_userdevicetoken (userid, devicetoken, device_type, longitude, latitude,sessionid,currency_id,time_zone,date_format,hour_format,language) VALUES (?,?,?,?,?,?,?,?,?,?,?)", array($userId, $device_key, $device_type,'0', '0',$sessionid,$current_user->currency_id,$current_user->time_zone,$current_user->date_format,$current_user->hour_format,$current_user->language));
				}
			}
		
			$userId = $current_user->id;
			if($userId!=''){
				$userImage = CTMobile_WS_Utils::getUserImage($userId);	
				$first_name = $current_user->first_name;
				$last_name = $current_user->last_name;
					
			}
			
			$moduleModel = Vtiger_Module_Model::getInstance('CTMobile');
			if($moduleModel->get('presence') != 0){
				$message = $this->CTTranslate('Please Enable CTMobile Module');
				$response->setError(404, $message);
				return $response;	
			}

			global $adb,$default_module;
			
			$version=$adb->pquery("SELECT * FROM vtiger_tab where name='CTMobileSettings'",array());
			$mobile_web_version = $adb->query_result($version,0,'version');

			$checkPerm = $adb->pquery("SELECT user_setting_type,user_setting_value FROM ctmobile_user_settings WHERE user_setting_type = ? AND user_setting_value = ?",array('access_user','1'));
			if($adb->num_rows($checkPerm) > 0){
				//for livetracking access to user
				$checkPermLive = $adb->pquery("SELECT user_setting_type,user_setting_value FROM ctmobile_user_settings WHERE user_setting_type = ? AND user_setting_value = ?",array('location_tracking','1'));
				if($adb->num_rows($checkPermLive) > 0){
					$liveuserQuery = $adb->pquery("SELECT 1 FROM ctmobile_livetracking_users WHERE userid = ?",array($current_user->id));
					if($adb->num_rows($liveuserQuery) > 0){
						$livetracking = true;
					}else{
						$livetracking = false;
					}
				}else{
					$livetracking = false;
				}
				//for ctmobile access to user
				$ctmobileAccessQuery = $adb->pquery("SELECT * FROM ctmobile_access_users",array());

				$allGroups = array_keys(Settings_Groups_Record_Model::getAll());
				$groupUsers = array();
				$selectedUsers = array();
				if($adb->num_rows($ctmobileAccessQuery) > 0){
					for($i=0;$i<($adb->num_rows($ctmobileAccessQuery));$i++){
						//$selectedUsers[] = $adb->query_result($ctmobileAccessQuery,$i,'userid');
						$userid = $adb->query_result($ctmobileAccessQuery,$i,'userid');
						if(in_array($userid,$allGroups)){
		                    $groupuser = Users_Record_Model::getAccessibleGroupUsers($userid);
		                    $groupUsers = array_merge($groupUsers,$groupuser);
		                }else{
		                	$Users[] = $userid;
		                }
					}
					if(!empty($Users)){
						$selectedUsers = array_merge($Users,$groupUsers);
					}else{
						$selectedUsers = $groupUsers;
					}
					if(in_array('selectAll',$selectedUsers) || in_array($current_user->id,$selectedUsers)){
						$ctmobileAccess = true;
					}else{
						$ctmobileAccess = false;
					}
				}else{
					$ctmobileAccess = false;
				}

				//for route planning access to user
				$checkPermRoute = $adb->pquery("SELECT user_setting_type,user_setting_value FROM ctmobile_user_settings WHERE user_setting_type = ? AND user_setting_value = ?",array('route_planner','1'));
				if($adb->num_rows($checkPermRoute) > 0){
					$ctmobileRouteAccessQuery = $adb->pquery("SELECT * FROM ctmobile_routegeneralsettings",array());
					if($adb->num_rows($ctmobileRouteAccessQuery) > 0){
						for($i=0;$i<($adb->num_rows($ctmobileRouteAccessQuery));$i++){
							//$selectedUsers[] = $adb->query_result($ctmobileAccessQuery,$i,'userid');
							$route_users = $adb->query_result($ctmobileRouteAccessQuery,$i,'route_users');
							$Routeusers = explode(',', $route_users);
							if(in_array($current_user->id,$Routeusers)){
			              		$route_planning_access = true;
			                }else{
			                	$route_planning_access = false;
			                }
						}
					}else{
						$route_planning_access = false;
					}
				}else{
					$route_planning_access = false;
				}

				//for timetracking permission to use
				$checkPermtime = $adb->pquery("SELECT user_setting_type,user_setting_value FROM ctmobile_user_settings WHERE user_setting_type = ? AND user_setting_value = ?",array('time_tracker','1'));
				if($adb->num_rows($checkPermtime) > 0){
					$time_tracker_access = true;
				}else{
					$time_tracker_access = false;
				}

				//check calllog permission to user
				$checkPermCall = $adb->pquery("SELECT user_setting_type,user_setting_value FROM ctmobile_user_settings WHERE user_setting_type = ? AND user_setting_value = ?",array('call_logging','1'));
				if($adb->num_rows($checkPermCall) > 0){
					$user_calllog_access = false;
					$calllogAccessQuery = $adb->pquery("SELECT * FROM ctmobile_calllog_users WHERE userid=?",array($current_user->id));
					if($adb->num_rows($calllogAccessQuery) > 0){
						$user_calllog_access = true;
					}

					//check auto activity create for calllog
					$auto_activity_create = false;
					$autoActivityQuery = $adb->pquery("SELECT * FROM ctmobile_calllog_autoactivity",array());
					if($adb->num_rows($autoActivityQuery) > 0){
						$auto_activity_create = true;
					}
				}else{
					$auto_activity_create = false;
					$user_calllog_access = false;
				}
			}else{
				$ctmobileAccess = false;
				$livetracking = false;
				$route_planning_access = false;
				$time_tracker_access = false;
				$user_calllog_access = false;
				$auto_activity_create = false;
			}
			
			$user_type = '';
			$expirydate = '';
			//for ctmobile usertype and expirydate
			$ctlicenseQuery = $adb->pquery("SELECT  expirydate,user_type FROM ctmobile_license_settings",array());
			if($adb->num_rows($ctlicenseQuery) > 0){
				$current_date = date('Y-m-d');
				$user_type = $adb->query_result($ctlicenseQuery,0,'user_type');
				$expirydate = $adb->query_result($ctlicenseQuery,0,'expirydate');
				if($current_date > $expirydate){
					$licencedata = CTMobileSettings_Module_Model::getLicenseData();
					if($licencedata['NextPaymentDate'] != ''){
						if($licencedata['NextPaymentDate'] > $expirydate){
							$adb->pquery("UPDATE ctmobile_license_settings SET expirydate = ?", array($licencedata['NextPaymentDate']));
							$expirydate = $licencedata['NextPaymentDate'];
						}
					}

				}

			}
			
			global $current_user;
			$current_user = $this->getActiveUser();
			$expirydate = Vtiger_Date_UIType::getDisplayValue($expirydate);

			$resultApi = $adb->pquery("SELECT * FROM ctmobile_api_settings",array());
			$api_key = $adb->query_result($resultApi,0,'api_key');
			global $default_module,$upload_maxsize;
			$uploaded_maxsizeinmb = $upload_maxsize/(1024*1024);
			$currency_symbol = html_entity_decode($current_user->currency_symbol, ENT_QUOTES, $default_charset);
			$userName = html_entity_decode($first_name." ".$last_name, ENT_QUOTES, $default_charset);

			//start code for event_reminder
			$event_reminder = false;
			$check_event_reminder = $adb->pquery("SELECT * FROM ctmobile_notification_settings WHERE notification_type = ?",array('event_reminder'));
			$notification_enabled = $adb->query_result($check_event_reminder,0,'notification_enabled');
	        if($notification_enabled == '1'){
	        	$event_reminder =  true;
	        	$notification_id = $adb->query_result($check_event_reminder,0,'notification_id');
        		$check_event_reminder2 = $adb->pquery("SELECT * FROM ctmobile_notification_restriction WHERE user_id = ? AND notification_id = ?",array($current_user->id,$notification_id));
            	if($adb->num_rows($check_event_reminder2)){
            		$event_reminder = false;
            	}
	        }

			$result = array();
			$result['login'] = array(
				'userImage'=>$userImage,
				'userName' => $userName,
				'userid' => $current_user->id,
				'email' => $current_user->email1,
				'is_admin'=>$current_user->is_owner,
				'crm_tz' => DateTimeField::getDBTimeZone(),
				'user_tz' => $current_user->time_zone,
                'start_hour'=>$current_user->start_hour,
                'callduration'=>$current_user->callduration,
                'eventduration'=>$current_user->othereventduration,
                'user_currency' => $current_user->currency_code,
                'currency_id'=>$current_user->currency_id,
                'currency_name'=>$current_user->currency_name,
                'currency_code'=>$current_user->currency_code,
                'currency_symbol'=>$currency_symbol,
                'currency_decimal_separator'=>$current_user->currency_decimal_separator,
                'currency_grouping_separator'=>$current_user->currency_grouping_separator,
                'currency_grouping_pattern'=>$current_user->currency_grouping_separator,
                'uploaded_maxsize'=>$uploaded_maxsizeinmb,
                'document_size_validation'=>vtranslate('Upload file size should be less than','CTMobile').' '.$uploaded_maxsizeinmb.vtranslate('MB','Documents'),
                'rtl_theme' => $theme,
                'language' => $current_user->language,
				'session'=> $sessionid,
				'due_date' => $due_date,
				'vtiger_version' => CTMobile_WS_Utils::getVtigerVersion(),
                'date_format' => $current_user->date_format, 
				'mobile_module_version' => CTMobile_WS_Utils::getVersion(),
				'hour_format'=>$current_user->hour_format,
				'default_module'=>$default_module,
				'default_module_label'=>vtranslate($default_module,$default_module),
				'mobile_web_version'=>$mobile_web_version,
				'api_key'=>$api_key,
				'livetracking'=>$livetracking,
				'ctmobile_access_user' => $ctmobileAccess,
				'route_planning_access'=> $route_planning_access,
				'time_tracker_access'=>$time_tracker_access,
				'user_calllog_access' => $user_calllog_access,
				'auto_activity_create' => $auto_activity_create,
				'user_type'=>$user_type,
				'expirydate'=>$expirydate,
				'event_reminder'=>$event_reminder
			);

			$livetrackingDependentFeature = array('meeting_checkin','attendance_checkin','nearby_customer','record_map_view','address_autofinder');

			$selectedPremiumFeature = array();
	        $p_result = $adb->pquery("SELECT * FROM ctmobile_premium_feature",array());
	        for ($i=0; $i < $adb->num_rows($p_result); $i++) { 
	            $feature_name = $adb->query_result($p_result,$i,'feature_name');
	            $feature_enabled = $adb->query_result($p_result,$i,'feature_enabled');
	            if($adb->num_rows($checkPerm) > 0){
		            if($feature_enabled == '1'){
		            	$feature_enabled =  true;
		            }else{
		            	$feature_enabled = false;
		            }
		        }else{
		        	$feature_enabled = false;
		        }

		        if($livetracking ==  false && in_array($feature_name, $livetrackingDependentFeature)){
		        	$feature_enabled = false;
		        }
	            $result['login'][$feature_name] = $feature_enabled;
	        }
			$response->setResult($result);

			$this->postProcess($response);
		
		}
		return $response;
	}

	function postProcess(CTMobile_API_Response $response) {
		return $response;
	}
}
