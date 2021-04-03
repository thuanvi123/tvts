<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

class CTMobile_WS_AdvanceFilterCondition extends CTMobile_WS_Controller {

	function process(CTMobile_API_Request $request) {
		global $adb,$current_user;
		$current_user = $this->getActiveUser();
		$module = $request->get('module');
		$fieldname = $request->get('fieldname');
		$moduleModel = Vtiger_Module_Model::getInstance($module);
		$fieldModels = $moduleModel->getFields();

		$AdvancedFilterOptions = Vtiger_Field_Model::getAdvancedFilterOptions();
		if($module == 'Calendar'){
			$advanceFilterOpsByFieldType = Calendar_Field_Model::getAdvancedFilterOpsByFieldType();
		} else{
			$advanceFilterOpsByFieldType = Vtiger_Field_Model::getAdvancedFilterOpsByFieldType();
		}
		$dateFilters = Vtiger_Field_Model::getDateFilterTypes();
		$advanceFilterOps = array();
		if($fieldModels[$fieldname]){
			$fieldModel = $fieldModels[$fieldname];
			$fieldtype = $fieldModel->getFieldType();
			$uitype = $fieldModel->get('uitype');
			if($fieldtype == 'D' || $fieldtype == 'DT'){
				$FilterOps = $advanceFilterOpsByFieldType[$fieldtype];
				foreach ($FilterOps as $key => $value) {
					if($value == 'y' || $value == 'ny' || $value == 'yesterday' || $value == 'today' || $value == 'tomorrow'){
						$input = false;
					}else{
						$input = true;
					}
					if(trim($value) == 'bw' || trim($value) == 'custom'){
						$datepickertype = "daterange";
					}else if(in_array(trim($value),array('lessthandaysago','morethandaysago','inlessthan','inmorethan','daysago','dayslater'))){
						$datepickertype = 'textbox';
					}else{
						$datepickertype = "datepicker";
					}
					$advanceFilterOps[] = array('value'=>$value,'label'=>vtranslate($AdvancedFilterOptions[$value]),'startdate'=>"",'enddate'=>"","isInput"=>$input,'datepickertype'=>$datepickertype,'iseditable'=>true);
				}
				$input = true;
				foreach ($dateFilters as $key => $value) {
					if($key == 'y' || $key == 'ny' || $key == 'yesterday' || $key == 'today' || $key == 'tomorrow'){
						$input = false;
					}else{
						$input = true;
					}
					if(trim($key) == 'bw' || trim($key) == 'custom'){
						$datepickertype = "daterange";
					}else if(in_array(trim($key),array('lessthandaysago','morethandaysago','inlessthan','inmorethan','daysago','dayslater'))){
						$datepickertype = 'textbox';
					}else{
						$datepickertype = "datepicker";
					}
					$iseditable = true;
					if($value['startdate'] != '' && $value['enddate'] != ''){
						$iseditable = false;
					}
					$advanceFilterOps[] = array('value'=>$key,'label'=>vtranslate($value['label']),'startdate'=>$value['startdate'],'enddate'=>$value['enddate'],"isInput"=>$input,'datepickertype'=>$datepickertype,'iseditable'=>$iseditable);
				}
			}else if($fieldtype == 'C' || $uitype == 56){
				$input = false;
				$advanceFilterOps[] = array('value'=>0,'label'=>Vtiger_Language_Handler::getJSTranslatedString($current_user->language,'JS_IS_DISABLED'),"isInput"=>$input);
				$advanceFilterOps[] = array('value'=>1,'label'=>Vtiger_Language_Handler::getJSTranslatedString($current_user->language,'JS_IS_ENABLED'),"isInput"=>$input);
			}else{
				if($fieldModel->get('uitype') == 15){
					$FilterOps = $advanceFilterOpsByFieldType[$fieldtype];
					$PValues = $fieldModel->getPicklistValues();
					$picklistValues = array();
					foreach ($PValues as $pkey => $pvalue) {
						$picklistValues[] = array("value"=>$pkey,"label"=>$pvalue);
					}
					foreach ($FilterOps as $key => $value) {
						if($value == 'y' || $value == 'ny'){
							$input = false;
						}else{
							$input = true;
						}
						$advanceFilterOps[] = array('value'=>$value,'label'=>vtranslate($AdvancedFilterOptions[$value]),"isInput"=>$input);
					}
				}else if($fieldname == 'assigned_user_id1'){
					$assignedUsers = Users_Record_Model::getAll();
					$USER_MODEL = Users_Record_Model::getCurrentUserModel();
					$AccessibleUsers = array_keys($USER_MODEL->getAccessibleUsers());
					$assignedTo = array();
					foreach ($assignedUsers as $userid => $users) {
						if(in_array($userid, $AccessibleUsers)){
							$assignedTo[] = array("value"=>$userid,"label"=>decode_html(decode_html($users->get('first_name')))." ".decode_html(decode_html($users->get('last_name'))));
						}
					}

					$FilterOps = $advanceFilterOpsByFieldType[$fieldtype];
					foreach ($FilterOps as $key => $value) {
						if($value == 'y' || $value == 'ny'){
							$input = false;
						}else{
							$input = true;
						}
						$advanceFilterOps[] = array('value'=>$value,'label'=>vtranslate($AdvancedFilterOptions[$value]),"isInput"=>$input);
					}
				}else{
					if($fieldModel->getFieldDataType() == 'reference'){
						$fieldtype = 'V';
					}
					$FilterOps = $advanceFilterOpsByFieldType[$fieldtype];
					foreach ($FilterOps as $key => $value) {
						if($value == 'y' || $value == 'ny'){
							$input = false;
						}else{
							$input = true;
						}
						$advanceFilterOps[] = array('value'=>$value,'label'=>vtranslate($AdvancedFilterOptions[$value]),"isInput"=>$input);
					}
				}
			}
		}

		$response = new CTMobile_API_Response();
		if($fieldModel->get('uitype') == 15){
			$response->setResult(array('advanceFilterOps'=>$advanceFilterOps,'picklistValues'=>$picklistValues));
		}else if($fieldname == 'assigned_user_id1'){
			$response->setResult(array('advanceFilterOps'=>$advanceFilterOps,'assignedTo'=>$assignedTo));
		}else{
			$response->setResult(array('advanceFilterOps'=>$advanceFilterOps));
		}
		return $response;

	}

}