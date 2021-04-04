<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
include_once dirname(__FILE__) . '/models/Alert.php';
include_once dirname(__FILE__) . '/models/SearchFilter.php';
include_once dirname(__FILE__) . '/models/Paging.php';

class CTMobile_WS_ListModuleRecords extends CTMobile_WS_Controller {
	public $totalQuery = "";
	public $totalParams = array();
	function isCalendarModule($module) {
		return ($module == 'Events' || $module == 'Calendar');
	}
	
	function getSearchFilterModel($module, $search) {
		return CTMobile_WS_SearchFilterModel::modelWithCriterias($module, Zend_JSON::decode($search));
	}
	
	function getPagingModel(CTMobile_API_Request $request) {
		$page = $request->get('page', 0);
		return CTMobile_WS_PagingModel::modelWithPageStart($page);
	}
	
	function process(CTMobile_API_Request $request) {
		
		return $this->processSearchRecordLabel($request);
	}

	function GroupDetails($pagingModel,$paging){
		global $adb;
		$index = $paging['index'];
		$size = $paging['size'];
		$limit = ($index*$size) - $size;
		$query = "SELECT * FROM vtiger_groups ORDER BY groupname ASC";
		if($index != '' && $size != '') {
			$query .= sprintf(" LIMIT %s, %s", $limit, $size);
		}
		$prequeryResult = $adb->pquery($query,array());
		$result = new SqlResultIterator($adb, $prequeryResult);
		$i = 0;
		$modifiedRecord = array();
		foreach($result as $record) {
			if ($record instanceof SqlResultIteratorRow) {
				$record = $record->data;
				$modifiedRecord[$i]['label'] = $modifiedRecord[$i]['id'] = "";
				foreach($record as $key => $values){
					if($key == 'groupid'){
						$modifiedRecord[$i]['id'] = '20x'.$values;
					}else if($key == 'groupname'){
						$modifiedRecord[$i]['label'] = $values;
					}
				}
			}
			$i =$i+1;
		}
		return $modifiedRecord;
	}
	
