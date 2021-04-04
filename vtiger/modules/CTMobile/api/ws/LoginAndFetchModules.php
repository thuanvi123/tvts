<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
include_once dirname(__FILE__) . '/Login.php';
require_once('include/utils/utils.php'); 

class CTMobile_WS_LoginAndFetchModules extends CTMobile_WS_Login {
	
	function postProcess(CTMobile_API_Response $response) {
		global $current_user;
		$current_user = $this->getActiveUser();
		if ($current_user) {
			$results = $response->getResult();
			$results['modules'] = $this->getAllVisibleModules();

			$userPrivModel = Users_Privileges_Model::getInstanceById($current_user->id);
			$moduleModel = Vtiger_Module_Model::getInstance('MailManager');
			$isMailManager = false;
			$menuModelsList = Vtiger_Menu_Model::getAll(true);
			$presence = array('0', '2');
			if(array_key_exists("MailManager",$menuModelsList) && ($userPrivModel->isAdminUser() ||
								$userPrivModel->hasGlobalReadPermission() ||
								$userPrivModel->hasModulePermission($moduleModel->get('id'))) && in_array($moduleModel->get('presence'), $presence) ){
				$isMailManager = true;
			}

			$moduleModel = Vtiger_Module_Model::getInstance('CTUserFilterView');
			$isCTUserFilterView = false;
			if(($userPrivModel->isAdminUser() ||
								$userPrivModel->hasGlobalReadPermission() ||
								$userPrivModel->hasModulePermission($moduleModel->get('id'))) &&in_array($moduleModel->get('presence'), $presence)){
				$isCTUserFilterView = true;
			}
			$results['isMailManager'] = $isMailManager;
			$results['isCTUserFilterView'] = $isCTUserFilterView; 
			$response->setResult($results);
		}
	}
	

