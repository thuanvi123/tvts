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

class CTMobile_WS_DashboardHistory extends CTMobile_WS_FetchRecord {
	
	function process(CTMobile_API_Request $request) {
		$default_charset = VTWS_PreserveGlobal::getGlobal('default_charset');
		global $adb,$current_user; // Required for vtws_update API
		$current_user = $this->getActiveUser();
		$moduleName = 'Home';
		$acrossAllModule = false;
		if ($moduleName == 'Home') $acrossAllModule = true;
		$index = trim($request->get('index'));
		$size = trim($request->get('size'));
		$start = ($index*$size) - $size;
		$sql = "SELECT vtiger_modtracker_basic.* FROM vtiger_modtracker_basic INNER JOIN vtiger_crmentity ON vtiger_modtracker_basic.crmid = vtiger_crmentity.crmid
				  GROUP BY DATE(vtiger_modtracker_basic.changedon), vtiger_modtracker_basic.crmid Having vtiger_modtracker_basic.module = 'Accounts' ORDER BY vtiger_modtracker_basic.id DESC";
		if($index != '' && $size != ''){
			$sql .= sprintf(' LIMIT %s,%s', $start, $size);
		}
		$params = array();
		$result = $adb->pquery($sql, $params);
		$recordValuesMap = array();
		$orderedIds = array();
		
		while ($row = $adb->fetch_array($result)) {
			
			$modules = $row['module'];
			$recordid = $row['id'];
			
			
			if(Users_Privileges_Model::isPermitted($modules, 'DetailView', $recordid)){
				$orderedIds[] = $row['id'];
				
				$whodid = $this->vtws_history_entityIdHelper('Users', $row['whodid']);
				$crmid = $this->vtws_history_entityIdHelper($acrossAllModule? '' : $moduleName, $row['crmid']);
				$status = $row['status'];
				$statuslabel = '';
				switch ($status) {
					case 0: $statuslabel = 'updated'; break;
					case 1: $statuslabel = 'deleted'; break;
					case 2: $statuslabel = 'created'; break;
					case 3: $statuslabel = 'restored'; break;
					case 4: $statuslabel = 'link'; break;
					case 5: $statuslabel = 'unlink'; break;
				}
				
				$item['modifieduser'] = $whodid;
				$item['id'] = $crmid;
				$item['modifiedtime'] = $row['changedon'];
				$item['ModifiedTime'] = Vtiger_Util_Helper::formatDateDiffInStrings($row['changedon']);
				$item['status'] = $status;
				$item['statuslabel'] = $statuslabel;
				$item['module'] = $row['module'];
				if($status == 1 && $statuslabel == 'deleted'){
					$getModTrackerRelQuery = $adb->pquery("SELECT vtiger_modtracker_basic . * 
						FROM vtiger_modtracker_basic
						INNER JOIN vtiger_crmentity ON vtiger_modtracker_basic.crmid = vtiger_crmentity.crmid where id = ?", array($row['id']));
					$targetid = $adb->query_result($getModTrackerRelQuery, 0, 'crmid');
					
					if($targetid) {
						$getCRMEntityQuery = $adb->pquery("SELECT setype, label FROM vtiger_crmentity where crmid = ? ", array($targetid));
						$setype = $adb->query_result($getCRMEntityQuery, 0, 'setype');
						$label = $adb->query_result($getCRMEntityQuery, 0, 'label');
						$label = html_entity_decode($label, ENT_QUOTES, $default_charset);
						$new_label = 'deleted '.$label;
					}
				}
				if($status == 4){
					$getModTrackerRelQuery = $adb->pquery("SELECT * FROM vtiger_modtracker_relations where id = ?", array($row['id']));
					$targetid = $adb->query_result($getModTrackerRelQuery, 0, 'targetid');
					if($targetid) {
						$getCRMEntityQuery = $adb->pquery("SELECT setype, label FROM vtiger_crmentity where crmid = ? and deleted = 0", array($targetid));
						$setype = $adb->query_result($getCRMEntityQuery, 0, 'setype');
						$label = $adb->query_result($getCRMEntityQuery, 0, 'label');
						$label = html_entity_decode($label, ENT_QUOTES, $default_charset);
						$item['entitydata'] = $setype." added ".$label;
						
						$new_label = '';
						$new_label = 'Commented On';
						$new_label.= '</br>';
						$new_label.= ' label </br>'.'"'.$label.'"';	
					}
				}
				if($status == 2 && $statuslabel == 'created' && $row['module'] =='ModComments'){
					$getModTrackerRelQuery = $adb->pquery("SELECT * FROM vtiger_modtracker_detail where id = ? AND fieldname = 'related_to'", array($row['id']));
					$parent_id = $adb->query_result($getModTrackerRelQuery, 0, 'postvalue');
					$query = $adb->pquery("SELECT * FROM vtiger_crmentity where crmid = ? and deleted = 0",array($parent_id));
					$label = $adb->query_result($query, 0, 'label');
					$label = html_entity_decode($label, ENT_QUOTES, $default_charset);
					$new_label = '';
					$new_label = 'added';
					$new_label.= ' "label" for </br>'.$label;
					
				}else if($status == 2 && $statuslabel == 'created'){
					$new_label = '';
					$new_label = 'added';
					$new_label.= ' label ';
				}
				
				
				if($status == 5){
					$getModTrackerRelQuery = $adb->pquery("SELECT * FROM vtiger_modtracker_relations where id = ?", array($row['id']));
					$targetid = $adb->query_result($getModTrackerRelQuery, 0, 'targetid');
					if($targetid) {
						$getCRMEntityQuery = $adb->pquery("SELECT setype, label FROM vtiger_crmentity where crmid = ? and deleted = 0", array($targetid));
						$setype = $adb->query_result($getCRMEntityQuery, 0, 'setype');
						$label = $adb->query_result($getCRMEntityQuery, 0, 'label');
						$label = html_entity_decode($label, ENT_QUOTES, $default_charset);
						$item['entitydata'] = $setype." removed ".$label;
					}
				}

				$item['values'] = array();
				$item['label'] = $new_label;
				$recordValuesMap[$row['id']] = $item;
			}
		}
		
		$historyItems = array();

		// Minor optimizatin to avoid 2nd query run when there is nothing to expect.
		if (!empty($orderedIds)) {
			$sql = 'SELECT vtiger_modtracker_detail.* FROM vtiger_modtracker_detail';
			$sql .= ' WHERE vtiger_modtracker_detail.id IN (' . generateQuestionMarks($orderedIds) . ')';

			// LIMIT here is not required as $ids extracted is with limit at record level earlier.
			$params = $orderedIds;

			$result = $adb->pquery($sql, $params);
			while ($row = $adb->fetch_array($result)) {
				$item = $recordValuesMap[$row['id']];
				
				// NOTE: For reference field values transform them to webservice id.
				$item['values'][$row['fieldname']] = array(
					'previous' => $row['prevalue'],
					'current'  => $row['postvalue']
				);
				if($row['fieldname'] == 'ModifiedTime' && $item['modifiedtime'] == null){
					$item['ModifiedTime'] = Vtiger_Util_Helper::formatDateDiffInStrings($row['postvalue']);
				}
					
				$recordValuesMap[$row['id']] = $item;
			}
			
			// Group the values per basic-transaction
			foreach ($orderedIds as $id) {
				$historyItems[] = $recordValuesMap[$id];
			}
		}
        $response = new CTMobile_API_Response();
		if(count($historyItems) == 0){
				$response->setResult(array('history'=>[],'code'=>404,'message'=>vtranslate('No Activity found','CTMobile')));
		}else{
			$result = array('history' => $historyItems);
			$response->setResult($result);
		}
		return $response;
	}
	
	function vtws_history_entityIdHelper($moduleName, $id) {
		static $wsEntityIdCache = NULL;
		if ($wsEntityIdCache === NULL) {
			$wsEntityIdCache = array('users' => array(), 'records' => array());
		}

		if (!isset($wsEntityIdCache[$moduleName][$id])) {
			// Determine moduleName based on $id
			if (empty($moduleName)) {
				$moduleName = getSalesEntityType($id);
			}
			if($moduleName == 'Calendar') {
				$moduleName = vtws_getCalendarEntityType($id);
			}

			$wsEntityIdCache[$moduleName][$id] = vtws_getWebserviceEntityId($moduleName, $id);
		}
		return $wsEntityIdCache[$moduleName][$id];
	}

}