	function processSearchRecordLabel(CTMobile_API_Request $request) {
		$default_charset = VTWS_PreserveGlobal::getGlobal('default_charset');
		global $current_user, $adb, $site_URL; // Few core API assumes this variable availability
		
		$current_user = $this->getActiveUser();
		
		$module = trim($request->get('module'));
		$alertid = trim($request->get('alertid'));
		$filterid = $request->get('filterid');
		$search = trim($request->get('search'));
		$index = trim($request->get('index'));
		$size = trim($request->get('size'));
		$type = trim($request->get('type'));
		$field_name = trim($request->get('field_name'));
		$field_name2 = trim($request->get('field_name2'));
		$field_value = $request->get('field_value');
		$field_value2 = trim($request->get('field_value2'));
		$order_by = trim($request->get('order_field'));
		$orderby = trim($request->get('orderby'));
		$display_params = $request->get('display_params');
		$search_params = $request->get('search_params');
		$params = Zend_Json::decode($display_params);
		$user_type = trim($request->get('user_type'));
		$related = trim($request->get('related'));
		$discontinued = trim($request->get('discontinued'));
		$isMentionUser = $request->get('isMentionUser');
		if($discontinued == ''){
			$discontinued = 0;
		}
		if($display_params == ''){
			$getDisplaySQL = $adb->pquery("SELECT * FROM ctmobile_display_fields WHERE module = ? AND userid = ?",array($module,$current_user->id));
			$totalRows = $adb->num_rows($getDisplaySQL);
			$entries = array();
			for ($i=0; $i < $totalRows; $i++) { 
				$params[] = $adb->query_result($getDisplaySQL,$i,'fieldname');
			}
		}
		$activitytype = "";
		$activitytype = trim($request->get('activitytype'));
		if($module == 'Calendar'){
			$activitytype = 'Task';
		}

		$searchType = trim($request->get('searchType'));
		$refrenceUitypes = array(10,51,57,58,59,66,73,75,76,77,78,80,81,101);

		$isRoute = trim($request->get('isRoute'));
		$relatedModule = trim($request->get('relatedModule'));

		if($module == 'Users'){
			if($type == 'owner'){
				$index = "";
				$size = "";
			}else{
				$current_user = Users::getActiveAdminUser();
			}
			$order_by = 'first_name';
			$orderby = 'ASC';
		}
		
		if(!getTabid($module)){
			$message = vtranslate($module,$module)." ".$this->CTTranslate('Module does not exists');
			throw new WebServiceException(404,$message);
		}else{
			if($module != 'Users'){
				if($module == 'Events'){
					$checkModule = 'Calendar';
				}else{
					$checkModule = $module;
				}
				$moduleModel = Vtiger_Module_Model::getInstance($checkModule);
				$currentUser = Users_Record_Model::getCurrentUserModel();
				$userPrivilegesModel = Users_Privileges_Model::getInstanceById($currentUser->getId());
				$permission = $userPrivilegesModel->hasModulePermission($moduleModel->getId());
				if(!$permission) {
					$message = vtranslate($module,$module)." ".vtranslate('LBL_PERMISSION_DENIED');
					throw new WebServiceException(404,$message);
				}
			}
		}

		$sourceModule = trim($request->get('sourceModule'));
		$sourceRecord = trim($request->get('sourceRecord'));
		$selectedRecords = array();
		if($sourceModule != '' && $sourceRecord!= ''){
			$selectedRecords = $this->getRelatedRecord($sourceModule,$module,$sourceRecord);
		}
		
		if($module == 'Groups'){
			$pagingModel = $this->getPagingModel($request);
			$paging = array('index'=>$index, 'size'=>$size);
			$modifiedRecords = array();
			$modifiedRecords = $this->GroupDetails($pagingModel,$paging);
			$response = new CTMobile_API_Response();
			if(count($modifiedRecords) == 0) {
				$message = $this->CTTranslate('No records found');
				$response->setResult(array('records'=>$modifiedRecords, 'module'=>$module, 'message'=>$message,'module_record_status'=>false));
			} else {
				$response->setResult(array('records'=>$modifiedRecords, 'module'=>$module, 'message'=>'','module_record_status'=>true));
			}
			return $response;
		}
		
		$userId = array();
		$moduleModel = Vtiger_Module_Model::getInstance($module);
		$fieldModels = $moduleModel->getFields();
		$Modulefields = array_keys($fieldModels);

		// code start for Entity Field By suresh /
		if($module == 'Events'){
			$modulename = 'Calendar';
		}else{
			$modulename = $module;
		}
		$entityQuery = $adb->pquery("SELECT * FROM vtiger_entityname WHERE modulename = ?",array($modulename));
		$entityField = $adb->query_result($entityQuery,0,'fieldname');
		$entityField_array = explode(',',$entityField);
		$entityField = $entityField_array[0];
		$tabid = getTabid($modulename);

		if($module == 'Assets'){
			$entityQuery = $adb->pquery("SELECT * FROM ctmobile_asset_field WHERE module = ?",array($module));
			$entityField = $adb->query_result($entityQuery,0,'fieldname');
			$entityField_array = explode(':',$entityField);
			$entityField = $entityField_array[2];
		}
		
		$entityQuery11 = $adb->pquery("SELECT * FROM vtiger_field WHERE columnname = ? and tabid= ?",array($entityField,$tabid));
		$fieldlabel = $adb->query_result($entityQuery11,0,'fieldlabel');
		$fieldlabel = vtranslate($fieldlabel,$modulename);
		if($module == 'Documents' && $entityField == 'title'){
			$entityField = 'notes_title';
		}
		if($module == 'HelpDesk' && $entityField == 'title'){
			$entityField = 'ticket_title';
		}
		$fields = $fieldModels[$entityField];
		$result = $adb->pquery("SELECT fieldtype FROM vtiger_ws_fieldtype WHERE uitype = ?",array($fields->get('uitype')));
		$fieldtype = $adb->query_result($result, 0, 'fieldtype');
		if(!$fieldtype){
			$typeofdata = explode('~',$fields->get('typeofdata'));
			switch($typeofdata[0]){
				case 'T': $fieldtype = "time";
				case 'D': $fieldtype = "date";
				case 'DT': $fieldtype =  "date";
				case 'E': $fieldtype =  "email";
				case 'N':
				case 'NN': $fieldtype = "double";
				case 'P': $fieldtype = "password";
				case 'I': $fieldtype = "integer";
				case 'V':
				default: $fieldtype = "string";
			}
		}
		if($fields->get('uitype') == 15 || $fields->get('uitype') == 33){
			$picklistValues1 = array();
			$picklistValues = Vtiger_Util_Helper::getRoleBasedPicklistValues($fields->get('name'),$roleid);
			foreach($picklistValues as $pvalue){
				$picklistValues1[] = array('value'=>$pvalue, 'label'=>vtranslate($pvalue,$module));
			}
			
			$entityFields = array('name'=>$fields->get('name'),'label'=>vtranslate($fields->get('label'),$module),'type'=>array('picklistValues'=>$picklistValues1,'name'=>$fieldtype,'defaultValue'=>$fields->getDefaultFieldValue()),'editable'=>$fields->isEditable(),'mandatory'=>$fields->isMandatory(),'isunique'=>$fields->get('isunique'), 'readonly'=>$fields->get('readonly'),'displaytype'=>$fields->get('displaytype'),'typeofdata'=>$fields->get('typeofdata'),'uitype'=>$fields->get('uitype'),'summaryfield'=>$fields->get('summaryfield'),'presence'=>$fields->get('presence'));
		}else{
			$entityFields = array('name'=>$fields->get('name'),'label'=>vtranslate($fields->get('label'),$module),'type'=>array('name'=>$fieldtype,'defaultValue'=>$fields->getDefaultFieldValue()),'mandatory'=>$fields->isMandatory(),'editable'=>$fields->isEditable(),'isunique'=>$fields->get('isunique'),'readonly'=>$fields->get('readonly'),'displaytype'=>$fields->get('displaytype'),'typeofdata'=>$fields->get('typeofdata'),'uitype'=>$fields->get('uitype'),'summaryfield'=>$fields->get('summaryfield'),'presence'=>$fields->get('presence'));
		}
		
		if(!empty($order_by) && !in_array($order_by,$Modulefields)){
			$message = vtranslate($order_by,$module)." ".$this->CTTranslate('Field does not exists');
			throw new WebServiceException(404,$message);
		}
		
		foreach($params as $fieldname){
			if(!empty($fieldname) && !in_array($fieldname,$Modulefields)){
				$message = vtranslate($fieldname,$module)." ".$this->CTTranslate('Field does not exists');
				throw new WebServiceException(404,$message);
			}
		}//end validation of fieldname
		
		$WithoutFilterModules = array('Users','CTUserFilterView');
		if(!$filterid && $module != ''  && !in_array($module, $WithoutFilterModules)) {
			$customView = new CustomView();
			$filterid = $customView->getViewId($module);
		}
		$filterOrAlertInstance = false;
		if(!empty($alertid)) {
			$filterOrAlertInstance = CTMobile_WS_AlertModel::modelWithId($alertid);
		}
		else if(!empty($filterid)) {
			$filterOrAlertInstance = CTMobile_WS_FilterModel::modelWithId($module, $filterid);
		}
		else if(!empty($search)) {
			
			$filterOrAlertInstance = $this->getSearchFilterModel($module, $search);
		}
		
		if($filterOrAlertInstance && strcmp($module, $filterOrAlertInstance->moduleName)) {
			$response = new CTMobile_API_Response();
			$message = $this->CTTranslate('Mismatched module information');
			$response->setError(1001, $message);
			return $response;
		}

		$getTimeTrackerQuery = $adb->pquery("SELECT * FROM ctmobile_timetracking_modules");
		$timeTrackerArray = array();
		for ($i=0; $i < $adb->num_rows($getTimeTrackerQuery); $i++) {
			$timeTrackerArray[] = $adb->query_result($getTimeTrackerQuery,$i,'module');
		}
		

		// Initialize with more information
		if($filterOrAlertInstance) {
			$filterOrAlertInstance->setUser($current_user);
		}
		
		// Paging model
		$pagingModel = $this->getPagingModel($request);
		$paging = array('index'=>$index, 'size'=>$size);
		if($user_type == 'free'){
			$maxLimit = $index * $size;
			//~ if($maxLimit > 30){
				//~ $result = array();
				//~ $response = new CTMobile_API_Response();
				//~ $msg = html_entity_decode('You do not have permission to view more records. Please subcscribe for Premium version.');
				//~ $response->setResult(array('records'=>$result,'msg'=>$msg,'module_record_status'=>false,'user_type'=>$user_type));
				//~ return $response;
			//~ }
		}

		/* Start: Added by Vijay Bhavsar */
		if($module == 'Leads') {
			$morefields = array('firstname', 'lastname', 'phone', 'company', 'designation', 'email', 'createdtime', 'modifiedtime','assigned_user_id');
			foreach ($params as $p) {
				if(!in_array($p, $morefields)){
					$morefields[]=$p;
				}
			}
		}else if($module == "Contacts"){
			$morefields = array('firstname', 'lastname', 'title', 'phone', 'email', 'createdtime', 'modifiedtime','assigned_user_id');
			foreach ($params as $p) {
				if(!in_array($p, $morefields)){
					$morefields[]=$p;
				}
			}
		}else if($module == "Products"){
			$morefields = array('productname','unit_price','purchase_cost', 'createdtime', 'modifiedtime','assigned_user_id','discontinued','description');
			foreach ($params as $p) {
				if(!in_array($p, $morefields)){
					$morefields[]=$p;
				}
			}
		}else if($module == "Services"){
			$morefields = array('servicename','unit_price','purchase_cost', 'createdtime', 'modifiedtime','assigned_user_id','discontinued','description');
			foreach ($params as $p) {
				if(!in_array($p, $morefields)){
					$morefields[]=$p;
				}
			}
		}else if($module == "CTUserFilterView"){
			$morefields = array('module_name','filter_id', 'filter_name','createdtime', 'modifiedtime', 'assigned_user_id');
			foreach ($params as $p) {
				if(!in_array($p, $morefields)){
					$morefields[]=$p;
				}
			}
		}else if($module == "Documents"){
			$morefields = array('notes_title','filename','filetype','createdtime', 'modifiedtime','assigned_user_id');
			foreach ($params as $p) {
				if(!in_array($p, $morefields)){
					$morefields[]=$p;
				}
			}
		}else if($module == "CTCalllog"){
			$morefields = array('calllog_no', 'calllog_name', 'modifiedtime','assigned_user_id');
			foreach ($params as $p) {
				if(!in_array($p, $morefields)){
					$morefields[]=$p;
				}
			}
		}else{
			$morefields = array();
			foreach ($params as $p) {
				$morefields[]=$p;
			}
			if($module == 'Users'){

			}else{
				$morefields[]= 'assigned_user_id';
			}
		}
		
		foreach($morefields as $key => $fields){
			if(!in_array($fields,$Modulefields)){
				unset($morefields[$key]);
			}else if($fieldModels[$fields]->isViewEnabled() != 1){
				unset($morefields[$key]);
			}
		}

		if($isRoute && $relatedModule != ''){
			$getRelatedField = $adb->pquery("SELECT * FROM vtiger_relatedlists INNER JOIN vtiger_field ON vtiger_field.fieldid = vtiger_relatedlists.relationfieldid WHERE vtiger_relatedlists.tabid  = ? AND vtiger_relatedlists.related_tabid = ?",array(getTabid($relatedModule),getTabid($module)));
			if($adb->num_rows($getRelatedField)){
				$relatedFieldName = $adb->query_result($getRelatedField,0,'fieldname');
				if($relatedFieldName != ''){
					$morefields[] = $relatedFieldName;
				}
			}
		}
		
		
		/* End: Added by Vijay Bhavsar */
		if($this->isCalendarModule($module)) {
			
			return $this->processSearchRecordLabelForCalendar($request, $filterOrAlertInstance, $pagingModel, $paging,$field_name, $field_value,$order_by,$orderby,$related,$params,$activitytype,$entityFields,$field_name2,$field_value2,$search_params,$isRoute,$relatedFieldName,$relatedModule,$selectedRecords);
		}
		
		$records = $this->fetchRecordLabelsForModule($module, $current_user, $morefields, $filterOrAlertInstance, $pagingModel, $paging, $field_name, $field_value,$order_by,$orderby,$related,$activitytype,$discontinued,$searchType,$field_name2,$field_value2,$search_params,$isRoute,$relatedFieldName,$relatedModule,$selectedRecords);
		$modifiedRecords = array();
		foreach($records as $record) {
			if ($record instanceof SqlResultIteratorRow) {
				$record = $record->data;
				// Remove all integer indexed mappings
				for($index = count($record); $index > -1; --$index) {
					if(isset($record[$index])) {
						unset($record[$index]);
					}
				}
			}
			
			if($module == 'CTUserFilterView'){
				$total_records_count = 0;
				$user_id = '19x'.$current_user->id;
				if($user_id!=$record['assigned_user_id']){
					continue;
				}elseif($record['filter_id']!='' && $record['module_name']!='' && ctype_digit($record['filter_id'])){
					if(Vtiger_Module_Model::getInstance($record['module_name'])){
						$listViewModel = Vtiger_ListView_Model::getInstance($record['module_name'], $record['filter_id']);
						$total_records_count = $listViewModel->getListViewCount();
					}else{
						continue;
					}
			
				}
			}

			$recordid = $record['id'];
			unset($record['id']);
			
			$eventstart = '';
			if($this->isCalendarModule($module)) {
				$eventstart = $record['date_start'];
				unset($record['date_start']);
			}

			$values = array_values($record);
			if($module == 'Users') {
				$label = implode(' ', $values);
			} else {
				$label = $values[1];
				$fieldnames = CTMobile_WS_Utils::getEntityFieldnames($module);
				$label = $record[$fieldnames[0]];
			}
			
			
			$record_id = explode('x', $recordid);
			$moduleModel = Vtiger_Module_Model::getInstance($module);
			$fieldModels = $moduleModel->getFields();
			/*$recordModel = Vtiger_Record_Model::getInstanceById($record_id[1],$module);*/
			foreach($record as $key => $value){
				$fieldModel = $fieldModels[$key];
				if($fieldModel){
					$uitype = $fieldModel->get('uitype');
					if($value){
						$record[$key] = html_entity_decode($value, ENT_QUOTES, $default_charset);
						if(isset($record[$key])){
				
																					 
							if($uitype != 53 && $uitype != 13 && $uitype != 17 && !in_array($uitype,$refrenceUitypes)){
								$record[$key] = $fieldModel->getDisplayValue($record[$key], $record_id[1], $recordModel);
							}
						}
					}else if($value == 0 && $uitype != 53 && $uitype != 13 && $uitype != 17 && !in_array($uitype,$refrenceUitypes)){
						$fieldModel = $fieldModels[$key];
						$record[$key] = $fieldModel->getDisplayValue($record[$key], $record_id[1], $recordModel);
						$record[$key] = html_entity_decode($record[$key], ENT_QUOTES, $default_charset);
					}else{
						$record[$key] = "";
					}										
				}
			}
			/* Start: Added by Vijay Bhavsar */
			$query = "SELECT * FROM vtiger_smsnotifier_servers WHERE isactive='1'";
			$result = $adb->pquery($query,array());
			$totalRecords = $adb->num_rows($result);
			
			if($module == "Leads") {
				$modifiedRecord = array('id' => $recordid, 'label'=>$record['firstname']." ".$record['lastname'],'phone' => $record['phone'],'email' => $record['email']); 
				
				foreach ($params as $key => $p) {
					$uitype = $fieldModels[$p]->get('uitype');
					$newKey = $key+2;
					$keys = 'label'.$newKey;
					if(in_array($uitype,$refrenceUitypes)){
						if($record[$p] == 0){
							$modifiedRecord[$keys]="";
						}else{
							$labelresult = $adb->pquery("SELECT label FROM vtiger_crmentity WHERE crmid = ?",array($record[$p]));
							$new = $adb->query_result($labelresult,0,'label');
							$modifiedRecord[$keys]=$new;
						}
					}else if($uitype == 53){
						$userRecordModel = Vtiger_Record_Model::getInstanceById($record[$p],'Users');
						if($userRecordModel->get('user_name') == ''){
							$query = "SELECT groupname FROM vtiger_groups WHERE groupid = ?";
							$groupResults = $adb->pquery($query,array($record[$p]));
							$modifiedRecord[$keys] = html_entity_decode($adb->query_result($groupResults,0,'groupname'),ENT_QUOTES,$default_charset);
						}else{
							$modifiedRecord[$keys] = html_entity_decode($userRecordModel->get('first_name').' '.$userRecordModel->get('last_name'),ENT_QUOTES,$default_charset);
						}
					}else if($uitype == 9){
						$modifiedRecord[$keys] = Vtiger_Double_UIType::getDisplayValue($record[$p]);
					}else{
						$modifiedRecord[$keys]=$record[$p];
				    }
				}

				if($totalRecords > 0){
					$modifiedRecord['sms_notifier'] = true;
				}else{
					$modifiedRecord['sms_notifier'] = false;
				}
				
			} else if($module == "Contacts"){
				$record_id = explode('x', $recordid);
				$AttachmentQuery =$adb->pquery("select vtiger_attachments.attachmentsid, vtiger_attachments.name, vtiger_attachments.subject, vtiger_attachments.path FROM vtiger_seattachmentsrel
											INNER JOIN vtiger_attachments ON vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid 
											LEFT JOIN vtiger_notes ON vtiger_notes.notesid = vtiger_seattachmentsrel.crmid 
											WHERE vtiger_seattachmentsrel.crmid = ?", array($record_id[1]));
											
				$AttachmentQueryCount = $adb->num_rows($AttachmentQuery);
				$document_path = array();
				
				if($AttachmentQueryCount > 0) {
					$name = $adb->query_result($AttachmentQuery, 0, 'name');
					$Path = $adb->query_result($AttachmentQuery, 0, 'path');
					$attachmentsId = $adb->query_result($AttachmentQuery, 0, 'attachmentsid');
					$contactImageUrl = $site_URL.$Path.$attachmentsId."_".$name;
				} else {
					$contactImageUrl = '';
				}
				$modifiedRecord = array('id' => $recordid, 'label'=>$record['firstname']." ".$record['lastname'],'ImageUrl' => $contactImageUrl,'phone'=> $record['phone'], 'email'=> $record['email']); 
				foreach ($params as $key => $p) {
					$uitype = $fieldModels[$p]->get('uitype');
					$newKey = $key+2;
					$keys = 'label'.$newKey;
					if(in_array($uitype,$refrenceUitypes)){
						if($record[$p] == 0){
							$modifiedRecord[$keys]="";
						}else{
							$labelresult = $adb->pquery("SELECT label FROM vtiger_crmentity WHERE crmid = ?",array($record[$p]));
							$new = $adb->query_result($labelresult,0,'label');
							$modifiedRecord[$keys]=$new;
						}
					}else if($uitype == 53){
						$userRecordModel = Vtiger_Record_Model::getInstanceById($record[$p],'Users');
						if($userRecordModel->get('user_name') == ''){
							$query = "SELECT groupname FROM vtiger_groups WHERE groupid = ?";
							$groupResults = $adb->pquery($query,array($record[$p]));
							$modifiedRecord[$keys] = html_entity_decode($adb->query_result($groupResults,0,'groupname'),ENT_QUOTES,$default_charset);
						}else{
							$modifiedRecord[$keys] = html_entity_decode($userRecordModel->get('first_name').' '.$userRecordModel->get('last_name'),ENT_QUOTES,$default_charset);
						}
					}else if($uitype == 9){
						$modifiedRecord[$keys] = Vtiger_Double_UIType::getDisplayValue($record[$p]);
					}else{
						$modifiedRecord[$keys]=$record[$p];
				    }
				}

				if($totalRecords > 0){
					$modifiedRecord['sms_notifier'] = true;
				}else{
					$modifiedRecord['sms_notifier'] = false;
				}
			
			}else if($module == "Products") {
				$record_id = explode('x', $recordid);
				$AttachmentQuery =$adb->pquery("select vtiger_attachments.attachmentsid, vtiger_attachments.name, vtiger_attachments.subject, vtiger_attachments.path FROM vtiger_seattachmentsrel
											INNER JOIN vtiger_attachments ON vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid 
											LEFT JOIN vtiger_notes ON vtiger_notes.notesid = vtiger_seattachmentsrel.crmid 
											WHERE vtiger_seattachmentsrel.crmid = ?", array($record_id[1]));
											
				$AttachmentQueryCount = $adb->num_rows($AttachmentQuery);
				$document_path = array();
				
				if($AttachmentQueryCount > 0) {
					$name = $adb->query_result($AttachmentQuery, 0, 'name');
					$Path = $adb->query_result($AttachmentQuery, 0, 'path');
					$attachmentsId = $adb->query_result($AttachmentQuery, 0, 'attachmentsid');
					$productImageUrl = $site_URL.$Path.$attachmentsId."_".$name;
				} else {
					$productImageUrl = '';
				}
				$modifiedRecord = array('id' => $recordid, 'label'=>$record['productname'], 'ImageUrl' => $productImageUrl,'unit_price' => $record['unit_price'],'purchase_cost' => $record['purchase_cost'],'discontinued'=>$record['discontinued'],'description'=>$record['description']);
				foreach ($params as $key => $p) {
					$uitype = $fieldModels[$p]->get('uitype');
					$newKey = $key+2;
					$keys = 'label'.$newKey;
					if(in_array($uitype,$refrenceUitypes)){
						if($record[$p] == 0){
							$modifiedRecord[$keys]="";
						}else{
							$labelresult = $adb->pquery("SELECT label FROM vtiger_crmentity WHERE crmid = ?",array($record[$p]));
							$new = $adb->query_result($labelresult,0,'label');
							$modifiedRecord[$keys]=$new;
						}
					}else if($uitype == 53){
						$userRecordModel = Vtiger_Record_Model::getInstanceById($record[$p],'Users');
						if($userRecordModel->get('user_name') == ''){
							$query = "SELECT groupname FROM vtiger_groups WHERE groupid = ?";
							$groupResults = $adb->pquery($query,array($record[$p]));
							$modifiedRecord[$keys] = html_entity_decode($adb->query_result($groupResults,0,'groupname'),ENT_QUOTES,$default_charset);
						}else{
							$modifiedRecord[$keys] = html_entity_decode($userRecordModel->get('first_name').' '.$userRecordModel->get('last_name'),ENT_QUOTES,$default_charset);
						}
					}else if($uitype == 9){
						$modifiedRecord[$keys] = Vtiger_Double_UIType::getDisplayValue($record[$p]);
					}else{
						$modifiedRecord[$keys]=$record[$p];
				    }
				}
			}else if($module == "Services") {
				$modifiedRecord = array('id' => $recordid, 'label'=>$record['servicename'], 'unit_price' => $record['unit_price'],'purchase_cost'=>$record['purchase_cost'],'discontinued'=>$record['discontinued'],'description'=>$record['description']); 
				foreach ($params as $key => $p) {
					$uitype = $fieldModels[$p]->get('uitype');
					$newKey = $key+2;
					$keys = 'label'.$newKey;
					if(in_array($uitype,$refrenceUitypes)){
						if($record[$p] == 0){
							$modifiedRecord[$keys]="";
						}else{
							$labelresult = $adb->pquery("SELECT label FROM vtiger_crmentity WHERE crmid = ?",array($record[$p]));
							$new = $adb->query_result($labelresult,0,'label');
							$modifiedRecord[$keys]=$new;
						}
					}else if($uitype == 53){
						$userRecordModel = Vtiger_Record_Model::getInstanceById($record[$p],'Users');
						if($userRecordModel->get('user_name') == ''){
							$query = "SELECT groupname FROM vtiger_groups WHERE groupid = ?";
							$groupResults = $adb->pquery($query,array($record[$p]));
							$modifiedRecord[$keys] = html_entity_decode($adb->query_result($groupResults,0,'groupname'),ENT_QUOTES,$default_charset);
						}else{
							$modifiedRecord[$keys] = html_entity_decode($userRecordModel->get('first_name').' '.$userRecordModel->get('last_name'),ENT_QUOTES,$default_charset);
						}
					}else if($uitype == 9){
						$modifiedRecord[$keys] = Vtiger_Double_UIType::getDisplayValue($record[$p]);
					}else{
						$modifiedRecord[$keys]=$record[$p];
				    }
				}
			}else if($module == "CTUserFilterView") {
				$modifiedRecord = array('id' => $recordid, 'label'=>$label, 'module_name'=>$record['module_name'], 'filter_id'=>$record['filter_id'], 'filter_name'=>$record['filter_name'],'modifiedtime'=>$record['modifiedtime'],'total_records_count'=>$total_records_count,'assigned_user_id'=>$record['assigned_user_id']); 
				foreach ($params as $key => $p) {
					$uitype = $fieldModels[$p]->get('uitype');
					$newKey = $key+2;
					$keys = 'label'.$newKey;
					if(in_array($uitype,$refrenceUitypes)){
						if($record[$p] == 0){
							$modifiedRecord[$keys]="";
						}else{
							$labelresult = $adb->pquery("SELECT label FROM vtiger_crmentity WHERE crmid = ?",array($record[$p]));
							$new = $adb->query_result($labelresult,0,'label');
							$modifiedRecord[$keys]=$new;
						}
					}else if($uitype == 53){
						$userRecordModel = Vtiger_Record_Model::getInstanceById($record[$p],'Users');
						if($userRecordModel->get('user_name') == ''){
							$query = "SELECT groupname FROM vtiger_groups WHERE groupid = ?";
							$groupResults = $adb->pquery($query,array($record[$p]));
							$modifiedRecord[$keys] = html_entity_decode($adb->query_result($groupResults,0,'groupname'),ENT_QUOTES,$default_charset);
						}else{
							$modifiedRecord[$keys] = html_entity_decode($userRecordModel->get('first_name').' '.$userRecordModel->get('last_name'),ENT_QUOTES,$default_charset);
						}
					}else if($uitype == 9){
						$modifiedRecord[$keys] = Vtiger_Double_UIType::getDisplayValue($record[$p]);
					}else{
						$modifiedRecord[$keys]=$record[$p];
				    }
				}
			}else if($module == "Documents") {
				$fileUrl = "";
				if($record['filename']){
					global $site_URL;
					$record_id = explode('x', $recordid);
					$attachResult = $adb->pquery("SELECT vtiger_attachments.attachmentsid,vtiger_attachments.path,vtiger_attachments.name FROM vtiger_attachments INNER JOIN vtiger_seattachmentsrel ON vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid WHERE vtiger_seattachmentsrel.crmid = ?",array($record_id[1]));
					$attachmentsid  = $adb->query_result($attachResult,0,'attachmentsid');
					$path  = $adb->query_result($attachResult,0,'path');
					$name = $adb->query_result($attachResult,0,'name');
					$fileUrl = $site_URL.$path.$attachmentsid.'_'.$name;
				}
				$modifiedRecord = array('id' => $recordid, 'label'=>$record['notes_title'],'filename'=>$record['filename'],'path'=>$fileUrl,'filetype'=>$record['filetype']); 
				foreach ($params as $key => $p) {
					$uitype = $fieldModels[$p]->get('uitype');
					$newKey = $key+2;
					$keys = 'label'.$newKey;
					if(in_array($uitype,$refrenceUitypes)){
						if($record[$p] == 0){
							$modifiedRecord[$keys]="";
						}else{
							$labelresult = $adb->pquery("SELECT label FROM vtiger_crmentity WHERE crmid = ?",array($record[$p]));
							$new = $adb->query_result($labelresult,0,'label');
							$modifiedRecord[$keys]=$new;
						}
					}else if($uitype == 53){
						$userRecordModel = Vtiger_Record_Model::getInstanceById($record[$p],'Users');
						if($userRecordModel->get('user_name') == ''){
							$query = "SELECT groupname FROM vtiger_groups WHERE groupid = ?";
							$groupResults = $adb->pquery($query,array($record[$p]));
							$modifiedRecord[$keys] = html_entity_decode($adb->query_result($groupResults,0,'groupname'),ENT_QUOTES,$default_charset);
						}else{
							$modifiedRecord[$keys] = html_entity_decode($userRecordModel->get('first_name').' '.$userRecordModel->get('last_name'),ENT_QUOTES,$default_charset);
						}
					}else{
						$modifiedRecord[$keys]=$record[$p];
				    }
				}
			}else if($module == "CTCalllog"){
				$modifiedRecord = array('id' => $recordid, 'label'=>$record['calllog_name']); 
				foreach ($params as $key => $p) {
					$uitype = $fieldModels[$p]->get('uitype');
					$newKey = $key+2;
					$keys = 'label'.$newKey;
					if(in_array($uitype,$refrenceUitypes)){
						if($record[$p] == 0){
							$modifiedRecord[$keys]="";
						}else{
							$labelresult = $adb->pquery("SELECT label FROM vtiger_crmentity WHERE crmid = ?",array($record[$p]));
							$new = $adb->query_result($labelresult,0,'label');
							$modifiedRecord[$keys]=$new;
						}
					}else if($uitype == 53){
						$userRecordModel = Vtiger_Record_Model::getInstanceById($record[$p],'Users');
						if($userRecordModel->get('user_name') == ''){
							$query = "SELECT groupname FROM vtiger_groups WHERE groupid = ?";
							$groupResults = $adb->pquery($query,array($record[$p]));
							$modifiedRecord[$keys] = html_entity_decode($adb->query_result($groupResults,0,'groupname'),ENT_QUOTES,$default_charset);
						}else{
							$modifiedRecord[$keys] = html_entity_decode($userRecordModel->get('first_name').' '.$userRecordModel->get('last_name'),ENT_QUOTES,$default_charset);
						}
					}else if($uitype == 9){
						$modifiedRecord[$keys] = Vtiger_Double_UIType::getDisplayValue($record[$p]);
					}else{
						$modifiedRecord[$keys]=$record[$p];
				    }
				}
			}else {
				$record_id = explode('x', $recordid);
				if($module == 'Users'){
					$recordModel = Users_Record_Model::getInstanceById($record_id[1],$module);
					$label = $recordModel->get('first_name').' '.$recordModel->get('last_name');
					$label = html_entity_decode($label, ENT_QUOTES, $default_charset);
				}else{
					$recordModel = Vtiger_Record_Model::getInstanceById($record_id[1],$module);
					$label = $recordModel->get('label');
					$label = html_entity_decode($label, ENT_QUOTES, $default_charset);
				}
				$modifiedRecord = array('id' => $recordid,'label'=>html_entity_decode($label, ENT_QUOTES, $default_charset));
				foreach ($params as $key => $p) {
					if(in_array($p,array('hdnGrandTotal','hdnSubTotal','pre_tax_total','txtAdjustment','received','balance','hdnTaxType'))){
						$newKey = $key+2;
						$keys = 'label'.$newKey;	
						if($p == 'hdnTaxType'){
							$modifiedRecord[$keys] = $recordModel->get($p);
						}else{
							$modifiedRecord[$keys] = number_format($recordModel->get($p),$current_user->no_of_currency_decimals,'.','');
						}
					}else{
						$uitype = $fieldModels[$p]->get('uitype');
						$newKey = $key+2;
						$keys = 'label'.$newKey;
						if(in_array($uitype,$refrenceUitypes)){
							if($record[$p] == 0){
								$modifiedRecord[$keys]="";
							}else{
								if($uitype == 77){
									$userRecordModel = Vtiger_Record_Model::getInstanceById($record[$p],'Users');
									if($userRecordModel->get('user_name') == ''){
										$query = "SELECT groupname FROM vtiger_groups WHERE groupid = ?";
										$groupResults = $adb->pquery($query,array($record[$p]));
										$modifiedRecord[$keys] = html_entity_decode($adb->query_result($groupResults,0,'groupname'),ENT_QUOTES,$default_charset);
									}else{
										$modifiedRecord[$keys] = html_entity_decode($userRecordModel->get('first_name').' '.$userRecordModel->get('last_name'),ENT_QUOTES,$default_charset);
									}
								}else{
									$labelresult = $adb->pquery("SELECT label FROM vtiger_crmentity WHERE crmid = ?",array($record[$p]));
									$new = $adb->query_result($labelresult,0,'label');
									$modifiedRecord[$keys]= decode_html(decode_html($new));
								}
							}
						}else if($uitype == 53){
							$userRecordModel = Vtiger_Record_Model::getInstanceById($record[$p],'Users');
							if($userRecordModel->get('user_name') == ''){
								$query = "SELECT groupname FROM vtiger_groups WHERE groupid = ?";
								$groupResults = $adb->pquery($query,array($record[$p]));
								$modifiedRecord[$keys] = html_entity_decode($adb->query_result($groupResults,0,'groupname'),ENT_QUOTES,$default_charset);
							}else{
								$modifiedRecord[$keys] = html_entity_decode($userRecordModel->get('first_name').' '.$userRecordModel->get('last_name'),ENT_QUOTES,$default_charset);
							}
						}else if($uitype == 9){
							$modifiedRecord[$keys] = Vtiger_Double_UIType::getDisplayValue($record[$p]);
						}else{
							$modifiedRecord[$keys]=$record[$p];
					    }
					    $modifiedRecord[$keys] = html_entity_decode($modifiedRecord[$keys],ENT_QUOTES,$default_charset);
					}
				}

			}

			$modifiedRecord['isRelatedRecord'] = false;
			$modifiedRecord['relationfield'] = "";
			if($isRoute && $relatedFieldName != ''){
				if($record[$relatedFieldName] != ''){
					$labelresult = $adb->pquery("SELECT setype FROM vtiger_crmentity WHERE crmid = ?",array($record[$relatedFieldName]));
					$setype = $adb->query_result($labelresult,0,'setype');
					if($setype == $relatedModule){
						$latlongData = $this->getLatLongOfRecord($record[$relatedFieldName]);
						if($latlongData['lat'] != '' && $latlongData['long'] != ''){
							$modifiedRecord['isRelatedRecord'] = true;
							$modifiedRecord['relationfield'] = CTMobile_WS_Utils::getEntityModuleWSId($relatedModule).'x'.$record[$relatedFieldName];
						}else{
							continue;
						}
					}else{
						continue;
					}
				}else{
					continue;
				}
			}else if($isRoute && $relatedModule != ''){
				continue;
			}

			/* End: Added by Vijay Bhavsar */
			
			//get Username Form userid
			if(!empty($modifiedRecord['assigned_user_id'])){
				if($module == 'CTUserFilterView'){
					$modifiedRecord['assigned_user_id'] = explode('x',$modifiedRecord['assigned_user_id']);
					$modifiedRecord['assigned_user_id'] = $modifiedRecord['assigned_user_id'][1];
				}
				$userRecordModel = Vtiger_Record_Model::getInstanceById($modifiedRecord['assigned_user_id'],'Users');
				if($userRecordModel->get('user_name') == ''){
					$query = "SELECT groupname FROM vtiger_groups WHERE groupid = ?";
					$groupResults = $adb->pquery($query,array($modifiedRecord['assigned_user_id']));
					$modifiedRecord['assigned_user_id'] = html_entity_decode($adb->query_result($groupResults,0,'groupname'),ENT_QUOTES,$default_charset);
				}else{
					$modifiedRecord['assigned_user_id'] = html_entity_decode($userRecordModel->get('first_name').' '.$userRecordModel->get('last_name'),ENT_QUOTES,$default_charset);
				}	
			}
			
			if(!empty($eventstart)) {
				$modifiedRecord['eventstart'] = $eventstart;
			}

			//code start for permission of Edit and Delete
			if($module == 'Events'){
				$prevModule = 'Calendar';
			}else{
				$prevModule = $module;
			}
			$editAction = Users_Privileges_Model::isPermitted($prevModule, 'EditView', $record_id[1]);
			$deleteAction = Users_Privileges_Model::isPermitted($prevModule, 'Delete', $record_id[1]);
			$modifiedRecord['editAction'] = $editAction;
			$modifiedRecord['deleteAction'] = $deleteAction;

			$tracking_user = $current_user->id;
			$cttimetrackerid = "";
			$tracking_status = false;
			$recordid = $record_id[1];
			$getStartTimeQuery = "SELECT * FROM vtiger_cttimetracker INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_cttimetracker.cttimetrackerid WHERE vtiger_crmentity.deleted = 0 AND vtiger_cttimetracker.tracking_user = '$tracking_user' AND vtiger_cttimetracker.tracking_status = 'Start'";
			$resultStartTime = $adb->pquery($getStartTimeQuery,array());
			if($adb->num_rows($resultStartTime) > 0){
				$cttimetrackerid = $adb->query_result($resultStartTime,0,'cttimetrackerid');
				$cttimetrackerid = vtws_getWebserviceEntityId('CTTimeTracker',$cttimetrackerid);
				$tracking_status = true;
				$related_to = $adb->query_result($resultStartTime,0,'related_to');
				if($related_to == $recordid){
					$isTimeTrackingSameRecord = true;
				}else{
					$isTimeTrackingSameRecord = false;
				}
			}

			$modifiedRecord['isTimeTracking'] = false;
			if(in_array($module,$timeTrackerArray)){
				$time_tracking_record = "";
				$getTimeQuery = "SELECT * FROM vtiger_cttimetracker INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_cttimetracker.cttimetrackerid WHERE vtiger_crmentity.deleted = 0 AND vtiger_cttimetracker.tracking_user = '$tracking_user' AND vtiger_cttimetracker.related_to = '$recordid'";
				$resultTime = $adb->pquery($getTimeQuery,array());
				$nooftimetracking = $adb->num_rows($resultTime);
				//$isTimeTrackingSameRecord = false;
				if($adb->num_rows($resultTime) > 0){
					$isTimeTracking = true;
					//$cttimetrackerid = $adb->query_result($resultTime,0,'cttimetrackerid');
					$time_tracking_record = $adb->query_result($resultTime,$nooftimetracking-1,'cttimetrackerid');
					$time_tracking_record = vtws_getWebserviceEntityId('CTTimeTracker',$time_tracking_record);
					$time_tracking_status = $adb->query_result($resultTime,$nooftimetracking-1,'tracking_status');
					if($time_tracking_status == 'Start'){
						$isTimeTrackingSameRecord = true;
						$tracking_status = true;
					}
				}else{
					$isTimeTracking = false;
				}

				$modifiedRecord['isTimeTracking'] = $isTimeTracking;
				$modifiedRecord['cttimetrackerid'] = $cttimetrackerid;
				$modifiedRecord['time_tracking_record'] = $time_tracking_record;
				$modifiedRecord['tracking_status'] = $tracking_status;
				$modifiedRecord['isTimeTrackingSameRecord'] = $isTimeTrackingSameRecord;
			}
			//code End for permission of Edit and Delete
			$modifiedRecords[] = $modifiedRecord;
		}
		if($module == 'Users' && $isMentionUser == true){
			$current_user =  $this->getActiveUser();
		}
		$USER_MODEL = Users_Record_Model::getCurrentUserModel();
		$AccessibleUsers = array_keys($USER_MODEL->getAccessibleUsers());
		
		$AllowedUsers = array();
		foreach ($modifiedRecords as $key => $part) {
			if($module == 'Users' && $type == 'owner'){
				$id = explode('x', $part['id']);
				$userid = $id[1];
				if(!in_array($userid, $AccessibleUsers)){
					unset($modifiedRecords[$key]);
				}else{
					$AllowedUsers[] = $modifiedRecords[$key];
				}
			}else if($module == 'Users' && $isMentionUser == true){
				$id = explode('x', $part['id']);
				$userid = $id[1];
				if(!in_array($userid, $AccessibleUsers)){
					unset($modifiedRecords[$key]);
				}else{
					$AllowedUsers[] = $modifiedRecords[$key];
				}
			}	
			$sort[$key] = strtotime($part['modifiedtime']);	
		}

		if($module == 'Users' && $isMentionUser == true){
			$modifiedRecords = $AllowedUsers;
		}
		
		if($module == 'Users' && $type == 'owner'){
			$users = $AllowedUsers;
			$pagingModel = $this->getPagingModel($request);
			$paging = array('index'=>$index, 'size'=>$size);
			$modifiedRecords = array();
			$modifiedRecords = $this->GroupDetails($pagingModel,$paging);
			$groups = $modifiedRecords;
			$modifiedRecord = array();
			$modifiedRecord['Users'] = $users;
			$modifiedRecord['Groups'] = $groups;
			$moduleLabel = vtranslate($module,$module);
			$response = new CTMobile_API_Response();
			$response->setResult(array('records'=>$modifiedRecord, 'module'=>$module,'moduleLabel'=>$moduleLabel, 'message'=>'','module_record_status'=>true));
			return $response;
		}
		//for create action 
		$userPrivModel = Users_Privileges_Model::getInstanceById($current_user->id);
		$createAction = $userPrivModel->hasModuleActionPermission($moduleModel->getId(), 'CreateView');
		$ModulesArray = array('SMSNotifier','PBXManager','CTPushNotification','CTCalllog','CTAttendance');
		if(in_array($module, $ModulesArray)){
			$createAction = false;
		}

		$isLast = true;
		$totalLabel = "";
		if($module == 'Users'){
			$this->totalQuery = "SELECT first_name,last_name from vtiger_users WHERE vtiger_users.status = 'Active' AND vtiger_users.deleted = 0 ";
			if(!empty($order_by) && !empty($orderby)){
				$orderClause = " ORDER BY ".$order_by." ".$orderby;
			}else{
				$orderClause = '';
			}
			$this->totalQuery.= $orderClause;
			$this->totalParams = array();
		}
		if($this->totalQuery != ""){
			$totalResults = $adb->pquery($this->totalQuery,$this->totalParams);
			$totalRecords = $adb->num_rows($totalResults);
			if($paging['index'] && $paging['size']){
				if($totalRecords > $paging['index']*$paging['size']){
					$isLast = false;	
					$pagesize = $paging['index']-1;
					$startRange = $pagesize*$paging['size']+1;
					$lastRange = $paging['index']*$paging['size'];	
				}else{
					$isLast = true;
					$pagesize = $paging['index']-1;
					$startRange = $pagesize*$paging['size']+1;
					$lastRange = $totalRecords;
				}
			}else{
				$isLast = true;
				$startRange = 1;
				$lastRange = $totalRecords;
			}

			$totalLabel = $startRange.' '.vtranslate('LBL_to', $module).' '.$lastRange.' '.vtranslate('LBL_OF', $module).' '.$totalRecords;
			
		}

		//code start for mapview by suresh
		$allowedmapviewModules =  array('Leads','Contacts','Accounts','Calendar');
		if(in_array($module, $allowedmapviewModules)){
			$is_map_enable = true;
			foreach ($modifiedRecords as $key => $part) {
				$id = explode('x', $part['id']);
				$recordid = $id[1];
				$latlongData = $this->getLatLongOfRecord($recordid);
				$modifiedRecords[$key]['latitude'] = $latlongData['lat'];
				$modifiedRecords[$key]['longitude'] = $latlongData['long'];
			}
		}else{
			$is_map_enable = false;
		}
		//code end for mapview by suresh

		//code start for Route by suresh
		if($isRoute && in_array($module, array('HelpDesk','Invoice','Quotes','SalesOrder','PurchaseOrder'))){
			foreach ($modifiedRecords as $key => $part) {
				$id = explode('x', $part['id']);
				$recordid = $id[1];
				$latlongData = $this->getLatLongFromRelatedRecord($recordid,$module);
				$modifiedRecords[$key]['latitude'] = $latlongData['lat'];
				$modifiedRecords[$key]['longitude'] = $latlongData['long'];
			}
		}
		//code end for Route 

		//code start for is_display_image by suresh
		$allowedDisplayImageModules = array('Contacts','Products');
		if(in_array($module, $allowedDisplayImageModules)){
			$is_display_image = true;
		}else{
			$is_display_image = false;
		}

		$relatedModuleList =  array();
		if($isRoute && $relatedModule == ''){
			$parentModuleModel = Vtiger_Module_Model::getInstance($module);
			$relationModels = $parentModuleModel->getRelations();
			foreach($relationModels as $key => $relationModules){
				if($relationModules->isAddActionSupported() && !empty($relationModules->getRelationField())){
					$relatedmoduleName = $relationModules->get('relatedModuleName');
					if($relatedmoduleName != 'CTTimeTracker'){
						$relatedModuleList[] =  array('name'=>$relatedmoduleName,'label'=>vtranslate($relatedmoduleName,$relatedmoduleName));
					}
				}
			}
		}

		if($isRoute && $relatedModule != ''){
			$startRange.' '.vtranslate('LBL_to', $module).' '.$lastRange.' '.vtranslate('LBL_OF', $module).' '.$totalRecords;
			if(count($modifiedRecords) < $totalRecords){
				$lastRange = count($modifiedRecords);
				$totalRecords = count($modifiedRecords);
				$totalLabel = $startRange.' '.vtranslate('LBL_to', $module).' '.$lastRange.' '.vtranslate('LBL_OF', $module).' '.$totalRecords;
			}
		}
		$moduleLabel = vtranslate($module,$module);
		$response = new CTMobile_API_Response();
		if(count($modifiedRecords) == 0) {
			$message = $this->CTTranslate('No records found');
			$response->setResult(array('records'=>$modifiedRecords, 'module'=>$module,'moduleLabel'=>$moduleLabel, 'message'=>$message,'module_record_status'=>false,'createAction'=>$createAction,'isLast'=>$isLast,'entityField'=>$entityFields,'pagingLabel'=>"",'is_map_enable'=>$is_map_enable,'is_display_image'=>$is_display_image,'relatedModuleList'=>$relatedModuleList));
		} else {
			$response->setResult(array('records'=>$modifiedRecords, 'module'=>$module,'moduleLabel'=>$moduleLabel, 'message'=>'','module_record_status'=>true,'createAction'=>$createAction,'isLast'=>$isLast,'entityField'=>$entityFields,'pagingLabel'=>$totalLabel,'is_map_enable'=>$is_map_enable,'is_display_image'=>$is_display_image,'relatedModuleList'=>$relatedModuleList));
		}
		
		return $response;
	}
	
	function processSearchRecordLabelForCalendar(CTMobile_API_Request $request,$filterOrAlertInstance = false, $pagingModel = false, $paging = array(),$field_name, $field_value,$order_by,$orderby,$related,$params,$activitytype,$entityFields,$field_name2,$field_value2,$search_params,$isRoute,$relatedFieldName,$relatedModule,$selectedRecords) {
		global $adb;
		$current_user = $this->getActiveUser();
		$module = $request->get('module');
		$refrenceUitypes = array(10,51,57,58,59,66,73,75,76,78,80,81,101);
		// Fetch both Calendar (Todo) and Event information

		$getTimeTrackerQuery = $adb->pquery("SELECT * FROM ctmobile_timetracking_modules");
		$timeTrackerArray = array();
		for ($i=0; $i < $adb->num_rows($getTimeTrackerQuery); $i++) {
			$timeTrackerArray[] = $adb->query_result($getTimeTrackerQuery,$i,'module');
		}
		
		if($module == 'Calendar' && $activitytype == 'Task'){
			$moreMetaFields = array('date_start', 'time_start', 'activitytype', 'location', 'subject', 'createdtime','due_date','taskstatus','taskpriority','eventstatus');
		}else{
			$moreMetaFields = array('date_start', 'time_start', 'activitytype', 'location', 'subject', 'createdtime','due_date','eventstatus');
		}
		$display_params = $params;
		foreach($params as $p){
			if(!in_array($p,$moreMetaFields)){
				$moreMetaFields[] = $p;
			}
		}
		if($relatedFieldName != ''){
			$moreMetaFields[] = $relatedFieldName;
		}

		$records=$this->fetchRecordLabelsForModule($module, $current_user, $moreMetaFields, $filterOrAlertInstance, $pagingModel, $paging,$field_name, $field_value,$order_by,$orderby,$related,$activitytype,$discontinued,$searchType,$field_name2,$field_value2,$search_params,$isRoute,$relatedFieldName,$relatedModule,$selectedRecords);

		if($module == 'Calendar'){
			if(!empty($params)){
				$params =  array_merge($params,array('date_start','due_date','taskstatus','taskpriority'));
			}else{
				$params = array('date_start','due_date','taskstatus','taskpriority');
			}
		}
		$modifiedRecords = array();
		foreach($records as $record) {

			if ($record instanceof SqlResultIteratorRow) {
				$record = $record->data;
				// Remove all integer indexed mappings
				for($index = count($record); $index > -1; --$index) {
					if(isset($record[$index])) {
						unset($record[$index]);
					}
				}
			}
			
		    $recordId = explode('x',$record['id']);
		    $modifiedRecord = array();
		    $EventTaskQuery = $adb->pquery("SELECT * FROM  `vtiger_activity` WHERE activitytype = ? AND activityid = ?",array('Task',$recordId[1])); 
		    if($adb->num_rows($EventTaskQuery) > 0){
				$wsid = CTMobile_WS_Utils::getEntityModuleWSId('Calendar');
				$record['id'] = $wsid.'x'.$recordId[1];
				$modifiedRecord['module'] = 'Calendar';
			}else{
				$wsid = CTMobile_WS_Utils::getEntityModuleWSId('Events');
				$record['id'] = $wsid.'x'.$recordId[1];
				$modifiedRecord['module'] = 'Events';
			}
			
			$moduleModel = Vtiger_Module_Model::getInstance($module);
			$fieldModels = $moduleModel->getFields();
			$Modulefields = array_keys($fieldModels);

			$modifiedRecord['id'] = $record['id'];
			$modifiedRecord['label'] =  $record['subject'];            
			unset($record['id']);

			foreach ($params as $key => $p) {
				if(!empty($p) && !in_array($p,$Modulefields)){
					$message = vtranslate($field_name,$module)." ".$this->CTTranslate('Field does not exists');
					throw new WebServiceException(404,$message);
				}else{

					$uitype = $fieldModels[$p]->get('uitype');
					$newKey = $key+2;
					$keys = 'label'.$newKey;
					
					if(in_array($uitype,$refrenceUitypes)){
						if($record[$p] == 0){
							$modifiedRecord[$keys]="";
						}else{
							$labelresult = $adb->pquery("SELECT label FROM vtiger_crmentity WHERE crmid = ?",array($record[$p]));
							$new = decode_html(decode_html($adb->query_result($labelresult,0,'label')));
							$modifiedRecord[$keys]=$new;
						}
					}else if($p == 'assigned_user_id'){
						$userRecordModel = Vtiger_Record_Model::getInstanceById($record['assigned_user_id'],'Users');
						if($userRecordModel->get('user_name') == ''){
							$query = "SELECT groupname FROM vtiger_groups WHERE groupid = ?";
							$groupResults = $adb->pquery($query,array($record['assigned_user_id']));
							$modifiedRecord[$keys] = $adb->query_result($groupResults,0,'groupname');
						}else{
							$modifiedRecord[$keys] = $userRecordModel->get('first_name').' '.$userRecordModel->get('last_name');
						}
					}else{
						$recordModel = Vtiger_Record_Model::getInstanceById($recordId[1],'Calendar');
						if($module == 'Calendar' && in_array($p, array('date_start','due_date','taskstatus','taskpriority','time_start','time_end'))){
							if(!in_array($p, $display_params)){
								$keys = $p;
								if(!empty($record[$p])){
									if($p == 'date_start'){
										$startDateTime = Vtiger_Datetime_UIType::getDisplayDateTimeValue($record['date_start'].' '.$record['time_start']);
										$DateTime = explode(' ', $startDateTime);
										$modifiedRecord[$keys] = $DateTime[0];
									}else if($p == 'due_date'){
										$endDateTime = Vtiger_Datetime_UIType::getDisplayDateTimeValue($record['due_date'].' '.$record['time_end']);
										$DateTime = explode(' ', $endDateTime);
										$modifiedRecord[$keys] = $DateTime[0];
									}else if($p == 'time_start' || $p == 'time_end'){
										$date = new DateTime();
										$dateTime = new DateTimeField($date->format('Y-m-d').' '.$record[$p]);
										$value = Vtiger_Time_UIType::getDisplayValue($dateTime->getDisplayTime());
										$value = explode(' ',$value);
										if(count($value) > 1){
										 $values = $value[0].' '.$value[1];
									    }else{
											$values = $value[0];
										}
										$modifiedRecord[$keys] = $values;
									}else if($p == 'taskstatus'){
										if($modifiedRecord['module'] == 'Events'){
											$modifiedRecord[$keys] = $fieldModels['eventstatus']->getDisplayValue($record['eventstatus'], $recordId[1], $recordModel);
										}else{
											$modifiedRecord[$keys] = $fieldModels[$p]->getDisplayValue($record[$p], $recordId[1], $recordModel);
										}
									}else{
										$modifiedRecord[$keys] = $fieldModels[$p]->getDisplayValue($record[$p], $recordId[1], $recordModel);
									}
								}else{
									if($p == 'taskstatus' && $modifiedRecord['module'] == 'Events'){
										if(!empty($record['eventstatus'])){
											$modifiedRecord[$keys] = $fieldModels['eventstatus']->getDisplayValue($record['eventstatus'], $recordId[1], $recordModel);
										}else{
											$modifiedRecord[$keys] = "";
										}
									}else{

										$modifiedRecord[$keys] = "";
									}
								}
							}else{
								
								if(!empty($record[$p])){
									if($p == 'date_start'){
										$startDateTime = Vtiger_Datetime_UIType::getDisplayDateTimeValue($record['date_start'].' '.$record['time_start']);
										$DateTime = explode(' ', $startDateTime);
										$modifiedRecord[$keys] = $DateTime[0];
									}else if($p == 'due_date'){
										$endDateTime = Vtiger_Datetime_UIType::getDisplayDateTimeValue($record['due_date'].' '.$record['time_end']);
										$DateTime = explode(' ', $endDateTime);
										$modifiedRecord[$keys] = $DateTime[0];
									}else if($p == 'time_start' || $p == 'time_end'){
										$date = new DateTime();
										$dateTime = new DateTimeField($date->format('Y-m-d').' '.$record[$p]);
										$value = Vtiger_Time_UIType::getDisplayValue($dateTime->getDisplayTime());
										$value = explode(' ',$value);
										if(count($value) > 1){
										 $values = $value[0].' '.$value[1];
									    }else{
											$values = $value[0];
										}
										$modifiedRecord[$keys] = $values;
									}else if($p == 'taskstatus'){
										if($modifiedRecord['module'] == 'Events'){
											$modifiedRecord[$keys] = $fieldModels['eventstatus']->getDisplayValue($record['eventstatus'], $recordId[1], $recordModel);
										}else{
											$modifiedRecord[$keys] = $fieldModels[$p]->getDisplayValue($record[$p], $recordId[1], $recordModel);
										}
									}else{
										$modifiedRecord[$keys] = $fieldModels[$p]->getDisplayValue($record[$p], $recordId[1], $recordModel);
									}
									$modifiedRecord[$p] = $modifiedRecord[$keys];
								}else{
									if($p == 'taskstatus' && $modifiedRecord['module'] == 'Events'){
										if(!empty($record['eventstatus'])){
											$modifiedRecord[$keys] = $fieldModels['eventstatus']->getDisplayValue($record['eventstatus'], $recordId[1], $recordModel);
											$modifiedRecord[$p] = $modifiedRecord[$keys];
										}else{
											$modifiedRecord[$keys] = "";
											$modifiedRecord[$p] = $modifiedRecord[$keys];
										}
									}else{

										$modifiedRecord[$keys] = "";
										$modifiedRecord[$p] = $modifiedRecord[$keys];
									}
								}
							}
						}else{
							if(!empty($record[$p])){
								$modifiedRecord[$keys] = $fieldModels[$p]->getDisplayValue($record[$p], $recordId[1], $recordModel);
							}else{
								$modifiedRecord[$keys] = "";
							}
						}
						//$modifiedRecord[$keys]=$record[$p];
				    }
				}
			}

			//code start for permission of Edit and Delete
			if($module == 'Events'){
				$prevModule = 'Calendar';
			}else{
				$prevModule = $module;
			}
			$editAction = Users_Privileges_Model::isPermitted($prevModule, 'EditView', $recordId[1]);
			$deleteAction = Users_Privileges_Model::isPermitted($prevModule, 'Delete', $recordId[1]);
			$modifiedRecord['editAction'] = $editAction;
			$modifiedRecord['deleteAction'] = $deleteAction;
			//code End for permission of Edit and Delete
			
			$modifiedRecord['isRelatedRecord'] = false;
			$modifiedRecord['relationfield'] = "";
			if($isRoute && $relatedFieldName != ''){
				if($record[$relatedFieldName] != ''){
					$labelresult = $adb->pquery("SELECT setype FROM vtiger_crmentity WHERE crmid = ?",array($record[$relatedFieldName]));
					$setype = $adb->query_result($labelresult,0,'setype');
					if($setype == $relatedModule){
						$latlongData = $this->getLatLongOfRecord($record[$relatedFieldName]);
						if($latlongData['lat'] != '' && $latlongData['long'] != ''){
							$modifiedRecord['isRelatedRecord'] = true;
							$modifiedRecord['relationfield'] = CTMobile_WS_Utils::getEntityModuleWSId($relatedModule).'x'.$record[$relatedFieldName];
						}else{
							continue;
						}
					}else{
						continue;
					}
				}else{
					continue;
				}
			}else if($isRoute && $relatedModule != ''){
				continue;
			}
			if(Users_Privileges_Model::isPermitted($prevModule, 'DetailView', $recordId[1])){
				$modifiedRecords[] = $modifiedRecord;
			}

		}
		if($order_by == ''){
			foreach ($modifiedRecords as $key => $part) {
				$sort[$key] = strtotime($part['startDateTime']);
			}
		}


		if($this->totalQuery != ""){
			$totalResults = $adb->pquery($this->totalQuery,$this->totalParams);
			$totalRecords = $adb->num_rows($totalResults);
			if($paging['index'] && $paging['size']){
				if($totalRecords > $paging['index']*$paging['size']){
					$isLast = false;	
					$pagesize = $paging['index']-1;
					$startRange = $pagesize*$paging['size']+1;
					$lastRange = $paging['index']*$paging['size'];	
				}else{
					$isLast = true;
					$pagesize = $paging['index']-1;
					$startRange = $pagesize*$paging['size']+1;
					$lastRange = $totalRecords;
				}
			}else{
				$isLast = true;
				$startRange = 1;
				$lastRange = $totalRecords;
			}

			$totalLabel = $startRange.' '.vtranslate('LBL_to', $module).' '.$lastRange.' '.vtranslate('LBL_OF', $module).' '.$totalRecords;
			
		}

		if($isRoute && $relatedModule != ''){
			$startRange.' '.vtranslate('LBL_to', $module).' '.$lastRange.' '.vtranslate('LBL_OF', $module).' '.$totalRecords;
			if(count($modifiedRecords) < $totalRecords){
				$lastRange = count($modifiedRecords);
				$totalRecords = count($modifiedRecords);
				$totalLabel = $startRange.' '.vtranslate('LBL_to', $module).' '.$lastRange.' '.vtranslate('LBL_OF', $module).' '.$totalRecords;
			}
		}

		$moduleModel = Vtiger_Module_Model::getInstance('Calendar');
		$userPrivModel = Users_Privileges_Model::getInstanceById($current_user->id);
		$createAction = $userPrivModel->hasModuleActionPermission($moduleModel->getId(), 'CreateView');
		if($order_by == ''){
			array_multisort($sort, SORT_DESC, $modifiedRecords);
		}

		//code start for mapview by suresh
		$is_map_enable = true;
		$tracking_user = $current_user->id;
		$cttimetrackerid = "";
		$tracking_status = false;
		$getStartTimeQuery = "SELECT * FROM vtiger_cttimetracker INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_cttimetracker.cttimetrackerid WHERE vtiger_crmentity.deleted = 0 AND vtiger_cttimetracker.tracking_user = '$tracking_user' AND vtiger_cttimetracker.tracking_status = 'Start'";
		$resultStartTime = $adb->pquery($getStartTimeQuery,array());
		if($adb->num_rows($resultStartTime) > 0){
			$cttimetrackerid = $adb->query_result($resultStartTime,0,'cttimetrackerid');
			$cttimetrackerid = vtws_getWebserviceEntityId('CTTimeTracker',$cttimetrackerid);
			$tracking_status = true;
			$related_to = $adb->query_result($resultStartTime,0,'related_to');
			
		}
		foreach ($modifiedRecords as $key => $part) {
			$id = explode('x', $part['id']);
			$recordid = $id[1];
			if($related_to == $recordid){
				$isTimeTrackingSameRecord = true;
			}else{
				$isTimeTrackingSameRecord = false;
			}
			$latlongData = $this->getLatLongOfRecord($recordid);
			$modifiedRecords[$key]['latitude'] = $latlongData['lat'];
			$modifiedRecords[$key]['longitude'] = $latlongData['long'];

			if(in_array($module,$timeTrackerArray)){
				$recordid = $record_id[1];
				$time_tracking_record = "";
				$getTimeQuery = "SELECT * FROM vtiger_cttimetracker INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_cttimetracker.cttimetrackerid WHERE vtiger_crmentity.deleted = 0 AND vtiger_cttimetracker.tracking_user = '$tracking_user' AND vtiger_cttimetracker.related_to = '$recordid'";
				$resultTime = $adb->pquery($getTimeQuery,array());
				$nooftimetracking = $adb->num_rows($resultTime);
				//$isTimeTrackingSameRecord = false;
				if($adb->num_rows($resultTime) > 0){
					$isTimeTracking = true;
					$time_tracking_record = $adb->query_result($resultTime,$nooftimetracking-1,'cttimetrackerid');
					$time_tracking_record = vtws_getWebserviceEntityId('CTTimeTracker',$time_tracking_record);
					$time_tracking_status = $adb->query_result($resultTime,$nooftimetracking-1,'tracking_status');
					if($time_tracking_status == 'Start'){
						$isTimeTrackingSameRecord = true;
					}
				}else{
					$isTimeTracking = false;
				}

				$modifiedRecord['isTimeTracking'] = $isTimeTracking;
				$modifiedRecord['cttimetrackerid'] = $cttimetrackerid;
				$modifiedRecord['time_tracking_record'] = $time_tracking_record;
				$modifiedRecord['tracking_status'] = $tracking_status;
				$modifiedRecord['isTimeTrackingSameRecord'] = $isTimeTrackingSameRecord;
			}
		}
		//code end for mapview by suresh

		//code start for is_display_image by suresh
		$is_display_image = false;

		$moduleLabel = vtranslate($module,$module);
		$response = new CTMobile_API_Response();
		if(count($modifiedRecords) == 0) {
			$message = $this->CTTranslate('No records found');
			$response->setResult(array('records'=>$modifiedRecords, 'module'=>$module,'moduleLabel'=>$moduleLabel, 'message'=>$message,'module_record_status'=>false,'isLast'=>$isLast,'createAction'=>$createAction,'entityField'=>$entityFields,'pagingLabel'=>"",'is_map_enable'=>$is_map_enable,'is_display_image'=>$is_display_image));
		} else {
			ksort($modifiedRecords);
			$response->setResult(array('records'=>$modifiedRecords, 'module'=>$module,'moduleLabel'=>$moduleLabel, 'message'=>'','module_record_status'=>true,'isLast'=>$isLast,'createAction'=>$createAction,'entityField'=>$entityFields,'pagingLabel'=>$totalLabel,'is_map_enable'=>$is_map_enable,'is_display_image'=>$is_display_image));
		}
		
		return $response;
	}
	
	function fetchRecordLabelsForModule($module, $user, $morefields=array(), $filterOrAlertInstance=false, $pagingModel = false, $paging=array(), $field_name, $field_value,$order_by,$orderby,$related,$activitytype,$discontinued,$searchType = '',$field_name2,$field_value2,$search_params,$isRoute,$relatedFieldName,$relatedModule,$selectedRecords) {
		
		if($module != 'Users') {
			$morefields[]='modifiedtime';
		}
		
		if($this->isCalendarModule($module)) {
			$fieldnames = CTMobile_WS_Utils::getEntityFieldnames('Calendar');
		} else {
			$fieldnames = CTMobile_WS_Utils::getEntityFieldnames($module);
		}
		
		if(!empty($morefields)) {
			foreach($morefields as $fieldname) $fieldnames[] = $fieldname;
		}

		if($filterOrAlertInstance === false) {
			$filterOrAlertInstance = CTMobile_WS_SearchFilterModel::modelWithCriterias($module);
			$filterOrAlertInstance->setUser($user);
		}

		return $this->queryToSelectFilteredRecords($module, $fieldnames, $filterOrAlertInstance, $pagingModel, $paging, $field_name, $field_value,$order_by,$orderby,$user,$related,$activitytype,$discontinued,$searchType,$field_name2,$field_value2,$search_params,$isRoute,$relatedFieldName,$relatedModule,$selectedRecords);
		
	}
	
	function queryToSelectFilteredRecords($module, $fieldnames, $filterOrAlertInstance, $pagingModel, $paging = array(), $field_name, $field_value,$order_by,$orderby,$user,$related,$activitytype,$discontinued,$searchType='',$field_name2,$field_value2,$search_params,$isRoute,$relatedFieldName,$relatedModule,$selectedRecords) {
		
		if (($filterOrAlertInstance instanceof CTMobile_WS_SearchFilterModel) && !$this->isCalendarModule($module)) {
			if(!empty($order_by) && !empty($orderby)){
				$orderClause = " ORDER BY ".$order_by." ".$orderby;
			}else{
				$orderClause = '';
			}
			return $filterOrAlertInstance->execute($fieldnames, $pagingModel, $paging, $orderClause,$field_name,$field_value);
			
		}
		if($field_name){
			$fieldnames = array_merge($fieldnames,Zend_JSON::decode($field_name));
		}
		global $adb,$current_user;
		$current_user = $this->getActiveUser();
		$moduleWSId = CTMobile_WS_Utils::getEntityModuleWSId($module);
		$columnByFieldNames = CTMobile_WS_Utils::getModuleColumnTableByFieldNames($module, $fieldnames);
		// Build select clause similar to Webservice query
		$selectColumnClause = "CONCAT('{$moduleWSId}','x',vtiger_crmentity.crmid) as id,";
		foreach($columnByFieldNames as $fieldname=>$fieldinfo) {
			$selectColumnClause .= sprintf("%s.%s as %s,", $fieldinfo['table'],$fieldinfo['column'],$fieldname);
		}
		
		$selectColumnClause = rtrim($selectColumnClause, ',');
		$var =array();
		for($i=0;$i<count($fieldnames);$i++){
			$var[]= $fieldnames[$i];
		}
		/*if($field_name){
			$var = array_merge($var,Zend_JSON::decode($field_name));
			//$var[]=$field_name;
		}*/
		if($field_name2){
			$var[]=$field_name2;
		}
		$var[]='id';
		$generator = new EnhancedQueryGenerator($module, $user);
		if($related != 1){
		 $generator->initForCustomViewById($filterOrAlertInstance->filterid);
	    }
		$generator->setFields($var);
		if(!empty($search_params)) {
			$search_params = Zend_JSON::decode($search_params);
			$moduleModel = Vtiger_Module_Model::getInstance($module);
			$search_params = Vtiger_Util_Helper::transferListSearchParamsToFilterCondition($search_params, $moduleModel);
			$glue = "";
			if(count($generator->getWhereFields()) > 0 && (count($search_params)) > 0) {
				$glue = QueryGenerator::$AND;
			}
			$generator->parseAdvFilterList($search_params, $glue);
		}else{
			$search_params = array();
		}

		$query = $generator->getQuery();
		
		$midq=explode('FROM', $query);
		$query="SELECT $selectColumnClause FROM ".$midq[1];

		if($module == 'Events' || $module == 'Calendar'){
			$Eventsquery = explode('WHERE',$query);
			$query = $Eventsquery[0]." WHERE ".$Eventsquery[1]." AND ( vtiger_activity.activitytype <> 'Emails')  AND vtiger_activity.activityid > 0 ";
		}else if($module == 'Products' && $discontinued == 1){
			$Productsquery = explode('WHERE',$query);
			$query = $Productsquery[0]." WHERE vtiger_products.discontinued = 1 AND ".$Productsquery[1];
		}else if($module == 'Services' && $discontinued == 1){
			$Servicesquery = explode('WHERE',$query);
			$query = $Servicesquery[0]." WHERE vtiger_service.discontinued = 1 AND ".$Servicesquery[1];
		}

		$moduleModel = Vtiger_Module_Model::getInstance($module);
		$fieldModels = $moduleModel->getFields();
		
		if($isRoute && $relatedModule != '' && $relatedFieldName != ''){
			$tablename =  $fieldModels[$relatedFieldName]->get('table');
			$column =  $fieldModels[$relatedFieldName]->get('column');
			$query .= " AND ".$tablename.".".$column." != ''";
		}

		if($field_name && $field_value){
			$field_names = Zend_JSON::decode($field_name);
			$field_values = Zend_JSON::decode($field_value);
			//print_r($field_values);
			foreach ($field_names as $key => $field_name) {
				$field_value = addslashes(trim($field_values[$key]));
				$tablename = $columnByFieldNames[$field_name]['table'];
				if($field_name){
					$uitype = $fieldModels[$field_name]->get('uitype');
					$typeofdata = explode('~',$fieldModels[$field_name]->get('typeofdata'));
				}
				$refrenceUitypes = array(10,51,57,58,59,66,73,75,76,77,78,80,81,101);
				if(in_array($uitype,$refrenceUitypes)){
					$tablename =  $fieldModels[$field_name]->get('table');
					$column =  $fieldModels[$field_name]->get('column');
					$fieldValue = explode('x',$field_value);
					$query .= " AND ".$tablename.".".$column." = '".$fieldValue[1]."'";
				}else if($field_name == 'assigned_user_id'){
					$tablename =  $fieldModels[$field_name]->get('table');
					$column =  $fieldModels[$field_name]->get('column');
					$fieldValue = explode('x',$field_value);
					$query .= " AND ".$tablename.".".$column." = '".$fieldValue[1]."'";
				}else if($uitype == 33){
					$fieldvalues = explode(',', $field_value);
					$tablename =  $fieldModels[$field_name]->get('table');
					$column =  $fieldModels[$field_name]->get('column');
					$query.= " AND (( ";
					foreach ($fieldvalues as $key => $fieldValue) {
						if($key+1 == count($fieldvalues)){
							$query.= " ".$tablename.".".$column." LIKE '%".$fieldValue."%' ";
						}else{
							$query.= " ".$tablename.".".$column." LIKE '%".$fieldValue."%' OR ";
						}
					}
					$query.= " )) ";
				}else if(in_array($uitype, array(5,6,23,70))){
					$tablename =  $fieldModels[$field_name]->get('table');
					$column =  $fieldModels[$field_name]->get('column');
					$fieldValue = explode(",",$field_value);
					if($uitype == 70){
						$date1 = Vtiger_Datetime_UIType::getDBDateTimeValue($fieldValue[0].' 00:00:01');
						$date2 = Vtiger_Datetime_UIType::getDBDateTimeValue($fieldValue[1].' 23:59:00');
						$query.= " AND ".$tablename.".".$column." BETWEEN '".$date1."' AND '".$date2."'";
					}else{
						$date1 = $nowInDBFormat = Vtiger_Date_UIType::getDBInsertedValue($fieldValue[0]);
						$date2 = $nowInDBFormat = Vtiger_Date_UIType::getDBInsertedValue($fieldValue[1]);
						$query.= " AND DATE(".$tablename.".".$column.") BETWEEN '".$date1."' AND '".$date2."'";
					}
					
				}else if($typeofdata[0] == 'T'){
					$tablename =  $fieldModels[$field_name]->get('table');
					$column =  $fieldModels[$field_name]->get('column');
					if($module == 'Calendar' || $module == 'Events'){
						$date = Vtiger_Date_UIType::getDisplayDateValue(date('Y-m-d'));
						$nowInUserFormat = $date.' '.$field_value;
						$nowInDBFormat = Vtiger_Datetime_UIType::getDBDateTimeValue($nowInUserFormat);
						$dateTime = explode(' ',$nowInDBFormat);
						$query.= " AND ".$tablename.".".$column." = '".$dateTime[1]."'";
					}else{
						$dateTime =  Vtiger_Time_UIType::getTimeValueWithSeconds($field_value);
						$query.= " AND ".$tablename.".".$column." = '".$dateTime."'";
					}
				}else if(in_array($uitype, array(71,72))){
					$tablename =  $fieldModels[$field_name]->get('table');
					$column =  $fieldModels[$field_name]->get('column');
					$query .= " AND ".$tablename.".".$column." = '".$field_value."'";
				}else if($uitype == 117){
					$tablename =  $fieldModels[$field_name]->get('table');
					$column =  $fieldModels[$field_name]->get('column');
					$fieldValuee = explode('x', $field_value);
					$query .= " AND ".$tablename.".".$column." = '".$fieldValuee[1]."'";
				}else if($field_name == 'taskstatus' || $field_name == 'taskstatus'){
					$query .= " AND ((vtiger_activity.status = '$field_value' ) OR (vtiger_activity.eventstatus = '$field_value' )) ";
				}else{
					$tablename =  $fieldModels[$field_name]->get('table');
					$column =  $fieldModels[$field_name]->get('column');
					if($field_name2 != '' && $field_value2 != ''){
						$query .= " AND ".$tablename.".".$column." = '".$field_value."'";
					}else{
						if($searchType != '' && $searchType == 'barcode'){
							$query .= " AND ".$tablename.".".$column." = '".$field_value."'";
						}else{
							$query .= " AND ".$tablename.".".$column." LIKE '%".$field_value."%'";
						}
					}
				}
			}
		}
		
		if($field_name2 != '' && $field_value2 != ''){
			$tablename = $columnByFieldNames[$field_name2]['table'];
			if($field_name2){
				$uitype = $fieldModels[$field_name2]->get('uitype');
			}
			$refrenceUitypes = array(10,51,57,58,59,66,73,75,76,77,78,80,81,101);
			if(in_array($uitype,$refrenceUitypes)){
				$tablename =  $fieldModels[$field_name2]->get('table');
				$column =  $fieldModels[$field_name2]->get('column');
				$query .= " AND ".$tablename.".".$column." IN (".$field_value2.")";
			}else if($field_name2 == 'assigned_user_id'){
				$field_value2 = explode('x', $field_value2);
				$tablename =  $fieldModels[$field_name2]->get('table');
				$column =  $fieldModels[$field_name2]->get('column');
				$query .= " AND ".$tablename.".".$column." IN (".$field_value2[1].")";
			}else{
				$tablename =  $fieldModels[$field_name2]->get('table');
				$column =  $fieldModels[$field_name2]->get('column');
				$query .= " AND ".$tablename.".".$column." = '".$field_value2."'";
				
			}
			
		}
		if(!empty($selectedRecords)){
			$basetable = $moduleModel->get('basetable');
			$basetableid = $moduleModel->get('basetableid');
			$query .= " AND ".$basetable.".".$basetableid." NOT IN (".implode(',',$selectedRecords).")";
		}
		
		if($pagingModel !== false) {
			$index = $paging['index'];
			$size = $paging['size'];
			$limit = ($index*$size) - $size;
			if($index != '' && $size != '') {
				if($order_by){
					if($module == 'Calendar'){
						$query.= ' GROUP BY vtiger_activity.activityid ';
					}
					$moduleModel = Vtiger_Module_Model::getInstance($module);
					$fieldModels = $moduleModel->getFields();
					$tablename =  $fieldModels[$order_by]->get('table');
					$column =  $fieldModels[$order_by]->get('column');
				   if($orderby){
					   	if($order_by == 'date_start') {
				            $query .= " ORDER BY str_to_date(concat(date_start,time_start),'%Y-%m-%d %H:%i:%s') $orderby ";
				        } else if($order_by == 'due_date') {
				            $query .= " ORDER BY str_to_date(concat(due_date,time_end),'%Y-%m-%d %H:%i:%s') $orderby ";
				        } else {
							$query .= " ORDER BY $tablename.$column $orderby ";
						}
				   }else{
						$query .= " ORDER BY $tablename.$column ASC ";
				   }	
				}else{
				   $query .= " ORDER BY vtiger_crmentity.modifiedtime DESC";
				}
				$this->totalQuery = $query;
				$this->totalParams = $filterOrAlertInstance->queryParameters();
				$query .= sprintf(" LIMIT %s, %s", $limit, $size);
			}
		}else{
			$this->totalQuery = $query;
			$this->totalParams = $filterOrAlertInstance->queryParameters();
		}
		$prequeryResult = $adb->pquery($query, $filterOrAlertInstance->queryParameters());
		return new SqlResultIterator($adb, $prequeryResult);
	}

	function getLatLongOfRecord($recordid){
		global $adb;
		$data['lat'] = "";
		$data['long'] = "";
		if($recordid){
			$result  = $adb->pquery("SELECT * FROM `ct_address_lat_long` WHERE recordid = ? ",array($recordid));
			if($adb->num_rows($result) > 0){
				$data['lat'] = $adb->query_result($result,0,'latitude');
				$data['long'] = $adb->query_result($result,0,'longitude');
			}

		}

		return $data;
	}

	function getLatLongFromRelatedRecord($recordid,$module){
		global $adb;
		$data['lat'] = "";
		$data['long'] = "";
		if($recordid){
			$checkDeleted = $adb->pquery("SELECT * FROM vtiger_crmentity WHERE crmid = ? AND deleted = 0",array($recordid));
			if($adb->num_rows($checkDeleted) > 0 ){
				$recordModel = Vtiger_Record_Model::getInstanceById($recordid);
				if($module == 'HelpDesk'){
					$record1 = $recordModel->get('parent_id');
					$record2 = $recordModel->get('contact_id');
				}else if($module == 'PurchaseOrder'){
					$record1 = $recordModel->get('contact_id');
				}else{
					$record1 = $recordModel->get('account_id');
					$record2 = $recordModel->get('contact_id');
				}

				if($record1 != ""){
					$result  = $adb->pquery("SELECT * FROM `ct_address_lat_long` WHERE recordid = ? ",array($record1));
					if($adb->num_rows($result) > 0){
						$data['lat'] = $adb->query_result($result,0,'latitude');
						$data['long'] = $adb->query_result($result,0,'longitude');
					}

				}
				if($record2 != "" && $data['lat'] == "" && $data['long'] == ""){
					$result  = $adb->pquery("SELECT * FROM `ct_address_lat_long` WHERE recordid = ? ",array($record2));
					if($adb->num_rows($result) > 0){
						$data['lat'] = $adb->query_result($result,0,'latitude');
						$data['long'] = $adb->query_result($result,0,'longitude');
					}
				}
			}
		}

		return $data;
	}

	function getRelatedRecord($sourceModule,$relatedModule,$recordid){
		global $adb;
		$parentModuleModel = Vtiger_Module_Model::getInstance($sourceModule);
		$record = explode('x', $recordid); 
		$parentRecordModel = Vtiger_Record_Model::getInstanceById($record[1], $sourceModule);
		$relationModels = $parentModuleModel->getRelations();
		foreach($relationModels as $relation) {
			if($relatedModule == $relation->get('relatedModuleName')){
				$relation_label = $relation->get('label');
			}
		}

		$relationListView = Vtiger_RelationListView_Model::getInstance($parentRecordModel, $relatedModule, $relation_label);
		$relationModel = $relationListView->getRelationModel();
		$deleteAction = $relationModel->isDeletable();
		$query = $relationListView->getRelationQuery();

		$getfunctionres = $adb->pquery($query,array());
		$numofrows2 = $adb->num_rows($getfunctionres);
		$relatedRecords = array();
		for ($i=0; $i < $numofrows2; $i++) {
			$relatedRecords[] = $adb->query_result($getfunctionres,$i,'crmid');
			//$relatedRecords[] = vtws_getWebserviceEntityId($relatedModule, $adb->query_result($getfunctionres,$i,'crmid'));
		}
		return $relatedRecords;
	}
	
}
