<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
include_once 'vtlib/Vtiger/Module.php';

class CTMobileSettings_SaveTimeTrackerSettings_Action extends Vtiger_Save_Action {

    public function process(Vtiger_Request $request) {
        global $adb;
        $moduleLists = $request->get("moduleList");

        $getTimeTrackerQuery = $adb->pquery("SELECT * FROM ctmobile_timetracking_modules");
        $timeTrackerArray = array();
        for ($i=0; $i < $adb->num_rows($getTimeTrackerQuery); $i++) {
            $prevModule = $adb->query_result($getTimeTrackerQuery,$i,'module');
            //$timeTrackerArray[] = $prevModule;
            if(!in_array($prevModule, $moduleLists)){
                $CTTimeTrackerModuleModel = Vtiger_Module_Model::getInstance('CTTimeTracker');
                $fieldModel = Vtiger_Field::getInstance('related_to', $CTTimeTrackerModuleModel);
                if ($fieldModel) {
                     $fieldModel->unsetRelatedModules(array($prevModule));
                }
                $result = $adb->pquery('SELECT 1 FROM vtiger_relatedlists where tabid=? AND relationfieldid=? AND related_tabid=? AND presence = 0', array(getTabid($prevModule), $fieldModel->id, getTabid('CTTimeTracker')));
                if(($adb->num_rows($result))) {
                    $RelatedModuleModel = Vtiger_Module_Model::getInstance($prevModule);
                    $RelatedModuleModel->unsetRelatedList($CTTimeTrackerModuleModel, 'TimeTracker','get_dependents_list');
                }
            }
        }
        // Clear data

        $adb->pquery("TRUNCATE ctmobile_timetracking_modules");
        // Save selected fields
        if(is_array($moduleLists)) {
            foreach($moduleLists as $key => $moduleName) {
                $adb->pquery("INSERT INTO ctmobile_timetracking_modules (module) VALUES (?)",array($moduleName));

                $CTTimeTrackerModuleModel = Vtiger_Module_Model::getInstance('CTTimeTracker');
                $fieldModel = Vtiger_Field::getInstance('related_to', $CTTimeTrackerModuleModel);
                if ($fieldModel) {
                    $fieldModel->setRelatedModules(array($moduleName));
                    $result = $adb->pquery('SELECT 1 FROM vtiger_relatedlists where tabid=? AND relationfieldid=? AND related_tabid=? AND presence = 0', array(getTabid($moduleName), $fieldModel->id, getTabid('CTTimeTracker')));
                    if (!($adb->num_rows($result))) {
                        $RelatedModuleModel = Vtiger_Module_Model::getInstance($moduleName);
                        $RelatedModuleModel->setRelatedList($CTTimeTrackerModuleModel, 'TimeTracker', array(), 'get_dependents_list', $fieldModel->id);
                    }
                }
            }
        }
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        //$response->setResult(array('_module'=>));
        $response->emit();
    } 
}

