<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

include_once 'modules/Vtiger/CRMEntity.php';

class CTRouteAttendance extends Vtiger_CRMEntity {
	var $table_name = 'vtiger_ctrouteattendance';
	var $table_index= 'ctrouteattendanceid';

	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('vtiger_ctrouteattendancecf', 'ctrouteattendanceid');

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	var $tab_name = Array('vtiger_crmentity', 'vtiger_ctrouteattendance', 'vtiger_ctrouteattendancecf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	var $tab_name_index = Array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_ctrouteattendance' => 'ctrouteattendanceid',
		'vtiger_ctrouteattendancecf'=>'ctrouteattendanceid');

	/**
	 * Mandatory for Listing (Related listview)
	 */
	var $list_fields = Array (
		/* Format: Field Label => Array(tablename, columnname) */
		// tablename should not have prefix 'vtiger_'
		'CTRoute Attendance No' => Array('ctrouteattendance', 'ctrouteattendance_no'),
		'Assigned To' => Array('crmentity','smownerid')
	);
	var $list_fields_name = Array (
		/* Format: Field Label => fieldname */
		'CTRoute Attendance No' => 'ctrouteattendance_no',
		'Assigned To' => 'assigned_user_id',
	);

	// Make the field link to detail view
	var $list_link_field = 'ctrouteattendance_no';

	// For Popup listview and UI type support
	var $search_fields = Array(
		/* Format: Field Label => Array(tablename, columnname) */
		// tablename should not have prefix 'vtiger_'
		'CTRoute Attendance No' => Array('ctrouteattendance', 'ctrouteattendance_no'),
		'Assigned To' => Array('vtiger_crmentity','assigned_user_id'),
	);
	var $search_fields_name = Array (
		/* Format: Field Label => fieldname */
		'CTRoute Attendance No' => 'ctrouteattendance_no',
		'Assigned To' => 'assigned_user_id',
	);

	// For Popup window record selection
	var $popup_fields = Array ('ctrouteattendance_no');

	// For Alphabetical search
	var $def_basicsearch_col = 'ctrouteattendance_no';

	// Column value to use on detail view record text display
	var $def_detailview_recname = 'ctrouteattendance_no';

	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	var $mandatory_fields = Array('ctrouteattendance_no','assigned_user_id');

	var $default_order_by = 'ctrouteattendance_no';
	var $default_sort_order='ASC';

	/**
	* Invoked when special actions are performed on the module.
	* @param String Module name
	* @param String Event Type
	*/
	function vtlib_handler($moduleName, $eventType) {
		global $adb;
 		if($eventType == 'module.postinstall') {
 			self::solveWSEntity();
			// TODO Handle actions after this module is installed.
		} else if($eventType == 'module.disabled') {
			// TODO Handle actions before this module is being uninstalled.
		} else if($eventType == 'module.preuninstall') {
			// TODO Handle actions when this module is about to be deleted.
		} else if($eventType == 'module.preupdate') {
			// TODO Handle actions before this module is updated.
		} else if($eventType == 'module.postupdate') {
			self::solveWSEntity();
			// TODO Handle actions after this module is updated.
		}
 	}

 	static function solveWSEntity() {
        global $adb;
        $result = $adb->pquery("SELECT * FROM vtiger_ws_entity WHERE name = ?",array('CTRouteAttendance'));
        if($adb->num_rows($result) == 0){
        	$selentity = $adb->pquery("SELECT id FROM vtiger_ws_entity_seq",array());
        	$id = $adb->query_result($selentity,0,'id');
        	$entityid = $id+1;
        	$adb->pquery("INSERT INTO vtiger_ws_entity (id,name,handler_path,handler_class,ismodule) VALUES (?,?,?,?,?)",array($entityid,'CTRouteAttendance','include/Webservices/VtigerModuleOperation.php','VtigerModuleOperation','1'));
        	$adb->pquery("UPDATE vtiger_ws_entity_seq SET id = ?",$entityid);
        }
    }
}
