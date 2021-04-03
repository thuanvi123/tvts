<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

class CTMobile_WS_SaveCallLog extends CTMobile_WS_Controller {

	protected $recordValues = false;

	function process(CTMobile_API_Request $request) {
		global $adb,$current_user;
		$current_user = $this->getActiveUser();
		$assigned_user_id = $current_user->id;
		$record = trim($request->get("record"));
		$contact_id = explode('x',$request->get("contact_id"));
		$contactid = $contact_id[1];
		$subject = trim($request->get("subject"));
		$description = trim($request->get("description"));
		$date_start = trim($request->get("date_start"));
		$time_start = trim($request->get("time_start"));
		$due_date = trim($request->get("due_date"));
		$time_end = trim($request->get("time_end"));

		$startDate = $date_start;
		if(!empty($startDate)) {
			//Start Date and Time values
			$startTime = Vtiger_Time_UIType::getTimeValueWithSeconds($time_start);
			$startDateTime = Vtiger_Datetime_UIType::getDBDateTimeValue($date_start." ".$startTime);
			list($startDate, $startTime) = explode(' ', $startDateTime);
			$time_start = $startTime;
			$date_start = $startDate;
		}

		$endDate = $due_date;
		if(!empty($endDate)) {
			//End Date and Time values
			$endTime = $time_end;
			$endDate = Vtiger_Date_UIType::getDBInsertedValue($due_date);

			if ($endTime) {
				$endTime = Vtiger_Time_UIType::getTimeValueWithSeconds($endTime);
				$endDateTime = Vtiger_Datetime_UIType::getDBDateTimeValue($due_date." ".$endTime);
				list($endDate, $endTime) = explode(' ', $endDateTime);
				$time_end = $endTime;
				$due_date = $endDate;
			}
		}

		$time = (strtotime($endTime))- (strtotime($startTime));
		$diffinSec=  (strtotime($endDate))- (strtotime($startDate));
		$diff_days=floor($diffinSec/(60*60*24));
		  
		$hours=((float)$time/3600)+($diff_days*24);
		$minutes = ((float)$hours-(int)$hours)*60; 
		
		$duration_hours = $hours;
		$duration_minutes = $minutes;

		$moduleName = 'Events';
		$response = new CTMobile_API_Response();

		if($subject == '' || $contact_id == ''){
			$message = $this->CTTranslate('Required fields not found');
			throw new WebServiceException(404,$message);
		}
		$recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
		$recordModel->set('mode', '');
		$recordModel->set('subject',$subject);
		$recordModel->set('description',$description);
		if(CTMobile_WS_Utils::detectModulenameFromRecordId($request->get("contact_id")) == 'Contacts'){
			$recordModel->set('contact_id',$contactid);
		}else{
			$recordModel->set('parent_id',$contactid);
		}
		$recordModel->set('eventstatus','Held');
		$recordModel->set('activitytype','Call');
		$recordModel->set('date_start',$date_start);
		$recordModel->set('time_start',$time_start);
		$recordModel->set('due_date',$due_date);
		$recordModel->set('time_end',$time_end);
		$recordModel->set('visibility','Public');
		$recordModel->set('assigned_user_id',$assigned_user_id);

		$recordModel->save();
		$moduleWSId = CTMobile_WS_Utils::getEntityModuleWSId($moduleName);
		$recordId = $recordModel->getId();
		$this->recordValues['id'] = $moduleWSId.'x'.$recordId;

		if(!empty($_FILES['filename'])){
			$moduleModel = Vtiger_Module_Model::getInstance('Documents');
			if(in_array($moduleModel->get('presence'), array('0', '2'))){

				$DocumentrecordModel = Vtiger_Record_Model::getCleanInstance('Documents');
				$DocumentrecordModel->set('notes_title',$subject);
				$DocumentrecordModel->set('assigned_user_id',$assigned_user_id);
				$DocumentrecordModel->set('notecontent',$subject);
				$DocumentrecordModel->set('filedownloadcount','0');
				$DocumentrecordModel->set('folderid','1');
				$DocumentrecordModel->set('source','CRM');
				$DocumentrecordModel->save();
					
				$parentModuleName = 'Calendar';
				$parentModuleModel = Vtiger_Module_Model::getInstance($parentModuleName);
				$parentRecordId = $recordId;
				$relatedModule = $DocumentrecordModel->getModule();
				$relatedRecordId = $DocumentrecordModel->getId();

				$relationModel = Vtiger_Relation_Model::getInstance($parentModuleModel, $relatedModule);
				$relationModel->addRelation($parentRecordId, $relatedRecordId);

				$this->uploadAndSaveFiles($_FILES['filename'],$relatedRecordId,'Documents');
				$query = "UPDATE vtiger_notes SET filestatus = '1' WHERE notesid = ?";
				$result = $adb->pquery($query,array($relatedRecordId));
			}
		}

		$recordLabel = $subject;
		$message = $this->CTTranslate('Call Log save successfully');
		$result = array('id'=>$this->recordValues['id'],'recordLabel'=>$recordLabel,'module'=>$moduleName,'message'=>$message);
		$response->setResult($result);
		return $response;
	}

	function uploadAndSaveFiles($files,$docid,$module){
		if (!empty($files)) {
            global $adb,$site_URL,$root_directory;
            $current_user = $this->getActiveUser();
            $moduleName = $module;
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
				$adb->pquery($delquery, array($docid));
				
	            $lastInsertedId = $adb->pquery("select attachmentsid from vtiger_attachments order by attachmentsid DESC limit 0,1");
	            $attachmentsid = $adb->query_result($lastInsertedId, 0, 'attachmentsid');
	            $query1 = $adb->pquery("insert into vtiger_crmentity (`crmid`,`setype`) VALUES(?,?)",array($crm_id,'Documents Attachment'));
	            $query2 = $adb->pquery("insert into vtiger_attachments (`attachmentsid`,`name`,`type`,`path`) VALUES(?,?,?,?)",array($crm_id,$files['name'],$files['type'],$interior));
	            $grtLastInserted = $adb->pquery("select attachmentsid,subject from vtiger_attachments where attachmentsid > ".$attachmentsid);
	            $total = $adb->num_rows($grtLastInserted);
	            for ($i=0; $i < $total; $i++) { 
	                $grtAttachmentsId = $adb->query_result($grtLastInserted, $i, 'attachmentsid');
	                $subject = $adb->query_result($grtLastInserted, $i, 'subject');
	                $adb->pquery("insert into vtiger_seattachmentsrel (`crmid`,`attachmentsid`) VALUES(?,?)",array($docid,$grtAttachmentsId));
	            }
	            $adb->pquery("UPDATE vtiger_notes SET filename = '".$files['name']."', filetype = '".$files['type']."', filelocationtype = 'I', filesize = '".$files['size']."' WHERE notesid = ".$docid);
            }
			return $crm_id;     
        }
	}
}