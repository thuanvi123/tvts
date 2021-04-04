<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
include_once 'include/QueryGenerator/QueryGenerator.php';

class CTMobileSettings_Module_Model extends Vtiger_Module_Model {
    
    public static $CTMOBILE_VERSION_URL = 'https://ctapps.crmtiger.com/checkversion.php';
    public static $CTMOBILE_CHECKLICENSE_URL = 'https://ctapps.crmtiger.com/checklicense.php';
    public static $GOOGLE_ADDRESSAPI_URL = 'https://maps.googleapis.com/maps/api/geocode/json?address=';
    public static $CTMOBILE_UPGRADEVIEW_URL = 'index.php?module=CTMobileSettings&parent=Settings&view=Upgrade';
    public static $CTMOBILE_TEAMTRACKING_URL = 'index.php?module=CTMobileSettings&parent=Settings&view=TeamTracking';

    public static $CTMOBILE_MYACCOUNT_URL = 'https://crmtiger.com/m/my-account/';
    public static $CTMOBILE_RELEASE_NOTE_URL = 'http://kb.crmtiger.com/knowledge-base/release-notes/';
    public static $CTMOBILE_HELP_URL = 'http://kb.crmtiger.com/article-categories/mobileapps/';
	public static $CTMOBILE_ANDROID_STORE_URL = 'https://play.google.com/store/apps/details?id=com.crmtiger.vtigercrm&hl=en';
	public static $CTMOBILE_APPLE_STORE_URL = 'https://apps.apple.com/in/app/crmtiger-vtiger-mobile/id1274011679';
	public static $CTMOBILE_LICENSE_DETAILVIEW_URL = 'index.php?module=CTMobileSettings&parent=Settings&view=LicenseDetail';
	public static $CTMOBILE_DETAILVIEW_URL = 'index.php?module=CTMobileSettings&parent=Settings&view=Details';

	public static $CTMOBILE_CTPUSHNOTIFICATION_URL = 'index.php?module=CTMobileSettings&parent=Settings&view=CTSendPushNotification';

	public static $CTMOBILE_GEOLOCATION_SETUP_URL = 'index.php?module=CTMobileSettings&parent=Settings&view=GEOSettings';

	public static $CTMOBILE_WORKFLOW_URL = 'index.php?module=Workflows&parent=Settings&view=List';

	public static $CTMOBILE_GOOGLEMAP_EDIT_URL = 'index.php?module=CTMobileSettings&parent=Settings&view=Settings&mode=GoogleMap';

	public static $CTMOBILE_OPENSTREETMAP_EDIT_URL = 'index.php?module=CTMobileSettings&parent=Settings&view=Settings&mode=OpenStreetMap';

	public static $CTMOBILE_LANGUAGE_URL = 'index.php?module=CTMobileSettings&parent=Settings&view=CTLanguage';

    public static $CTMOBILE_ROUTE_ANALYTICS_URL = 'index.php?module=CTMobileSettings&parent=Settings&view=RouteAnalytics';//Route Analytics Setting

    public static $CTMOBILE_FIELD_SETTINGS_URL = 'index.php?module=CTMobileSettings&parent=Settings&view=FieldSettings';

    public static $CTMOBILE_USER_SETTINGS_URL = 'index.php?module=CTMobileSettings&parent=Settings&view=UserSettings';

    public static $CTMOBILE_ACCESSUSER_URL = 'index.php?module=CTMobileSettings&parent=Settings&view=CTMobileAccessUser';

    public static $MY_ACCOUNT_SUMMARY_URL = 'https://kb.crmtiger.com/knowledge-base/mobile-app-account-summary/';
    public static $GENERAL_SETTINGS_URL = 'https://kb.crmtiger.com/knowledge-base/mobile-app-general-settings/';
    public static $VERSION_AND_UPDATES_URL = 'https://kb.crmtiger.com/knowledge-base/mobile-app-version-updates/';
    public static $GEO_SETTINGS_URL = 'https://kb.crmtiger.com/knowledge-base/mobile-app-gro-settings/';
    public static $REPORTS_AND_ANALYTICS_URL = 'https://kb.crmtiger.com/knowledge-base/mobile-app-reports-analytics/';
    public static $NOTIFICATIONS_URL = 'https://kb.crmtiger.com/article-categories/mobileapps/';

    public static $NOTIFICATIONS_SETTINGS_URL = 'index.php?module=CTMobileSettings&parent=Settings&view=NotificationSettings';

    public static $PREMIUM_FEATURE_MANAGEMENT_URL = 'index.php?module=CTMobileSettings&parent=Settings&view=PremiumFeatureManagement';


