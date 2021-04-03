<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

$Module_Mobile_Configuration = array(

	'Default.Skin'     => 'default.css', // Available in resources/skins
	'Navigation.Limit' => 25,

	// Control number of records sent out through API (SyncModuleRecords, Query...) which supports paging.	
	'API_RECORD_FETCH_LIMIT' => 99, // NOTE: vtws_query internally limits fetch to 100 and give room to perform 1 extra fetch to determine paging
	
);

?>
