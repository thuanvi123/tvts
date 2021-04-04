<?php

 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

class CTMobile_WS_FetchModuleOwners extends CTMobile_WS_Controller {

    function process(CTMobile_API_Request $request) {
        global $current_user;
        
        $response = new CTMobile_API_Response();
        $current_user = $this->getActiveUser();
        
        $currentUserModel = Users_Record_Model::getInstanceFromUserObject($current_user);
        
        $moduleName = trim($request->get('module'));
        $users = $this->getUsers($currentUserModel, $moduleName);
        $groups = $this->getGroups($currentUserModel, $moduleName);
        
        $result = array('users' => $users, 'groups' => $groups);
        $response->setResult($result);
        
        return $response;
    }

    function getUsers($currentUserModel, $moduleName) {
        $users = $currentUserModel->getAccessibleUsersForModule($moduleName);
        $userIds = array_keys($users);
        $usersList = array();
        $usersWSId = CTMobile_WS_Utils::getEntityModuleWSId('Users');
        foreach ($userIds as $userId) {
            $userRecord = Users_Record_Model::getInstanceById($userId, 'Users');
            $usersList[] = array('value' => $usersWSId . 'x' . $userId,
                                 'label' => $userRecord->get("first_name") . ' ' . $userRecord->get('last_name')
                                );
        }
        return $usersList;
    }

    function getGroups($currentUserModel, $moduleName) {
        $groups = $currentUserModel->getAccessibleGroupForModule($moduleName);
        $groupIds = array_keys($groups);
        $groupsList = array();
        $groupsWSId = CTMobile_WS_Utils::getEntityModuleWSId('Groups');
        foreach ($groupIds as $groupId) {
            $groupName = getGroupName($groupId);
            $groupsList[] = array('value' => $groupsWSId . 'x' . $groupId,
                                  'label' => $groupName[0]
                                 );
        }
        return $groupsList;
    }
}

