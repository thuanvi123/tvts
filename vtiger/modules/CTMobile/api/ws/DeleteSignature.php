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
include_once 'include/Webservices/Delete.php';
include_once 'include/Webservices/Create.php';
include_once 'include/Webservices/Update.php';

class CTMobile_WS_DeleteSignature extends CTMobile_WS_FetchRecordWithGrouping {
	protected $recordValues = false;
	
	function process(CTMobile_API_Request $request) {
		global $adb,$current_user; // Required for vtws_update API
		$current_user = $this->getActiveUser();
		$module = trim($request->get('module'));
		$fileid = trim($request->get('fileid'));
		$record = trim($request->get('record'));
		$fieldname = trim($request->get('fieldname'));
		$fieldType = trim($request->get('fieldType'));
		if($module == ''){
			$message = $this->CTTranslate('Invalid Module name');
			throw new WebServiceException(404,$message);
		}
		if($fileid == '' || $record == ''){
			$message = $this->CTTranslate('Record cannot be empty');
			throw new WebServiceException(404,$message);
		}

		$RelQuery = $adb->pquery("SELECT vtiger_attachments.name,vtiger_attachments.path,vtiger_seattachmentsrel.crmid FROM `vtiger_attachments` INNER JOIN vtiger_seattachmentsrel ON vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid WHERE vtiger_attachments.attachmentsid = ?",array($fileid));
		$crmid = $adb->query_result($RelQuery,0,'crmid');
		$name = $adb->query_result($RelQuery,0,'name');
		$path = $adb->query_result($RelQuery,0,'path');

		if (!empty($crmid)) {

			$deleteRecord = CTMobile_WS_Utils::getEntityModuleWSId('Documents').'x'.$crmid;
			//$delete = vtws_delete($deleteRecord, $current_user);

				
			$this->recordValues = vtws_retrieve($record, $current_user);
			$fieldnameValue = $this->recordValues[$fieldname];

            if($fieldnameValue != '' && $fieldType == 'Documents'){
            	$urlofdoc = $site_URL.$path.$fileid.'_'.$name;
            	if(strpos($fieldnameValue,','.$urlofdoc.',') !== false){
            		$fileUrl = str_replace(','.$urlofdoc,"",$fieldnameValue);
            	}else if(strpos($fieldnameValue,','.$urlofdoc) !== false){
            		$fileUrl = str_replace(','.$urlofdoc,"",$fieldnameValue);
            	}else if(strpos($fieldnameValue,$urlofdoc.',') !== false){
            		$fileUrl = str_replace($urlofdoc.',',"",$fieldnameValue);
            	}else if(strpos($fieldnameValue,$urlofdoc) !== false){
            		$fileUrl = str_replace($urlofdoc,"",$fieldnameValue);
            	}
            }else{
            	$fileUrl = "";
            }
			$this->recordValues[$fieldname] = $fileUrl;
			$this->recordValues = vtws_update($this->recordValues, $current_user);

            if($fieldType == 'Documents'){
            	$message = $this->CTTranslate('Files/photos deleted successfully');
            }else{
            	$message = $this->CTTranslate('Digital signature deleted');
            }
            
            $response = new CTMobile_API_Response();
			$response->setResult(array("message"=>$message));
			return $response;

        }else{
			$response = new CTMobile_API_Response();
			$message = $this->CTTranslate('Something went wrong');
			$response->setError(403,$message);
			return $response; 
		}
		
	}

		
}
