<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

class CTMobile_WS_GetPDFTemplate extends CTMobile_WS_Controller {
	
	
	function process(CTMobile_API_Request $request) {
		global $adb, $current_user;
		
		$module = trim($request->get('module'));
		if($module == ''){
			$message = $this->CTTranslate('Required fields not found');
			throw new WebServiceException(404,$message);
		}
		$getTemplateQuery = $adb->pquery("SELECT * FROM vtiger_pdfmaker WHERE module = ? AND deleted = 0", array($module));
		$countTemplate = $adb->num_rows($getTemplateQuery);
		
		for($i=0;$i<$countTemplate;$i++){
			$templateid = trim($adb->query_result($getTemplateQuery, $i, 'templateid'));
			$filename = decode_html(decode_html(trim($adb->query_result($getTemplateQuery, $i, 'filename'))));
			$PDFTemplateData[] = array('pdftemplateid' => $templateid, 'templates_name' => $filename); 
		}
		
		$response = new CTMobile_API_Response();
		$response->setResult(array('records'=>$PDFTemplateData,'code'=>'','message'=>''));
		
		if ($countTemplate == 0) {
			$message = $this->CTTranslate('No Templates found - create it from PDF Maker');
			$response->setResult(array('records'=>array(),'code'=>404,'message'=>$message));
		}
		
		return $response;
	}
}

?>
