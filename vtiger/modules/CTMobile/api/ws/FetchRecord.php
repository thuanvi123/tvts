<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
include_once 'include/Webservices/Retrieve.php';

class CTMobile_WS_FetchRecord extends CTMobile_WS_Controller {
	
	private $module = false;
	
	protected $resolvedValueCache = array();
	
	protected function detectModuleName($recordid) {
		if($this->module === false) {
			$this->module = CTMobile_WS_Utils::detectModulenameFromRecordId($recordid);
		}
		return $this->module;
	}
	
	protected function processRetrieve(CTMobile_API_Request $request) {
		global $adb;
		$current_user = $this->getActiveUser();

		$recordid = trim($request->get('record'));
		
		$calendarmodule = explode('x', $request->get('record'));
		
		$record = vtws_retrieve($recordid, $current_user);
		
		$recordId = explode('x', $record['id']);
		
		$getLabelQuery = $adb->pquery("SELECT label from vtiger_crmentity where crmid = ?", array($recordId[1]));
		$recordLabel = trim($adb->query_result($getLabelQuery, 0, 'label'));
		$record['recordLabel'] = $recordLabel;
		
		return $record;
	}
	
	function process(CTMobile_API_Request $request) {
		$current_user = $this->getActiveUser();
		$record = $this->processRetrieve($request);
		
		$this->resolveRecordValues($record, $current_user);
		
		$response = new CTMobile_API_Response();
		$response->setResult(array('record' => $record));
		
		return $response;
	}
	
	function resolveRecordValues(&$record, $user, $ignoreUnsetFields=false) {
		if(empty($record)) return $record;
		
		$fieldnamesToResolve = CTMobile_WS_Utils::detectFieldnamesToResolve(
			$this->detectModuleName($record['id']) );
		
		if(!empty($fieldnamesToResolve)) {
			foreach($fieldnamesToResolve as $resolveFieldname) {
				if ($ignoreUnsetFields === false || isset($record[$resolveFieldname])) {
					$fieldvalueid = $record[$resolveFieldname];
					$fieldvalue = $this->fetchRecordLabelForId($fieldvalueid, $user);
					$record[$resolveFieldname] = array('value' => $fieldvalueid, 'label'=>$fieldvalue);
				}
			}
		}

	}
	
	function fetchRecordLabelForId($id, $user) {
		$value = null;
		
		if (isset($this->resolvedValueCache[$id])) {
			$value = $this->resolvedValueCache[$id];
		} else if(!empty($id)) {
			$value = trim(vtws_getName($id, $user));
			$this->resolvedValueCache[$id] = $value;
		} else {
			$value = $id;
		}
		return $value;
	}
}
