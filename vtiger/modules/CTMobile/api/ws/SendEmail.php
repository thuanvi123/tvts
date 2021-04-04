<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

require_once 'modules/Emails/class.phpmailer.php';
require_once 'modules/Emails/mail.php';   
class CTMobile_WS_SendEmail extends CTMobile_WS_Controller {
	
	function getFromEmailAddress() {
		global $current_user;
		$current_user = $this->getActiveUser();
		$currentUserModel = Users_Record_Model::getCurrentUserModel();

		$fromEmail = $currentUserModel->get('email1');
		return $fromEmail;
	}
	

	function process(CTMobile_API_Request $request) {
		global $root_directory, $adb, $current_user;
		$current_user = $this->getActiveUser();
		$moduleName = trim($request->get('module'));
		$recordid = trim($request->get('record'));
		$record = explode('x', $recordid);
		$toEmailInfo = $request->get('to');
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$first_name = $currentUserModel->get('first_name');
		$last_name = $currentUserModel->get('last_name');
		$fromName = $first_name.' '.$last_name;

		$body = $request->get('body');
		$subject = trim($request->get('subject'));
		
		if(empty($moduleName) || empty($toEmailInfo) || empty($body) || empty($subject)){
			$message = $this->CTTranslate('Required fields not found');
			throw new WebServiceException(404,$message);
		}
		// if(!filter_var($toEmailInfo, FILTER_VALIDATE_EMAIL)) {
			// throw new WebServiceException(419,'Invalid Email');
		// }
		$fromEmail = $this->getFromEmailAddress();

		global $root_directory, $adb, $current_user;
		$current_user = $this->getActiveUser();
		$current_user_id = $current_user->id;
		$crm_id = $adb->getUniqueID("vtiger_crmentity");
	    $lastInsertedId = $adb->pquery("select crmid from vtiger_crmentity order by crmid DESC limit 0,1");
     	$Unique_id = $adb->query_result($lastInsertedId, 0, 'crmid');

		
		$startDateTime = date('Y-m-d H:i:s');
		$datetime  = explode(' ', $startDateTime);
		$createdtime =  $startDateTime;
		$modifiedtime = $startDateTime;
		$date_start = $datetime[0];
		$time_start = $datetime[1];
		$uniquecrmid = $Unique_id+1;
		$toEmailInfo = explode(",",$toEmailInfo);

		if(is_array($toEmailInfo)){
			$to = implode(',', $toEmailInfo);
			$to = '["'.$to.'"]';
			$toEmailInfo = implode(',', $toEmailInfo);
		}else{
			$to = '["'.$toEmailInfo.'"]';
		}

		$crmentity_query = "insert into vtiger_crmentity(crmid,smcreatorid,smownerid,modifiedby,setype,description,presence,createdtime,modifiedtime,label)values ('$uniquecrmid','$current_user_id','$current_user_id','$current_user_id','Emails','$body','1', '$createdtime','$modifiedtime','$body')";
		$crmentity_query_result = $adb->pquery($crmentity_query,array());

		$emaildetails = "INSERT into vtiger_emaildetails (emailid,from_email,to_email,cc_email,bcc_email,assigned_user_email,idlists) values ('$uniquecrmid','$fromEmail','$to','','','','$record[1]@$current_user_id|')";
		$emaildetail_result = $adb->pquery($emaildetails,array());

		$activitysql = "insert into vtiger_activity (activityid,subject,activitytype,date_start,time_start,visibility) values ('$uniquecrmid','$subject','Emails','$date_start','$time_start','all')";
		$activity_result = $adb->pquery($activitysql,array());

		$seactivityrel = "insert into vtiger_seactivityrel (crmid,activityid) values ('$record[1]','$uniquecrmid')";
		$seactivity_result = $adb->pquery($seactivityrel,array());

		$mailtrackid = "INSERT INTO vtiger_email_track(crmid, mailid,  access_count) VALUES('$record[1]','$uniquecrmid','0')";
		$mailtrack_result = $adb->pquery($mailtrackid,array());

		$updatecrmid_seq = "update vtiger_crmentity_seq set id='$uniquecrmid'";
		$updateresult_seq =  $adb->pquery($updatecrmid_seq,array());
		if(!empty($_FILES)){
			foreach ($_FILES as $key => $files) {
				$uploadedFileNames[] = $this->uploadAndSaveFiles($files,$uniquecrmid,'Emails');
			}
		}
		$status = send_mail($moduleName, $toEmailInfo, $fromName, $fromEmail, $subject, $body,'','','all',$uniquecrmid,'',true);
		if($status != 1) {
			$message = $this->CTTranslate('Could not send mail, Please try later');
			$result = array('code' => 0,'message' => $message);
		}else{
			$message = $this->CTTranslate('Mail send successfully');
			$result = array('code' => 1,'message' => $message);
			$updatecrmid = "update vtiger_emaildetails set email_flag='SENT' where emailid='".$uniquecrmid."' ";
			$updateresult =  $adb->pquery($updatecrmid,array());
		}
		
		$response = new CTMobile_API_Response();
		$response->setResult($result);
		return $response;
	}


	function uploadAndSaveFiles($files,$uniquecrmid,$module){
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
	            $lastInsertedId = $adb->pquery("select attachmentsid from vtiger_attachments order by attachmentsid DESC limit 0,1");
	            $attachmentsid = $adb->query_result($lastInsertedId, 0, 'attachmentsid');
	            $query1 = $adb->pquery("insert into vtiger_crmentity (`crmid`,`setype`) VALUES(?,?)",array($crm_id,'Emails Attachment'));
	            $query2 = $adb->pquery("insert into vtiger_attachments (`attachmentsid`,`name`,`type`,`path`) VALUES(?,?,?,?)",array($crm_id,$files['name'],$files['type'],$interior));
	            $grtLastInserted = $adb->pquery("select attachmentsid,subject from vtiger_attachments where attachmentsid > ".$attachmentsid);
	            $total = $adb->num_rows($grtLastInserted);
	            for ($i=0; $i < $total; $i++) { 
	                $grtAttachmentsId = $adb->query_result($grtLastInserted, $i, 'attachmentsid');
	                $subject = $adb->query_result($grtLastInserted, $i, 'subject');
	                $adb->pquery("insert into vtiger_seattachmentsrel (`crmid`,`attachmentsid`) VALUES(?,?)",array($uniquecrmid,$grtAttachmentsId));
	            }
            }
			return $crm_id;     
        }
	}
}
		
