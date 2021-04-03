<?php

 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
require_once 'include/utils/utils.php';
require_once 'include/utils/VtlibUtils.php';
require_once 'modules/Emails/class.phpmailer.php';
require_once 'modules/Emails/mail.php';
require_once 'modules/Vtiger/helpers/ShortURL.php';

class CTMobile_WS_ForgotPassword extends CTMobile_WS_Controller {
	function process(CTMobile_API_Request $request) {
		global $adb;
		$response = new CTMobile_API_Response();
		$username = vtlib_purify($request->get('user_name'));
		$emailId = $request->get('emailId');
		if(empty($username) || empty($emailId)){
			$message =  $this->CTTranslate('Required fields not found');
			throw new WebServiceException(404,$message);
		}
		$result = $adb->pquery('select email1 from vtiger_users where user_name= ? ', array($username));
		if ($adb->num_rows($result) > 0) {
			$email = $adb->query_result($result, 0, 'email1');
		}
		
		if (vtlib_purify($request->get('emailId')) == $email) {
			$result1 = $adb->pquery("SELECT * FROM `vtiger_systems`",array());
			$system_Count = $adb->num_rows($result1);
			if($system_Count != 0){
				$time = time();
				$options = array(
					'handler_path' => 'modules/Users/handlers/ForgotPassword.php',
					'handler_class' => 'Users_ForgotPassword_Handler',
					'handler_function' => 'changePassword',
					'handler_data' => array(
						'username' => $username,
						'email' => $email,
						'time' => $time,
						'hash' => md5($username . $time)
					)
				);
				$trackURL = Vtiger_ShortURL_Helper::generateURL($options);
				$content = 'Dear Customer,<br><br> 
									You recently requested a password reset for your CRM .<br> 
									To create a new password, click on the link <a target="_blank" href=' . $trackURL . '>here</a>. 
									<br><br> 
									This request was made on ' . date("Y-m-d H:i:s") . ' and will expire in next 24 hours.<br><br> 
							Regards,<br> 
							CRMTiger Team.<br>' ;
				$mail = new PHPMailer();
				$query = "select from_email_field,server_username from vtiger_systems where server_type=?";
				$params = array('email');
				$result = $adb->pquery($query,$params);
				$from = $adb->query_result($result,0,'from_email_field');
				if($from == '') {$from =$adb->query_result($result,0,'server_username'); }
				$subject='Request : ForgotPassword - CRMTiger';
				
				setMailerProperties($mail,$subject, $content, $from, $username, $email);
				$status = MailSend($mail);
				if ($status === 1){
				   $statusMessage =  $this->CTTranslate('Mail send successfully');
				   $result = array('code' => 1,'message' => $statusMessage);
				   $response->setResult($result);
				}else{
				   $statusMessage =  $this->CTTranslate('Mail not sent');
				   $result = array('code' => 0,'message' => $statusMessage);
				   $response->setError(0,$statusMessage);
				}
			}else{
				 $statusMessage =  $this->CTTranslate('Outgoing server is not enabled, please configure from CRM');
				 $result = array('code' => 0,'message' => $statusMessage);
				 $response->setError(0,$statusMessage);
			}
		}else {
			$statusMessage =  $this->CTTranslate('Email Id or username not match with your record');
			$result = array('code' => 0, 'message' => $statusMessage);
			$response->setError(0,$statusMessage);
		}
  						
		return $response;
		
	}
}