    function getLicenseData(){
		global $adb;
		$result = $adb->pquery("SELECT * FROM ctmobile_license_settings",array());
		$num_rows = $adb->num_rows($result);
		if($num_rows > 0){
			$license_key = $adb->query_result($result,0,'license_key');
			$domain = $adb->query_result($result,0,'domain');
			$url = self::$CTMOBILE_CHECKLICENSE_URL;
			$ch = curl_init($url);
			// Setup request to send json via POST.
			$data = array( "license_key"=>$license_key,"domain"=>$domain,"action"=>"get_licence_data");
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
			// Return response instead of printing.
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			// Send request.
			$result = curl_exec($ch);
			curl_close($ch);
			$licencedata = json_decode($result);
			$LastPaymentDate = $licencedata->PrevPaymentDate;
			$user_type = $licencedata->user_type;
			$NextPaymentdate = $licencedata->NextPaymentdate;
			$ORDER_ID = $licencedata->order_id;
			$message = $licencedata->NextPaymentdate;
			$expirydate = $licencedata->expirydate;
			$currentDate = date('Y-m-d');
			if($user_type != ''){
				if($user_type == 'Premium - One Month Free'){
					$user_type = 'Premium ( Monthly )';
				}else if($user_type == 'Free'){
					$user_type = 'Free';
				}else{
					$user_type = 'Premium ( '.$user_type.' )';
				}
				if(strtolower($user_type) != 'free' && strtotime($expirydate) < strtotime($currentDate)){
					$user_type = 'Free';
				}
				$date = strtotime($LastPaymentDate);
				$LastPaymentDate = date('d-m-Y',$date);
				$data = array("Plan"=>$user_type,"LastPaymentDate"=>$LastPaymentDate,"NextPaymentDate"=>$NextPaymentdate,'ORDER_ID'=>$ORDER_ID);
			}else{
				$data = array("Plan"=>$user_type,"LastPaymentDate"=>"","NextPaymentDate"=>"","message"=>"Invalid License Key");
			}
		}else{
			$data = array("Plan"=>"","LastPaymentDate"=>"","NextPaymentDate"=>"","message"=>"No Licence Key");
		}
		return $data;
	}


	
	function getMobileUser(){
		global $adb;
		$selectAll = $adb->pquery("SELECT * FROM ctmobile_access_users WHERE userid = 'selectAll'",array());
		if($adb->num_rows($selectAll) > 0){
			$sql = 'SELECT id FROM vtiger_users';
			$params = array();
			
			$sql .= ' WHERE status = ?';
			$params[] = 'Active';
			
			$result = $adb->pquery($sql, $params);

			$MobileUsers = $adb->num_rows($result);
		}else{
			$selected = $adb->pquery("SELECT * FROM ctmobile_access_users",array());
			$MobileUsers = $adb->num_rows($selected);
		}
		return $MobileUsers;
	}
	
	function getMeetingCount(){
		global $adb;
		$moduleName = 'CTAttendance';
		$listHeaders = array('id','attendance_status','eventid');
		$currentUser = Users_Record_Model::getCurrentUserModel();
		$generator = new QueryGenerator($moduleName, $currentUser);
		$generator->setFields($listHeaders);
		$query = $generator->getQuery();
		$query.= " AND ( vtiger_ctattendance.eventid IS NOT NULL && vtiger_ctattendance.eventid != '') AND vtiger_ctattendance.attendance_status = ?";
		$result = $adb->pquery($query,array('check_in'));
		$MeetingRecords = $adb->num_rows($result);
		return $MeetingRecords;
	}
	
	static function getGoogleApiKey(){
		global $adb;
		//get Google Api key
		$searchApi=$adb->pquery("SELECT * FROM `ctmobile_api_settings`",array());
		$GoogleApi = '';
		if($adb->num_rows($searchApi)>0) {
            $GoogleApi = $adb->query_result($searchApi,0,'api_key');
        }
        return $GoogleApi;
	}

	function getCheckOutCount(){
		global $adb;
		$moduleName = 'CTAttendance';
		$listHeaders = array('id','attendance_status','eventid');
		$currentUser = Users_Record_Model::getCurrentUserModel();
		$generator = new QueryGenerator($moduleName, $currentUser);
		$generator->setFields($listHeaders);
		$query = $generator->getQuery();
		$query.= " AND ( vtiger_ctattendance.eventid IS NOT NULL && vtiger_ctattendance.eventid != '') AND vtiger_ctattendance.attendance_status = ?";
		$result = $adb->pquery($query,array('check_out'));
		$checkOutRecords = $adb->num_rows($result);
		return $checkOutRecords;
	}
	
	function getTotalCrmUsers(){
		global $adb;
		$Users = $adb->pquery("SELECT * FROM `vtiger_users` WHERE deleted = 0 AND status = ?",array('Active'));
		$numofUsers = $adb->num_rows($Users);
		return $numofUsers;
	}						 
	function pushNotificationData(){
		global $adb;
		$moduleName = 'CTPushNotification';
		$customView = new CustomView();
		if(!$cvId) {
			$cvId = $customView->getViewId($moduleName);
		}
		$listHeaders = array('id','pn_title','description','modifiedtime');
		$currentUser = Users_Record_Model::getCurrentUserModel();
		$generator = new QueryGenerator($moduleName, $currentUser);
		$generator->setFields($listHeaders);
		$query = $generator->getQuery();
		$query.= " ORDER BY vtiger_crmentity.modifiedtime DESC LIMIT 0,3";
		$result = $adb->pquery($query,array());
		$num_rows = $adb->num_rows($result);
		$notificationData['data'] = array();
		$notificationData['totalRecords'] = array();
		for($i=0;$i<$num_rows;$i++){
			$ctpushnotificationid = $adb->query_result($result,$i,'ctpushnotificationid');
			$pn_title = $adb->query_result($result,$i,'pn_title');
			$description = $adb->query_result($result,$i,'description');
			$modifiedtime = $adb->query_result($result,$i,'modifiedtime');
			if($modifiedtime){
				$modifiedtime = Vtiger_Util_Helper::formatDateDiffInStrings($modifiedtime);
			}
			$Notificationdata['data'][] = array('id'=>$ctpushnotificationid,'title'=>$pn_title,'description'=>$description,'modifiedtime'=>$modifiedtime);
		}
		$Notificationdata['totalRecords'] = $num_rows;
		return $Notificationdata;
	}
	function getCTRouteUser(){
		$UsersModel = Users_Record_Model::getCurrentUserModel();
        $users = $UsersModel->getAccessibleUsers();
        $userArray =  array();
        foreach($users as $key => $value){
        	$userArray[] = array('id'=>$key,'name'=>decode_html($value));
        }
		return $userArray;
	}
	function getActiveUser(){
		global $adb;
		$datefind=date("Y-m-d H:i:s",strtotime("-30 minutes"));
		$query = "SELECT DISTINCT(userid) FROM ctmobile_userderoute  INNER JOIN vtiger_users ON vtiger_users.id = ctmobile_userderoute.userid WHERE vtiger_users.deleted = 0 AND createdtime > ?";
		$result = $adb->pquery($query,array($datefind));
		$activeuser = $adb->num_rows($result);
		return $activeuser;
	}
	
