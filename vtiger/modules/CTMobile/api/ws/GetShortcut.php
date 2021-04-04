<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
include_once dirname(__FILE__) . '/FetchRecordWithGrouping.php';

include_once 'include/Webservices/Create.php';
include_once 'include/Webservices/Update.php';

class CTMobile_WS_GetShortcut extends CTMobile_WS_FetchRecordWithGrouping {
	public $totalQuery = "";
	public $totalParams = array();
	function process(CTMobile_API_Request $request) {
		global $adb,$current_user; // Required for vtws_update API
		$current_user = $this->getActiveUser();
		$module = trim($request->get('module'));
		$shortcutType = trim($request->get('shortcutType'));
		if($shortcutType == ''){
			$message = $this->CTTranslate('Required fields not found');
			throw new WebServiceException(404,$message);
		}
		$index = trim($request->get('index'));
		$size = trim($request->get('size'));
		$limit = ($index*$size) - $size;
		$shortcutdata = array();
		if($shortcutType == 'filter'){
			$query = "SELECT ctmobile_filter_shortcut.shortcutid,ctmobile_filter_shortcut.shortcutname,ctmobile_filter_shortcut.filterid,vtiger_customview.viewname,ctmobile_filter_shortcut.search_value,ctmobile_filter_shortcut.fieldname,ctmobile_filter_shortcut.userid,ctmobile_filter_shortcut.module,ctmobile_filter_shortcut.createdtime FROM ctmobile_filter_shortcut INNER JOIN vtiger_customview ON vtiger_customview.cvid = ctmobile_filter_shortcut.filterid WHERE ctmobile_filter_shortcut.userid = ? ORDER BY ctmobile_filter_shortcut.createdtime DESC ";
			$this->totalQuery = $query;
			$this->totalParams = array($current_user->id);
			if($index && $size){
				$query .= sprintf(" LIMIT %s, %s", $limit, $size);
			}
			
			$params = array($current_user->id);
			$result = $adb->pquery($query,$params);
			for($i=0;$i<$adb->num_rows($result);$i++){
				$shortcutid = $adb->query_result($result,$i,'shortcutid');
				$shortcutname = $adb->query_result($result,$i,'shortcutname');
				$filterid = $adb->query_result($result,$i,'filterid');
				$viewname = $adb->query_result($result,$i,'viewname');
				$search_value = $adb->query_result($result,$i,'search_value');
				$fieldname = $adb->query_result($result,$i,'fieldname');
				$module = $adb->query_result($result,$i,'module');
				$moduleModel = Vtiger_Module_Model::getInstance($module);
				$fieldModels = $moduleModel->getFields();
				$tabid = getTabid($module);
				$fieldlabel = '';
				$fields = array();
				if($fieldname != ''){
					$fieldnames = explode('::', $fieldname);
					$searchvalues = explode('::', $search_value);
					foreach ($fieldnames as $key => $field_name) {
						if($field_name != ''){
							$uitype = '';
							$type = '';
							if($fieldModels[$field_name]){
								$uitype = $fieldModels[$field_name]->get('uitype');
								//$type = $fieldModels[$field_name]->getFieldDataType();
								$webserviceField = $fieldModels[$field_name]->getWebserviceFieldObject();
								$type = $webserviceField->getFieldDataType();

							}
							$fieldResult = $adb->pquery("SELECT fieldlabel FROM vtiger_field WHERE fieldname = ? AND tabid = ? ",array($field_name,$tabid));
							$fieldlabel = $adb->query_result($fieldResult,0,'fieldlabel');
							$fieldlabel = vtranslate($fieldlabel,$module);
							if($type == 'picklist' || $type == 'multipicklist'){
								$picklistValues = array();
								$picklist = $fieldModels[$field_name]->getPicklistValues();
								foreach($picklist as $pvalue){
									if($pvalue != ''){
										$picklistValues[] = array('value'=>$pvalue, 'label'=>vtranslate($pvalue,$module));
									}
								}
								$fields[] = array('name'=>$field_name,'label'=>$fieldlabel,'search_value'=>$searchvalues[$key],'uitype'=>$uitype,'type'=>array("name"=>$type,'picklistValues'=>$picklistValues));
							}else if($type == 'reference'){
								if($searchvalues[$key] != ''){
									$value = substr($searchvalues[$key], stripos($searchvalues[$key], 'x')+1);
									$seQuery = $adb->pquery("SELECT setype,label FROM vtiger_crmentity WHERE crmid = ?",array($value));
									$searchvalues[$key] = array("value"=>$searchvalues[$key],"label"=>decode_html(decode_html($adb->query_result($seQuery,0,'label'))));
								}else{
									$searchvalues[$key] = array("value"=>"","label"=>"");
								}
								$fields[] = array('name'=>$field_name,'label'=>$fieldlabel,'search_value'=>$searchvalues[$key],'uitype'=>$uitype,'type'=>array("name"=>$type));
							}else{
								$fields[] = array('name'=>$field_name,'label'=>$fieldlabel,'search_value'=>$searchvalues[$key],'uitype'=>$uitype,'type'=>array("name"=>$type));
							}
						}
					}
				}
				$shortcutdata[] = array('shortcutid'=>$shortcutid,'shortcutname'=>$shortcutname,'filterid'=>$filterid,'filtername'=>$viewname,'module'=>$module,'moduleLabel'=>vtranslate($module,$module),'fields'=>$fields);

			}
			

		}
		if($shortcutType == 'record'){
			$query = "SELECT ctmobile_record_shortcut.shortcutid,ctmobile_record_shortcut.shortcutname,ctmobile_record_shortcut.recordid,ctmobile_record_shortcut.userid,ctmobile_record_shortcut.module,ctmobile_record_shortcut.createdtime FROM ctmobile_record_shortcut INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = ctmobile_record_shortcut.recordid WHERE vtiger_crmentity.deleted = 0 AND ctmobile_record_shortcut.userid = ?  ORDER BY ctmobile_record_shortcut.createdtime DESC ";
			$this->totalQuery = $query;
			$this->totalParams = array($current_user->id);
			if($index && $size){
				$query .= sprintf(" LIMIT %s, %s", $limit, $size);
			}
			$params = array($current_user->id);
			$result = $adb->pquery($query,$params);
			for($i=0;$i<$adb->num_rows($result);$i++){
				$shortcutid = $adb->query_result($result,$i,'shortcutid');
				$shortcutname = $adb->query_result($result,$i,'shortcutname');
				$recordid = $adb->query_result($result,$i,'recordid');
				$module = $adb->query_result($result,$i,'module');
				$recordLabel = '';
				if($recordid){
					if($module == 'Events'){
						$entityQuery = $adb->pquery("SELECT * FROM vtiger_entityname WHERE modulename = ?",array('Calendar'));
					}else{
						$entityQuery = $adb->pquery("SELECT * FROM vtiger_entityname WHERE modulename = ?",array($module));
					}
					
					$entityField = $adb->query_result($entityQuery,0,'fieldname');
					$entityField_array = explode(',',$entityField);
					$entityField = $entityField_array[0];
					if($module == 'Events'){
						$recordModel = Vtiger_Record_Model::getInstanceById($recordid,'Calendar');
					}else{
						$recordModel = Vtiger_Record_Model::getInstanceById($recordid,$module);
					}
					
					$recordLabel = $recordModel->get($entityField);
				}
				$wsid = CTMobile_WS_Utils::getEntityModuleWSId($module);
				$shortcutdata[] = array('shortcutid'=>$shortcutid,'shortcutname'=>$shortcutname,'recordid'=>$wsid.'x'.$recordid,'recordLabel'=>$recordLabel,'module'=>$module,'moduleLabel'=>vtranslate($module,$module));
			}

		}
		$isLast = true;
		if($this->totalQuery != ""){
			$totalResults = $adb->pquery($this->totalQuery,$this->totalParams);
			$totalRecords = $adb->num_rows($totalResults);
			if($totalRecords > $index*$size){
				$isLast = false;	
			}else{
				$isLast = true;
			}
		}

		if(count($shortcutdata) > 0){
				$response = new CTMobile_API_Response();
				$response->setResult(array("shortcutdata"=>$shortcutdata,"code"=>1,"message"=>'','isLast'=>$isLast));
		}else{
				$response = new CTMobile_API_Response();
				$message = $this->CTTranslate('No records found');
				$response->setResult(array("shortcutdata"=>$shortcutdata,"code"=>0,"message"=>$message,'isLast'=>$isLast));
		}
		return $response;
	}
}