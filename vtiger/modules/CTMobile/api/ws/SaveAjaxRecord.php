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

class CTMobile_WS_SaveAjaxRecord extends CTMobile_WS_FetchRecordWithGrouping {
	protected $recordValues = false;
	
	// Avoid retrieve and return the value obtained after Create or Update
	protected function processRetrieve(CTMobile_API_Request $request) {
		return $this->recordValues;
	}
	
	function process(CTMobile_API_Request $request) {


		global $current_user; // Required for vtws_update API
		$current_user = $this->getActiveUser();
		$refrenceUitypes = array(10,51,57,58,59,66,73,75,76,78,80,81,101);
		$module = trim($request->get('module'));
		$isCalendar = trim($request->get('isCalendar'));
		if($module == ''){
			$message = $this->CTTranslate('Required fields not found');
			throw new WebServiceException(404,$message);
		}

		//start validation for module & fields
		if(!getTabid($module)){
			$message = vtranslate($module,$module)." ".$this->CTTranslate('Module does not exists');
			throw new WebServiceException(404,$message);
		}
		
		$recordid = trim($request->get('record'));
		$valuesJSONString =  $request->get('values');
		$recordModel = Vtiger_Record_Model::getCleanInstance($module);
		$moduleModel = $recordModel->getModule();
		$fieldList = $moduleModel->getFields();

		$values = "";
		if(!empty($valuesJSONString) && is_string($valuesJSONString)) {
			$values = Zend_Json::decode($valuesJSONString);
		} else {
			$values = $valuesJSONString; // Either empty or already decoded.
		}

		$response = new CTMobile_API_Response();
		
		if (empty($values)) {
			$message =  $this->CTTranslate('Values cannot be empty');
			$response->setError(404, $message);
			return $response;
		}

		if($module == 'SalesOrder'){
			$values['enable_recurring'] = 0;
			$values['invoicestatus'] = "Created";
		}
		
		
		
		try {
			// Retrieve or Initalize
			if (!empty($recordid) && !$this->isTemplateRecordRequest($request)) {
				$this->recordValues = vtws_retrieve($recordid, $current_user);
			} 
			
			// Set the modified values
			foreach($values as $name => $value) {
				if($name == 'invite_user'){
					continue;
				}
				if($name != 'LineItems') {
					$uitype = $fieldList[$name]->get('uitype');
					$fieldtype = $fieldList[$name]->getFieldType();
					if($uitype == 33) {
						if($value){
							$value = implode(' |##| ', $value);
						}
					}else if($uitype == 5 || $uitype == 23){
						$value = Vtiger_Date_UIType::getDBInsertedValue($value);
					}else if($uitype == 72){
						$value = CurrencyField::convertToDBFormat($value, null, true);
					}else if($uitype == 71){
						$value = CurrencyField::convertToDBFormat($value);
					}else if(in_array($uitype, $refrenceUitypes)){
						$fieldModel = $fieldList[$name];
						$refModules = $fieldModel->getReferenceList();
						if($value == ''){
							$value = CTMobile_WS_Utils::getEntityModuleWSId($refModules[0]).'x';
						}
					}else{
						if($fieldtype == 'T' && !(in_array($module, array('Events','Calendar')))){
							$value = Vtiger_Time_UIType::getTimeValueWithSeconds($value);
						}
					}
				}
							
				$this->recordValues[$name] = $value;
			}

			if($module == 'Faq'){
				if(!$this->recordValues['faqcategories']){
					$this->recordValues['faqcategories'] = 'General';
				}
			}
			
			// Update or Create
			if (isset($this->recordValues['id'])) {
				$mode = 'edit';
				if($module == 'ServiceContracts'){
					$record_id = explode('x',$recordid);
					$recordModel = Vtiger_Record_Model::getInstanceById($record_id[1],$module);
					$recordModel->set('mode','edit');
					foreach($this->recordValues as $key => $value){
						if($key == 'assigned_user_id'){
							$values = explode('x',$value);
							$recordModel->set($key,$values[1]);
						}else if($key == 'sc_related_to'){
							$values = explode('x',$value);
							$recordModel->set($key,$values[1]);
						}else{
							$recordModel->set($key,$value);
						}
					}
					$recordModel->set('id',$record_id[1]);
					$recordModel->save();
					$moduleWSId = CTMobile_WS_Utils::getEntityModuleWSId($module);
					$recordId = $recordModel->getId();
					$this->recordValues['id'] = $moduleWSId.'x'.$recordId;
				}else{
					$this->recordValues = vtws_update($this->recordValues, $current_user);
			    }
			} 
			// Update the record id
			$request->set('record', $this->recordValues['id']);
			
			if($request->get('user_lat')!='' && $request->get('user_long')!='' && $request->get('user_id')!=''){
				
				if($this->recordValues['id']!=''){
					global $adb;
					$date_var = date("Y-m-d H:i:s");
					$userId = explode('x', $request->get('user_id'));
					$recordId = explode('x', $this->recordValues['id']);
					$createdtime = $adb->formatDate($date_var, true);
					$query = $adb->pquery("INSERT INTO ctmobile_userderoute (userid, latitude, longitude, createdtime,action,record) VALUES (?,?,?,?,?,?)", array($userId[1], $request->get('user_lat'), $request->get('user_long'), $createdtime,$mode,$recordId[1]));
					
				}
				
			}
			$message = $this->CTTranslate('Record save successfully');
			if($isCalendar == "1" && $module == 'Events'){
				$isShowCheckin = true;
				if($this->recordValues['eventstatus'] == 'Held'){
					$isShowCheckin = false;
				}
				$result = array('id'=>$this->recordValues['id'],'module'=>$module,'message'=>$message,'isShowCheckin'=>$isShowCheckin);
			}else if($isCalendar == "1" && $module == 'Calendar'){
				$isShowCheckin = false;
				$result = array('id'=>$this->recordValues['id'],'module'=>$module,'message'=>$message,'isShowCheckin'=>$isShowCheckin);
			}else{
				$result = array('id'=>$this->recordValues['id'],'module'=>$module,'message'=>$message);
			}
			$response->setResult($result);
			
			
		} catch(Exception $e) {
			$response->setError($e->getCode(), $e->getMessage());
		}
		return $response;
	}
	
}
