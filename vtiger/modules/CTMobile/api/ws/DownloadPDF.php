<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
chdir (dirname(__FILE__) . '/../../../../');
include_once 'includes/main/WebUI.php';
global $current_user,$adb,$root_directory,$log;
		
$moduleName = $_REQUEST['module'];
if($moduleName == 'Invoice'){
	vimport('~~/modules/Invoice/InvoicePDFController.php');
}else if ($moduleName == 'Quotes'){
	vimport('~~/modules/Quotes/QuotePDFController.php');
}else if($moduleName == 'PurchaseOrder'){
	vimport('~~/modules/PurchaseOrder/PurchaseOrderPDFController.php');
}else if ($moduleName == 'SalesOrder'){
	vimport('~~/modules/SalesOrder/SalesOrderPDFController.php');
}
$record = $_REQUEST['record'];
$idComponents = explode('x', $record);
$recordId = $idComponents[1];

$recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
$recordModel->getPDF();

?>
