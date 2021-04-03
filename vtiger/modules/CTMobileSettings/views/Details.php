<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

class CTMobileSettings_Details_View extends Settings_Vtiger_Index_View {

	public function checkPermission(Vtiger_Request $request) {
		return true;
	}

    public function process(Vtiger_Request $request) {
		$mode = $request->get('mode');
		if($mode){
			$this->$mode($request);
		}else{
			$count = CTMobileSettings_Module_Model::GetRequirement();
			if($count > 0){
				$this->step1($request);
			}else{
				global $adb;
				$getLicenseQuery=$adb->pquery("SELECT * FROM ctmobile_license_settings");
				$numOfLicense = $adb->num_rows($getLicenseQuery);
				if($numOfLicense > 0){
					$this->Details($request);
				}else{
					$licenseUrl = CTMobileSettings_Module_Model::$CTMOBILE_LICENSE_DETAILVIEW_URL;
					header("location:$licenseUrl");
				}
			}
		}
       
    }   
    
    function Details(Vtiger_Request $request){
		global $adb;
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
        $module = $request->getModule();
        $viewer = $this->getViewer($request);
        $viewer->assign('MODULES', $module);
        $REQUIREMENTS_DATA = CTMobileSettings_Module_Model::getCTRequirements();
        $viewer->assign('CT_REQUIREMENTS', $REQUIREMENTS_DATA['requirements']);
		$viewer->assign('CT_REQUIREMENTS_DATA', $REQUIREMENTS_DATA['arrFinalError']);											 
        $viewer->assign('LICENSE_DATA', CTMobileSettings_Module_Model::getLicenseData());
        $users = CTMobileSettings_Module_Model::getCTRouteUser();
        $activeuser = CTMobileSettings_Module_Model::getActiveUser();
        $mobileuser = CTMobileSettings_Module_Model::getMobileUser();
        $viewer->assign('ROUTE_USER', $users);
        $viewer->assign('ACTIVE_USER', $activeuser);
        $viewer->assign('MOBILE_USER', $mobileuser);
        $viewer->assign('CURRENT_USER', $currentUserModel);
        
        $meetingRecords = CTMobileSettings_Module_Model::getMeetingCount();
        $checkOutRecords = CTMobileSettings_Module_Model::getCheckOutCount();
        $viewer->assign('MEETING_RECORDS', $meetingRecords);
        $viewer->assign('CHECKOUT_RECORDS', $checkOutRecords);
        
        //for CTAttendance Report url 
        $CTATTENDANCE_URL = "";
        $METTING_ATTENDANCE_URL = "";
        if(getTabid('CTAttendance')){
			$CTAttendanceModuleModel = Vtiger_Module_Model::getInstance('CTAttendance');
			$CTATTENDANCE_URL = $CTAttendanceModuleModel->getListViewUrl();
			
			$query = "SELECT cvid FROM vtiger_customview WHERE viewname='Check in-out Event' AND entitytype = ?";
			$result = $adb->pquery($query, array('CTAttendance'));
			$viewId = $adb->query_result($result, 0, 'cvid');
			$METTING_ATTENDANCE_URL = $CTATTENDANCE_URL.'&viewname='.$viewId;
		}
		$viewer->assign('CTATTENDANCE_URL', $CTATTENDANCE_URL);
		$viewer->assign('METTING_ATTENDANCE_URL', $METTING_ATTENDANCE_URL);



		//for CTAttendance Report url 
		$TIME_TRACKING_LOG_URL = "";
        if(getTabid('CTTimeTracker')){
			$CTAttendanceModuleModel = Vtiger_Module_Model::getInstance('CTTimeTracker');
			$TIME_TRACKING_LOG_URL = $CTAttendanceModuleModel->getListViewUrl();
		}
		$viewer->assign('TIME_TRACKING_LOG_URL', $TIME_TRACKING_LOG_URL);
        
        //for CTPush-Notification list url 
        if(getTabid('CTPushNotification')){
			$CTAttendanceModuleModel = Vtiger_Module_Model::getInstance('CTPushNotification');
			$listViewUrl = $CTAttendanceModuleModel->getListViewUrl();
			$viewer->assign('CTPUSHNOTIFICATION_URL', $listViewUrl);
			$pushnotificationData = CTMobileSettings_Module_Model::pushNotificationData();
			$CTPushNotificationModuleModel = Vtiger_Module_Model::getInstance('CTPushNotification');
			$viewer->assign('CTPUSHNOTIFICATION_MODULEMODEL', $CTPushNotificationModuleModel);
			$viewer->assign('CTPUSHNOTIFICATION_DATA', $pushnotificationData);
		}
        
        $version=$adb->pquery("SELECT * FROM vtiger_tab where name='CTMobileSettings'",array());
        $ver = $adb->query_result($version,0,'version');
        $url = CTMobileSettings_Module_Model::$CTMOBILE_VERSION_URL;
        $ch = curl_init($url);
		$data = array( "vt_version"=>'7.x');
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($ch);
		curl_close($ch);
		$jason_result = json_decode($result);
		$ext_ver = $jason_result->ext_version;
        $viewer->assign('VERSION', $ver);
        $viewer->assign('ext_ver', $ext_ver);
		
		global $root_directory;
		$source2 = $root_directory.'/test/'.$ext_ver.'/CTMobileApi.php';
        $dest2 = $root_directory.'/CTMobileApi.php';

        if (file_exists($dest2)) {
            $file_exist2 = true;
        } else {
            if(copy($source2, $dest2)) {
                $file_exist2 = true;
            }
        }
		
        echo $viewer->view('CTMobileDetails.tpl',$module,true); 
	}
	
