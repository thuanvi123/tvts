<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class CTRoutePlanning_Detail_View extends Vtiger_Detail_View {

	function checkPermission(Vtiger_Request $request) {
		$moduleName = $request->getModule();
		$recordId = $request->get('record');

		$recordPermission = Users_Privileges_Model::isPermitted($moduleName, 'DetailView', $recordId);
		if(!$recordPermission) {
			throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
		}
		return true;
	}

	/**
	 * Function shows the entire detail for the record
	 * @param Vtiger_Request $request
	 * @return <type>
	 */
	function showModuleDetailView(Vtiger_Request $request) {
		$recordId = $request->get('record');
		$moduleName = $request->getModule();

		$detailViewModel = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
		$recordModel = $detailViewModel->getRecord();
		$recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
		$structuredValues = $recordStrucure->getStructure();
		$moduleModel = $recordModel->getModule();

		
		$RELATED_TO = $recordModel->getRelatedCTRouteInfo();
		foreach($RELATED_TO as $index=>$contactInfo) {
			$recordsModel = Vtiger_Record_Model::getInstanceById($contactInfo['id']);
			$CTRouteModule = $recordsModel->getModuleName();
			$contactRecordModel = Vtiger_Record_Model::getCleanInstance($CTRouteModule);
			$contactRecordModel->setId($contactInfo['id']);
			$contactInfo['_model'] = $contactRecordModel;
			$RELATED_TO[$index] = $contactInfo;
		}

		$viewer = $this->getViewer($request);
		$viewer->assign('RECORD', $recordModel);
		$viewer->assign('RECORD_STRUCTURE', $structuredValues);
		$viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
		$viewer->assign('RECORD_STRUCTURE_MODEL', $recordStrucure);
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
		$viewer->assign('MODULE_NAME', $moduleName);
		$viewer->assign('RELATED_TO', $RELATED_TO);
		$viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));

		$picklistDependencyDatasource = Vtiger_DependencyPicklist::getPicklistDependencyDatasource($moduleName);
		$viewer->assign('PICKIST_DEPENDENCY_DATASOURCE', Vtiger_Functions::jsonEncode($picklistDependencyDatasource));

		if ($request->get('displayMode') == 'overlay') {
			$viewer->assign('MODULE_MODEL', $moduleModel);
			$this->setModuleInfo($request, $moduleModel);
			$viewer->assign('MODULE', $request->getModule());
			return $viewer->view('OverlayDetailView.tpl', $moduleName);
		} else {
			return $viewer->view('DetailViewFullContents.tpl', $moduleName, true);
		}
	}

	/**
	 * Function shows basic detail for the record
	 * @param <type> $request
	 */
	function showModuleBasicView($request) {
		return $this->showModuleDetailView($request);
	}

	/**
	 * Function to get Ajax is enabled or not
	 * @param Vtiger_Record_Model record model
	 * @return <boolean> true/false
	 */
	function isAjaxEnabled($recordModel) {
		return false;
	}
}
