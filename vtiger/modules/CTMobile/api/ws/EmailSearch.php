<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

class CTMobile_WS_EmailSearch extends CTMobile_WS_Controller {

	function process(CTMobile_API_Request $request) {
		$default_charset = VTWS_PreserveGlobal::getGlobal('default_charset');
		global $current_user,$adb, $site_URL;
		$current_user = $this->getActiveUser();
		$db = PearDatabase::getInstance();
		$searchValue = $request->get('searchValue');

		$EmailsModuleModel = Vtiger_Module_Model::getInstance('Emails');
		$emailSupportedModulesList = $EmailsModuleModel->getEmailRelatedModules();
		foreach ($emailSupportedModulesList as $module) {
			if ($module != 'Users' && $module != 'ModComments') {
                    $activeModules[] = "'".$module."'";
                    $activeModuleModel = Vtiger_Module_Model::getInstance($module);
                    $moduleEmailFields = $activeModuleModel->getFieldsByType('email');
					foreach ($moduleEmailFields as $fieldName => $fieldModel) {
						if ($fieldModel->isViewable()) {
								$fieldIds[] = $fieldModel->get('id');
							}
						}
					}
				}

			if ($moduleName) {
                $activeModules = array("'".$moduleName."'");
            }
            
            $query = "SELECT vtiger_emailslookup.crmid, vtiger_emailslookup.setype, vtiger_emailslookup.value, 
                          vtiger_crmentity.label FROM vtiger_emailslookup INNER JOIN vtiger_crmentity on 
                          vtiger_crmentity.crmid = vtiger_emailslookup.crmid AND vtiger_crmentity.deleted=0 WHERE 
						  vtiger_emailslookup.fieldid in (".implode(',', $fieldIds).") and 
						  vtiger_emailslookup.setype in (".implode(',', $activeModules).") 
                          and (vtiger_emailslookup.value LIKE ? OR vtiger_crmentity.label LIKE ?)";

			$emailOptOutIds = $this->getEmailOptOutRecordIds();
			if (!empty($emailOptOutIds)) {
				$query .= " AND vtiger_emailslookup.crmid NOT IN (".implode(',', $emailOptOutIds).")";
			}

			$result = $db->pquery($query, array('%'.$searchValue.'%', '%'.$searchValue.'%'));
            $isAdmin = is_admin($current_user);
			while ($row = $db->fetchByAssoc($result)) {
				if (!$isAdmin) {
					$recordPermission = Users_Privileges_Model::isPermitted($row['setype'], 'DetailView', $row['crmid']);
					if (!$recordPermission) {
						continue;
					}
				}
			$emailsResult[] = array('module'=>$row['setype'],'moduleLabel'=>vtranslate($row['setype'], $row['setype']),'id'=>CTMobile_WS_Utils::getEntityModuleWSId($row['setype']).'x'.$row['crmid'],'value' => $row['value'],'label' => decode_html($row['label']).' <b>('.$row['value'].')</b>','name' => decode_html($row['label']));
            }
            
            // For Users we should only search in users table
            $additionalModule = array('Users');
            if(!$moduleName || in_array($moduleName, $additionalModule)){
                foreach($additionalModule as $moduleName){
                    $moduleInstance = CRMEntity::getInstance($moduleName);
                    $searchFields = array();
                    $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
                    $emailFieldModels = $moduleModel->getFieldsByType('email');

                    foreach ($emailFieldModels as $fieldName => $fieldModel) {
                        if ($fieldModel->isViewable()) {
                                $searchFields[] = $fieldName;
                        }
                    }
                    $emailFields = $searchFields;

                    $nameFields = $moduleModel->getNameFields();
                    foreach ($nameFields as $fieldName) {
                        $fieldModel = Vtiger_Field_Model::getInstance($fieldName, $moduleModel);
                        if ($fieldModel->isViewable()) {
                                $searchFields[] = $fieldName;
                        }
                    }

				if ($emailFields) {
					$userQuery = 'SELECT '.$moduleInstance->table_index.', '.implode(',',$searchFields).' FROM vtiger_users WHERE deleted=0';
                        $result = $db->pquery($userQuery, array());
                        $numOfRows = $db->num_rows($result);
                        for($i=0; $i<$numOfRows; $i++) {
                            $row = $db->query_result_rowdata($result, $i);
                            foreach ($emailFields as $emailField) {
                                    $emailFieldValue = $row[$emailField];
                                    if ($emailFieldValue) {
                                            $recordLabel = getEntityFieldNameDisplay($moduleName, $nameFields, $row);
                                            if (strpos($emailFieldValue, $searchValue) !== false || strpos($recordLabel, $searchValue) !== false) {
                                                    $emailsResult[] = array('module'=>$moduleName,'moduleLabel'=>vtranslate($moduleName, $moduleName),'id'=>CTMobile_WS_Utils::getEntityModuleWSId($moduleName).'x'.$row[$moduleInstance->table_index],'value'	=> $emailFieldValue,'name'	=> $recordLabel,'label'	=> $recordLabel. ' <b>('.$emailFieldValue.')</b>');

                                            }
                                    }
                            }
                        }
                    }
                }
            }
            $response = new CTMobile_API_Response();
            if(!empty($emailsResult)){
            	$response->setResult(array("emailsResult"=>$emailsResult,"message"=>""));
            }else{
            	$message = $this->CTTranslate('No records found');
            	$response->setResult(array("emailsResult"=>array(),"message"=>$message));
            }
            return $response;

    }

    function getEmailOptOutRecordIds() {
		$emailOptOutIds = array();
		$db = PearDatabase::getInstance();
		$contactResult = $db->pquery("SELECT crmid FROM vtiger_crmentity INNER JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted = ? AND vtiger_contactdetails.emailoptout = ?", array('0', '1'));
		$contactCount = $db->num_rows($contactResult);
		for($i = 0; $i < $contactCount; $i++) {
			$emailOptOutIds[] = $db->query_result($contactResult, $i, 'crmid');
		}
		$accountResult = $db->pquery("SELECT crmid FROM vtiger_crmentity INNER JOIN vtiger_account ON vtiger_account.accountid = vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted = ? AND vtiger_account.emailoptout = ?", array('0', '1'));
		$accountCount = $db->num_rows($accountResult);
		for($i = 0; $i < $accountCount; $i++) {
			$emailOptOutIds[] = $db->query_result($accountResult, $i, 'crmid');
		}
		$leadResult = $db->pquery("SELECT crmid FROM vtiger_crmentity INNER JOIN vtiger_leaddetails ON vtiger_leaddetails.leadid = vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted = ? AND vtiger_leaddetails.emailoptout = ?", array('0', '1'));
		$leadCount = $db->num_rows($leadResult);
		for($i = 0; $i < $leadCount; $i++) {
			$emailOptOutIds[] = $db->query_result($leadResult, $i, 'crmid');
		}
		
		return $emailOptOutIds;
	}

}