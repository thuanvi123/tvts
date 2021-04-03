<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class CTMobileSettings_WhatsNew_View extends Vtiger_Popup_View {
	protected $listViewEntries = false;
	protected $listViewHeaders = false;
	protected $listQuery = "";
	protected $listViewCount = 0;
	/**
	 * Function returns the module name for which the popup should be initialized
	 * @param Vtiger_request $request
	 * @return <String>
	 */
	function getModule(Vtiger_request $request) {
		$moduleName = $request->getModule();
		return $moduleName;
	}

	function process (Vtiger_Request $request) {
		$viewer = $this->getViewer ($request);
		$moduleName = $this->getModule($request);
		
		$viewer->view('WhatsNew.tpl', $moduleName);
	}

	

	/**
	 * Function to get the list of Script models to be included
	 * @param Vtiger_Request $request
	 * @return <Array> - List of Vtiger_JsScript_Model instances
	 */
	function getHeaderScripts(Vtiger_Request $request) {
		$headerScriptInstances = parent::getHeaderScripts($request);
		$moduleName = $request->getModule();

		$jsFileNames = array(
						'libraries.bootstrap.js.eternicode-bootstrap-datepicker.js.bootstrap-datepicker',
			'~libraries/bootstrap/js/eternicode-bootstrap-datepicker/js/locales/bootstrap-datepicker.'.Vtiger_Language_Handler::getShortLanguageName().'.js',
			'~libraries/jquery/timepicker/jquery.timepicker.min.js',

			'modules.Vtiger.resources.Popup',
			"modules.$moduleName.resources.Popup",
			'modules.Vtiger.resources.BaseList',
			"modules.$moduleName.resources.BaseList",
			'libraries.jquery.jquery_windowmsg',
			'modules.Vtiger.resources.validator.BaseValidator',
			'modules.Vtiger.resources.validator.FieldValidator',
			"modules.$moduleName.resources.validator.FieldValidator"
		);

		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
	}

}