	function GetRequirement(){
		$count = 0;
		if(!extension_loaded('zip')){
			$count =$count + 1;
		}
		if(!extension_loaded('gd')){
			$count =$count + 1;
		}
		if(!extension_loaded('Zlib')){
			$count =$count + 1;
		}
		if(!extension_loaded('Curl')){
			$count =$count + 1;
		}
		if(!extension_loaded('mbstring')){
			$count =$count + 1;
		}
		$default_socket_timeout = ini_get('default_socket_timeout');
		$max_execution_time = ini_get('max_execution_time');
		$max_input_time = ini_get('max_input_time');
		$memory_limit = str_replace('M','',ini_get('memory_limit'));
		$post_max_size = str_replace('M','',ini_get('post_max_size'));
		$upload_max_filesize = str_replace('M','',ini_get('upload_max_filesize'));
		$max_input_vars = ini_get('max_input_vars');
		
		return $count;
	}
	
	function getGeocodingReport(){
		global $adb;
		//Contacts
		$contotalquery = "SELECT * FROM vtiger_contactdetails INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_contactdetails.contactid WHERE vtiger_crmentity.deleted = 0";
		$contotalresult  = $adb->pquery($contotalquery,array());
		$contotal = $adb->num_rows($contotalresult);
		$congeocodedquery = "SELECT * FROM vtiger_contactdetails INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_contactdetails.contactid INNER JOIN ct_address_lat_long ON ct_address_lat_long.recordid = vtiger_contactdetails.contactid 
						  WHERE vtiger_crmentity.deleted = 0 AND ct_address_lat_long.latitude IS NOT NULL AND ct_address_lat_long.longitude IS NOT NULL";
		$congeocodedresult  = $adb->pquery($congeocodedquery,array());
		$congeocoded = $adb->num_rows($congeocodedresult);
		$connongeocodedquery = "SELECT * FROM vtiger_contactdetails INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_contactdetails.contactid INNER JOIN ct_address_lat_long ON ct_address_lat_long.recordid = vtiger_contactdetails.contactid 
						  INNER JOIN vtiger_contactaddress ON vtiger_contactaddress.contactaddressid = vtiger_contactdetails.contactid WHERE vtiger_crmentity.deleted = 0 AND ct_address_lat_long.latitude IS NULL AND ct_address_lat_long.longitude IS NULL";
		$conaddressQuery = $adb->pquery("SELECT * FROM ctmobile_address_fields WHERE module = ?",array('Contacts'));
		for($i=0;$i<$adb->num_rows($conaddressQuery);$i++){
			$fields = $adb->query_result($conaddressQuery,$i,'fieldname');
			$test = explode(":",$fields);
			$field = $test[1];
			if($i == 0){
				$connongeocodedquery .= " AND ( vtiger_contactaddress.".$field." != ''";
			}else if($i == $adb->num_rows($conaddressQuery)-1){
				$connongeocodedquery .= " OR vtiger_contactaddress.".$field." != '' ) ";
			}else{
				$connongeocodedquery .= " OR vtiger_contactaddress.".$field." != '' ";
			}
		}
		
		$connongeocodedresult  = $adb->pquery($connongeocodedquery,array());
		$connongeocoded = $adb->num_rows($connongeocodedresult);
		
		$conaddressQuery = $adb->pquery("SELECT * FROM ctmobile_address_fields WHERE module = ?",array('Contacts'));
		$connonAddressQuery = "SELECT * FROM vtiger_contactdetails INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_contactdetails.contactid INNER JOIN vtiger_contactaddress ON vtiger_contactaddress.contactaddressid = vtiger_contactdetails.contactid WHERE vtiger_crmentity.deleted = 0";
		for($i=0;$i<$adb->num_rows($conaddressQuery);$i++){
			$fields = $adb->query_result($conaddressQuery,$i,'fieldname');
			$test = explode(":",$fields);
			$field = $test[1];
			$connonAddressQuery .= " AND vtiger_contactaddress.".$field."= ''";
		}
		$connonAddressQuery = $adb->pquery($connonAddressQuery,array());
		$connonaddress = $adb->num_rows($connonAddressQuery);
		$conpending = $contotal - ($congeocoded + $connonaddress + $connongeocoded);
		$data['Contacts'] = array('total'=>$contotal,'geocoded'=>$congeocoded,'nongeocoded'=>$connongeocoded,'pending'=>$conpending,'nonAddress'=>$connonaddress);
		
		//Leads
		$ledtotalquery = "SELECT * FROM vtiger_leaddetails INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_leaddetails.leadid WHERE vtiger_crmentity.deleted = 0";
		$ledtotalresult  = $adb->pquery($ledtotalquery,array());
		$ledtotal = $adb->num_rows($ledtotalresult);
		$ledgeocodedquery = "SELECT * FROM vtiger_leaddetails INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_leaddetails.leadid INNER JOIN ct_address_lat_long ON ct_address_lat_long.recordid = vtiger_leaddetails.leadid 
						  WHERE vtiger_crmentity.deleted = 0 AND ct_address_lat_long.latitude IS NOT NULL AND ct_address_lat_long.longitude IS NOT NULL";
		$ledgeocodedresult  = $adb->pquery($ledgeocodedquery,array());
		$ledgeocoded = $adb->num_rows($ledgeocodedresult);
		$lednongeocodedquery = "SELECT * FROM vtiger_leaddetails INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_leaddetails.leadid INNER JOIN ct_address_lat_long ON ct_address_lat_long.recordid = vtiger_leaddetails.leadid 
						   INNER JOIN vtiger_leadaddress ON vtiger_leadaddress.leadaddressid = vtiger_leaddetails.leadid WHERE vtiger_crmentity.deleted = 0 AND ct_address_lat_long.latitude IS NULL AND ct_address_lat_long.longitude IS NULL";
		$ledaddressQuery = $adb->pquery("SELECT * FROM ctmobile_address_fields WHERE module = ?",array('Leads'));
		for($i=0;$i<$adb->num_rows($ledaddressQuery);$i++){
			$fields = $adb->query_result($ledaddressQuery,$i,'fieldname');
			$test = explode(":",$fields);
			$field = $test[1];
			if($i == 0){
				$lednongeocodedquery .= " AND ( vtiger_leadaddress.".$field." != ''";
			}else if($i == $adb->num_rows($ledaddressQuery)-1){
				$lednongeocodedquery .= " OR vtiger_leadaddress.".$field." != '' ) ";
			}else{
				$lednongeocodedquery .= " OR vtiger_leadaddress.".$field." != '' ";
			}
		}
		
		$lednongeocodedresult  = $adb->pquery($lednongeocodedquery,array());
		$lednongeocoded = $adb->num_rows($lednongeocodedresult);
		$ledaddressQuery = $adb->pquery("SELECT * FROM ctmobile_address_fields WHERE module = ?",array('Leads'));
		$lednonAddressQuery = "SELECT * FROM vtiger_leaddetails INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_leaddetails.leadid INNER JOIN vtiger_leadaddress ON vtiger_leadaddress.leadaddressid = vtiger_leaddetails.leadid WHERE vtiger_crmentity.deleted = 0";
		for($i=0;$i<$adb->num_rows($ledaddressQuery);$i++){
			$fields = $adb->query_result($ledaddressQuery,$i,'fieldname');
			$test = explode(":",$fields);
			$field = $test[1];
			$lednonAddressQuery .= " AND vtiger_leadaddress.".$field."= ''";
		}
		$lednonAddressQuery = $adb->pquery($lednonAddressQuery,array());
		$lednonaddress = $adb->num_rows($lednonAddressQuery);
		$ledpending = $ledtotal - ($ledgeocoded + $lednonaddress + $lednongeocoded);
		$data['Leads'] = array('total'=>$ledtotal,'geocoded'=>$ledgeocoded,'nongeocoded'=>$lednongeocoded,'pending'=>$ledpending,'nonAddress'=>$lednonaddress);
		
		//Accounts
		$acctotalquery = "SELECT * FROM vtiger_account INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_account.accountid WHERE vtiger_crmentity.deleted = 0";
		$acctotalresult  = $adb->pquery($acctotalquery,array());
		$acctotal = $adb->num_rows($acctotalresult);
		$accgeocodedquery = "SELECT * FROM vtiger_account INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_account.accountid INNER JOIN ct_address_lat_long ON ct_address_lat_long.recordid = vtiger_account.accountid 
						  WHERE vtiger_crmentity.deleted = 0 AND ct_address_lat_long.latitude IS NOT NULL AND ct_address_lat_long.longitude IS NOT NULL";
		$accgeocodedresult  = $adb->pquery($accgeocodedquery,array());
		$accgeocoded = $adb->num_rows($accgeocodedresult);
		$accnongeocodedquery = "SELECT * FROM vtiger_account INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_account.accountid INNER JOIN ct_address_lat_long ON ct_address_lat_long.recordid = vtiger_account.accountid 
						  INNER JOIN vtiger_accountbillads ON vtiger_accountbillads.accountaddressid = vtiger_account.accountid INNER JOIN vtiger_accountshipads ON vtiger_accountshipads.accountaddressid = vtiger_account.accountid 
						  WHERE vtiger_crmentity.deleted = 0 AND ct_address_lat_long.latitude IS NULL AND ct_address_lat_long.longitude IS NULL";
		$accaddressQuery = $adb->pquery("SELECT * FROM ctmobile_address_fields WHERE module = ?",array('Accounts'));
		for($i=0;$i<$adb->num_rows($accaddressQuery);$i++){
			$fields = $adb->query_result($accaddressQuery,$i,'fieldname');
			$test = explode(":",$fields);
			$field = $test[1];
			if($i == 0){
				$accnongeocodedquery .= " AND ( ".$field." != ''";
			}else if($i == $adb->num_rows($accaddressQuery)-1){
				$accnongeocodedquery .= " OR ".$field." != '' ) ";
			}else{
				$accnongeocodedquery .= " OR ".$field." != '' ";
			}
		}
		$accnongeocodedresult  = $adb->pquery($accnongeocodedquery,array());
		$accnongeocoded = $adb->num_rows($accnongeocodedresult);
		$accpending = $acctotal - ($accgeocoded + $accnongeocoded);
		$accaddressQuery = $adb->pquery("SELECT * FROM ctmobile_address_fields WHERE module = ?",array('Accounts'));
		$accnonAddressQuery = "SELECT * FROM vtiger_account INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_account.accountid INNER JOIN vtiger_accountbillads ON vtiger_accountbillads.accountaddressid = vtiger_account.accountid INNER JOIN vtiger_accountshipads ON vtiger_accountshipads.accountaddressid = vtiger_account.accountid WHERE vtiger_crmentity.deleted = 0";
		for($i=0;$i<$adb->num_rows($accaddressQuery);$i++){
			$fields = $adb->query_result($accaddressQuery,$i,'fieldname');
			$test = explode(":",$fields);
			$field = $test[1];
			$accnonAddressQuery .= " AND ".$field."= ''";
		}
		$accnonAddressQuery = $adb->pquery($accnonAddressQuery,array());
		$accnonaddress = $adb->num_rows($accnonAddressQuery);
		$accpending = $acctotal - ($accgeocoded + $accnonaddress + $accnongeocoded);
		$data['Accounts'] = array('total'=>$acctotal,'geocoded'=>$accgeocoded,'nongeocoded'=>$accnongeocoded,'pending'=>$accpending,'nonAddress'=>$accnonaddress);
		
		//Calendar
		$caltotalquery = "SELECT * FROM vtiger_activity INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_activity.activityid WHERE vtiger_crmentity.deleted = 0";
		$caltotalresult  = $adb->pquery($caltotalquery,array());
		$caltotal = $adb->num_rows($caltotalresult);
		$calgeocodedquery = "SELECT * FROM vtiger_activity INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_activity.activityid INNER JOIN ct_address_lat_long ON ct_address_lat_long.recordid = vtiger_activity.activityid 
						  WHERE vtiger_crmentity.deleted = 0 AND ct_address_lat_long.latitude IS NOT NULL AND ct_address_lat_long.longitude IS NOT NULL";
		$calgeocodedresult  = $adb->pquery($calgeocodedquery,array());
		$calgeocoded = $adb->num_rows($calgeocodedresult);
		$calnongeocodedquery = "SELECT * FROM vtiger_activity INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_activity.activityid INNER JOIN ct_address_lat_long ON ct_address_lat_long.recordid = vtiger_activity.activityid 
						  WHERE vtiger_crmentity.deleted = 0 AND ct_address_lat_long.latitude IS NULL AND ct_address_lat_long.longitude IS NULL";
		$caladdressQuery = $adb->pquery("SELECT * FROM ctmobile_address_fields WHERE module = ?",array('Calendar'));
		for($i=0;$i<$adb->num_rows($caladdressQuery);$i++){
			$fields = $adb->query_result($caladdressQuery,$i,'fieldname');
			$test = explode(":",$fields);
			$field = $test[1];
			if($i == 0){
				$calnongeocodedquery .= " AND ( ".$field." != ''";
			}else if($i == $adb->num_rows($caladdressQuery)-1){
				$calnongeocodedquery .= " OR ".$field." != '' ) ";
			}else{
				$calnongeocodedquery .= " OR ".$field." != '' ";
			}
		}
		$calnongeocodedresult  = $adb->pquery($calnongeocodedquery,array());
		$calnongeocoded = $adb->num_rows($calnongeocodedresult);
		$caladdressQuery = $adb->pquery("SELECT * FROM ctmobile_address_fields WHERE module = ?",array('Calendar'));
		$calnonAddressQuery = "SELECT * FROM vtiger_activity INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_activity.activityid WHERE vtiger_crmentity.deleted = 0";
		for($i=0;$i<$adb->num_rows($caladdressQuery);$i++){
			$fields = $adb->query_result($caladdressQuery,$i,'fieldname');
			$test = explode(":",$fields);
			$field = $test[1];
			$calnonAddressQuery .= " AND ".$field."= ''";
		}
		$calnonAddressQuery = $adb->pquery($calnonAddressQuery,array());
		$calnonaddress = $adb->num_rows($calnonAddressQuery);
		$calpending = $caltotal - ($calgeocoded + $calnonaddress + $calnongeocoded);
		$data['Calendar'] = array('total'=>$caltotal,'geocoded'=>$calgeocoded,'nongeocoded'=>$calnongeocoded,'pending'=>$calpending,'nonAddress'=>$calnonaddress);
		
		return $data;	
	}
	
