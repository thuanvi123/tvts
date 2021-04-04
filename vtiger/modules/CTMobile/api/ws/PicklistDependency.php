<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

class CTMobile_WS_PicklistDependency extends CTMobile_WS_Controller {

	function process(CTMobile_API_Request $request) {
		global $current_user, $adb, $site_URL; // Few core API assumes this variable availability
		
		$current_user = $this->getActiveUser();
		$module = trim($request->get('module'));
		$field_name = trim($request->get('field_name'));
		$field_value = trim($request->get('field_value'));
		$target_field = trim($request->get('targetfield'));
		$record = trim($request->get('record'));
		$dependecyData =  array();
		if($module && $field_name && $field_value){
			$data = Vtiger_DependencyPicklist::getDependentPicklistFields($module);
			$targetfield = "";
			if(count($data) > 0){
				foreach($data as $key => $values){
					if($values['sourcefield'] == $field_name){
						$PickListDependency = Vtiger_DependencyPicklist::getPickListDependency($module,$values['sourcefield'],$values['targetfield']);
						$valuemapping = $PickListDependency['valuemapping'];
						foreach($valuemapping as $keys => $depValues){
							if($depValues['sourcevalue'] == $field_value){
								$picklistValues = array();
								foreach($depValues['targetvalues'] as $k => $pvalues){
									$picklistValues[] = array('value'=>$pvalues,'label'=>vtranslate($pvalues,$module));
								}
								$dependecyData[] = array('sourcefield'=>$values['sourcefield'],'sourcevalue'=>$depValues['sourcevalue'],'targetfield'=>$values['targetfield'],'targetvalues'=>$picklistValues);
							}
						}
						if(count($dependecyData) == 0){
							$targetfield = $PickListDependency['targetfield'];
						}
					}
				}
			}
		}else if($module && $target_field && $record){
			$data = Vtiger_DependencyPicklist::getDependentPicklistFields($module);
			$targetfield = "";
			if(count($data) > 0){
				if($record != ''){
					$recordid = substr($record, stripos($record, 'x')+1);
					$recordModel = Vtiger_Record_Model::getInstanceById($recordid,$module);
					foreach($data as $key => $values){
						if($values['sourcefield']){
							if($values['targetfield'] == $target_field){
								$PickListDependency = Vtiger_DependencyPicklist::getPickListDependency($module,$values['sourcefield'],$values['targetfield']);
								$valuemapping = $PickListDependency['valuemapping'];
								foreach($valuemapping as $keys => $depValues){
									if($depValues['sourcevalue'] == $recordModel->get($values['sourcefield'])){
										$picklistValues = array();
										foreach($depValues['targetvalues'] as $k => $pvalues){
											$picklistValues[] = array('value'=>$pvalues,'label'=>vtranslate($pvalues,$module));
										}
										$dependecyData[] = array('sourcefield'=>$values['sourcefield'],'sourcevalue'=>$depValues['sourcevalue'],'targetfield'=>$values['targetfield'],'targetvalues'=>$picklistValues);
									}
								}
								if(count($dependecyData) == 0){
									$targetfield = $PickListDependency['targetfield'];
								}
							}
						}
					}
				}else{
					foreach($data as $key => $values){
						if($values['sourcefield']){
							if($values['targetfield'] == $target_field){
								$PickListDependency = Vtiger_DependencyPicklist::getPickListDependency($module,$values['sourcefield'],$values['targetfield']);
								$valuemapping = $PickListDependency['valuemapping'];
								foreach($valuemapping as $keys => $depValues){
									if($depValues['sourcevalue']){
										$picklistValues = array();
										foreach($depValues['targetvalues'] as $k => $pvalues){
											$picklistValues[] = array('value'=>$pvalues,'label'=>vtranslate($pvalues,$module));
										}
										$dependecyData[] = array('sourcefield'=>$values['sourcefield'],'sourcevalue'=>$depValues['sourcevalue'],'targetfield'=>$values['targetfield'],'targetvalues'=>$picklistValues);
									}
								}
								if(count($dependecyData) == 0){
									$targetfield = $PickListDependency['targetfield'];
								}
							}
						}
					}
				}
			}
		}
		if(count($dependecyData) > 0){
			$response = new CTMobile_API_Response();
			$response->setResult(array('dependecyData'=>$dependecyData,'message'=>''));
			return $response;
		}else{
			$response = new CTMobile_API_Response();
			$message = $this->CTTranslate('No dependency found for picklist');	
			$response->setResult(array('dependecyData'=>$dependecyData,'message'=>$message,'targetfield'=>$targetfield));
			return $response;
		}
	}
}