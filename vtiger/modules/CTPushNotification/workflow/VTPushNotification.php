<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
require_once('modules/com_vtiger_workflow/VTEntityCache.inc');
require_once('modules/com_vtiger_workflow/VTWorkflowUtils.php');
require_once('modules/com_vtiger_workflow/VTSimpleTemplate.inc');

require_once('modules/CTPushNotification/CTPushNotification.php');
require_once('modules/CTPushNotification/PUSHNotifier.php');
class VTPushNotification extends VTTask {
	public $executeImmediately = true; 
	
	public function getFieldNames(){
		return array('content', 'sms_recepient');
	}
	
	public function doTask($entity){

			global $adb, $current_user,$log;
			
			$util = new VTWorkflowUtils();
			$admin = $util->adminUser();
			$ws_id = $entity->getId();
			$entityCache = new VTEntityCache($admin);
			
			
			$et = new VTSimpleTemplate($this->sms_recepient);
			$recepient = $et->render($entityCache, $ws_id);
			$recepients = explode(',',$recepient);
			
			$ct = new VTSimpleTemplate($this->content);
			$content = $ct->render($entityCache, $ws_id);
			$relatedCRMid = substr($ws_id, stripos($ws_id, 'x')+1);
			
			$relatedModule = $entity->getModuleName();
			
			/** Pickup only non-empty numbers */
			$tonumbers = array();
			foreach($recepients as $tonumber) {
				if(!empty($tonumber)) {
					if($tonumber == 'record_owner'){
						$userId = $entity->get('assigned_user_id');
						$assignedTo = explode('x', $userId);
						$tonumbers[] = $assignedTo[1];
					}else{
						$tonumbers[] = $tonumber;
					}	
				}
			}
			$title = $this->summary;
			$workflowId = $this->workflowId;
			
			PUSHNotifier::sendnotification($content, array_unique($tonumbers), $current_user->id, $relatedCRMid, $relatedModule,$title,$ws_id,$workflowId);
	}
}
?>
