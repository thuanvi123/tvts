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
require_once('data/Tracker.php');
require_once 'vtlib/Vtiger/Module.php';
require_once('modules/com_vtiger_workflow/include.inc');

class CTPushNotification extends Vtiger_CRMEntity {
	var $table_name = 'vtiger_ctpushnotification';
	var $table_index= 'ctpushnotificationid';

	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('vtiger_ctpushnotificationcf', 'ctpushnotificationid');

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	var $tab_name = Array('vtiger_crmentity', 'vtiger_ctpushnotification', 'vtiger_ctpushnotificationcf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	var $tab_name_index = Array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_ctpushnotification' => 'ctpushnotificationid',
		'vtiger_ctpushnotificationcf'=>'ctpushnotificationid');

	/**
	 * Mandatory for Listing (Related listview)
	 */
	var $list_fields = Array (
		/* Format: Field Label => Array(tablename, columnname) */
		// tablename should not have prefix 'vtiger_'
		'Push Notification Title' => Array('ctpushnotification', 'pn_title'),
		'Assigned To' => Array('crmentity','smownerid')
	);
	var $list_fields_name = Array (
		/* Format: Field Label => fieldname */
		'Push Notification Title' => 'pn_title',
		'Assigned To' => 'assigned_user_id',
	);

	// Make the field link to detail view
	var $list_link_field = 'pn_title';

	// For Popup listview and UI type support
	var $search_fields = Array(
		/* Format: Field Label => Array(tablename, columnname) */
		// tablename should not have prefix 'vtiger_'
		'Push Notification Title' => Array('ctpushnotification', 'pn_title'),
		'Assigned To' => Array('vtiger_crmentity','assigned_user_id'),
	);
	var $search_fields_name = Array (
		/* Format: Field Label => fieldname */
		'Push Notification Title' => 'pn_title',
		'Assigned To' => 'assigned_user_id',
	);

	// For Popup window record selection
	var $popup_fields = Array ('pn_title');

	// For Alphabetical search
	var $def_basicsearch_col = 'pn_title';

	// Column value to use on detail view record text display
	var $def_detailview_recname = 'pn_title';

	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	var $mandatory_fields = Array('pn_title','assigned_user_id');

	var $default_order_by = 'pn_title';
	var $default_sort_order='ASC';

	/**
	* Invoked when special actions are performed on the module.
	* @param String Module name
	* @param String Event Type
	*/
	 function vtlib_handler($modulename, $event_type) {
        global $adb;
        if($event_type == 'module.postinstall') {
           
            self::installWorkflow();
            self::solveWSEntity();

        } else if($event_type == 'module.disabled') {
            // TODO Handle actions when this module is disabled.
           
            self::removeWorkflows();
        } else if($event_type == 'module.enabled') {
            // TODO Handle actions when this module is enabled.
                      
            self::installWorkflow();

        } else if($event_type == 'module.preuninstall') {
            // TODO Handle actions when this module is about to be deleted.
           
            self::removeWorkflows();
        } else if($event_type == 'module.preupdate') {
            // TODO Handle actions before this module is updated.
        } else if($event_type == 'module.postupdate') {
        	self::solveWSEntity();
            
        }
    }


	static function installWorkflow() {
        global $adb;
        $name='VTPushNotification';
        $dest1 = "modules/com_vtiger_workflow/tasks/".$name.".inc";
        $source1 = "modules/CTPushNotification/workflow/".$name.".inc";

        if (file_exists($dest1)) {
            $file_exist1 = true;
        } else {
            if(copy($source1, $dest1)) {
                $file_exist1 = true;
            }
        }

        $dest2 = "layouts/v7/modules/Settings/Workflows/Tasks/".$name.".tpl";
        $source2 = "layouts/v7/modules/CTPushNotification/taskforms/".$name.".tpl";

        if (file_exists($dest2)) {
            $file_exist2 = true;
        } else {
            if(copy($source2, $dest2)) {
                $file_exist2 = true;
            }
        }
		
		
		$name1='VTPushNotificationTask';
		 $dest2 = "layouts/v7/modules/Settings/Workflows/Tasks/".$name1.".tpl";
        $source2 = "layouts/v7/modules/CTPushNotification/taskforms/".$name1.".tpl";

        if (file_exists($dest2)) {
            $file_exist2 = true;
        } else {
            if(copy($source2, $dest2)) {
                $file_exist2 = true;
            }
        }

        if ($file_exist1 && $file_exist2) {
            $sql1 = "SELECT * FROM com_vtiger_workflow_tasktypes WHERE tasktypename = ?";
            $result1 = $adb->pquery($sql1,array($name));

            if ($adb->num_rows($result1) == 0) {
                // Add workflow task
				
                $taskType = array("name"=>"VTPushNotification", "label"=>"CRMTiger Mobile - Push Notification", "classname"=>"VTPushNotification", "classpath"=>"modules/com_vtiger_workflow/tasks/VTPushNotification.inc", "templatepath"=>"modules/Settings/Workflows/Tasks/VTPushNotification.tpl", "modules"=>array('include' => array(), 'exclude'=>array()), "sourcemodule"=>'CTPushNotification');
                VTTaskType::registerTaskType($taskType);
            }
        }
    }

    static function removeWorkflows() {
        global $adb;
        $sql1 = "DELETE FROM com_vtiger_workflow_tasktypes WHERE sourcemodule = ?";
        $adb->pquery($sql1, array('CTPushNotification'));

        $sql2 = "DELETE FROM com_vtiger_workflowtasks WHERE task LIKE ?";
        $adb->pquery($sql2,array('%:"VTPushNotification":%'));

        @shell_exec('rm -f modules/com_vtiger_workflow/tasks/VTPushNotification.inc');
        @shell_exec('rm -f layouts/v7/modules/Settings/Workflows/Tasks/VTPushNotification.tpl');
    }

    static function solveWSEntity() {
        global $adb;
        $result = $adb->pquery("SELECT * FROM vtiger_ws_entity WHERE name = ?",array('CTPushNotification'));
        if($adb->num_rows($result) == 0){
        	$selentity = $adb->pquery("SELECT id FROM vtiger_ws_entity_seq",array());
        	$id = $adb->query_result($selentity,0,'id');
        	$entityid = $id+1;
        	$adb->pquery("INSERT INTO vtiger_ws_entity (id,name,handler_path,handler_class,ismodule) VALUES (?,?,?,?,?)",array($entityid,'CTPushNotification','include/Webservices/VtigerModuleOperation.php','VtigerModuleOperation','1'));
        	$adb->pquery("UPDATE vtiger_ws_entity_seq SET id = ?",$entityid);
        }
    }
	
}
