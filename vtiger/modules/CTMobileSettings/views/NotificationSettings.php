<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

class CTMobileSettings_NotificationSettings_View extends Settings_Vtiger_Index_View {

    function __construct() {
        parent::__construct();
    }

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

    public function preProcess(Vtiger_Request $request) {
        parent::preProcess($request);        
    }

    public function process(Vtiger_Request $request) {
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $module = $request->getModule();
        $adb = PearDatabase::getInstance();
        $mode = $request->getMode();
        $selectedNotification = array();
        $selectedNotificationTitleMessage = array();
        $result = $adb->pquery("SELECT * FROM ctmobile_notification_settings",array());
        $countchecked = 0;
        $reminder_time = 0;
        $task_reminder_time = 0;
        for ($i=0; $i < $adb->num_rows($result); $i++) { 
            $notification_type = $adb->query_result($result,$i,'notification_type');
            $notification_title = decode_html(decode_html($adb->query_result($result,$i,'notification_title')));
            $notification_message = decode_html(decode_html($adb->query_result($result,$i,'notification_message')));
            if($notification_type == 'event_reminder'){
                $reminder_time = $adb->query_result($result,$i,'reminder_time');
            }
            if($notification_type == 'task_reminder'){
                $task_reminder_time = $adb->query_result($result,$i,'reminder_time');
            }
            $notification_enabled = $adb->query_result($result,$i,'notification_enabled');
            $selectedNotification[$notification_type] = $notification_enabled;
            $selectedNotificationTitleMessage[$notification_type]['notification_title'] = $notification_title;
            $selectedNotificationTitleMessage[$notification_type]['notification_message'] = $notification_message;
            if($notification_enabled){
                $countchecked++;
            }
        }

        $allFieldoptions1 = $this->getAllModuleEmailTemplateFields('Events');
        $allFieldoptions2 = $this->getAllModuleEmailTemplateFields('Calendar');
        $allFieldoptions3 = $this->getAllModuleEmailTemplateFields('ModComments');

     

        if($countchecked > 0){
            $allow_notification_settings = true;
        }else{
            $allow_notification_settings =  false;
        }
        $viewer = $this->getViewer($request);
        $viewer->assign('ALLOW_NOTIFICATION_SETTINGS', $allow_notification_settings);
        $viewer->assign('SELECTED_NOTIFICATION', $selectedNotification);
        $viewer->assign('SELECTED_NOTIFICATION_TITLE_MESSAGE', $selectedNotificationTitleMessage);

        $allModules = Settings_Workflows_Module_Model::getSupportedModules();
        foreach ($allModules as $tabid => $moduleModel) {
            if(in_array($moduleModel->getName(),array('CTMobileSettings','CTMobile','CTAttendance','CTPushNotification','CTUserFilterView','CTTimeTracker','CTMessageTemplate','CTRouteAttendance','CTRoutePlanning','CTTimeControl','SMSNotifier','ModComments','PBXManager','MailManager','Emails'))){
                unset($allModules[$tabid]);
            }
        }
        $AssignedRecordModules = $this->getAssignedRecordModules();
        $AssignedRecordCommentsModules = $this->getAssignedRecordCommentsModules();
        $followRecordModules = $this->getFollowRecordModules();
        $viewer->assign('ALL_MODULE', $allModules);
        $viewer->assign('ASSIGNED_RECORD_MODULES', $AssignedRecordModules);
        $viewer->assign('ASSIGNED_RECORD_COMMENTS_MODULES', $AssignedRecordCommentsModules);
        $viewer->assign('FOLLOW_RECORD_MODULES', $followRecordModules);
        $viewer->assign('REMINDER_VALUES', $reminder_time);
        $viewer->assign('TASK_REMINDER_VALUES', $task_reminder_time);
        $viewer->assign('EVENTS_FIELD_OPTIONS',$allFieldoptions1);
        $viewer->assign('TASK_FIELD_OPTIONS',$allFieldoptions2);
        $viewer->assign('COMMENTS_FIELD_OPTIONS',$allFieldoptions3);
        echo $viewer->view('CTMobileNotificationSettings.tpl',$module,true);
    }

    public function getAssignedRecordModules(){
        global $adb;
        $getAssignedRecordModulesQuery = $adb->pquery("SELECT * FROM ctmobile_notification_module_settings INNER JOIN ctmobile_notification_settings ON ctmobile_notification_settings.notification_id =  ctmobile_notification_module_settings.notification_id WHERE ctmobile_notification_settings.notification_type = 'record_assigned'",array());
        $noofTAssignedRecordModulesRows = $adb->num_rows($getAssignedRecordModulesQuery);
        $moduleList = array();
        for ($i=0; $i <$noofTAssignedRecordModulesRows ; $i++) {
            $moduleName = $adb->query_result($getAssignedRecordModulesQuery,$i,'modulename');
            $moduleList[] = $moduleName;
        }
        return $moduleList;
    }

    public function getAssignedRecordCommentsModules(){
        global $adb;
        $getAssignedRecordCommentQuery = $adb->pquery("SELECT * FROM ctmobile_notification_module_settings INNER JOIN ctmobile_notification_settings ON ctmobile_notification_settings.notification_id =  ctmobile_notification_module_settings.notification_id WHERE ctmobile_notification_settings.notification_type = 'comment_assigned'",array());
        $noofTAssignedRecordCommentRows = $adb->num_rows($getAssignedRecordCommentQuery);
        $moduleList = array();
        for ($i=0; $i <$noofTAssignedRecordCommentRows ; $i++) {
            $moduleName = $adb->query_result($getAssignedRecordCommentQuery,$i,'modulename');
            $moduleList[] = $moduleName;
        }
        return $moduleList;
    }