	public function getAllVisibleModules() {
		global $adb;
		$CommentsModule = $ActivitiesModule = $SummaryModule = array();
		$query = "SELECT vtiger_tab.name, vtiger_tab.tabid,vtiger_relatedlists.label FROM vtiger_relatedlists INNER JOIN vtiger_tab ON vtiger_relatedlists.tabid = vtiger_tab.tabid where vtiger_relatedlists.presence = 0 AND vtiger_relatedlists.label IN ('ModComments','Activities')";
		$params = array();
		$result = $adb->pquery($query , $params);
		$numrows = $adb->num_rows($result);
		$CommentsModule = array();
		$ActivitiesModule = array();
		for($i=0;$i<$numrows;$i++){
			$label = $adb->query_result($result,$i,'label');
			if($label == 'ModComments'){
				$CommentsModule[] = $adb->query_result($result,$i,'name');
			}else{
				$ActivitiesModule[] = $adb->query_result($result,$i,'name');
			}
		}

		$AllowedModules = array();
        $MResults = $adb->pquery("SELECT module FROM ctmobile_modules_management",array());
        for ($i=0; $i < $adb->num_rows($MResults); $i++) { 
            $AllowedModules[] = $adb->query_result($MResults,$i,'module');
        }
		
		
		$checkSummarySQL = "SELECT * FROM  `vtiger_tab` WHERE  `isentitytype` =1 AND  `presence` =0";
		$resultCheckSummary = $adb->pquery($checkSummarySQL ,array());
		$numrows = $adb->num_rows($resultCheckSummary);
		$SummaryModule = array();
		for($i=0;$i<$numrows;$i++){
			$Module = $adb->query_result($resultCheckSummary,$i,'name');
			$moduleModel = Vtiger_Module_Model::getInstance($Module); 
			if($moduleModel->isSummaryViewSupported()) {
				$SummaryModule[] = $Module;
			}else{
				continue;
			}
		}
		$inventoryModules = getInventoryModules();
		$current_user = $this->getActiveUser();
		$listresult = vtws_listtypes(null,$current_user);
		$menuModelsList = Vtiger_Menu_Model::getAll(true);
		$newMenuModulesList = array();
		$language = $current_user->language;
		$other_modules = ctTranslate('other_modules',$language);
		$modules[$other_modules]=$modules[vtranslate('LBL_TOOLS','CTMobile')]=$modules[vtranslate('LBL_PROJECT','CTMobile')]=$modules[vtranslate('LBL_SUPPORT','CTMobile')]=$modules[vtranslate('LBL_INVENTORY','Vtiger')]=$modules[vtranslate('LBL_SALES','CTMobile')]=$modules[vtranslate('LBL_MARKETING','CTMobile')]= array();
		$newMenuList = array_keys($modules);
		foreach($newMenuList as $key => $value){
			$newMenuModulesList[$key]['tab_key'] = $key;
			$newMenuModulesList[$key]['tab_name'] = $value;
			$presence = array('0', '2');
			$db = PearDatabase::getInstance();
			$result = $db->pquery('SELECT * FROM vtiger_app2tab WHERE visible = ? ORDER BY appname,sequence', array(1));
			$count = $db->num_rows($result);
			$userPrivModel = Users_Privileges_Model::getInstanceById($current_user->id);
			if ($count > 0) {
				for ($i = 0; $i < $count; $i++) {
					$appname = $db->query_result($result, $i, 'appname');
					if(vtranslate('LBL_'.$appname,'CTMobile') == $value){
					$tabid = $db->query_result($result, $i, 'tabid');
					$sequence = $db->query_result($result, $i, 'sequence');
					$moduleName = getTabModuleName($tabid);
					if(checkModulePermission($moduleName,$AllowedModules)){
						$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
						$restrictedModule = array('CTMobile','Rss','Portal','RecycleBin','ExtensionStore','CTPushNotification','EmailTemplates','CTAttendance','MailManager');
						if (empty($moduleModel))
							continue;
						if (in_array($moduleModel->get('name'),$restrictedModule))
							continue;
						$moduleModel->set('app2tab_sequence', $sequence);
						if (($userPrivModel->isAdminUser() ||
								$userPrivModel->hasGlobalReadPermission() ||
								$userPrivModel->hasModulePermission($moduleModel->getId())) && in_array($moduleModel->get('presence'), $presence)) {
							
							$view = 'List';
							$module = $moduleModel->get('name');
							$ModulesArray = array('SMSNotifier','PBXManager','CTPushNotification','CTCalllog','CTAttendance');
							if(in_array($module,$ModulesArray)){
								$QuickCreateAction = false;
								$editAction = false;
								$createAction = false;
							}else{
								$QuickCreateAction = $moduleModel->isQuickCreateSupported();
								$editAction = $userPrivModel->hasModuleActionPermission($moduleModel->getId(), 'EditView');
								$createAction = $userPrivModel->hasModuleActionPermission($moduleModel->getId(), 'CreateView');
							}
							
							
							
							$singular = vtranslate($moduleModel->get('name'),$module);
							if($appname == ''){
								$appname ='Other Modules';
							}
							$appname = vtranslate('LBL_'.$appname,'CTMobile');
							//allow access false when user type Free
							$restrictedModules = array();
							if(in_array($module,$restrictedModules)){     
								$module_access = false;
							}else{
								$module_access = true;
							}
							if(in_array($module,$CommentsModule)){
								$isCommentModule = true;
							}else{
								$isCommentModule = false;
							}
							if(in_array($module,$ActivitiesModule)){
								$isActivityModule = true;
							}else{
								$isActivityModule = false;
							}
							if(in_array($module,$SummaryModule)){
								$isSummerymodule = true;
							}else{
								$isSummerymodule = false;
							}
							if(in_array($module, $inventoryModules)) {
								$isInventoryModule = true;
							}else{
								$isInventoryModule = false;
							}

							$newMenuModulesList[$key]['modules_list'][] = array(
								'id'=> $moduleModel->get('id'),
								'name' => trim($moduleModel->get('name')),
								'isEntity' => $moduleModel->get('isentitytype'),
								'label' => vtranslate($moduleModel->get('label'),$module),
								'singular' => $singular,
								'parent' => $appname,
								'view' => $view,
								'img_url' => CTMobile_WS_Utils::getModuleURL($moduleModel->get('name')),
								'module_access' => $module_access,
								'createAction' => $createAction,
								'editAction' => $editAction,
								'QuickCreateAction'=>$QuickCreateAction,
								'isCommentModule'=>$isCommentModule,
								'isActivityModule'=>$isActivityModule,
								'isSummerymodule'=>$isSummerymodule,
								'isInventoryModule'=>$isInventoryModule
								);
							}	
						}
					}
				}
				if($value == $other_modules){

					if(checkModulePermission('MailManager',$AllowedModules)){
						if(in_array('MailManager',$CommentsModule)){
							$isCommentModule = true;
						}else{
							$isCommentModule = false;
						}
						if(in_array('MailManager',$ActivitiesModule)){
							$isActivityModule = true;
						}else{
							$isActivityModule = false;
						}
						if(in_array('MailManager',$SummaryModule)){
							$isSummerymodule = true;
						}else{
							$isSummerymodule = false;
						}

						$moduleModel = Vtiger_Module_Model::getInstance('Documents');
						$QuickCreateAction = $moduleModel->isQuickCreateSupported();
						if(array_key_exists("Documents",$menuModelsList) && ($userPrivModel->isAdminUser() ||
									$userPrivModel->hasGlobalReadPermission() ||
									$userPrivModel->hasModulePermission($menuModelsList['Documents']->get('id'))) && in_array($menuModelsList['Documents']->get('presence'), $presence)){
							$editAction = $userPrivModel->hasModuleActionPermission($menuModelsList['Documents']->get('id'), 'EditView');
							$createAction = $userPrivModel->hasModuleActionPermission($menuModelsList['Documents']->get('id'), 'CreateView');
							$newMenuModulesList[$key]['modules_list'][] = array(
							'id'=> $menuModelsList['Documents']->get('id'),
							'name' => $menuModelsList['Documents']->get('name'),
							'isEntity' => $menuModelsList['Documents']->get('isentitytype'),
							'label' => vtranslate($menuModelsList['Documents']->get('label'),'Documents'),
							'singular' => $moduleModel->get('label'),
							'parent' =>  $other_modules,
							'view' => 'List',
							'img_url' =>  CTMobile_WS_Utils::getModuleURL('Documents'),
							'module_access' => true,
							'createAction' => $createAction,
							'editAction' => $editAction,
							'QuickCreateAction'=>$QuickCreateAction,
							'isCommentModule'=>$isCommentModule,
							'isActivityModule'=>$isActivityModule,
							'isSummerymodule'=>$isSummerymodule,
							'isInventoryModule'=>false
							);
						}
					}

					if(checkModulePermission('Calendar',$AllowedModules)){
						if(in_array('Calendar',$CommentsModule)){
							$isCommentModule = true;
						}else{
							$isCommentModule = false;
						}
						if(in_array('Calendar',$ActivitiesModule)){
							$isActivityModule = true;
						}else{
							$isActivityModule = false;
						}
						if(in_array('Calendar',$SummaryModule)){
							$isSummerymodule = true;
						}else{
							$isSummerymodule = false;
						}

						$moduleModel = Vtiger_Module_Model::getInstance('Calendar');
						$QuickCreateAction = $moduleModel->isQuickCreateSupported();
						if(array_key_exists("Calendar",$menuModelsList) && ($userPrivModel->isAdminUser() ||
									$userPrivModel->hasGlobalReadPermission() ||
									$userPrivModel->hasModulePermission($menuModelsList['Calendar']->get('id'))) && in_array($menuModelsList['Calendar']->get('presence'), $presence)){
							$editAction = $userPrivModel->hasModuleActionPermission($menuModelsList['Calendar']->get('id'), 'EditView');
							$createAction = $userPrivModel->hasModuleActionPermission($menuModelsList['Calendar']->get('id'), 'CreateView');
							$newMenuModulesList[$key]['modules_list'][] = array(
							'id'=> $menuModelsList['Calendar']->get('id'),
							'name' => $menuModelsList['Calendar']->get('name'),
							'isEntity' => $menuModelsList['Calendar']->get('isentitytype'),
							'label' => vtranslate($menuModelsList['Calendar']->get('label'),'Calendar'),
							'singular' => 'Task',
							'parent' =>  $other_modules,
							'view' => 'Calendar',
							'img_url' => CTMobile_WS_Utils::getModuleURL('Calendar'),
							'module_access' => true,
							'createAction' => $createAction,
							'editAction' => $editAction,
							'QuickCreateAction'=>$QuickCreateAction,
							'isCommentModule'=>$isCommentModule,
							'isActivityModule'=>$isActivityModule,
							'isSummerymodule'=>$isSummerymodule,
							'isInventoryModule'=>false
							);
						}
					}
				}
			}
		}
		$newModulesList = array();
		foreach($newMenuModulesList as $key => $value){
			if(count($value['modules_list']) > 0){
				$newModulesList[] =  $value;
			}else{
				unset($newMenuModulesList[$key]);
			}
		}
		return $newModulesList;
	}	
		
	

	
}

