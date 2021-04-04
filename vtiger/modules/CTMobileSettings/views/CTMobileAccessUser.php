<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

class CTMobileSettings_CTMobileAccessUser_View extends Settings_Vtiger_Index_View {

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
        $viewer->assign('USER_MODEL', $userArray);
        $viewer->assign('GROUPS_MODEL', $groupsArray);
		
		$selected = $adb->pquery("SELECT * FROM ctmobile_access_users",array());
		$selectedUsers = array();
		for($i=0;$i<($adb->num_rows($selected));$i++){
			$selectedUsers[] = $adb->query_result($selected,$i,'userid');
		}
        $viewer->assign('SELECTED_FIELDS', $selectedUsers);
        echo $viewer->view('CTMobileAccessUser.tpl',$module,true); 
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
            "modules.CTMobileSettings.resources.CTMobileAccessUser",
        );

        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }
}
