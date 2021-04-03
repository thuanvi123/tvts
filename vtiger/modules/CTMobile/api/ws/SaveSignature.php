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

class CTMobile_WS_SaveSignature extends CTMobile_WS_FetchRecordWithGrouping {
	protected $recordValues = false;
	
	function process(CTMobile_API_Request $request) {
		global $adb,$current_user; // Required for vtws_update API
		$current_user = $this->getActiveUser();
		$module = trim($request->get('module'));
		$record = trim($request->get('record'));
		$fieldname = trim($request->get('fieldname'));
		$fieldType = trim($request->get('fieldType'));
		if($module == ''){
			$message = $this->CTTranslate('Invalid Module name');
			throw new WebServiceException(404,$message);
		}
		if($record == ''){
			$message = $this->CTTranslate('Record cannot be empty');
			throw new WebServiceException(404,$message);
		}
		if (!empty($_FILES)) {
			$sourceRecord = explode('x',$record);
			$parentRecordId = $sourceRecord[1];

			$getFolder = $adb->pquery('SELECT * FROM `vtiger_attachmentsfolder`',array());
			$folderid = $adb->query_result($getFolder,0,'folderid');


			if($fieldType == 'Documents'){
				$uploadedFileNames = array();
	            foreach ($_FILES as $key => $files) {

	            	$notes_title = pathinfo($files['name'], PATHINFO_FILENAME);

					$moduleName = 'Documents';
					$recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
					$recordModel->set('mode', '');
					$recordModel->set('notes_title', $notes_title);
					$recordModel->set('folderid',$folderid);
					$recordModel->set('assigned_user_id', $current_user->id);
					$recordModel->set('notecontent', '');
					$recordModel->save();
					$documentid = $recordModel->getId();

					if($module == 'Events'){
						$module = 'Calendar';
					}
					$parentModuleModel = Vtiger_Module_Model::getInstance($module);
					$relatedModule = $recordModel->getModule();
					$relationModel = Vtiger_Relation_Model::getInstance($parentModuleModel, $relatedModule);
					$relationModel->addRelation($parentRecordId, $documentid);

					$uploadedFileNames = $this->uploadAndSaveFiles($files,$documentid);
				}

				$fileUrl = $uploadedFileNames['fileUrl'];
				$fileid = $uploadedFileNames['fileid'];
			}else{

				$notes_title = pathinfo($_FILES['filename']['name'], PATHINFO_FILENAME);

				$moduleName = 'Documents';
				$recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
				$recordModel->set('mode', '');
				$recordModel->set('notes_title', $notes_title);
				$recordModel->set('folderid',$folderid);
				$recordModel->set('assigned_user_id', $current_user->id);
				$recordModel->set('notecontent', '');
				$recordModel->save();
				$documentid = $recordModel->getId();

				if($module == 'Events'){
					$module = 'Calendar';
				}
				$parentModuleModel = Vtiger_Module_Model::getInstance($module);
				$relatedModule = $recordModel->getModule();
				$relationModel = Vtiger_Relation_Model::getInstance($parentModuleModel, $relatedModule);
				$relationModel->addRelation($parentRecordId, $documentid);

				$files = $_FILES['filename'];
	            $data = $this->uploadAndSaveFiles($files,$documentid);
	            $fileUrl = $data['fileUrl'];
	            $fileid = $data['fileid'];
			}

			$this->recordValues = vtws_retrieve($record, $current_user);
			$fieldnameValue = $this->recordValues[$fieldname];
            if($fieldnameValue != '' && $fieldType == 'Documents'){
            	$fileUrl = $fieldnameValue.",".$fileUrl;
            }
			$this->recordValues[$fieldname] = $fileUrl;
			$this->recordValues = vtws_update($this->recordValues, $current_user);

            if($fieldType == 'Documents'){
            	$message = $this->CTTranslate('Files/photos uploaded successfully');
            }else{
            	$message = $this->CTTranslate('Digital signature saved');
            }
            
            $response = new CTMobile_API_Response();
			$response->setResult(array('fileid'=>$fileid,"message"=>$message));
			return $response;

        }else{
			$response = new CTMobile_API_Response();
			$message = $this->CTTranslate('Please upload Signature or Files/photos');
			$response->setError(403,$message);
			return $response; 
		}
		
	}

