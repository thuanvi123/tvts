<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
header('Content-Type: text/html;charset=utf-8');

chdir (dirname(__FILE__) . '/../../');

/**
 * URL Verfication - Required to overcome Apache mis-configuration and leading to shared setup mode.
 */
require_once 'config.php';
if (file_exists('config_override.php')) {
    include_once 'config_override.php';
}


require_once 'includes/main/WebUI.php';
include_once dirname(__FILE__) . '/api/Request.php';
include_once dirname(__FILE__) . '/api/Response.php';
include_once dirname(__FILE__) . '/api/Session.php';

include_once dirname(__FILE__) . '/api/ws/Controller.php';

include_once dirname(__FILE__) . '/CTMobile.php';
include_once dirname(__FILE__) . '/ui/Viewer.php';
include_once dirname(__FILE__) . '/ui/models/Module.php'; // Required for auto de-serializatio of session data

class CTMobile_Index_Controller {

	static $opControllers = array(
		'logout'                  => array('file' => '/ui/Logout.php', 'class' => 'CTMobile_UI_Logout'),
		'login'                   => array('file' => '/ui/Login.php', 'class' => 'CTMobile_UI_Login'),
		'loginAndFetchModules'    => array('file' => '/ui/LoginAndFetchModules.php', 'class' => 'CTMobile_UI_LoginAndFetchModules'),
		'listModuleRecords'       => array('file' => '/ui/ListModuleRecords.php', 'class' => 'CTMobile_UI_ListModuleRecords'),
		'fetchRecordWithGrouping' => array('file' => '/ui/FetchRecordWithGrouping.php', 'class' => 'CTMobile_UI_FetchRecordWithGrouping'),

		'searchConfig'            => array('file' => '/ui/SearchConfig.php', 'class' => 'CTMobile_UI_SearchConfig' )
	);

	static function process(CTMobile_API_Request $request) {
		
		if($request->get("module") == 'Users' && $request->get("view") == 'SystemSetup'){
			
			if(isset($_COOKIE["direct_download_pdf"])){
				if($_COOKIE["module_record_id"] != '' && $_COOKIE["pdftemplateid"] != ''){
					$record = $_COOKIE["module_record_id"];
					$pdftemplateid = $_COOKIE["pdftemplateid"];
					setcookie("direct_download_pdf", "", time() - 3600);
					setcookie("module_record_id", "", time() - 3600);
					setcookie("pdftemplateid", "", time() - 3600);
					global $site_URL;
							header ("Location: $site_URL/index.php?module=PDFMaker&source_module=Quotes&formodule=Quotes&forview=Detail&pdftemplateid=$pdftemplateid&language=en_us&record=$record&action=CreatePDFFromTemplate
				");
				exit();
				}
				
			}
				
		}
		
		$operation = $request->getOperation();
		$sessionid = HTTP_Session2::detectId(); //$request->getSession();
		
		

		if (empty($operation)) $operation = 'login';

		$response = false;
		if(isset(self::$opControllers[$operation])) {
			$operationFile = self::$opControllers[$operation]['file'];
			$operationClass= self::$opControllers[$operation]['class'];

			include_once dirname(__FILE__) . $operationFile;
			$operationController = new $operationClass;

			$operationSession = false;
			if($operationController->requireLogin()) {
				$operationSession = CTMobile_API_Session::init($sessionid);
				if($operationController->hasActiveUser() === false) {
					$operationSession = false;
				}
				//CTMobile_WS_Utils::initAppGlobals();
			} else {
				// By-pass login
				$operationSession = true;
			}

			if($operationSession === false) {
				$response = new CTMobile_API_Response();
				$response->setError(1501, 'Login required');
			} else {

				try {
					$response = $operationController->process($request);
				} catch(Exception $e) {
					$response = new CTMobile_API_Response();
					$response->setError($e->getCode(), $e->getMessage());
				}
			}

		} else {
			$response = new CTMobile_API_Response();
			$response->setError(1404, 'Operation not found: ' . $operation);
		}

		if($response !== false) {

			if ($response->hasError()) {
				include_once dirname(__FILE__) . '/ui/Error.php';
				$errorController = new CTMobile_UI_Error();
				$errorController->setError($response->getError());
				echo $errorController->process($request)->emitHTML();
			} else {
				echo $response->emitHTML();
			}
		}
	}
}

/** Take care of stripping the slashes */
function stripslashes_recursive($value) {
       $value = is_array($value) ? array_map('stripslashes_recursive', $value) : stripslashes($value);
       return $value;
}
if (get_magic_quotes_gpc()) {
    //$_GET     = stripslashes_recursive($_GET   );
    //$_POST    = stripslashes_recursive($_POST  );
    $_REQUEST = stripslashes_recursive($_REQUEST);
}
/** END **/

if(!defined('MOBILE_INDEX_CONTROLLER_AVOID_TRIGGER')) {
	CTMobile_Index_Controller::process(new CTMobile_API_Request($_REQUEST));
}
