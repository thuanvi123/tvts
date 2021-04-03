<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

chdir (dirname(__FILE__) . '/../../');

include_once 'config.php';
include_once 'include/Webservices/Relation.php';

include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';


setcookie("module_record_id", $_REQUEST['module_record_id'], time() + (86400 * 30), "/");
setcookie("pdftemplateid", $_REQUEST['pdftemplateid'], time() + (86400 * 30), "/");
setcookie("direct_download_pdf", "yes", time() + (86400 * 30), "/");

$webUI = new Vtiger_WebUI();
$webUI->process(new Vtiger_Request($_REQUEST, $_REQUEST));

