<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
include_once 'include/Webservices/Retrieve.php';
include_once dirname(__FILE__) . '/FetchRecord.php';
include_once 'include/Webservices/DescribeObject.php';

class CTMobile_WS_GetFolderList extends CTMobile_WS_FetchRecord {
	
	protected $mConnector = false;
	
	public $mUsername;
	
	/**
	 * MailBox folder name
	 * @var string
	 */
	protected $mFolder = false;

	/**
	 * Connector to the IMAP server
	 * @var MailManager_Mailbox_Model
	 */
	protected $mMailboxModel = false;
	
	public $mBox;

	function process(CTMobile_API_Request $request) {

		global $current_user,$adb;
		$current_user = $this->getActiveUser();
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		 
		$response = new CTMobile_API_Response();
		$result = $adb->pquery("SELECT * FROM vtiger_mail_accounts WHERE user_id=? AND status=1 AND set_default=0", array($currentUserModel->getId()));
		if ($adb->num_rows($result)) {

			$query = "SELECT id FROM vtiger_systems WHERE server_type='email'";
			$result = $adb->pquery($query,array());
			if($adb->num_rows($result) > 0){
				$EmailModuleModel = Vtiger_Module_Model::getInstance('Emails');
				if(!in_array($EmailModuleModel->get('presence'), array(0,2))){
					$isComposeMail = false;
				}else{
					$currentUserPriviligesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
					if(!$currentUserPriviligesModel->hasModulePermission($EmailModuleModel->getId())) {
						$isComposeMail = false;
					}else{
						$isComposeMail = true;
					}
				}
			}else{
				$isComposeMail = false;
			}
			
			$connector = $this->getConnector();
			
			$folderList = $connector->folders();
			$connector->updateFolders();
			$folderListWithCount = array();
			
			foreach($folderList as $folder){
				if($folder->unreadCount() == ''){
					$unreadCount = 0;
				}else{
					$unreadCount =$folder->unreadCount();
				}
				$folderListWithCount[] = array('foldename'=> $folder->name(),'unreadcount' => $unreadCount);
			}
			
			$response->setResult(array('isComposeMail'=>$isComposeMail,'folderList'=>$folderListWithCount, 'module'=>'MailManager', 'mUsername'=>$this->mUsername, 'message'=>''));
		}else{
			$message = $this->CTTranslate('No MailBox found. Please Create Mailbox');
			throw new WebServiceException(404,$message);
		}	
		
		return $response;
	}

	public function getConnector($folder='') {
		if (!$this->mConnector || ($this->mFolder != $folder)) {
			
			if($folder == "__vt_drafts") {
				$draftController = new MailManager_Draft_View();
				$this->mConnector = $draftController->connectorWithModel();
			} else {
				if ($this->mConnector) $this->mConnector->close();

				$model = $this->getMailboxModel();
				$this->mUsername =  $model->mUsername;
				$this->mConnector = MailManager_Connector_Connector::connectorWithModel($model, $folder);
			}
			$this->mFolder = $folder;
		}
		return $this->mConnector;
	}
	
	public function getMailboxModel() {
		if ($this->mMailboxModel === false) {
			$this->mMailboxModel = MailManager_Mailbox_Model::activeInstance();
		}
		return $this->mMailboxModel;
	}
	
}
