
<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

class CTMobile_WS_ConvertLead extends CTMobile_WS_Controller {

	function process(CTMobile_API_Request $request) {
		global $adb,$current_user; // Required for vtws_update API
		$current_user = $this->getActiveUser();
		$roleid = $current_user->roleid;
		$record = explode('x',$request->get('record'));
		$recordId = $record[1];
		$response = new CTMobile_API_Response();
		if ($recordId == '') {
			$message = $this->CTTranslate('record cannot be empty');
			$response->setError(404, $message);
			return $response;
		}

		$recordModel = Vtiger_Record_Model::getInstanceById($recordId);
		$lead_company_name = $recordModel->get('company');
		$Fields = $recordModel->getConvertLeadFields();
		$moduleModel = $recordModel->getModule();
		$assignedToFieldModel = $moduleModel->getField('assigned_user_id');
		$assignedToFieldModel->set('fieldvalue', $recordModel->get('assigned_user_id'));
		$assigned_user_id = CTMobile_WS_Utils::getEntityModuleWSId('Users').'x'.$recordModel->get('assigned_user_id');

		$usersRecordModel = Vtiger_Record_Model::getInstanceById($recordModel->get('assigned_user_id'),'Users');

		$contactsModuleModel = Vtiger_Module_Model::getInstance('Contacts');
		$accountField = Vtiger_Field_Model::getInstance('account_id', $contactsModuleModel);
		$contact_account_field_model = $accountField;

		if(!$Fields['Accounts'] && !$Fields['Contacts']){
			$message = decode_html(decode_html(vtranslate('LBL_CONVERT_LEAD_ERROR',"Leads")));
			$response->setError(413,$message);
			return $response;
		}else{
			$convertLeadFields = array();
			$count = 0;
			foreach($Fields as $index => $value){
				 if($index == 'Contacts' || ($lead_company_name != '' && $index == 'Accounts') || ($contact_account_field_model && $contact_account_field_model->isMandatory() && $index != 'Potentials')){
				 	$convertLeadFields[$count] = array('module'=>$index,'moduleLabel'=>vtranslate($index,$index),'selected'=>true,'blockid'=>$count,'blockname'=>'LBL_CREATE_SINGLE_'.strtoupper($index),'blocklabel'=>decode_html(decode_html(vtranslate('LBL_CREATE', $index).' '.vtranslate("SINGLE_".$index, $index))));
				 }else{
				 	$convertLeadFields[$count] = array('module'=>$index,'moduleLabel'=>vtranslate($index,$index),'selected'=>false,'blockid'=>$count,'blockname'=>'LBL_CREATE_SINGLE_'.strtoupper($index),'blocklabel'=>decode_html(decode_html(vtranslate('LBL_CREATE', $index).' '.vtranslate("SINGLE_".$index, $index))));
				 }
				
				foreach($value as $key => $fields){
					$result = $adb->pquery("SELECT fieldtype FROM vtiger_ws_fieldtype WHERE uitype = ?",array($fields->get('uitype')));
					$fieldtype = $adb->query_result($result, 0, 'fieldtype');
					if(!$fieldtype){
						$typeofdata = explode('~',$fields->get('typeofdata'));
						switch($typeofdata[0]){
							case 'T': $fieldtype = "time";
							case 'D': $fieldtype = "date";
							case 'DT': $fieldtype =  "date";
							case 'E': $fieldtype =  "email";
							case 'N':
							case 'NN': $fieldtype = "double";
							case 'P': $fieldtype = "password";
							case 'I': $fieldtype = "integer";
							case 'V':
							default: $fieldtype = "string";
						}
					}
					if($fields->get('name') == 'closingdate'){
						$fieldtype = 'date';
					}
					if($fields->get('uitype') == 15 || $fields->get('uitype') == 16 || $fields->get('uitype') == 33){
						$picklistValues1 = array();
						if($fields->isRoleBased()){
							$picklistValues = Vtiger_Util_Helper::getRoleBasedPicklistValues($fields->get('name'),$roleid);
						}else{
							$picklistValues = Vtiger_Util_Helper::getPickListValues($fields->get('name'));
						}
						foreach($picklistValues as $pvalue){
							$picklistValues1[] = array('value'=>$pvalue, 'label'=>decode_html(decode_html(vtranslate($pvalue,$module))));
						}
						$convertLeadFields[$count]['fields'][] = array('name'=>$fields->get('name'),'label'=>decode_html(decode_html(vtranslate($fields->get('label'),$index))),'type'=>array('picklistValues'=>$picklistValues1,'name'=>$fieldtype,'defaultValue'=>html_entity_decode($fields->get('fieldvalue'),ENT_QUOTES,'UTF-8')),'editable'=>$fields->isEditable(),'mandatory'=>$fields->isMandatory(),'isunique'=>$fields->get('isunique'), 'readonly'=>$fields->get('readonly'),'displaytype'=>$fields->get('displaytype'),'typeofdata'=>$fields->get('typeofdata'),'uitype'=>$fields->get('uitype'),'summaryfield'=>$fields->get('summaryfield'),'presence'=>$fields->get('presence'));
					}else{
						$block = $fields->get('block');
						$convertLeadFields[$count]['fields'][] = array('name'=>$fields->get('name'),'label'=>decode_html(decode_html(vtranslate($fields->get('label'),$index))),'type'=>array('name'=>$fieldtype,'defaultValue'=>html_entity_decode($fields->get('fieldvalue'),ENT_QUOTES,'UTF-8')),'mandatory'=>$fields->isMandatory(),'editable'=>$fields->isEditable(),'isunique'=>$fields->get('isunique'),'readonly'=>$fields->get('readonly'),'displaytype'=>$fields->get('displaytype'),'typeofdata'=>$fields->get('typeofdata'),'uitype'=>$fields->get('uitype'),'summaryfield'=>$fields->get('summaryfield'),'presence'=>$fields->get('presence'));
					}
					
				}
				$count++;
			}

			$result = $adb->pquery("SELECT fieldtype FROM vtiger_ws_fieldtype WHERE uitype = ?",array($assignedToFieldModel->get('uitype')));
			$fieldtype = $adb->query_result($result, 0, 'fieldtype');
			$usersWSId = CTMobile_WS_Utils::getEntityModuleWSId('Users');
			$userName = html_entity_decode($usersRecordModel->get('first_name').' '.$usersRecordModel->get('last_name'),ENT_QUOTES,'UTF-8');
			$convertLeadFields[$count] = array('module'=>"",'moduleLabel'=>"",'selected'=>false,'blockid'=>$count,'blockname'=>"",'blocklabel'=>"");
			$convertLeadFields[$count]['fields'][0] = array('name'=>$assignedToFieldModel->get('name'),'label'=>vtranslate($assignedToFieldModel->get('label')),'type'=>array('name'=>$fieldtype,'defaultValue'=>array("value"=>$usersWSId.'x'.$recordModel->get('assigned_user_id'),"label"=>$userName)),'editable'=>$assignedToFieldModel->isEditable(),'mandatory'=>$assignedToFieldModel->isMandatory(),'value'=>$assigned_user_id,'isunique'=>$assignedToFieldModel->get('isunique'),'readonly'=>$assignedToFieldModel->get('readonly'),'displaytype'=>$assignedToFieldModel->get('displaytype'),'typeofdata'=>$assignedToFieldModel->get('typeofdata'),'uitype'=>$assignedToFieldModel->get('uitype'),'summaryfield'=>$assignedToFieldModel->get('summaryfield'),'presence'=>$assignedToFieldModel->get('presence'));

			$convertLeadFields[$count]['fields'][1] = array("name"=>"transferModule","label"=>decode_html(decode_html(vtranslate('LBL_TRANSFER_RELATED_RECORD', 'Leads'))),"type"=> array('name'=>"boolean","defaultValue"=>""),								  "mandatory"=> true,
												 "isunique"=> false,
							                     "nullable"=> true,
							                     "editable"=>true,
							                     "default"=> "on",
							                     "headerfield"=> "0",
							                     "summaryfield"=> "0",
							                     "uitype"=> "56",
							                     "typeofdata"=> "C~O",
							                     "displaytype"=> "1",
							                     "quickcreate"=> "1");
			foreach($Fields as $modulename => $value){
				if($modulename != 'Potentials'){
					$transferModule = array("label"=>decode_html(decode_html(vtranslate("SINGLE_".$modulename,$modulename))),'value'=>$modulename,"selected"=>false);
					if($Fields['Contacts'] && $modulename == 'Contacts'){
						$transferModule['selected'] = true;
					}else if(!$Fields['Contacts'] && $modulename =="Accounts"){
						$transferModule['selected'] = true;
					}
					$convertLeadFields[$count]['fields'][1]['type']['values'][] = $transferModule;
				}	
			}
			
			$response->setResult(array('blocks'=>$convertLeadFields));
			return $response;
		}
		
	}

}