function checkModulePermission($module,$AllowedModules){
	if(!in_array('selectAll',$AllowedModules)){
		if(in_array($module,$AllowedModules)){
			return true;
		}else{
			return false;
		}
	}else{
		return true;
	}
}


function ctTranslate($keyword,$language){
	global $adb;
	$default_charset = VTWS_PreserveGlobal::getGlobal('default_charset');
	$checkLangSQL = "SELECT language_keyword FROM ctmobile_language_keyword WHERE keyword = ? AND keyword_lang = ?";
	$resultLang = $adb->pquery($checkLangSQL,array($keyword,$language));
	if($adb->num_rows($resultLang) > 0){
		return html_entity_decode($adb->query_result($resultLang,0,'language_keyword'),ENT_QUOTES,$default_charset);
	}else{
		$checkdefaultLangSQL = "SELECT language_keyword FROM ctmobile_language_keyword WHERE keyword = ? AND keyword_lang = ?";
		$resultDefaultLang = $adb->pquery($checkdefaultLangSQL,array($keyword,'en_us'));
		if($adb->num_rows($resultLang) > 0){
			return html_entity_decode($adb->query_result($resultDefaultLang,0,'language_keyword'),ENT_QUOTES,$default_charset);
		}else{
			return $keyword;
		}
	}
}
