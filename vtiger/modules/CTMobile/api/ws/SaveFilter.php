<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

class CTMobile_WS_SaveFilter extends CTMobile_WS_Controller {
	protected $recordValues = false;
	function process(CTMobile_API_Request $request) {
		global $adb,$current_user;
		$current_user = $this->getActiveUser();

		$sourceModuleName = $request->get('module');
        $moduleModel = Vtiger_Module_Model::getInstance($sourceModuleName);
		$cvId = $request->get('record');

		if(!empty($cvId)) {
			$customViewModel = CustomView_Record_Model::getInstanceById($cvId);
		} else {
			$customViewModel = CustomView_Record_Model::getCleanInstance();
			$customViewModel->setModule($request->get('module'));
		}

		$customViewData = array(
					'cvid' => $cvId,
					'viewname' => $request->get('viewname'),
					'setdefault' => $request->get('setdefault'),
					'setmetrics' => $request->get('setmetrics'),
					'status' => $request->get('status')
		);
		$selectedColumnsList = $request->get('columnslist');
		if(!empty($selectedColumnsList)) {

			$moduleModel = Vtiger_Module_Model::getInstance($sourceModuleName);
	        $recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($moduleModel);
	        $recordStructure = $recordStructureInstance->getStructure();
	        // for Inventory module we should now allow item details block
	        if(in_array($sourceModuleName, getInventoryModules())){
	            $itemsBlock = "LBL_ITEM_DETAILS";
	            unset($recordStructure[$itemsBlock]);
	        }
	        if(!is_array($selectedColumnsList)){
	        	$selectedColumnsList = Zend_Json::decode($selectedColumnsList);
	        }
	        $newSelectedField = array();
	        foreach ($selectedColumnsList as $key => $fieldname) {
		        foreach ($recordStructure as $blockname => $blockfield) {
		        	if(array_key_exists($fieldname, $blockfield)){
		        		$newSelectedField[] = $blockfield[$fieldname]->getCustomViewColumnName();
		        	}
		        }
	        }
			$customViewData['columnslist'] = $newSelectedField;
		}
		$stdFilterList = $request->get('stdfilterlist');
		if(!empty($stdFilterList)) {
			$customViewData['stdfilterlist'] = $stdFilterList;
		}
		$advFilterList = $request->get('advfilterlist');
		$conditions = array();
        $andcond = array();
        $orcond = array();
		if(!empty($advFilterList)) {
			$moduleModel = Vtiger_Module_Model::getInstance($sourceModuleName);
			$fieldModels = $moduleModel->getFields();
	        $recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($moduleModel);
	        $recordStructure = $recordStructureInstance->getStructure();
	        // for Inventory module we should now allow item details block
	        if(in_array($sourceModuleName, getInventoryModules())){
	            $itemsBlock = "LBL_ITEM_DETAILS";
	            unset($recordStructure[$itemsBlock]);
	        }
	        
	        $advFilterList = Zend_Json::decode($advFilterList);
	        $newSelectedField = array();
	        foreach ($advFilterList as $key => $advFilter) {
		        foreach ($recordStructure as $blockname => $blockfield) {
		        	if(array_key_exists($advFilter['fieldname'], $blockfield)){
		        		if($advFilter['fieldname'] == 'assigned_user_id'){
		        			$values = explode(',', $advFilter['value']);
		        			$assigned_user_id = array();
		        			foreach ($values as $keys => $value) {
		        				$temp_val =  explode('x',$value);
		        				$userRecordModel =  Users_Record_Model::getInstanceById($temp_val[1],'Users');
		        				$assigned_user_id[] = $userRecordModel->get('first_name').' '.$userRecordModel->get('last_name');
		        			}
		        			$advFilter['value'] =  implode(',', $assigned_user_id);
		        		}
		        		$fieldModel = $fieldModels[$advFilter['fieldname']];
		        		$fieldtype = $fieldModel->getFieldType();
		        		if($fieldtype == 'T' && (!(in_array($sourceModuleName, array('Events','Calendar')) && in_array($advFilter['fieldname'], array('time_start','time_end'))))){
		        			if($current_user->date_format == 'dd-mm-yyyy'){
								$format = 'd-m-Y';
							}else if($current_user->date_format == 'mm-dd-yyyy'){
								$format = 'm-d-Y';
							}else{
								$format = 'Y-m-d';
							}
							$date_start = date($format);
		        			$startDateTime = Vtiger_Datetime_UIType::getDBDateTimeValue($date_start." ".$advFilter['value']);
							list($startDate, $startTime) = explode(' ', $startDateTime);
							$advFilter['value'] = $startTime;
		        		}
		        		if($fieldModel->get('uitype') == 56){
		        			$advFilter['value'] = $advFilter['comparator'];
		        			$advFilter['comparator'] = 'e';
		        		}
		        		$columnname = $blockfield[$advFilter['fieldname']]->getCustomViewColumnName();
		        		if($advFilter['column_condition'] == 'and'){
		        			$newSelectedField["1"]["columns"]["$key"] = array('columnname'=>$columnname,'comparator'=>$advFilter['comparator'],'value'=>$advFilter['value'],'column_condition'=>$advFilter['column_condition']);
		        			$conditions[] = array('columnname'=>$columnname,'comparator'=>$advFilter['comparator'],'value'=>$advFilter['value'],'column_condition'=>$advFilter['column_condition'],'groupid'=>'1');
		        			$andcond[] = $key;
		        		}else{
		        			$newSelectedField["2"]["columns"]["$key"] = array('columnname'=>$columnname,'comparator'=>$advFilter['comparator'],'value'=>$advFilter['value'],'column_condition'=>$advFilter['column_condition']);
		        			$conditions[] = array('columnname'=>$columnname,'comparator'=>$advFilter['comparator'],'value'=>$advFilter['value'],'column_condition'=>$advFilter['column_condition'],'groupid'=>'2');
		        			$orcond[] = $key;
		        		}
		        	}
		        }
	        }
		}
        if($request->has('sharelist')) {
            $customViewData['sharelist'] = $request->get('sharelist');
            if($customViewData['sharelist'] == '1')
                $customViewData['members'] = $request->get('members');
        }
		$customViewModel->setData($customViewData);
		$response = new CTMobile_API_Response();
		if (!$customViewModel->checkDuplicate()) {
			$customViewModel->save();
			$cvId = $customViewModel->getId();
			if(!empty($advFilterList)) {
				foreach ($conditions as $key => $condition) {
					if($condition['column_condition'] == 'and'){
						if($key+1 == count($andcond)){
							$condition['column_condition'] = "";
						}
					}else if($condition['column_condition'] == 'or'){
						if($key+1 == count($orcond)){
							$condition['column_condition'] = "";
						}
					}
					$adb->pquery("INSERT INTO `vtiger_cvadvfilter`(cvid,columnindex,columnname,comparator,value,groupid,column_condition) VALUES(?,?,?,?,?,?,?)",array($cvId,$key,$condition['columnname'],$condition['comparator'],$condition['value'],$condition['groupid'],$condition['column_condition']));
				}
			}
			if(!empty($andcond)){
				$condition_expression = implode('and', $andcond);
				$group_condition = "";
				if(count($andcond) > 1){
					$group_condition =  "and";
				}else if(count($andcond) == 1 && !empty($orcond)){
					$group_condition =  "and";
				}
				$adb->pquery("INSERT INTO `vtiger_cvadvfilter_grouping`(groupid,cvid,group_condition,condition_expression) VALUES(?,?,?,?)",array('1',$cvId,$group_condition,$condition_expression));
			}
			if(!empty($orcond)){
				$condition_expression = implode('or', $orcond);
				$group_condition = "";
				if(count($orcond) > 1){
					$group_condition =  "or";
				}
				$adb->pquery("INSERT INTO `vtiger_cvadvfilter_grouping`(groupid,cvid,group_condition,condition_expression) VALUES(?,?,?,?)",array('2',$cvId,$group_condition,$condition_expression));
			}
			$message = $this->CTTranslate('Filters save successfully');
			$result = array('cvId'=>$cvId,'message'=>$message);
			$response->setResult($result);
		}else{
			$response->setError('',vtranslate('LBL_CUSTOM_VIEW_NAME_DUPLICATES_EXIST', $moduleName));
		}
		return $response;
	}
}