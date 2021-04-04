<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
include_once dirname(__FILE__) . '/FetchRecord.php';

class CTMobile_WS_GlobalSearch extends CTMobile_WS_FetchRecord {
	
	function process(CTMobile_API_Request $request) {
		global $adb,$current_user; // Required for vtws_update API
		$current_user = $this->getActiveUser();
		$searchKey = trim($request->get('value'));
		if(empty($searchKey)){
			$message =  $this->CTTranslate('Required fields not found');
			throw new WebServiceException(404,$message);
		}
		$searchModule = false;
			
		if($request->get('searchModule')) {
			$searchModule = trim($request->get('searchModule'));
			$searchableModules = array($searchModule);
		}else{
			$searchableModules = Vtiger_Module_Model::getSearchableModules();
		}
		$matchingRecordsList = array();
		//$matchingRecords =  Vtiger_Record_Model::getSearchResult($searchKey, $searchModule);
		$EventsRecords = array();
		$count = 0;
		foreach ($searchableModules as $module => $searchModuleModel) {
			$matchingRecords = Vtiger_Record_Model::getSearchResult($searchKey, $module);
			$recordModelsList = $matchingRecords[$module];
			
			$noofrecords = count($recordModelsList);
			$matchingRecordsList[$count]['TotalRecords'] = $noofrecords;
			if($module == 'ModComments'){
				$img_url = CTMobile_WS_Utils::getModuleURL('mod_comments');
			}else{
				$img_url = CTMobile_WS_Utils::getModuleURL($module);
			}
			$matchingRecordsList[$count]['img_url'] = $img_url;

			$customView = new CustomView();
			$cvId = $customView->getViewIdByName('All', $module);
			$generator = new QueryGenerator($module, $current_user);
			$generator->initForCustomViewById($cvId);
			$generator->setFields(array('id'));
			$query = $generator->getQuery();
			$moduleModel = Vtiger_Module_Model::getInstance($module);
			$basetableid = $moduleModel->get('basetableid');
			$allowedRecords = array();
			$QueryResults = $adb->pquery($query,array());
			for($i=0;$i<$adb->num_rows($QueryResults);$i++){
				$allowedRecords[] = $adb->query_result($QueryResults,$i,$basetableid);
			}

			$matchingRecordsList[$count]['records'] = array();

			foreach($recordModelsList as $key => $value){
				$crmid = $value->get('crmid');
				$label = $value->get('label');
				$createdtime = Vtiger_Util_Helper::formatDateDiffInStrings($value->get('createdtime'));
				if($module == 'Calendar' || $module == 'Events'){
					$EventTaskQuery = $adb->pquery("SELECT * FROM  `vtiger_activity` WHERE activitytype = ? AND activityid = ?",array('Task',$crmid)); 
					if($adb->num_rows($EventTaskQuery) > 0){
						$wsid = CTMobile_WS_Utils::getEntityModuleWSId('Calendar');
						$recordid = $wsid.'x'.$crmid;
						$recordModule = 'Calendar';
					}else{
						$wsid = CTMobile_WS_Utils::getEntityModuleWSId('Events');
						$recordid = $wsid.'x'.$crmid;
						$recordModule = 'Events';

						$moduleLabel = vtranslate($recordModule,$recordModule);
						$recordArray = array('record'=>$recordid,'label'=>decode_html(decode_html($label)),'createdtime'=>$createdtime);
						$EventsRecords[] = $recordArray;
						continue;
					}
				}else{
					$recordModule = $module;
					$wsid = CTMobile_WS_Utils::getEntityModuleWSId($recordModule);
					$recordid = $wsid.'x'.$crmid;
				}
				
				if(in_array($crmid,$allowedRecords)){
					$moduleLabel = vtranslate($recordModule,$recordModule);
					$recordArray = array('record'=>$recordid,'label'=>decode_html(decode_html($label)),'createdtime'=>$createdtime);
					$matchingRecordsList[$count]['records'][] = $recordArray;
				}
			}
			if(!empty($matchingRecordsList[$count]['records'])){
				$matchingRecordsList[$count]['TotalRecords'] = count($matchingRecordsList[$count]['records']);
				$matchingRecordsList[$count]['module'] = $module;
				$matchingRecordsList[$count]['moduleLabel'] = vtranslate($module,$module);
				$count++;
			}else{
				unset($matchingRecordsList[$count]);
			}
		}
		if(!empty($EventsRecords)){
			$matchingRecordsList[$count]['TotalRecords'] = count($EventsRecords);
			$img_url = CTMobile_WS_Utils::getModuleURL('Events');
			$matchingRecordsList[$count]['img_url'] = $img_url;
			$matchingRecordsList[$count]['records'] = $EventsRecords;
			$matchingRecordsList[$count]['module'] = 'Events';
			$matchingRecordsList[$count]['moduleLabel'] = vtranslate('Events','Events');
		}
		$response = new CTMobile_API_Response();
		if(count($matchingRecordsList) == 0){
			$message =  $this->CTTranslate('No records found for').' "'.$searchKey.'"';
			$response->setResult(array('search_results'=>$matchingRecordsList,'code'=>404,'message'=>$message));
		}else{
			$response->setResult(array('search_results'=>$matchingRecordsList,'message'=>''));
		}
		return $response;
		
	}

}