    public function getFollowRecordModules(){
        global $adb;
        $getAssignedRecordCommentQuery = $adb->pquery("SELECT * FROM ctmobile_notification_module_settings INNER JOIN ctmobile_notification_settings ON ctmobile_notification_settings.notification_id =  ctmobile_notification_module_settings.notification_id WHERE ctmobile_notification_settings.notification_type = 'follow_record'",array());
        $noofTAssignedRecordCommentRows = $adb->num_rows($getAssignedRecordCommentQuery);
        $moduleList = array();
        for ($i=0; $i <$noofTAssignedRecordCommentRows ; $i++) {
            $moduleName = $adb->query_result($getAssignedRecordCommentQuery,$i,'modulename');
            $moduleList[] = $moduleName;
        }
        return $moduleList;
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
            "modules.CTMobileSettings.resources.NotificationSettings",
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

    public function getAllModuleEmailTemplateFields($module) {
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $allRelFields = array();
        
        $fieldList = $this->getRelatedFields($module, $currentUserModel);
        
        $allFields = array();
        foreach ($fieldList as $key => $field) {
            $option = array(vtranslate($field['module'], $field['module']) . ':' . vtranslate($field['fieldlabel'], $field['module']), "$" . strtolower($field['module']) . "-" . $field['columnname'] . "$");
            $allFields[] = $option;
            if (!empty($field['referencelist'])) {
                foreach ($field['referencelist'] as $referenceList) {
                    foreach($referenceList as $key => $relField) {
                    $relOption = array(vtranslate($field['fieldlabel'], $field['module']) . ':' . '(' . vtranslate($relField['module'], $relField['module']) . ')' . vtranslate($relField['fieldlabel'],$relField['module']), "$" . strtolower($field['module']) . "-" . $field['columnname'] . ":" . $relField['columnname'] . "$");
                    $allRelFields[] = $relOption;
                }
            }
        }
        }
        if(is_array($allFields) && is_array($allRelFields)){
            $allFields = array_merge($allFields, $allRelFields);
            $allRelFields= array();
        }
        return $allFields;
    }

    function getRelatedFields($module, $currentUserModel) {
        $handler = vtws_getModuleHandlerFromName($module, $currentUserModel);
        $meta = $handler->getMeta();
        $moduleFields = $meta->getModuleFields();
        $db = PearDatabase::getInstance();
        //adding record id merge tag option 
        $fieldInfo = array('columnname' => 'id','fieldname' => 'id','fieldlabel' =>vtranslate('LBL_RECORD_ID', $module));
        $recordIdField = WebserviceField::fromArray($db, $fieldInfo);
        $moduleFields[$recordIdField->getFieldName()] = $recordIdField;

        $returnData = array();
        foreach ($moduleFields as $key => $field) {
            if(!in_array($field->getPresence(), array(0,2))){
                continue;
            }
            $referencelist = array();
            $relatedField = $field->getReferenceList();
            if ($field->getFieldName() == 'assigned_user_id') {
                $relModule = 'Users';
                $referencelist[] = $this->getRelatedModuleFieldList($relModule, $currentUserModel);
            }
            if (!empty($relatedField)) {
                foreach ($relatedField as $ind => $relModule) {
                    $referencelist[] = $this->getRelatedModuleFieldList($relModule, $currentUserModel);
                }
            }
            $returnData[] = array('module' => $module, 'fieldname' => $field->getFieldName(), 'columnname' => $field->getColumnName(), 'fieldlabel' => $field->getFieldLabelKey(), 'referencelist' => $referencelist);
        }
        return $returnData;
    }

    function getRelatedModuleFieldList($relModule, $user) {
        $handler = vtws_getModuleHandlerFromName($relModule, $user);
        $relMeta = $handler->getMeta();
        if (!$relMeta->isModuleEntity()) {
            return array();
        }
        $relModuleFields = $relMeta->getModuleFields();
        $relModuleFieldList = array();
        foreach ($relModuleFields as $relind => $relModuleField) {
            if(!in_array($relModuleField->getPresence(), array(0,2))){
                continue;
            }
            if($relModule == 'Users') {
                if(in_array($relModuleField->getFieldDataType(),array('string','phone','email','text'))) {
                    $skipFields = array(98,115,116,31,32);
                    if(!in_array($relModuleField->getUIType(), $skipFields) && $relModuleField->getFieldName() != 'asterisk_extension'){
                        $relModuleFieldList[] = array('module' => $relModule, 'fieldname' => $relModuleField->getFieldName(), 'columnname' => $relModuleField->getColumnName(), 'fieldlabel' => $relModuleField->getFieldLabelKey());
                    }
                }
            } else {
                $relModuleFieldList[] = array('module' => $relModule, 'fieldname' => $relModuleField->getFieldName(), 'columnname' => $relModuleField->getColumnName(), 'fieldlabel' => $relModuleField->getFieldLabelKey());
            }
        }
        return $relModuleFieldList;
    }

}
