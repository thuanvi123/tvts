<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

class CTMobile_WS_DeleteRelation extends CTMobile_WS_Controller {
	
	function process(CTMobile_API_Request $request) {
		global $adb,$current_user; // Required for vtws_update API
		$current_user = $this->getActiveUser();
		//relation Operation Pramaters
		$sourceModule = trim($request->get('sourceModule'));
		$sourceRecord = trim($request->get('sourceRecord'));
		$relatedModule = trim($request->get('relatedModule'));
		$relatedRecord = trim($request->get('relatedRecord'));

		if($sourceModule == '' || $sourceRecord == '' || $relatedModule == '' || $relatedRecord == ''){
			$message = $this->CTTranslate('Required fields not found');
			throw new WebServiceException(404,$message);
		}
		$src_record = explode('x', $sourceRecord);
		$sourceRecordId = $src_record[1];
		if($relatedModule == 'Events'){
			$relatedModule = 'Calendar';
		}
		//Setting related module as current module to delete the relation
		vglobal('currentModule', $relatedModule);

		$sourceModuleModel = Vtiger_Module_Model::getInstance($sourceModule);
		$relatedModuleModel = Vtiger_Module_Model::getInstance($relatedModule);
		$relationModel = Vtiger_Relation_Model::getInstance($sourceModuleModel, $relatedModuleModel);
		$related_record = explode('x', $relatedRecord);
		$relatedRecordId = $related_record[1];
		$results = $relationModel->deleteRelation($sourceRecordId,$relatedRecordId);
		if($results){
			//get source module tabid
			$sourcemoduletabid = getTabid($sourceModule);
			//get Related Module tabid
			$relatedmoduletabid = getTabid($relatedModule);
			//Campare to sourcemodule and relatedmodule
			$comparetabidsql = "SELECT relation_id,name,label FROM vtiger_relatedlists where tabid = '".$sourcemoduletabid."' AND related_tabid = '".$relatedmoduletabid."' AND name != 'get_history'";
			$getfunctionres = $adb->pquery($comparetabidsql,array());
			
			$relatedfunctionname = array();
			foreach($getfunctionres as $gval){
				$relatedfunctionname = $gval['name'];
				$relation_id = $gval['relation_id'];
				$relation_label = $gval['label'];
			}
			if($sourcemoduleName == $relatedModuleName){
				$relation_label = $tablabel;
			}
			global $currentModule;
			$currentModule = $sourceModule;
			
			$parentRecordModel = Vtiger_Record_Model::getInstanceById($sourceRecordId, $sourceModule);
			$relationListView = Vtiger_RelationListView_Model::getInstance($parentRecordModel, $relatedModule, $relation_label);
			$query = $relationListView->getRelationQuery();
			$getfunctionres = $adb->pquery($query,array());
			$numofrows2 = $adb->num_rows($getfunctionres);
			$message = $this->CTTranslate('relation of records removed successfully');
			$result = array('sourceModule'=>$sourceModule,'relatedmodule'=>$relatedModule,'numofrows'=>$numofrows2,'message'=>$message);
			$response = new CTMobile_API_Response();
			$response->setResult($result);
			return $response;
		}else{
			$response = new CTMobile_API_Response();
			$message = $this->CTTranslate('relation of records not removed');
			$response->setError(403,$message);
			return $response;
		}
		
	}
	
}
