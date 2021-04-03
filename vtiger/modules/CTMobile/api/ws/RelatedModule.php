<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
//include_once dirname(__FILE__) . '/models/Alert.php';

//include_once 'include/data/CRMEntity.php';
include_once 'include/Webservices/Retrieve.php';

class CTMobile_WS_RelatedModule extends CTMobile_WS_Controller {
	
	
	function getSearchFilterModel($module, $search) {
		return CTMobile_WS_SearchFilterModel::modelWithCriterias($module, Zend_JSON::decode($search));
	}
	
	function getPagingModel(CTMobile_API_Request $request) {
		$page = $request->get('page', 0);
		return CTMobile_WS_PagingModel::modelWithPageStart($page);
	}
	
	function process(CTMobile_API_Request $request) {
		global $adb, $current_user;
		$current_user = $this->getActiveUser();
		$record = trim($request->get('record'));
		$view = trim($request->get('view'));
		$moduleName = trim($request->get('module'));
		$records = vtws_retrieve($record, $current_user);
		$recordid = explode('x', $record);
		$sql1 = "select tabid,name,tablabel from vtiger_tab where name='".$moduleName."'";
		$result1 = $adb->pquery($sql1,array()); 
		$matchtabid =$adb->query_result($result1,0,'tabid');

		$parentModuleModel = Vtiger_Module_Model::getInstance($moduleName);
		$relatedid = array();
		if($parentModuleModel->isSummaryViewSupported() && $moduleName != 'Users'){
			$relatedid[] = array('linktype'=>'DETAILVIEWTAB','moduleName' => ($moduleName)?$moduleName:'','record' => $recordid[1],'linkKey'=>'Summary','tablabel'=>vtranslate('LBL_SUMMARY', $moduleName),'img_url'=>CTMobile_WS_Utils::getModuleURL('summary'));
		}
		$relatedid[] = array('linktype'=>'DETAILVIEWTAB','moduleName' => ($moduleName)?$moduleName:'','record' => $recordid[1],'linkKey'=>'Details','tablabel'=>vtranslate('LBL_DETAILS', $moduleName),'img_url'=>CTMobile_WS_Utils::getModuleURL('details'));
		if($parentModuleModel->isTrackingEnabled()) {
			$relatedid[] = array('linktype'=>'DETAILVIEWTAB','moduleName' => ($moduleName)?$moduleName:'','record' => $recordid[1],'linkKey'=>'Updates','tablabel'=>vtranslate('LBL_UPDATES', $moduleName),'img_url'=>CTMobile_WS_Utils::getModuleURL('update'));
		}

		if($moduleName == 'Emails' || $moduleName == 'Calendar'){
			$response = new CTMobile_API_Response();
			$response->setResult($relatedid);
			return $response;
		}

		$relationModels = $parentModuleModel->getRelations();

		$userPrivModel = Users_Privileges_Model::getInstanceById($current_user->id);
		foreach($relationModels as $key => $relationModules){
			$tabid = $relationModules->get('tabid');
			$related_tabid = $relationModules->get('related_tabid');
			$relatedmodulelabel = $relationModules->get('label');
			$actions = $relationModules->get('actions');	
			$relatedmoduleName = $relationModules->get('relatedModuleName');
			
			$relatedfunctionname = $relationModules->get('name');
			$relation_id = $relationModules->get('relation_id');
			$relation_label =  $relationModules->get('label');
			$relationfieldid = $relationModules->get('relationfieldid');
			
			$relatedfieldname = "";
			if($relationfieldid != 0){
				$relatedFieldQuery = $adb->pquery('SELECT fieldname FROM vtiger_field WHERE fieldid = ?',array($relationfieldid));
				$relatedfieldname = $adb->query_result($relatedFieldQuery,0,'fieldname');
			}
			$visible = $relationModules->get('presence');
			if($visible != 0){
				continue;
			}


			
			$moduleModel = Vtiger_Module_Model::getInstance($relatedmoduleName);
			$createAction = $userPrivModel->hasModuleActionPermission($moduleModel->getId(), 'CreateView');
			
			if(($userPrivModel->isAdminUser() ||
						$userPrivModel->hasGlobalReadPermission() ||
						$userPrivModel->hasModulePermission($moduleModel->getId()))){
				global $currentModule;
				$currentModule = $moduleName;
				$parentRecordModel = Vtiger_Record_Model::getInstanceById($recordid[1], $moduleName);
				$relationListView = Vtiger_RelationListView_Model::getInstance($parentRecordModel, $relatedmoduleName, $relatedmodulelabel);
				$relationModel = $relationListView->getRelationModel();
				$selectAction = $relationModel->isSelectActionSupported();
				$query = $relationListView->getRelationQuery();
				$relatedmodulelabel = vtranslate($relatedmodulelabel, $relatedmoduleName);
				$moduleModel = Vtiger_Module_Model::getInstance($relatedmoduleName);
				$basetableid = $moduleModel->get('basetableid');
				$getfunctionres = $adb->pquery($query,array());
				$numofrows2 = $adb->num_rows($getfunctionres);
				$recordArray = array();
				for($j=0;$j<$numofrows2;$j++){
					$crmid = $adb->query_result($getfunctionres,$j,$basetableid);
					if(Users_Privileges_Model::isPermitted($relatedmoduleName, 'DetailView', $crmid)){
							$recordArray[] = $crmid;
					}
				}
				if ($numofrows2 == '') {
					$sql3 = "SELECT relcrmid FROM vtiger_crmentityrel INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_crmentityrel.relcrmid WHERE crmid='".$recordid[1]."' AND relmodule='".$relatedmoduleName."' AND vtiger_crmentity.deleted = 0";
					$result3 = $adb->pquery($sql3,array());
					$numofrows2 = $adb->num_rows($result3);	
					for($j=0;$j<$numofrows2;$j++){
						$crmid = $adb->query_result($result3,$j,'relcrmid');
						if(Users_Privileges_Model::isPermitted($relatedmoduleName, 'DetailView', $crmid)){
								$recordArray[] = $crmid;
						}
					}
				}
				$isDependencyAddress = false;
				if($relatedmoduleName == 'Contacts' && $moduleName == 'Accounts'){
					$isDependencyAddress = true;
				}else if(($relatedmoduleName == 'SalesOrder' || $relatedmoduleName == 'Quotes' || $relatedmoduleName == 'PurchaseOrder' || $relatedmoduleName == 'Invoice') && $moduleName == 'Accounts' ){
					$isDependencyAddress = true;
				}else if(($relatedmoduleName == 'SalesOrder' || $relatedmoduleName == 'Quotes' || $relatedmoduleName == 'PurchaseOrder' || $relatedmoduleName == 'Invoice') && $moduleName == 'Contacts'){
					$isDependencyAddress = true;
				}else if($relatedmoduleName == 'PurchaseOrder' && $moduleName == 'Vendors'){
					$isDependencyAddress = true;
				}
				$numofrows2 = count($recordArray);
				$relatedid[] =  array('linktype'=>'DETAILVIEWRELATED','moduleName' => ($moduleName)?$moduleName:'','record' => $recordid[1],'related_tabid' => ($related_tabid)?$related_tabid:'','relatedmoduleName' => ($relatedmoduleName)?$relatedmoduleName:'','tabid' => ($tabid)?$tabid:'','tablabel'=>($relatedmodulelabel)?$relatedmodulelabel:'','numofrows'=>$numofrows2, 'actions'=>$actions,'createAction'=>$createAction,'relatedfieldname'=>$relatedfieldname,'img_url'=>CTMobile_WS_Utils::getModuleURL($relatedmoduleName),'selectAction'=>$selectAction,'isDependencyAddress'=>$isDependencyAddress);
			}
		}
		
	   	if(empty($relatedid)){
	   		$relatedid = array();
	   	}
   		$response = new CTMobile_API_Response();
		$response->setResult($relatedid);
		return $response;
   		
	}
		
}
