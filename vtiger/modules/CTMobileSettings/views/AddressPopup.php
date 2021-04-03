<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class CTMobileSettings_AddressPopup_View extends Vtiger_Popup_View {
	protected $listViewEntries = false;
	protected $listViewHeaders = false;
	protected $listQuery = "";
	protected $listViewCount = 0;
	/**
	 * Function returns the module name for which the popup should be initialized
	 * @param Vtiger_request $request
	 * @return <String>
	 */
	function getModule(Vtiger_request $request) {
		$moduleName = $request->getModule();
		return $moduleName;
	}

	function process (Vtiger_Request $request) {
		$viewer = $this->getViewer ($request);
		$moduleName = $this->getModule($request);
		$companyDetails = Vtiger_CompanyDetails_Model::getInstanceById();
		$companyLogo = $companyDetails->getLogo();

		$this->initializeListViewContents($request, $viewer);

		$viewer->assign('COMPANY_LOGO',$companyLogo);

		$viewer->view('Popup.tpl', $moduleName);
	}

	function postProcess(Vtiger_Request $request) {
		$viewer = $this->getViewer ($request);
		$moduleName = $this->getModule($request);
		$viewer->view('PopupFooter.tpl', $moduleName);
	}

	/**
	 * Function to get the list of Script models to be included
	 * @param Vtiger_Request $request
	 * @return <Array> - List of Vtiger_JsScript_Model instances
	 */
	function getHeaderScripts(Vtiger_Request $request) {
		$headerScriptInstances = parent::getHeaderScripts($request);
		$moduleName = $request->getModule();

		$jsFileNames = array(
						'libraries.bootstrap.js.eternicode-bootstrap-datepicker.js.bootstrap-datepicker',
			'~libraries/bootstrap/js/eternicode-bootstrap-datepicker/js/locales/bootstrap-datepicker.'.Vtiger_Language_Handler::getShortLanguageName().'.js',
			'~libraries/jquery/timepicker/jquery.timepicker.min.js',

			'modules.Vtiger.resources.Popup',
			"modules.$moduleName.resources.Popup",
			'modules.Vtiger.resources.BaseList',
			"modules.$moduleName.resources.BaseList",
			'libraries.jquery.jquery_windowmsg',
			'modules.Vtiger.resources.validator.BaseValidator',
			'modules.Vtiger.resources.validator.FieldValidator',
			"modules.$moduleName.resources.validator.FieldValidator"
		);

		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
	}

	/*
	 * Function to initialize the required data in smarty to display the List View Contents
	 */
	public function initializeListViewContents(Vtiger_Request $request, Vtiger_Viewer $viewer) {
		$moduleName = $request->get('src_module');
		$cvId = $request->get('cvid');
		$pageNumber = $request->get('page');
		$orderBy = $request->get('orderby');
		$sortOrder = $request->get('sortorder');
		$sourceModule = $request->get('src_module');
		$sourceField = $request->get('src_field');
		$sourceRecord = $request->get('src_record');
		$searchKey = $request->get('search_key');
		$searchValue = $request->get('search_value');
		$currencyId = $request->get('currency_id');
		$relatedParentModule = $request->get('related_parent_module');
		$relatedParentId = $request->get('related_parent_id');
				$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
				$searchParams=$request->get('search_params');

		$relationId = $request->get('relationId'); 

		//To handle special operation when selecting record from Popup
		$getUrl = $request->get('get_url');
		$autoFillModule = $moduleModel->getAutoFillModule($moduleName);

		//Check whether the request is in multi select mode
		$multiSelectMode = $request->get('multi_select');
		if(empty($multiSelectMode)) {
			$multiSelectMode = false;
		}

		if(empty($getUrl) && !empty($sourceField) && !empty($autoFillModule) && !$multiSelectMode) {
			$getUrl = 'getParentPopupContentsUrl';
		}

		if(empty($cvId)) {
			$cvId = '0';
		}
		if(empty ($pageNumber)){
			$pageNumber = '1';
		}

		$pagingModel = new Vtiger_Paging_Model();
		$pagingModel->set('page', $pageNumber);

		$recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($moduleModel);

		$isRecordExists = Vtiger_Util_Helper::checkRecordExistance($relatedParentId);

		if($isRecordExists) {
			$relatedParentModule = '';
			$relatedParentId = '';
		} else if($isRecordExists === NULL) {
			$relatedParentModule = '';
			$relatedParentId = '';
		}

		if(!empty($relatedParentModule) && !empty($relatedParentId)) {
			$parentRecordModel = Vtiger_Record_Model::getInstanceById($relatedParentId, $relatedParentModule);
			$listViewModel = Vtiger_RelationListView_Model::getInstance($parentRecordModel, $moduleName, $label,$relationId);
			$searchModuleModel = $listViewModel->getRelatedModuleModel();
		}else{
			$listViewModel = Vtiger_ListView_Model::getInstance($moduleName,$cvId);
			$searchModuleModel = $listViewModel->getModule();
		}

		if($moduleName == 'Documents' && $sourceModule == 'Emails') {
			$listViewModel->extendPopupFields(array('filename'=>'filename'));
		}
		if(!empty($orderBy)) {
			$listViewModel->set('orderby', $orderBy);
			$listViewModel->set('sortorder', $sortOrder);
		}
		if(!empty($sourceModule)) {
			$listViewModel->set('src_module', $sourceModule);
			$listViewModel->set('src_field', $sourceField);
			$listViewModel->set('src_record', $sourceRecord);
		}
		if((!empty($searchKey)) && (!empty($searchValue)))  {
			$listViewModel->set('search_key', $searchKey);
			$listViewModel->set('search_value', $searchValue);
		}
		$listViewModel->set('relationId',$relationId);

				if(!empty($searchParams)){
					$transformedSearchParams = $this->transferListSearchParamsToFilterCondition($searchParams, $searchModuleModel);
					$listViewModel->set('search_params',$transformedSearchParams);
				}
		if(!empty($relatedParentModule) && !empty($relatedParentId)) {
			$this->listViewHeaders = $listViewModel->getHeaders();

			$models = $listViewModel->getEntries($pagingModel);
			$noOfEntries = count($models);
			foreach ($models as $recordId => $recordModel) {
				foreach ($this->listViewHeaders as $fieldName => $fieldModel) {
					$recordModel->set($fieldName, $recordModel->getDisplayValue($fieldName));
				}
				$models[$recordId] = $recordModel;
			}
			$this->listViewEntries = $models;
			if(count($this->listViewEntries) > 0 ){
				$parent_related_records = true;
			}
		}else{
			$this->listViewHeaders = $listViewModel->getListViewHeaders();
			$this->listViewEntries = $this->getListViewEntries($pagingModel,$listViewModel);
			//$this->listViewEntries = $listViewModel->getListViewEntries($pagingModel);
		}

		// If there are no related records with parent module then, we should show all the records
		if(!$parent_related_records && !empty($relatedParentModule) && !empty($relatedParentId)){
			$relatedParentModule = null;
			$relatedParentId = null;
			$listViewModel = Vtiger_ListView_Model::getInstanceForPopup($moduleName);

			if(!empty($orderBy)) {
				$listViewModel->set('orderby', $orderBy);
				$listViewModel->set('sortorder', $sortOrder);
			}
			if(!empty($sourceModule)) {
				$listViewModel->set('src_module', $sourceModule);
				$listViewModel->set('src_field', $sourceField);
				$listViewModel->set('src_record', $sourceRecord);
			}
			if((!empty($searchKey)) && (!empty($searchValue)))  {
				$listViewModel->set('search_key', $searchKey);
				$listViewModel->set('search_value', $searchValue);
			}

			if(!empty($searchParams)) {
				$transformedSearchParams = $this->transferListSearchParamsToFilterCondition($searchParams, $searchModuleModel);
				$listViewModel->set('search_params',$transformedSearchParams);
			}
			$this->listViewHeaders = $listViewModel->getListViewHeaders();
			$this->listViewEntries = $listViewModel->getListViewEntries($pagingModel);
		}
		// End  
				if(empty($searchParams)) {
					$searchParams = array();
				}
			   //To make smarty to get the details easily accesible
				foreach($searchParams as $fieldListGroup){
					foreach($fieldListGroup as $fieldSearchInfo){
						$fieldSearchInfo['searchValue'] = $fieldSearchInfo[2];
						$fieldSearchInfo['fieldName'] = $fieldName = $fieldSearchInfo[0];
						$fieldSearchInfo['comparator'] = $fieldSearchInfo[1];
						$searchParams[$fieldName] = $fieldSearchInfo;
					}
		}

		$noOfEntries = count($this->listViewEntries);

		if(empty($sortOrder)){
			$sortOrder = "ASC";
		}
		if($sortOrder == "ASC"){
			$nextSortOrder = "DESC";
			$sortImage = "icon-chevron-down";
			$faSortImage = "fa-sort-desc";
		}else{
			$nextSortOrder = "ASC";
			$sortImage = "icon-chevron-up";
			$faSortImage = "fa-sort-asc";
		}

		$viewer->assign('MODULE', $this->getModule($request));
				$viewer->assign('RELATED_MODULE', $moduleName);
		$viewer->assign('MODULE_NAME',$this->getModule($request));

		$viewer->assign('SOURCE_MODULE', $sourceModule);
		$viewer->assign('SOURCE_FIELD', $sourceField);
		$viewer->assign('SOURCE_RECORD', $sourceRecord);
		$viewer->assign('RELATED_PARENT_MODULE', $relatedParentModule);
		$viewer->assign('RELATED_PARENT_ID', $relatedParentId);

		$viewer->assign('SEARCH_KEY', $searchKey);
		$viewer->assign('SEARCH_VALUE', $searchValue);

		$viewer->assign('RELATION_ID',$relationId);
		$viewer->assign('ORDER_BY',$orderBy);
		$viewer->assign('SORT_ORDER',$sortOrder);
		$viewer->assign('NEXT_SORT_ORDER',$nextSortOrder);
		$viewer->assign('SORT_IMAGE',$sortImage);
		$viewer->assign('FASORT_IMAGE',$faSortImage);
		$viewer->assign('GETURL', $getUrl);
		$viewer->assign('CURRENCY_ID', $currencyId);

		$viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
		$viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());

		$viewer->assign('PAGING_MODEL', $pagingModel);
		$viewer->assign('PAGE_NUMBER',$pageNumber);

		$viewer->assign('LISTVIEW_ENTRIES_COUNT',$noOfEntries);
		$viewer->assign('LISTVIEW_HEADERS', $this->listViewHeaders);
		$viewer->assign('LISTVIEW_ENTRIES', $this->listViewEntries);
		$viewer->assign('SEARCH_DETAILS', $searchParams);
		$viewer->assign('MODULE_MODEL', $moduleModel);
		$viewer->assign('VIEW', 'AddressPopup');

		$totalCount = $this->listViewCount;
		$pageLimit = $pagingModel->getPageLimit();
		$pageCount = ceil((int) $totalCount / (int) $pageLimit);

		if($pageCount == 0){
			$pageCount = 1;
		}
		$viewer->assign('PAGE_COUNT', $pageCount);
		$viewer->assign('LISTVIEW_COUNT', $totalCount);
		

		$viewer->assign('MULTI_SELECT', $multiSelectMode);
		$viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
	}


	public function transferListSearchParamsToFilterCondition($listSearchParams, $moduleModel) {
		return Vtiger_Util_Helper::transferListSearchParamsToFilterCondition($listSearchParams, $moduleModel);
	}

	public function getListViewEntries($pagingModel,$listViewModel) {
		$db = PearDatabase::getInstance();

		$moduleName = $listViewModel->getModule()->get('name');
		$moduleFocus = CRMEntity::getInstance($moduleName);
		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);

		$queryGenerator = $listViewModel->get('query_generator');
		$listViewContoller = $listViewModel->get('listview_controller');

		 $searchParams = $listViewModel->get('search_params');
		if(empty($searchParams)) {
			$searchParams = array();
		}
		$glue = "";
		if(count($queryGenerator->getWhereFields()) > 0 && (count($searchParams)) > 0) {
			$glue = QueryGenerator::$AND;
		}
		$queryGenerator->parseAdvFilterList($searchParams, $glue);

		$searchKey = $listViewModel->get('search_key');
		$searchValue = $listViewModel->get('search_value');
		$operator = $listViewModel->get('operator');
		if(!empty($searchKey)) {
			$queryGenerator->addUserSearchConditions(array('search_field' => $searchKey, 'search_text' => $searchValue, 'operator' => $operator));
		}

		$orderBy = $listViewModel->get('orderby');
		$sortOrder = $listViewModel->get('sortorder');

		if(!empty($orderBy)){
			$queryGenerator = $listViewModel->get('query_generator');
			$fieldModels = $queryGenerator->getModuleFields();
			$orderByFieldModel = $fieldModels[$orderBy];
			if($orderByFieldModel && ($orderByFieldModel->getFieldDataType() == Vtiger_Field_Model::REFERENCE_TYPE ||
					$orderByFieldModel->getFieldDataType() == Vtiger_Field_Model::OWNER_TYPE)){
				$queryGenerator->addWhereField($orderBy);
			}
		}
		$listQuery = $listViewModel->getQuery();

		$basetable = $moduleModel->get('basetable');
        $basetableid = $moduleModel->get('basetableid');

		$result2 = $db->pquery("SELECT id FROM vtiger_ws_entity WHERE name=?", array($moduleName));
        $moduleId = $db->query_result($result2, 0, 'id');

	    $listQuery  = $listQuery." AND $basetable.$basetableid IN (SELECT recordid FROM ct_address_lat_long WHERE moduleid = $moduleId ) ";

	    $this->$listQuery = $listQuery;
		$sourceModule = $listViewModel->get('src_module');
		if(!empty($sourceModule)) {
			if(method_exists($moduleModel, 'getQueryByModuleField')) {
				$overrideQuery = $moduleModel->getQueryByModuleField($sourceModule, $listViewModel->get('src_field'), $listViewModel->get('src_record'), $listQuery,$listViewModel->get('relationId'));
				if(!empty($overrideQuery)) {
					$listQuery = $overrideQuery;
				}
			}
		}

		$startIndex = $pagingModel->getStartIndex();
		$pageLimit = $pagingModel->getPageLimit();

		if(!empty($orderBy) && $orderByFieldModel) {
			if($orderBy == 'roleid' && $moduleName == 'Users'){
				$listQuery .= ' ORDER BY vtiger_role.rolename '.' '. $sortOrder; 
			} else {
				$listQuery .= ' ORDER BY '.$queryGenerator->getOrderByColumn($orderBy).' '.$sortOrder;
			}

			if ($orderBy == 'first_name' && $moduleName == 'Users') {
				$listQuery .= ' , last_name '.' '. $sortOrder .' ,  email1 '. ' '. $sortOrder;
			} 
		} else if(empty($orderBy) && empty($sortOrder) && $moduleName != "Users"){
			//List view will be displayed on recently created/modified records
			$listQuery .= ' ORDER BY vtiger_crmentity.modifiedtime DESC';
		}

		$viewid = ListViewSession::getCurrentView($moduleName);
		if(empty($viewid)) {
			$viewid = $pagingModel->get('viewid');
		}
		$_SESSION['lvs'][$moduleName][$viewid]['start'] = $pagingModel->get('page');

		ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);

		$totalListResult = $db->pquery($listQuery, array());
		$this->listViewCount = $db->num_rows($totalListResult);

		$listQuery .= " LIMIT $startIndex,".($pageLimit+1);
		$listResult = $db->pquery($listQuery, array());

		$listViewRecordModels = array();
		$listViewEntries =  $listViewContoller->getListViewRecords($moduleFocus,$moduleName, $listResult);

		$pagingModel->calculatePageRange($listViewEntries);

		if($db->num_rows($listResult) > $pageLimit){
			array_pop($listViewEntries);
			$pagingModel->set('nextPageExists', true);
		}else{
			$pagingModel->set('nextPageExists', false);
		}

		$index = 0;
		foreach($listViewEntries as $recordId => $record) {
			$rawData = $db->query_result_rowdata($listResult, $index++);
			$record['id'] = $recordId;
			$listViewRecordModels[$recordId] = $moduleModel->getRecordFromArray($record, $rawData);
		}
		return $listViewRecordModels;
	}

}