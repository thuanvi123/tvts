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

class CTMobile_WS_FetchCommentModules extends CTMobile_WS_FetchRecord {

	function process(CTMobile_API_Request $request) {
		global $current_user,$adb, $site_URL;
		$current_user = $this->getActiveUser();
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$query = "SELECT vtiger_tab.name, vtiger_tab.tabid FROM vtiger_relatedlists INNER JOIN vtiger_tab ON vtiger_relatedlists.tabid = vtiger_tab.tabid where vtiger_relatedlists.presence = 0 AND vtiger_relatedlists.label=?";
		$params = array("ModComments");
		$result = $adb->pquery($query , $params);
		$numrows = $adb->num_rows($result);
		$CommentsModule = array();
		for($i=0;$i<$numrows;$i++){
			$CommentsModule[] = $adb->query_result($result,$i,'name');
		}
		
		$query2 = "SELECT vtiger_tab.name, vtiger_tab.tabid FROM vtiger_relatedlists INNER JOIN vtiger_tab ON vtiger_relatedlists.tabid = vtiger_tab.tabid where vtiger_relatedlists.presence = 0 AND vtiger_relatedlists.label=?";
		$params2 = array("Activities");
		$result2 = $adb->pquery($query2 , $params2);
		$numrows = $adb->num_rows($result2);
		$ActivitiesModule = array();
		for($i=0;$i<$numrows;$i++){
			$ActivitiesModule[] = $adb->query_result($result2,$i,'name');
		}
		
		$query3 = "SELECT * FROM  `vtiger_tab` WHERE  `isentitytype` =1 AND  `presence` =0";
		$result3 = $adb->pquery($query3 ,array());
		$numrows = $adb->num_rows($result3);
		$SummaryModule = array();
		for($i=0;$i<$numrows;$i++){
			$Module = $adb->query_result($result3,$i,'name');
			$moduleModel = Vtiger_Module_Model::getInstance($Module); 
			if($moduleModel->isSummaryViewSupported()) {
				$SummaryModule[] = $Module;
			}else{
				continue;
			}
		}
		$response = new CTMobile_API_Response();
		$response->setResult(array('CommentsModule'=>$CommentsModule,'ActivitiesModule'=>$ActivitiesModule,'SummaryModule'=>$SummaryModule));
		return $response;
		
	}

	
}
