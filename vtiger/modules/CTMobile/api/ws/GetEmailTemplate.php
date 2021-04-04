<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

class CTMobile_WS_GetEmailTemplate extends CTMobile_WS_Controller {
	
	public $totalQuery = "";
	public $totalParams = array();

	function process(CTMobile_API_Request $request) {
		global $adb, $current_user;
		$current_user = $this->getActiveUser();
		$index = trim($request->get('index'));
		$size = trim($request->get('size'));

		$getTemplateQuery = "SELECT vtiger_emailtemplates.templateid,vtiger_emailtemplates.foldername,vtiger_emailtemplates.templatename,vtiger_emailtemplates.subject, vtiger_emailtemplates.description, vtiger_emailtemplates.body,vtiger_emailtemplates.module FROM vtiger_emailtemplates LEFT JOIN vtiger_tab ON vtiger_tab.name = vtiger_emailtemplates.module AND (vtiger_tab.isentitytype=1 or vtiger_tab.name = 'Users') WHERE (vtiger_tab.presence IN (0,2) OR vtiger_emailtemplates.module IS null OR vtiger_emailtemplates.module = '')";

		$params = array();
		
		if($index != '' || $size != '') {
			$this->totalQuery = $getTemplateQuery;
			$this->totalParams = $params;
			$limit = ($index*$size) - $size;
			$getTemplateQuery .= " LIMIT ".$limit.",".$size;
		}

		$getTemplateResult = $adb->pquery($getTemplateQuery, $params);

		$countTemplate = $adb->num_rows($getTemplateResult);
		
		for($i=0;$i<$countTemplate;$i++){
			$emailTemplateId = trim($adb->query_result($getTemplateResult, $i, 'templateid'));
			$template_name = trim($adb->query_result($getTemplateResult, $i, 'templatename'));
			$subject = trim($adb->query_result($getTemplateResult, $i, 'subject'));
			$description = trim($adb->query_result($getTemplateResult, $i, 'description'));
			$module = trim($adb->query_result($getTemplateResult, $i, 'module'));
			$template_content = decode_html(decode_html(trim($adb->query_result($getTemplateResult, $i, 'body'))));

			$messageTemplateData[] = array('emailTemplateId' => $emailTemplateId, 'template_name' => $template_name, 'subject' => $subject, 'description' => $description, 'module' => $module, 'template_content' => $template_content); 
		}

	 	$isLast = true;
		if($this->totalQuery != ""){
			$totalResults = $adb->pquery($this->totalQuery,$this->totalParams);
			$totalRecords = $adb->num_rows($totalResults);
			if($totalRecords > $index*$size){
				$isLast = false;	
			}else{
				$isLast = true;
			}
		}
		
		$response = new CTMobile_API_Response();
		if ($countTemplate == 0) {
			$message = $this->CTTranslate('No templates found - Create it from Email Templates module');
			$response->setResult(array('records'=>array(),'code'=>404,'message'=>$message,"isLast"=>$isLast));
		}else{
			$response->setResult(array('records'=>$messageTemplateData,'code'=>'','message'=>'',"isLast"=>$isLast));
		}
		
		return $response;
	}
}

?>
