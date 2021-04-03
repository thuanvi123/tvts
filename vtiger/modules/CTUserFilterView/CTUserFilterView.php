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

class CTUserFilterView extends Vtiger_CRMEntity {
	var $table_name = 'vtiger_ctuserfilterview';
	var $table_index= 'ctuserfilterviewid';

	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('vtiger_ctuserfilterviewcf', 'ctuserfilterviewid');

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	var $tab_name = Array('vtiger_crmentity', 'vtiger_ctuserfilterview', 'vtiger_ctuserfilterviewcf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	var $tab_name_index = Array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_ctuserfilterview' => 'ctuserfilterviewid',
		'vtiger_ctuserfilterviewcf'=>'ctuserfilterviewid');

	/**
	 * Mandatory for Listing (Related listview)
	 */
	var $list_fields = Array (
		/* Format: Field Label => Array(tablename, columnname) */
		// tablename should not have prefix 'vtiger_'
		'CTMobile User Filter View No' => Array('ctuserfilterview', 'ctuser_filter_view_no'),
		'Assigned To' => Array('crmentity','smownerid')
	);
	var $list_fields_name = Array (
		/* Format: Field Label => fieldname */
		'CTMobile User Filter View No' => 'ctuser_filter_view_no',
		'Assigned To' => 'assigned_user_id',
	);

	// Make the field link to detail view
	var $list_link_field = 'ctuser_filter_view_no';

	// For Popup listview and UI type support
	var $search_fields = Array(
		/* Format: Field Label => Array(tablename, columnname) */
		// tablename should not have prefix 'vtiger_'
		'CTMobile User Filter View No' => Array('ctuserfilterview', 'ctuser_filter_view_no'),
		'Assigned To' => Array('vtiger_crmentity','assigned_user_id'),
	);
	var $search_fields_name = Array (
		/* Format: Field Label => fieldname */
		'CTMobile User Filter View No' => 'ctuser_filter_view_no',
		'Assigned To' => 'assigned_user_id',
	);

	// For Popup window record selection
	var $popup_fields = Array ('ctuser_filter_view_no');

	// For Alphabetical search
	var $def_basicsearch_col = 'ctuser_filter_view_no';

	// Column value to use on detail view record text display
	var $def_detailview_recname = 'ctuser_filter_view_no';

	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	var $mandatory_fields = Array('ctuser_filter_view_no','assigned_user_id');

	var $default_order_by = 'ctuser_filter_view_no';
	var $default_sort_order='ASC';

	/**
	* Invoked when special actions are performed on the module.
	* @param String Module name
	* @param String Event Type
	*/
	function vtlib_handler($moduleName, $eventType) {
		global $adb;
 		if($eventType == 'module.postinstall') {
			$this->AddRecordLatLong();
			self::solveWSEntity();
			// TODO Handle actions after this module is installed.
		} else if($eventType == 'module.disabled') {
			$this->AddRecordLatLong();
			// TODO Handle actions before this module is being uninstalled.
		} else if($eventType == 'module.preuninstall') {
			$this->AddRecordLatLong();
			// TODO Handle actions when this module is about to be deleted.
		} else if($eventType == 'module.preupdate') {
			$this->AddRecordLatLong();
			// TODO Handle actions before this module is updated.
		} else if($eventType == 'module.postupdate') {
			$this->AddRecordLatLong();
			self::solveWSEntity();
			// TODO Handle actions after this module is updated.
		}
 	}

 	static function solveWSEntity() {
        global $adb;
        $result = $adb->pquery("SELECT * FROM vtiger_ws_entity WHERE name = ?",array('CTUserFilterView'));
        if($adb->num_rows($result) == 0){
        	$selentity = $adb->pquery("SELECT id FROM vtiger_ws_entity_seq",array());
        	$id = $adb->query_result($selentity,0,'id');
        	$entityid = $id+1;
        	$adb->pquery("INSERT INTO vtiger_ws_entity (id,name,handler_path,handler_class,ismodule) VALUES (?,?,?,?,?)",array($entityid,'CTUserFilterView','include/Webservices/VtigerModuleOperation.php','VtigerModuleOperation','1'));
        	$adb->pquery("UPDATE vtiger_ws_entity_seq SET id = ?",$entityid);
        }
    }
	
