<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

class CTMobile_WS_SaveRoute extends CTMobile_WS_Controller {
	protected $recordValues = false;
	function process(CTMobile_API_Request $request) {
		global $adb,$current_user;
		$current_user = $this->getActiveUser();
		
		$record = trim($request->get("record"));
		$isEditRoute = trim($request->get("isEditRoute"));
		$ctroutename = trim($request->get("ctroutename"));
		$ctroute_date = trim($request->get("ctroute_date"));
		$ctroute_status = trim($request->get("ctroute_status"));
		$assigned_user_id = trim($request->get("assigned_user_id"));
		if(is_array( $request->get('ctroute_realtedto'))){
			$ctrouteidlist = $request->get('ctroute_realtedto');
		}else{
			$ctrouteidlist = Zend_Json::decode($request->get('ctroute_realtedto'));
		}

		$insertion_mode = "";
		$moduleName = 'CTRoutePlanning';
		$response = new CTMobile_API_Response();
		if($isEditRoute && !empty($record)){
			$insertion_mode = "edit";
			$record_id = explode('x', $record);
			$recordId = $record_id[1];
			$recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
			$recordModel->set('id', $recordId);
			$recordModel->set('mode', 'edit');
			$recordModel->set('ctroute_status',$ctroute_status);
			$recordModel->set('ctroutename',$ctroutename);
			$recordModel->set('ctroute_date',$ctroute_date);
			//$recordModel->set('ctroute_status',$ctroute_status);
			$assigned_user = explode('x', $assigned_user_id);
			$recordModel->set('assigned_user_id',$assigned_user[1]);
			$recordModel->save();
			$moduleWSId = CTMobile_WS_Utils::getEntityModuleWSId($moduleName);
			$recordId = $recordModel->getId();
			$ctroutename = $recordModel->get('ctroutename');
			$this->recordValues['id'] = $moduleWSId.'x'.$recordId;

			if(!empty($ctrouteidlist) && !empty($recordId)) {
				$adb->pquery( 'DELETE from vtiger_ctrouteplanrel WHERE ctrouteplanningid = ?', array($recordId));

				$count = count($ctrouteidlist);

				$sql = 'INSERT INTO vtiger_ctrouteplanrel VALUES ';
				for($i=0; $i<$count; $i++) {
					$ctrouteid = explode('x',$ctrouteidlist[$i]);
					$sql .= " ($recordId,$ctrouteid[1])";
					if ($i != $count - 1) {
						$sql .= ',';
					}
				}
				$adb->pquery($sql, array());
			}else if ($insertion_mode == "edit" && !empty($recordId)) {
				$adb->pquery('DELETE FROM vtiger_ctrouteplanrel WHERE ctrouteplanningid = ?', array($recordId));
			}
			
			$recordLabel = $ctroutename;
			$message = $this->CTTranslate('Route status updated successfully');
			$result = array('id'=>$this->recordValues['id'],'recordLabel'=>$recordLabel,'module'=>$moduleName,'message'=>$message);
			$response->setResult($result);
		}else if(!empty($record)){
			$insertion_mode = "edit";
			$record_id = explode('x', $record);
			$recordId = $record_id[1];
			$recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
			$recordModel->set('id', $recordId);
			$recordModel->set('mode', 'edit');
			$recordModel->set('ctroute_status',$ctroute_status);
			$recordModel->save();
			$moduleWSId = CTMobile_WS_Utils::getEntityModuleWSId($moduleName);
			$recordId = $recordModel->getId();
			$ctroutename = $recordModel->get('ctroutename');
			$this->recordValues['id'] = $moduleWSId.'x'.$recordId;

			$recordLabel = $ctroutename;
			$message = $this->CTTranslate('Route status updated successfully');
			$result = array('id'=>$this->recordValues['id'],'recordLabel'=>$recordLabel,'module'=>$moduleName,'message'=>$message);
			$response->setResult($result);
		}else{

			if($ctroutename == '' || $ctroute_date == '' || $assigned_user_id == ''){
				$message = $this->CTTranslate('Required fields not found');
				throw new WebServiceException(404,$message);
			}
			$recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
			$recordModel->set('mode', '');

			$assigned_user = explode('x', $assigned_user_id);
			$recordModel->set('ctroutename',$ctroutename);
			$recordModel->set('ctroute_date',$ctroute_date);
			//$recordModel->set('ctroute_status',$ctroute_status);
			$recordModel->set('assigned_user_id',$assigned_user[1]);

			$recordModel->save();
			$moduleWSId = CTMobile_WS_Utils::getEntityModuleWSId($moduleName);
			$recordId = $recordModel->getId();
			$this->recordValues['id'] = $moduleWSId.'x'.$recordId;

			if(!empty($ctrouteidlist) && !empty($recordId)) {
				$adb->pquery( 'DELETE from vtiger_ctrouteplanrel WHERE ctrouteplanningid = ?', array($recordId));

				$count = count($ctrouteidlist);

				$sql = 'INSERT INTO vtiger_ctrouteplanrel VALUES ';
				for($i=0; $i<$count; $i++) {
					$ctrouteid = explode('x',$ctrouteidlist[$i]);
					$sql .= " ($recordId,$ctrouteid[1])";
					if ($i != $count - 1) {
						$sql .= ',';
					}
				}
				$adb->pquery($sql, array());
			} else if ($insertion_mode == "edit" && !empty($recordId)) {
				$adb->pquery('DELETE FROM vtiger_ctrouteplanrel WHERE ctrouteplanningid = ?', array($recordId));
			}

			$recordLabel = $ctroutename;
			$message = $this->CTTranslate('Route save successfully');
			$result = array('id'=>$this->recordValues['id'],'recordLabel'=>$recordLabel,'module'=>$moduleName,'message'=>$message);
			$response->setResult($result);
		}
		return $response;
	}
}