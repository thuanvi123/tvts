<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
include_once dirname(__FILE__) . '/FetchRecord.php';

class CTMobile_WS_DeleteWidgets extends CTMobile_WS_FetchRecord {
	
	function process(CTMobile_API_Request $request) {
		$default_charset = VTWS_PreserveGlobal::getGlobal('default_charset');
		global $adb,$current_user; // Required for vtws_update API
		$current_user = $this->getActiveUser();
  		$reportid = trim($request->get('reportid'));
  		$widgetid = trim($request->get('widgetid'));
  		$currentuserid = $current_user->id;
  		if(!empty($reportid)){

  			$widgetInstance = Vtiger_Widget_Model::getInstanceWithReportId($reportid, $currentuserid);
			$widgetInstance->remove();

			$del_query = $adb->pquery("DELETE FROM ctmobile_dashboard_sequence WHERE id = ? AND userid = ? AND type = ?",array($reportid,$currentuserid,'report'));
  		}else{
  			if($widgetid != ''){

	  			$widget = Vtiger_Widget_Model::getInstance($widgetid, $currentuserid);
	  			$widgetsid = $widget->get('id');
	  			if($widgetsid != ''){
	  				$widgetInstance = Vtiger_Widget_Model::getInstanceWithWidgetId($widgetsid, $currentuserid);
	  				$widgetInstance->remove();
	  			}else{
	  				$widget->remove();
	  			}
	  			
	  			$del_query = $adb->pquery("DELETE FROM ctmobile_dashboard_sequence WHERE id = ? AND userid = ? AND type = ?",array($widgetid,$currentuserid,'widget'));
  			}
  		}
  		
		$response = new CTMobile_API_Response();
		$message = $this->CTTranslate('Removed Successfully');
		$response->setResult(array('message'=>$message));
		return $response;
	}

}