	function AddRecordLatLong() {
		global $adb;
			require_once('include/utils/utils.php');
			require_once 'modules/com_vtiger_workflow/VTEntityMethodManager.inc';
			$db = PearDatabase::getInstance();
			
			$deleterow = $db->pquery("DELETE FROM `vtiger_cron_task` WHERE `name` = 'AddRecordLatLong'", array());
			$result = $db->pquery("select method_name from com_vtiger_workflowtasks_entitymethod where module_name=? and method_name = ?", array('Leads','AddRecordLatLong'));
			if($db->num_rows($result)==0){
				$entityMethodManager = new VTEntityMethodManager($db); 
				$entityMethodManager->addEntityMethod("Leads", "AddRecordLatLong","modules/CTUserFilterView/AddRecordLatLong.php","AddRecordLatLong");
				
				$result = $db->pquery("SELECT * FROM  `com_vtiger_workflows_seq`",array());
				$id = $db->query_result($result,0,'id');
				$NewID = $id + 1;
				$db->pquery("INSERT INTO com_vtiger_workflows(workflow_id,module_name,summary,test,execution_condition,defaultworkflow,	type,filtersavedinnew,schtypeid,schdayofmonth,schdayofweek,schannualdates,schtime,nexttrigger_time,status,workflowname) 
							VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",array($NewID,"Leads","create latlong","[]","3","","basic","6","0","0","0","0","0","","1","CTLatLong for Leads Module"));
				$db->pquery("UPDATE com_vtiger_workflows_seq SET id = ? WHERE id = ? ", array($NewID,$id));
				
				$result = $db->pquery("SELECT * FROM  `com_vtiger_workflowtasks_seq`",array());
				$id = $db->query_result($result,0,'id');
				$TaskID = $id + 1;
				$task = 'O:18:"VTEntityMethodTask":7:{s:18:"executeImmediately";b:1;s:10:"workflowId";s:2:"'.$NewID.'";s:7:"summary";s:12:"add lat long";s:6:"active";b:1;s:7:"trigger";N;s:10:"methodName";s:16:"AddRecordLatLong";s:2:"id";i:'.$TaskID.';}';

				$db->pquery("INSERT INTO com_vtiger_workflowtasks (task_id,workflow_id,summary,task) VALUES(?,?,?,?)",array($TaskID,$NewID,'add lat long',$task));
				$db->pquery("UPDATE com_vtiger_workflowtasks_seq SET id = ? WHERE id = ? ", array($TaskID,$id));
				
			}
			$result = $db->pquery("select method_name from com_vtiger_workflowtasks_entitymethod where module_name=? and method_name = ?", array('Contacts','AddRecordLatLong'));
			if($db->num_rows($result)==0){
				$entityMethodManager = new VTEntityMethodManager($db); 
				$entityMethodManager->addEntityMethod("Contacts", "AddRecordLatLong","modules/CTUserFilterView/AddRecordLatLong.php","AddRecordLatLong");
				
				$result = $db->pquery("SELECT * FROM  `com_vtiger_workflows_seq`",array());
				$id = $db->query_result($result,0,'id');
				$NewID = $id + 1;
				$db->pquery("INSERT INTO com_vtiger_workflows(workflow_id,module_name,summary,test,execution_condition,defaultworkflow,	type,filtersavedinnew,schtypeid,schdayofmonth,schdayofweek,schannualdates,schtime,nexttrigger_time,status,workflowname) 
							VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",array($NewID,"Contacts","create latlong","[]","3","","basic","6","0","0","0","0","0","","1","CTLatLong for Contacts Module"));
				$db->pquery("UPDATE com_vtiger_workflows_seq SET id = ? WHERE id = ? ", array($NewID,$id));
				
				$result = $db->pquery("SELECT * FROM  `com_vtiger_workflowtasks_seq`",array());
				$id = $db->query_result($result,0,'id');
				$TaskID = $id + 1;
				$task = 'O:18:"VTEntityMethodTask":7:{s:18:"executeImmediately";b:1;s:10:"workflowId";s:2:"'.$NewID.'";s:7:"summary";s:12:"add lat long";s:6:"active";b:1;s:7:"trigger";N;s:10:"methodName";s:16:"AddRecordLatLong";s:2:"id";i:'.$TaskID.';}';

				$db->pquery("INSERT INTO com_vtiger_workflowtasks (task_id,workflow_id,summary,task) VALUES(?,?,?,?)",array($TaskID,$NewID,'add lat long',$task));
				$db->pquery("UPDATE com_vtiger_workflowtasks_seq SET id = ? WHERE id = ? ", array($TaskID,$id));
			}
			$result = $db->pquery("select method_name from com_vtiger_workflowtasks_entitymethod where module_name=? and method_name = ?", array('Accounts','AddRecordLatLong'));
			if($db->num_rows($result)==0){
				$entityMethodManager = new VTEntityMethodManager($db); 
				$entityMethodManager->addEntityMethod("Accounts", "AddRecordLatLong","modules/CTUserFilterView/AddRecordLatLong.php","AddRecordLatLong");
				
				$result = $db->pquery("SELECT * FROM  `com_vtiger_workflows_seq`",array());
				$id = $db->query_result($result,0,'id');
				$NewID = $id + 1;
				$db->pquery("INSERT INTO com_vtiger_workflows(workflow_id,module_name,summary,test,execution_condition,defaultworkflow,	type,filtersavedinnew,schtypeid,schdayofmonth,schdayofweek,schannualdates,schtime,nexttrigger_time,status,workflowname) 
							VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",array($NewID,"Accounts","create latlong","[]","3","","basic","6","0","0","0","0","0","","1","CTLatLong for Organization Module"));
				$db->pquery("UPDATE com_vtiger_workflows_seq SET id = ? WHERE id = ? ", array($NewID,$id));
				
				$result = $db->pquery("SELECT * FROM  `com_vtiger_workflowtasks_seq`",array());
				$id = $db->query_result($result,0,'id');
				$TaskID = $id + 1;
				$task = 'O:18:"VTEntityMethodTask":7:{s:18:"executeImmediately";b:1;s:10:"workflowId";s:2:"'.$NewID.'";s:7:"summary";s:12:"add lat long";s:6:"active";b:1;s:7:"trigger";N;s:10:"methodName";s:16:"AddRecordLatLong";s:2:"id";i:'.$TaskID.';}';

				$db->pquery("INSERT INTO com_vtiger_workflowtasks (task_id,workflow_id,summary,task) VALUES(?,?,?,?)",array($TaskID,$NewID,'add lat long',$task));
				$db->pquery("UPDATE com_vtiger_workflowtasks_seq SET id = ? WHERE id = ? ", array($TaskID,$id));
			}
			$result = $db->pquery("select method_name from com_vtiger_workflowtasks_entitymethod where module_name=? and method_name = ?", array('Calendar','AddRecordLatLong'));
			if($db->num_rows($result)==0){
				$entityMethodManager = new VTEntityMethodManager($db); 
				$entityMethodManager->addEntityMethod("Calendar", "AddRecordLatLong","modules/CTUserFilterView/AddRecordLatLong.php","AddRecordLatLong");
				
				$result = $db->pquery("SELECT * FROM  `com_vtiger_workflows_seq`",array());
				$id = $db->query_result($result,0,'id');
				$NewID = $id + 1;
				$db->pquery("INSERT INTO com_vtiger_workflows(workflow_id,module_name,summary,test,execution_condition,defaultworkflow,	type,filtersavedinnew,schtypeid,schdayofmonth,schdayofweek,schannualdates,schtime,nexttrigger_time,status,workflowname) 
							VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",array($NewID,"Calendar","create latlong","[]","3","","basic","6","0","0","0","0","0","","1","CTLatLong for Calendar Module"));
				$db->pquery("UPDATE com_vtiger_workflows_seq SET id = ? WHERE id = ? ", array($NewID,$id));
				
				$result = $db->pquery("SELECT * FROM  `com_vtiger_workflowtasks_seq`",array());
				$id = $db->query_result($result,0,'id');
				$TaskID = $id + 1;
				$task = 'O:18:"VTEntityMethodTask":7:{s:18:"executeImmediately";b:1;s:10:"workflowId";s:2:"'.$NewID.'";s:7:"summary";s:12:"add lat long";s:6:"active";b:1;s:7:"trigger";N;s:10:"methodName";s:16:"AddRecordLatLong";s:2:"id";i:'.$TaskID.';}';

				$db->pquery("INSERT INTO com_vtiger_workflowtasks (task_id,workflow_id,summary,task) VALUES(?,?,?,?)",array($TaskID,$NewID,'add lat long',$task));
				$db->pquery("UPDATE com_vtiger_workflowtasks_seq SET id = ? WHERE id = ? ", array($TaskID,$id));
			}
			
			$result = $db->pquery("select method_name from com_vtiger_workflowtasks_entitymethod where module_name=? and method_name = ?", array('Events','AddRecordLatLong'));
			if($db->num_rows($result)==0){
				$entityMethodManager = new VTEntityMethodManager($db); 
				$entityMethodManager->addEntityMethod("Events", "AddRecordLatLong","modules/CTUserFilterView/AddRecordLatLong.php","AddRecordLatLong");
				
				$result = $db->pquery("SELECT * FROM  `com_vtiger_workflows_seq`",array());
				$id = $db->query_result($result,0,'id');
				$NewID = $id + 1;
				$db->pquery("INSERT INTO com_vtiger_workflows(workflow_id,module_name,summary,test,execution_condition,defaultworkflow,	type,filtersavedinnew,schtypeid,schdayofmonth,schdayofweek,schannualdates,schtime,nexttrigger_time,status,workflowname) 
							VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",array($NewID,"Events","create latlong","[]","3","","basic","6","0","0","0","0","0","","1","CTLatLong for Events Module"));
				$db->pquery("UPDATE com_vtiger_workflows_seq SET id = ? WHERE id = ? ", array($NewID,$id));
				
				$result = $db->pquery("SELECT * FROM  `com_vtiger_workflowtasks_seq`",array());
				$id = $db->query_result($result,0,'id');
				$TaskID = $id + 1;
				$task = 'O:18:"VTEntityMethodTask":7:{s:18:"executeImmediately";b:1;s:10:"workflowId";s:2:"'.$NewID.'";s:7:"summary";s:12:"add lat long";s:6:"active";b:1;s:7:"trigger";N;s:10:"methodName";s:16:"AddRecordLatLong";s:2:"id";i:'.$TaskID.';}';

				$db->pquery("INSERT INTO com_vtiger_workflowtasks (task_id,workflow_id,summary,task) VALUES(?,?,?,?)",array($TaskID,$NewID,'add lat long',$task));
				$db->pquery("UPDATE com_vtiger_workflowtasks_seq SET id = ? WHERE id = ? ", array($TaskID,$id));
			}
		
	}	
}