	function uploadAndSaveFiles($files,$documentid){
		if (!empty($files)) {
            global $adb,$site_URL,$root_directory;
            $current_user = $this->getActiveUser();
            $moduleName = 'Documents';
            $storagePath = 'storage/';
            $year  = date('Y');
            $month = date('F');
            $day   = date('j');
            $week  = '';
            
			$date_var = date("Y-m-d H:i:s");
			
            if (!is_dir($root_directory.$storagePath . $year)) {
                mkdir($root_directory.$storagePath . $year);
                chmod($root_directory.$storagePath . $year, 0777);
            }

            if (!is_dir($root_directory.$storagePath . $year . "/" . $month)) {
                mkdir($root_directory.$storagePath . "$year/$month");
                chmod($root_directory.$storagePath . "$year/$month", 0777);
            }

            if ($day > 0 && $day <= 7){
                $week = 'week1';
            }elseif ($day > 7 && $day <= 14){
                $week = 'week2';
            }elseif ($day > 14 && $day <= 21){
                $week = 'week3';
            }elseif ($day > 21 && $day <= 28){
                $week = 'week4';
            }else{
                $week = 'week5'; 
            }
            
            if (!is_dir($root_directory.$storagePath . $year . "/" . $month . "/" . $week)) {
                mkdir($root_directory.$storagePath . "$year/$month/$week");
                chmod($root_directory.$storagePath . "$year/$month/$week", 0777);
            }
            $interior = $storagePath . $year . "/" . $month . "/" . $week . "/";
            $crm_id = $adb->getUniqueID("vtiger_crmentity");
            $upload_status = move_uploaded_file($files['tmp_name'],$interior.$crm_id.'_'. $files['name']);
            if($upload_status){
	            $delquery = 'delete from vtiger_seattachmentsrel where crmid = ?';
				$adb->pquery($delquery, array($documentid));
				
	            $lastInsertedId = $adb->pquery("select attachmentsid from vtiger_attachments order by attachmentsid DESC limit 0,1");
	            $attachmentsid = $adb->query_result($lastInsertedId, 0, 'attachmentsid');
	            $query1 = $adb->pquery("insert into vtiger_crmentity (`crmid`,`setype`) VALUES(?,?)",array($crm_id,'Documents Attachment'));
	            $query2 = $adb->pquery("insert into vtiger_attachments (`attachmentsid`,`name`,`type`,`path`) VALUES(?,?,?,?)",array($crm_id,$files['name'],$files['type'],$interior));
	            $grtLastInserted = $adb->pquery("select attachmentsid,subject from vtiger_attachments where attachmentsid > ".$attachmentsid);
	            $total = $adb->num_rows($grtLastInserted);
	            for ($i=0; $i < $total; $i++) { 
	                $grtAttachmentsId = $adb->query_result($grtLastInserted, $i, 'attachmentsid');
	                $subject = $adb->query_result($grtLastInserted, $i, 'subject');
	                $adb->pquery("insert into vtiger_seattachmentsrel (`crmid`,`attachmentsid`) VALUES(?,?)",array($documentid,$grtAttachmentsId));
	            }
	            $adb->pquery("UPDATE vtiger_notes SET filename = '".$files['name']."', filetype = '".$files['type']."', filelocationtype = 'I', filesize = '".$files['size']."', filestatus = '1' WHERE notesid = ".$documentid);
            }
            $fileUrl =  $site_URL.$interior.$crm_id.'_'. $files['name'];
            
            $data['fileid'] = $crm_id;
            $data['fileUrl'] = $fileUrl;
			return $data;     
        }
	}

		
}
