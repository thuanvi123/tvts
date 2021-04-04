<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

include_once dirname(__FILE__) . '/QueryWithGrouping.php';

class CTMobile_WS_RelatedRecordsWithGrouping extends CTMobile_WS_QueryWithGrouping {
	
	function process(CTMobile_API_Request $request) {
		global $current_user, $adb, $currentModule;
		$current_user = $this->getActiveUser();
		
		$response = new CTMobile_API_Response();

		$record = trim($request->get('record'));
		$relatedmodule = trim($request->get('relatedmodule'));
		$currentPage = $request->get('page', 0);
		
		// Input validation
		if (empty($record)) {
			$message = vtranslate('Record id is empty','CTMobile');
			$response->setError(1001, $message);
			return $response;
		}
		$recordid = vtws_getIdComponents($record);
		$recordid = $recordid[1];
		
		$module = CTMobile_WS_Utils::detectModulenameFromRecordId($record);

		// Initialize global variable
		$currentModule = $module;
		
		$functionHandler = CTMobile_WS_Utils::getRelatedFunctionHandler($module, $relatedmodule); 
		
		if ($functionHandler) {
			$sourceFocus = CRMEntity::getInstance($module);
			$relationResult = call_user_func_array(	array($sourceFocus, $functionHandler), array($recordid, getTabid($module), getTabid($relatedmodule)) );
			$query = $relationResult['query'];
		
			$querySEtype = "vtiger_crmentity.setype as setype";
			if ($relatedmodule == 'Calendar') {
				$querySEtype = "vtiger_activity.activitytype as setype";
			}
			
			$query = sprintf("SELECT vtiger_crmentity.crmid, $querySEtype %s", substr($query, stripos($query, 'FROM')));
			$queryResult = $adb->query($query);
			
			// Gather resolved record id's
			$relatedRecords = array();
			while($row = $adb->fetch_array($queryResult)) {
				$targetSEtype = $row['setype'];
				if ($relatedmodule == 'Calendar') {
					if ($row['setype'] != 'Task' && $row['setype'] != 'Emails') {
						$targetSEtype = 'Events';
					} else {
						$targetSEtype = $relatedmodule;
					}
				}
				$relatedRecords[] = sprintf("%sx%s", CTMobile_WS_Utils::getEntityModuleWSId($targetSEtype), $row['crmid']);
			}
			
			// Perform query to get record information with grouping
			$wsquery = sprintf("SELECT * FROM %s WHERE id IN ('%s');", $relatedmodule, implode("','", $relatedRecords));
			$newRequest = new CTMobile_API_Request();
			$newRequest->set('module', $relatedmodule);
			$newRequest->set('query', $wsquery);
			$newRequest->set('page', $currentPage);

			$response = parent::process($newRequest);
		}
		
		return $response;
	}
}