	function step1(Vtiger_Request $request){
			global $adb;
			$count = CTMobileSettings_Module_Model::GetRequirement();
			
			if($count > 0){

			}else{
				$getLicenseQuery=$adb->pquery("SELECT * FROM ctmobile_license_settings");
				$numOfLicense = $adb->num_rows($getLicenseQuery);
				if($numOfLicense > 0){
					$this->Details($request);
				}else{
					$licenseUrl = CTMobileSettings_Module_Model::$CTMOBILE_LICENSE_DETAILVIEW_URL;
					header("location:$licenseUrl");
				}
			}
			$module = $request->getModule();
			$viewer = $this->getViewer($request);
			$viewer->assign('QUALIFIED_MODULES', $module);
			$extensions = array();
			if(extension_loaded('zip')){
				$ExtensionsName = "Zip";
				$Extensions_status = 1;
				$install_guide = "sudo apt-get install zip";
				$extensions[] = array('ExtensionsName'=>$ExtensionsName,'Extensions_status'=>$Extensions_status,'install_guide'=>$install_guide);
			}else{
				$ExtensionsName = "Zip";
				$Extensions_status = 0;
				$install_guide = "sudo apt-get install zip";
				$extensions[] = array('ExtensionsName'=>$ExtensionsName,'Extensions_status'=>$Extensions_status,'install_guide'=>$install_guide);
			}
			if(extension_loaded('gd')){
				$ExtensionsName = "GD";
				$Extensions_status = 1;
				$install_guide = "sudo apt-get install php5-gd <br/>sudo service apache2 restart";
				$extensions[] = array('ExtensionsName'=>$ExtensionsName,'Extensions_status'=>$Extensions_status,'install_guide'=>$install_guide);
			}else{
				$ExtensionsName = "GD";
				$Extensions_status = 0;
				$install_guide = "sudo apt-get install php5-gd <br/>sudo service apache2 restart";
				$extensions[] = array('ExtensionsName'=>$ExtensionsName,'Extensions_status'=>$Extensions_status,'install_guide'=>$install_guide);
			}
			if(extension_loaded('Zlib')){
				$ExtensionsName = "Zlib";
				$Extensions_status = 1;
				$install_guide = "https://www.digitalocean.com/community/questions/php-7-0-ziparchive-library-is-missing-or-disabled";
				$extensions[] = array('ExtensionsName'=>$ExtensionsName,'Extensions_status'=>$Extensions_status,'install_guide'=>$install_guide);
			}else{
				$ExtensionsName = "Zlib";
				$Extensions_status = 0;
				$install_guide = "https://www.digitalocean.com/community/questions/php-7-0-ziparchive-library-is-missing-or-disabled";
				$extensions[] = array('ExtensionsName'=>$ExtensionsName,'Extensions_status'=>$Extensions_status,'install_guide'=>$install_guide);
			}
			if(extension_loaded('Curl')){
				$ExtensionsName = "Curl";
				$Extensions_status = 1;
				$install_guide = "sudo apt-get install php5-curl";
				$extensions[] = array('ExtensionsName'=>$ExtensionsName,'Extensions_status'=>$Extensions_status,'install_guide'=>$install_guide);
			}else{
				$ExtensionsName = "Curl";
				$Extensions_status = 0;
				$install_guide = "sudo apt-get install php5-curl";
				$extensions[] = array('ExtensionsName'=>$ExtensionsName,'Extensions_status'=>$Extensions_status,'install_guide'=>$install_guide);
			}
			if(extension_loaded('mbstring')){
				$ExtensionsName = "Mbstring";
				$Extensions_status = 1;
				$install_guide = "yum install php-mbstring";
				$extensions[] = array('ExtensionsName'=>$ExtensionsName,'Extensions_status'=>$Extensions_status,'install_guide'=>$install_guide);
			}else{
				$ExtensionsName = "Mbstring";
				$Extensions_status = 0;
				$install_guide = "yum install php-mbstring";
				$extensions[] = array('ExtensionsName'=>$ExtensionsName,'Extensions_status'=>$Extensions_status,'install_guide'=>$install_guide);
			}
			
			$viewer->assign('EXTENSIONS', $extensions);
			
			$viewer->assign('default_socket_timeout', ini_get('default_socket_timeout'));
			$viewer->assign('max_execution_time', ini_get('max_execution_time'));
			$viewer->assign('max_input_time', ini_get('max_input_time'));
			$viewer->assign('memory_limit', str_replace('M','',ini_get('memory_limit')));
			$viewer->assign('post_max_size', str_replace('M','',ini_get('post_max_size')));
			$viewer->assign('upload_max_filesize', str_replace('M','',ini_get('upload_max_filesize')));
			$viewer->assign('max_input_vars', ini_get('max_input_vars'));
			
			echo $viewer->view('Step1.tpl',$module,true); 
	}

   

    /**
     * Function to get the list of Script models to be included
     * @param Vtiger_Request $request
     * @return <Array> - List of Vtiger_JsScript_Model instances
     */
    function getHeaderScripts(Vtiger_Request $request) {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName = $request->getModule();

        $jsFileNames = array(
            "modules.CTMobileSettings.resources.OtherSettings",
        );

        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }
}
