<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

class CTMobileSettings_UserSettings_View extends Settings_Vtiger_Index_View {

    public function checkPermission(Vtiger_Request $request) {
        $license_data = CTMobileSettings_Module_Model::getLicenseData();
        if(strtolower($license_data['Plan']) === 'free'){
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED', 'Vtiger'));
        }else{
            $currentUserModel = Users_Record_Model::getCurrentUserModel();
            if(!$currentUserModel->isAdminUser()) {
                throw new AppException(vtranslate('LBL_PERMISSION_DENIED', 'Vtiger'));
            }
        }
    }

    public function process(Vtiger_Request $request) {
        $adb = PearDatabase::getInstance();
        $module = $request->getModule();
        $viewer = $this->getViewer($request);
        $viewer->assign('MODULES', $module);
        
        $users = Users_Record_Model::getAll();
        $userArray =  array();
        foreach($users as $key => $value){
            $name = $value->get('first_name').' '.$value->get('last_name');
            $userArray[] = array('userid'=>$key,'username'=>$name);
        }
        $UsersModel = Users_Record_Model::getCurrentUserModel();
        $groups = $UsersModel->getAccessibleGroups();
        $groupsArray = array();
        foreach ($groups as $key => $value) {
            $groupsArray[] = array('userid'=>$key,'username'=>$value);
        }
        $viewer->assign('ACCESS_USER_MODEL', $userArray);
        $viewer->assign('ACCESS_GROUPS_MODEL', $groupsArray);
		
		$selected = $adb->pquery("SELECT * FROM ctmobile_access_users",array());
		$selectedUsers = array();
		for($i=0;$i<($adb->num_rows($selected));$i++){
			$selectedUsers[] = $adb->query_result($selected,$i,'userid');
		}
        $viewer->assign('ACCESS_SELECTED_FIELDS', $selectedUsers);

        $users = CTMobileSettings_Module_Model::getCTRouteUser();
        $viewer->assign('ROUTE_USER', $users);
        $Users =$adb->pquery("SELECT * FROM `vtiger_users` WHERE deleted = 0 AND status = ?",array('Active'));
        $Api = '';
        $userArray = array();
        for($i=0;$i<($adb->num_rows($Users));$i++){
            $id = $adb->query_result($Users,$i,'id');
            $name = $adb->query_result($Users,$i,'first_name').' '.$adb->query_result($Users,$i,'last_name');
            $userArray[] = array('userid'=>$id,'username'=>$name);
        }
        
        $selected = $adb->pquery("SELECT * FROM ctmobile_livetracking_users",array());
        $selectedUsers = array();
        for($i=0;$i<($adb->num_rows($selected));$i++){
            $selectedUsers[] = $adb->query_result($selected,$i,'userid');
        }
        $viewer->assign('LIVE_USER_MODEL', $userArray);
        $viewer->assign('LIVE_SELECTED_FIELDS', $selectedUsers);

        $generalSettings = CTMobileSettings_Module_Model::getRouteGeneralSettings();
        $Users =$adb->pquery("SELECT * FROM `vtiger_users` WHERE deleted = 0 AND status = ?",array('Active'));
        $userArray = array();
        for($i=0;$i<($adb->num_rows($Users));$i++){
            $id = $adb->query_result($Users,$i,'id');
            $name = $adb->query_result($Users,$i,'first_name').' '.$adb->query_result($Users,$i,'last_name');
            $userArray[] = array('userid'=>$id,'username'=>$name);
        }
        $routeStatus = CTMobileSettings_Module_Model::getRouteStatusFields();
        $viewer->assign('DISTANCE_UNIT', $generalSettings['distance_unit']);
        $viewer->assign('ROUTE_USERS', $generalSettings['route_users']);
        $viewer->assign('USER_MODEL', $userArray);
        $viewer->assign('ROUTE_STATUS', $routeStatus);

        $timetracking_allModules = Settings_Workflows_Module_Model::getSupportedModules();
        foreach ($timetracking_allModules as $tabid => $moduleModel) {
            if(in_array($moduleModel->getName(),array('CTMobileSettings','CTMobile','CTAttendance','CTPushNotification','CTUserFilterView','CTTimeTracker','CTMessageTemplate','CTRouteAttendance','CTRoutePlanning','CTTimeControl','SMSNotifier','ModComments','PBXManager','MailManager','Emails'))){
                unset($timetracking_allModules[$tabid]);
            }
        }
        $timeTrackerModules = CTMobileSettings_Module_Model::getTimeTrackerModules();
        $viewer->assign('TIMETRACKING_ALL_MODULE', $timetracking_allModules);
        $viewer->assign('TIMETRACKEMODULES', $timeTrackerModules);

        //calllog users code start
        $selectedCalllog = $adb->pquery("SELECT * FROM ctmobile_calllog_users",array());
        $selectedCalllogUsers = array();
        for($i=0;$i<($adb->num_rows($selectedCalllog));$i++){
            $selectedCalllogUsers[] = $adb->query_result($selectedCalllog,$i,'userid');
        }
        $viewer->assign('SELECTED_CALLLOG_USERS', $selectedCalllogUsers);
        //code end

        //check auto create for activity for calllog users - by sapna
        $autoActivityCreate =0;
        $SQL = $adb->pquery("SELECT * FROM `ctmobile_calllog_autoactivity`",array());
        if($adb->num_rows($SQL) > 0){
            $autoActivityCreate = $adb->query_result($SQL,'isAutoActivityCreate');
        }
        $viewer->assign('AUTOCREATEACTIVITY', $autoActivityCreate);

        $selectedUserSettings = array();
        $result = $adb->pquery("SELECT * FROM ctmobile_user_settings",array());
        for ($i=0; $i < $adb->num_rows($result); $i++) { 
            $user_setting_type = $adb->query_result($result,$i,'user_setting_type');
            $user_setting_value = $adb->query_result($result,$i,'user_setting_value');
            $selectedUserSettings[$user_setting_type] = $user_setting_value;
        }
        $viewer->assign('SELECTED_USER_SETTINGS', $selectedUserSettings);

        $ModuleManageMentModules = array();
        $MResult = $adb->pquery("SELECT module FROM ctmobile_modules_management",array());
        for ($i=0; $i < $adb->num_rows($MResult); $i++) { 
            $ModuleManageMentModules[] = $adb->query_result($MResult,$i,'module');
        }
        $viewer->assign('MODULE_MANAGEMENT_MODULES', $ModuleManageMentModules);

        $selectedNotification = array();
        $result = $adb->pquery("SELECT feature_name,feature_enabled FROM ctmobile_premium_feature",array());
        for ($i=0; $i < $adb->num_rows($result); $i++) { 
            $feature_name = $adb->query_result($result,$i,'feature_name');
            $feature_enabled = $adb->query_result($result,$i,'feature_enabled');
            $selectedNotification[$feature_name] = $feature_enabled;
        }
        $viewer->assign('SELECTED_FEATURE', $selectedNotification);

        echo $viewer->view('CTMobileUserSettings.tpl',$module,true); 
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
            "modules.CTMobileSettings.resources.UserSettings",
            '~/libraries/jquery/bootstrapswitch/js/bootstrap-switch.min.js'
        );

        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }
    
    public function getHeaderCss(Vtiger_Request $request) {
        $headerCssInstances = parent::getHeaderCss($request);
        $cssFileNames = array(
            '~/libraries/jquery/bootstrapswitch/css/bootstrap3/bootstrap-switch.min.css'
        );
        $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
        $headerCssInstances = array_merge($headerCssInstances, $cssInstances);
        return $headerCssInstances;
    }
}
