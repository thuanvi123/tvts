<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

class CTMobile_WS_MenuEditor extends CTMobile_WS_Controller {
	
	function process(CTMobile_API_Request $request) {
		global $adb,$current_user; // Required for vtws_update API
		$current_user = $this->getActiveUser();
		$mode = $request->get('mode');
		$response = new CTMobile_API_Response();
		if($mode == 'removeModule'){
			$this->removeModule($request);
			$message =  $this->CTTranslate('Module Removed Successfully');
			$result =  array('message' => $message);
		}else if($mode == 'addModule'){
			$this->addModule($request);
			$message =  $this->CTTranslate('Module Added Successfully');
			$result =  array('message' => $message);
		}else if($mode == 'saveSequence'){
			$this->saveSequence($request);
			$message =  $this->CTTranslate('Module sequence saved Successfully');
			$result =  array('message' => $message);
		}else if($mode == 'getHiddenModules'){
			$appName = $request->get('tab_name');
			$modules = Settings_MenuEditor_Module_Model::getHiddenModulesForApp($appName);
			$hiddenModules = array();
			foreach ($modules as $key => $module) {
				$hiddenModules[] = array("ModuleName"=>$module,"ModuleLabel"=>vtranslate($module,$module));
			}
			$result =  array('tab_name'=>$appName,'modules' => $hiddenModules);
		}else if($mode == 'getVisibleModules'){
			//$tabs = Settings_MenuEditor_Module_Model::getAllVisibleModules();

			$tabs = array();
			$presence = array('0', '2');
			$db = PearDatabase::getInstance();
			$result = $db->pquery('SELECT * FROM vtiger_app2tab ORDER BY appname,sequence', array());
			$count = $db->num_rows($result);
			$userPrivModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
			if ($count > 0) {
				for ($i = 0; $i < $count; $i++) {
					$tabid = $db->query_result($result, $i, 'tabid');
					$moduleName = getTabModuleName($tabid);
					$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
					if (empty($moduleModel)) {
						continue;
					}
					$visible = $db->query_result($result, $i, 'visible');
					$sequence = $db->query_result($result, $i, 'sequence');
					$appname = $db->query_result($result, $i, 'appname');
					$moduleModel->set('app2tab_sequence', $sequence);
					if (($userPrivModel->isAdminUser() ||
							$userPrivModel->hasGlobalReadPermission() ||
							$userPrivModel->hasModulePermission($moduleModel->getId())) && in_array($moduleModel->get('presence'), $presence)) {
						$moduleModel->set('visible',$visible);
						$tabs[$appname][$moduleName] = $moduleModel;
					}
				}
			}
			$visibleModules = array();
			foreach ($tabs as $tab_name => $modules) {
				$AllModules = array();
				foreach ($modules as $key => $module) {
					$AllModules[] = array("ModuleName"=>$module->getName(),"ModuleLabel"=>vtranslate($module->getName(),$module->getName()),'module_icon'=>CTMobile_WS_Utils::getModuleURL($module->getName()),'visible'=>$module->get('visible'));
				}
				$visibleModules[] = array('tab_name'=>$tab_name,'modules'=>$AllModules);
			}
			$results =  $visibleModules;
		}
		$response->setResult($results);
		return $response;
	}


	function removeModule($request) {
		$sourceModule = $request->get('sourceModule');
		$appName = $request->get('tab_name');
		$db = PearDatabase::getInstance();
		$db->pquery('UPDATE vtiger_app2tab SET visible = ? WHERE tabid = ? AND appname = ?', array(0, getTabid($sourceModule), $appName));
	}

	function addModule($request) {
		$sourceModule = $request->get('sourceModule');
		$appName = $request->get('tab_name');
		$db = PearDatabase::getInstance();
		$db->pquery('UPDATE vtiger_app2tab SET visible = ? WHERE tabid = ? AND appname = ?', array(1, getTabid($sourceModule), $appName));

	}

	function saveSequence($request) {
		$moduleSequence = Zend_Json::decode($request->get('sequence'));
		$appName = $request->get('tab_name');
		$db = PearDatabase::getInstance();
		foreach ($moduleSequence as $moduleName => $sequence) {
			$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
			$db->pquery('UPDATE vtiger_app2tab SET sequence = ? WHERE tabid = ? AND appname = ?', array($sequence, $moduleModel->getId(), $appName));
		}
	}
}
