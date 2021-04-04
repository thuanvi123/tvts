<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

include_once 'config.php';
include_once 'include/Webservices/Relation.php';

include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';
	
global $adb;$current_user;

$full_license_key = $_REQUEST['license_key'];
$newlicense = explode('::',$full_license_key);
$license_key = $newlicense[1];

$getLicenseQuery=$adb->pquery("SELECT * FROM ctmobile_license_settings");
$numOfLicenseCount = $adb->num_rows($getLicenseQuery);
$record=$adb->query_result($getLicenseQuery,0,'id');
$query=$adb->pquery("UPDATE ctmobile_license_settings SET license_key=? WHERE id=?",array($license_key,$record));
?>
