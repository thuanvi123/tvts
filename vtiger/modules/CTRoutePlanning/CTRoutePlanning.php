<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

include_once 'modules/Vtiger/CRMEntity.php';

class CTRoutePlanning extends Vtiger_CRMEntity {
	var $table_name = 'vtiger_ctrouteplanning';
	var $table_index= 'ctrouteplanningid';

	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('vtiger_ctrouteplanningcf', 'ctrouteplanningid');

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	var $tab_name = Array('vtiger_crmentity', 'vtiger_ctrouteplanning', 'vtiger_ctrouteplanningcf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	var $tab_name_index = Array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_ctrouteplanning' => 'ctrouteplanningid',
		'vtiger_ctrouteplanningcf'=>'ctrouteplanningid',
		'vtiger_ctrouteplanrel'=>'ctrouteplanningid');

	/**
	 * Mandatory for Listing (Related listview)
	 */
	var $list_fields = Array (
		/* Format: Field Label => Array(tablename, columnname) */
		// tablename should not have prefix 'vtiger_'
		'Route Name' => Array('ctrouteplanning', 'ctroutename'),
		'Assigned To' => Array('crmentity','smownerid')
	);
	var $list_fields_name = Array (
		/* Format: Field Label => fieldname */
		'Route Name' => 'ctroutename',
		'Assigned To' => 'assigned_user_id',
	);

	// Make the field link to detail view
	var $list_link_field = 'ctroutename';

	// For Popup listview and UI type support
	var $search_fields = Array(
		/* Format: Field Label => Array(tablename, columnname) */
		// tablename should not have prefix 'vtiger_'
		'Route Name' => Array('ctrouteplanning', 'ctroutename'),
		'Assigned To' => Array('vtiger_crmentity','assigned_user_id'),
	);
	var $search_fields_name = Array (
		/* Format: Field Label => fieldname */
		'Route Name' => 'ctroutename',
		'Assigned To' => 'assigned_user_id',
	);

	// For Popup window record selection
	var $popup_fields = Array ('ctroutename');

	// For Alphabetical search
	var $def_basicsearch_col = 'ctroutename';

	// Column value to use on detail view record text display
	var $def_detailview_recname = 'ctroutename';

	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	var $mandatory_fields = Array('ctroutename','assigned_user_id');

	var $default_order_by = 'ctroutename';
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

 	function CTRoutePlanning() {
		$this->log = LoggerManager::getLogger('CTRoutePlanning');
		$this->db = PearDatabase::getInstance();
		$this->column_fields = getColumnFields('CTRoutePlanning');
	}

	function save_module($module)
	{
		global $adb;
		//Handling module specific save
		//Insert into seactivity rel
		$insertion_mode = $this->mode;

		$recordId = intval($this->id);
		if(isset($_REQUEST['ctrouteidlist']) && $_REQUEST['ctrouteidlist'] != '') {
			$adb->pquery( 'DELETE from vtiger_ctrouteplanrel WHERE ctrouteplanningid = ?', array($recordId));

			$contactIdsList = explode (';', $_REQUEST['ctrouteidlist']);
			$count = count($contactIdsList);

			$sql = 'INSERT INTO vtiger_ctrouteplanrel VALUES ';
			for($i=0; $i<$count; $i++) {
				$contactIdsList[$i] = intval($contactIdsList[$i]);
				$sql .= " ($recordId,$contactIdsList[$i])";
				if ($i != $count - 1) {
					$sql .= ',';
				}
			}
			$adb->pquery($sql, array());
		} else if ($_REQUEST['ctrouteidlist'] == '' && $insertion_mode == "edit") {
			//$adb->pquery('DELETE FROM vtiger_ctrouteplanrel WHERE ctrouteplanningid = ?', array($recordId));
		}

		//Insert into cntactivity rel
		if(isset($this->column_fields['ctroute_realtedto']) && $this->column_fields['ctroute_realtedto'] != '' && !isset($_REQUEST['ctrouteidlist']))
		{
				$this->insertIntoEntityTable('vtiger_ctrouteplanrel', $module);
		}
		elseif($this->column_fields['ctroute_realtedto'] =='' && $insertion_mode=="edit" && !isset($_REQUEST['ctrouteidlist']))
		{
				$this->deleteRelation('vtiger_ctrouteplanrel');
		}
	}

	static function solveWSEntity() {
        global $adb;
        $result = $adb->pquery("SELECT * FROM vtiger_ws_entity WHERE name = ?",array('CTRoutePlanning'));
        if($adb->num_rows($result) == 0){
        	$selentity = $adb->pquery("SELECT id FROM vtiger_ws_entity_seq",array());
        	$id = $adb->query_result($selentity,0,'id');
        	$entityid = $id+1;
        	$adb->pquery("INSERT INTO vtiger_ws_entity (id,name,handler_path,handler_class,ismodule) VALUES (?,?,?,?,?)",array($entityid,'CTRoutePlanning','include/Webservices/VtigerModuleOperation.php','VtigerModuleOperation','1'));
        	$adb->pquery("UPDATE vtiger_ws_entity_seq SET id = ?",$entityid);
        }
    }
}