	static function sendpushnotificationAll($message,$title) {
		global $log, $adb;
		$perm_qry = "SELECT devicetoken,device_type FROM ctmobile_userdevicetoken";
		$perm_result = $adb->pquery($perm_qry, array());
		$perm_rows = $adb->num_rows($perm_result);
		if($perm_rows > 0){
			for($i=0;$i<$perm_rows;$i++){
				$devicetoken = $adb->query_result($perm_result,$i,'devicetoken');
				$device_type = $adb->query_result($perm_result,$i,'device_type');
				if($devicetoken && $device_type){ 
					if($device_type == 'ios' && $title == 'logout'){

					}else{
						$result = self::sendpushnotification($message,$devicetoken,$device_type,$title);
					}
				}
			}
		}
	}
	
	static function sendpushnotification($message,$devicekey,$device_type,$title,$type='normal',$recordId='',$moduleName='') {
		$title = html_entity_decode(decode_html($title),ENT_QUOTES,'UTF-8');
		$message = html_entity_decode(decode_html($message),ENT_QUOTES,'UTF-8');
		define( 'API_ACCESS_KEY', 'AAAA_kGRtQ8:APA91bEWdbKg2fAycMdQGfhh6wWgdorH8D4J7lmcKq6tLE8RTKFg6_BKOQLNa_-agDsJugMCM3BrhFIPbvNq6EqW2PKO5E6SN-KwFs4RWRNcfl7TWrbNCkFhuaLtVg9F_FTrHal1tn7t' );
        $fcmMsg = array(
			 'content_available'=> 'true',
        );
        if($device_type == 'ios'){
			//define( 'API_ACCESS_KEY', 'AIzaSyC9q9_LoSE5_faOalJx_6wl9Q7aeOq584I' );
			$notification = array('title' =>$title , 'body' => $message, 'sound' => 'default');
			$dataPayload = array('type'=>$type,'recordId' => $recordId , 'moduleName' => $moduleName);
			$fcmFields = array(
				'to' => $devicekey ,
				'priority' => 'high',
				'notification' => $notification,
				'data' => $dataPayload
			);
			
		}else{
			$dataPayload = array('type'=>$type,'message' => $message, 'title' => $title,'recordId' => $recordId , 'moduleName' => $moduleName);
			
			$fcmFields = array(
				'to' => $devicekey ,
				'priority' => 'high',
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

	
	static function sendLinkPushnotification($message,$devicekey,$device_type,$title,$url) {
		$title = html_entity_decode(decode_html($title),ENT_QUOTES,'UTF-8');
		$message = html_entity_decode(decode_html($message),ENT_QUOTES,'UTF-8');
		define( 'API_ACCESS_KEY', 'AAAA_kGRtQ8:APA91bEWdbKg2fAycMdQGfhh6wWgdorH8D4J7lmcKq6tLE8RTKFg6_BKOQLNa_-agDsJugMCM3BrhFIPbvNq6EqW2PKO5E6SN-KwFs4RWRNcfl7TWrbNCkFhuaLtVg9F_FTrHal1tn7t' );
        $fcmMsg = array(
			 'content_available'=> 'true',
        );
        if($device_type == 'ios'){
			//define( 'API_ACCESS_KEY', 'AIzaSyC9q9_LoSE5_faOalJx_6wl9Q7aeOq584I' );
			$notification = array('title' =>$title , 'text' => $message, 'sound' => 'default');
			$dataPayload = array('type'=>'link','recordId' => '' , 'moduleName' => '','url'=>$url);
			$fcmFields = array(
				'to' => $devicekey ,
				'priority' => 'high',
				'notification' => $notification,
				'data' => $dataPayload
			);
			
		}else{
			$dataPayload = array('type'=>'link','message' => $message, 'title' => $title, 'recordId' => '' , 'moduleName' => '','url'=>$url);
			
			$fcmFields = array(
				'to' => $devicekey ,
				'priority' => 'high',
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

	static function getCTRequirements(){
		$arrFinalError = array();
		$arrTemp = array();
		$requirements = true;
		$tables = array('ctmobile_userdevicetoken','ctmobile_record_shortcut','ctmobile_filter_shortcut','ctmobile_session_expire');
		$arrTemp = CTMobileSettings_Module_Model::checkModuleError('CTMobile',false,$tables);
		$arrFinalError['CTMobile'] = $arrTemp;
		if($arrTemp['requirements'] === false){
			$requirements = false;
		}
		else
		{
		}
		$tables = array('ctmobile_address_modules','ctmobile_address_fields','ctmobile_api_settings','ctmobile_license_settings','ctmobile_livetracking_users','ctmobile_access_users','ct_address_lat_long','ctmobile_userderoute','ctmobile_language_section','ctmobile_language_keyword','ctmobile_dashboard_sequence','ctmobile_vcard_fields','ctmobile_asset_field','ctmobile_barcode_fields','ctmobile_timetracking_modules','ctmobile_routestatus','ctmobile_routegeneralsettings','ctmobile_signature_fields','ctmobile_display_fields','ctmobile_address_autofields','ctmobile_calllog_autoactivity','ctmobile_calllog_users');
		$arrTemp = CTMobileSettings_Module_Model::checkModuleError('CTMobileSettings',false,$tables);
		$arrFinalError['CTMobileSettings'] = $arrTemp;
		if($arrTemp['requirements'] === false){
			$requirements = false;
		}
		
		$tables = array('vtiger_ctmessagetemplate','vtiger_ctmessagetemplatecf');
		$arrTemp = CTMobileSettings_Module_Model::checkModuleError('CTMessageTemplate',false,$tables);
		$arrFinalError['CTMessageTemplate'] = $arrTemp;
		if($arrTemp['requirements'] === false){
			$requirements = false;
		}
		
		$tables = array('vtiger_ctattendance','vtiger_ctattendancecf');
		$arrTemp = CTMobileSettings_Module_Model::checkModuleError('CTAttendance',false,$tables);
		$arrFinalError['CTAttendance'] = $arrTemp;
		if($arrTemp['requirements'] === false){
			$requirements = false;
		}
		
		$tables = array('vtiger_ctpushnotification','vtiger_ctpushnotificationcf');
		$arrTemp = CTMobileSettings_Module_Model::checkModuleError('CTPushNotification',false,$tables);
		$arrFinalError['CTPushNotification'] = $arrTemp;
		if($arrTemp['requirements'] === false){
			$requirements = false;
		}

		$tables = array('vtiger_ctuserfilterview','vtiger_ctuserfilterviewcf');
		$arrTemp = CTMobileSettings_Module_Model::checkModuleError('CTUserFilterView',false,$tables);
		$arrFinalError['CTUserFilterView'] = $arrTemp;
		if($arrTemp['requirements'] === false){
			$requirements = false;
		}

		$tables = array('vtiger_ctrouteplanning','vtiger_ctrouteplanningcf','vtiger_ctrouteplanrel');
		$arrTemp = CTMobileSettings_Module_Model::checkModuleError('CTRoutePlanning',false,$tables);
		$arrFinalError['CTRoutePlanning'] = $arrTemp;
		if($arrTemp['requirements'] === false){
			$requirements = false;
		}

		$tables = array('vtiger_ctrouteattendance','vtiger_ctrouteattendancecf');
		$arrTemp = CTMobileSettings_Module_Model::checkModuleError('CTRouteAttendance',false,$tables);
		$arrFinalError['CTRouteAttendance'] = $arrTemp;
		if($arrTemp['requirements'] === false){
			$requirements = false;
		}

		$tables = array('vtiger_cttimecontrol','vtiger_cttimecontrolcf');
		$arrTemp = CTMobileSettings_Module_Model::checkModuleError('CTTimeControl',false,$tables);
		$arrFinalError['CTTimeControl'] = $arrTemp;
		if($arrTemp['requirements'] === false){
			$requirements = false;
		}

		$tables = array('vtiger_cttimetracker','vtiger_cttimetrackercf');
		$arrTemp = CTMobileSettings_Module_Model::checkModuleError('CTTimeTracker',false,$tables);
		$arrFinalError['CTTimeTracker'] = $arrTemp;
		if($arrTemp['requirements'] === false){
			$requirements = false;
		}
		

		return array('requirements'=>$requirements,'arrFinalError'=>$arrFinalError);
	}

	static function checkModuleError($moduleName,$isLayoutFolder,$tables){
		$requirements = true;
		$requirements_module = array();
		$requirements_desc = array();
		$requirements_tables = array();
		global $adb,$root_directory;
		if(!file_exists($root_directory."modules/$moduleName/$moduleName.php")){
			$requirements = false;
			$requirements_code = 'CT-01';
			$requirements_error = $moduleName;
			$requirements_status = "$moduleName Module Folder is not present";
			$requirements_solutions = vtranslate('CT-01-solutions','CTMobileSettings');
		}else{
			if(!getTabid($moduleName)){
				$requirements = false;
				$requirements_code = 'CT-02';
				$requirements_error = $moduleName;
				$requirements_status = "$moduleName Module tabdetails is not present";
				$requirements_solutions = vtranslate('CT-02-solutions','CTMobileSettings');
			}else{
				$ModuleModel = Vtiger_Module_Model::getInstance($moduleName);
				if(!in_array($ModuleModel->get('presence'), array('0','2'))){
					$requirements = false;
					$requirements_code = 'CT-03';
					$requirements_error = $moduleName;
					$requirements_status = "$moduleName Module is disabled";
					$requirements_solutions = vtranslate('CT-03-solutions','CTMobileSettings');
				}
			}
		}

		foreach($tables as $key => $table){
			$result = $adb->pquery("SHOW TABLES LIKE '$table'",array());
			$num_rows = $adb->num_rows($result);
			if($num_rows == 0){
				$requirements = false;
				$requirements_code = 'CT-06';
				$requirements_error = $table;
				$requirements_status = $table.' tables is not present in database';
				$requirements_solutions = vtranslate('CT-06-solutions','CTMobileSettings');
			}
		}

		$data = array('requirements'=>$requirements,'requirements_code'=>$requirements_code,'requirements_module'=>$moduleName,'requirements_desc'=>$requirements_status);
		return $data;
	}

	//function that check api working code by sapna start
	function checkApiWorking(){
		global $site_URL;
		$curl = curl_init();
		$url = $site_URL."CTMobileApi.php";
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,false);

		$data = curl_exec($curl);
		$httpCode1 = curl_getinfo($curl,CURLINFO_HTTP_CODE);
		curl_close($curl);
		$api_result1 = json_decode($data,true);
	
		if($httpCode1 != 200){
			$curl = curl_init();
			$url = $site_URL."modules/CTMobile/api.php";
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,false);

			$data = curl_exec($curl);
			$httpCode2 = curl_getinfo($curl,CURLINFO_HTTP_CODE);
			curl_close($curl);
			$api_result2 = json_decode($data,true);
			if($httpCode2 == 200)
				return true;
			else
				return false;
		}else{
			return true;
		}
	}
	//code end

	static function destroyAllUserSession(){
		global $adb,$current_user;
		$query = "SELECT userid,sessionid FROM ctmobile_userdevicetoken";
		$results = $adb->pquery($query,array());
		for($i=0;$i<$adb->num_rows($results);$i++){
			$sessionid = $adb->query_result($results,$i,'sessionid');
			$userid = $adb->query_result($results,$i,'userid');
			if($sessionid != ''){
				
				$InsertOldSession = $adb->pquery("INSERT INTO ctmobile_session_expire (userid, sessionid) VALUES(?,?)",array($userid,$sessionid));

			 	$update = $adb->pquery("UPDATE ctmobile_userdevicetoken SET sessionid = ? WHERE userid = ?", array('', $userid));
			}
		}
	}

	static function getAddressRecords($module,$filterid){
		global $adb;
		$listViewModel = Vtiger_ListView_Model::getInstance($module, $filterid);
		$query = $listViewModel->getQuery();
	
		$ModuleModel =Vtiger_Module_Model::getInstance($module);
		$basetable = $ModuleModel->get('basetable');
		$basetableid = $ModuleModel->get('basetableid');
		
		$explodedQuery = explode('WHERE',$query);

		$newQuery = $explodedQuery[0]." INNER JOIN ct_address_lat_long ON ct_address_lat_long.recordid = $basetable.$basetableid WHERE ".$explodedQuery[1]."
						  AND ct_address_lat_long.latitude IS NOT NULL AND ct_address_lat_long.longitude IS NOT NULL ";
		$result  = $adb->pquery($newQuery,array());
		$noOfRecords = $adb->num_rows($result);				  
		return $noOfRecords;
	}

	static function getNonAddressRecords($module,$filterid){
		global $adb;
		$listViewModel = Vtiger_ListView_Model::getInstance($module, $filterid);
		$query = $listViewModel->getQuery();
	
		$ModuleModel =Vtiger_Module_Model::getInstance($module);
		$basetable = $ModuleModel->get('basetable');
		$basetableid = $ModuleModel->get('basetableid');
		
		$explodedQuery = explode('WHERE',$query);

		$newQuery = $explodedQuery[0]." INNER JOIN ct_address_lat_long ON ct_address_lat_long.recordid = $basetable.$basetableid WHERE ".$explodedQuery[1]."
						  AND ct_address_lat_long.latitude IS  NULL AND ct_address_lat_long.longitude IS  NULL ";
		$result  = $adb->pquery($newQuery,array());
		$noOfRecords = $adb->num_rows($result);				  
		return $noOfRecords;				  
		
	}

	static function getNonAddressRecordsList($module,$filterid){
		global $adb;
		$listViewModel = Vtiger_ListView_Model::getInstance($module, $filterid);
		$query = $listViewModel->getQuery();
	
		$ModuleModel =Vtiger_Module_Model::getInstance($module);
		$basetable = $ModuleModel->get('basetable');
		$basetableid = $ModuleModel->get('basetableid');
		
		$NameFields = $ModuleModel->getNameFields();
		
		$result  = $adb->pquery($query,array());
		$noOfRecords = $adb->num_rows($result);
		$nonAddressData = array();
		for($i=0;$i<$noOfRecords;$i++){
			$rawData = $adb->query_result_rowdata($result,$i);
			$id = $rawData[$basetableid];
			$label = ""; 
			foreach($NameFields as $field){
				if($rawData[$field]){
					$label = $label.' '.$rawData[$field];
				}
			}
			$newquery = $adb->pquery("SELECT * FROM ct_address_lat_long WHERE recordid = ?",array($id));
			$noRow = $adb->num_rows($newquery);
			if($noRow > 0){
				$latitude = $adb->query_result($newquery,0,'latitude');
				$longitude = $adb->query_result($newquery,0,'longitude');
				if($latitude == '' && $longitude == ''){
					$nonAddressData[] = array('id'=>$id,'label'=>$label);
				}
			}else{
				$nonAddressData[] = array('id'=>$id,'label'=>$label);
			}
		}				  
		return $nonAddressData;				  
		
	}


	static function getAllSection(){
		global $adb;
		$query = "SELECT * FROM ctmobile_language_section";
		$result = $adb->pquery($query,array());
		$numRows = $adb->num_rows($result);
		$section = array();
		if($numRows){
			for($i=0;$i<$numRows;$i++){
				$sectionid = $adb->query_result($result,$i,'section_id');
				$sectionname = $adb->query_result($result,$i,'section_name');
				$section[$sectionid] = $sectionname;
			}
		}
		return $section;
	}

	static function getLanguageFields($language,$section_id){
		global $adb;
		$query = "SELECT * FROM ctmobile_language_keyword WHERE keyword_lang = ?  AND sectionid = ?";
		$results = $adb->pquery($query,array($language,$section_id));
		$numRows = $adb->num_rows($results);
		$fields = array();
		if($numRows){
			for($i=0;$i<$numRows;$i++){
				$keyword_id = $adb->query_result($results,$i,'keyword_id');
				$keyword_name = $adb->query_result($results,$i,'keyword_name');
				$language_keyword = $adb->query_result($results,$i,'language_keyword');
				$fields[] = array('keyword_id'=>$keyword_id,'keyword_name'=>$keyword_name,'language_keyword'=>$language_keyword);
			}
		}
		return $fields;
	}

	/*Time Tracking Module Setting Starts*/
	public function getTimeTrackerModules(){
		global $adb;
		$getTimeTrackingQuery = $adb->pquery("SELECT * FROM ctmobile_timetracking_modules");
		$noofTimeTrackingRows = $adb->num_rows($getTimeTrackingQuery);
		$moduleList = array();
		for ($i=0; $i <$noofTimeTrackingRows ; $i++) {
			$moduleName = $adb->query_result($getTimeTrackingQuery,$i,'module');
			$moduleList[] = $moduleName;
		}
		return $moduleList;
	}
	/*Time Tracking Module Setting Ends*/

	/*Route Distance Unit Setting Starts*/
	static function getRouteGeneralSettings(){
		global $adb;
		$getRouteDistanceUnitQuery = $adb->pquery("SELECT route_distance_unit,route_users FROM ctmobile_routegeneralsettings");
		$noofRouteDistanceUnitRows = $adb->num_rows($getRouteDistanceUnitQuery);
		$distance_unit = '';
		$route_users = array();
		if($noofRouteDistanceUnitRows){
			$distance_unit = $adb->query_result($getRouteDistanceUnitQuery,0,'route_distance_unit');
			$route_users = explode(',',$adb->query_result($getRouteDistanceUnitQuery,0,'route_users'));
		}
		$data['distance_unit'] = $distance_unit;
		$data['route_users'] = $route_users;
		return $data;
	}

	static function getRouteStatusFields(){
		global $adb;
		$getRouteStatusQuery = $adb->pquery("SELECT * FROM ctmobile_routestatus");
		$noofRouteStatusRows = $adb->num_rows($getRouteStatusQuery);
		$routeStatus = array();
		if($noofRouteStatusRows){ 
			for($i=0;$i<$noofRouteStatusRows;$i++){
				$routestatusid = $adb->query_result($getRouteStatusQuery,$i,'routestatusid');
				$routestatusname = decode_html(decode_html($adb->query_result($getRouteStatusQuery,$i,'routestatusname')));
				$routestatuslabel = decode_html(decode_html($adb->query_result($getRouteStatusQuery,$i,'routestatuslabel')));
				$routeStatus[] = array('routestatusid'=>$routestatusid,'routestatusname'=>$routestatusname,'routestatuslabel'=>$routestatuslabel);
			}
		}
		return $routeStatus;
	}
	/*Route Distance Unit Setting Ends*/
}
