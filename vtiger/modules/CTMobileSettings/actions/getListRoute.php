<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
class CTMobileSettings_getListRoute_Action extends Vtiger_Save_Action {
    
	public function process(Vtiger_Request $request) {
		global $adb;
		$CurrentUserModel = Users_Record_Model::getCurrentUserModel();
		$mode = $request->get('mode');
		if($mode == 'getAutoAddress'){
			$entries = array();
			//Get selected auto address field by module 
            $autoaddresss_field=$adb->pquery("SELECT * FROM `ctmobile_address_autofields`",array());
            $totalRows = $adb->num_rows($autoaddresss_field);
            if($totalRows > 0) {
            	for ($i=0; $i < $totalRows; $i++) { 
	            	$id = $adb->query_result($autoaddresss_field,$i,'id');
	            	$module = $adb->query_result($autoaddresss_field,$i,'module');
	            	$moduleModel = Vtiger_Module_Model::getInstance($module);
					$fieldModels = $moduleModel->getFields();
	                $auto_search = $adb->query_result($autoaddresss_field,$i,'auto_search');
	                $auto_search_label = '';
	                if($auto_search != ''){
	                	$auto_search_label = $fieldModels[$auto_search]->get('label');
	                }
	                $street  = $adb->query_result($autoaddresss_field,$i,'street');
	                $street_label = '';
	                if($street != ''){
	                	$street_label = $fieldModels[$street]->get('label');
	                }
	                $city = $adb->query_result($autoaddresss_field,$i,'city'); 
	                $city_label = '';
	                if($city != ''){
	                	$city_label = $fieldModels[$city]->get('label');
	                }   
	                $state = $adb->query_result($autoaddresss_field,$i,'state'); 
	                $state_label = '';
	                if($state != ''){
	                	$state_label = $fieldModels[$state]->get('label');
	                } 
	                $postalcode = $adb->query_result($autoaddresss_field,$i,'postalcode');
	                $postalcode_label = '';
	                if($postalcode != ''){
	                	$postalcode_label = $fieldModels[$postalcode]->get('label');
	                }  
	                $country = $adb->query_result($autoaddresss_field,$i,'country');
	                $country_label = '';
	                if($country != ''){
	                	$country_label = $fieldModels[$country]->get('label');
	                } 
	                $javascript = 'javascript:editAutoSearch("'.$module.'")';
	                $deleteButton = "<a class='btn btn-info' style='background:#287DF2 !important;' href='$javascript'><i class='fa fa-edit'></i></a>&nbsp;";
		            $deleteButton.= "<a class='btn btn-danger' href='javascript:deleteAutoAddressField(".$id.")'><i class='fa fa-trash'></i></a>";
		            $modulelabel = vtranslate($module,$module);
					$entries[] = array($modulelabel,$auto_search_label,$street_label,$city_label,$state_label,$postalcode_label,$country_label,$deleteButton);
				}
            }

            $html = json_encode($entries);

			$response = new Vtiger_Response();
			$response->setEmitType(Vtiger_Response::$EMIT_JSON);
			$response->setResult($html);
			$response->emit();

		}else if($mode == 'gettimezone'){
			$time_zone = $CurrentUserModel->get('time_zone');

			$todayDate = date('Y-m-d H:i:s');
			$date = new DateTime($todayDate, new DateTimeZone('UTC'));
			$date->setTimezone(new DateTimeZone($time_zone));
			$today = $date->format('Y/m/d');
			
			$yesterdayDate = date('Y-m-d H:i:s',strtotime("-1 days"));
			$date = new DateTime($yesterdayDate, new DateTimeZone('UTC'));
			$date->setTimezone(new DateTimeZone($time_zone));
			$yesterday = $date->format('Y/m/d');

			$last7daysDate = date('Y-m-d H:i:s',strtotime("-7 days"));
			$date = new DateTime($last7daysDate, new DateTimeZone('UTC'));
			$date->setTimezone(new DateTimeZone($time_zone));
			$last7days = $date->format('Y/m/d');

			$last30daysDate = date('Y-m-d H:i:s',strtotime("-30 days"));
			$date = new DateTime($last30daysDate, new DateTimeZone('UTC'));
			$date->setTimezone(new DateTimeZone($time_zone));
			$last30days = $date->format('Y/m/d');

			$monthStartDay = date('Y/m/01');
			$monthEndDay  = date('Y/m/t');

			$yearStartDay = date('Y/01/01');
			$yearEndDay  = date('Y/12/31');

			$lastyearStartDay = (date('Y')-1).'/01/01';
			$lastyearEndDay  = (date('Y')-1).'/12/31';

			$result = array('today'=>$today,'yesterday'=>$yesterday,'last7days'=>$last7days,'last30days'=>$last30days,'monthStartDay'=>$monthStartDay,'monthEndDay'=>$monthEndDay,'yearStartDay'=>$yearStartDay,'yearEndDay'=>$yearEndDay,'lastyearStartDay'=>$lastyearStartDay,'lastyearEndDay'=>$lastyearEndDay);

			$response = new Vtiger_Response();
			$response->setEmitType(Vtiger_Response::$EMIT_JSON);
			$response->setResult($result);
			$response->emit();
		}else if($mode == 'getlistSignature'){
			$getSignSQL = $adb->pquery("SELECT * FROM ctmobile_signature_fields",array());
			$totalRows = $adb->num_rows($getSignSQL);
			$entries = array();
			for ($i=0; $i < $totalRows; $i++) { 
				$id = $adb->query_result($getSignSQL,$i,'id');
				$module = $adb->query_result($getSignSQL,$i,'module');
				$modulelabel = vtranslate($module,$module);
				$sign_fieldname = $adb->query_result($getSignSQL,$i,'fieldname');
				$doc_type = $adb->query_result($getSignSQL,$i,'doc_type');

				$sign_field_array = explode(':',$sign_fieldname);
				$SignField = $sign_field_array[2];

				$sign_fieldlabel = $adb->pquery("SELECT columnname,fieldname,fieldlabel,tabid FROM vtiger_field WHERE columnname = ? and tabid= ?",array($SignField,getTabid($module)));
				$sign_fieldlabel = $adb->query_result($sign_fieldlabel,0,'fieldlabel');
				$sign_fieldlabel = vtranslate($sign_fieldlabel,$module);

				$deleteButton = "<a class='btn btn-danger' href='javascript:deleteSignature(".$id.")'><i class='fa fa-trash'></i></a>";

				$entries[] = array($modulelabel,$sign_fieldlabel,$doc_type,$deleteButton);
			}

			$html = json_encode($entries);

			$response = new Vtiger_Response();
			$response->setEmitType(Vtiger_Response::$EMIT_JSON);
			$response->setResult($html);
			$response->emit();

		}else if($mode == 'getlistDisplayFields'){
			$getDisplaySQL = $adb->pquery("SELECT * FROM ctmobile_display_fields",array());
			$totalRows = $adb->num_rows($getDisplaySQL);
			$entries = array();
			for ($i=0; $i < $totalRows; $i++) { 
				$id = $adb->query_result($getDisplaySQL,$i,'id');
				$module = $adb->query_result($getDisplaySQL,$i,'module');
				$userid = $adb->query_result($getDisplaySQL,$i,'userid');
				$UserName = '';
				if($userid != ''){
 					$userModel = Users_Record_Model::getInstanceById($userid,'Users');
 					$UserName = decode_html(decode_html($userModel->get('first_name').' '.$userModel->get('last_name')));
 				}
				$modulelabel = vtranslate($module,$module);
				$display_fieldname = $adb->query_result($getDisplaySQL,$i,'fieldname');
				$fieldtype = $adb->query_result($getDisplaySQL,$i,'fieldtype');

				$display_fieldlabel = $adb->pquery("SELECT fieldname,fieldlabel,tabid FROM vtiger_field WHERE fieldname = ? and tabid= ?",array($display_fieldname,getTabid($module)));
				$display_fieldlabel = $adb->query_result($display_fieldlabel,0,'fieldlabel');
				$display_fieldlabel = vtranslate($display_fieldlabel,$module);

				$deleteButton = "<a class='btn btn-danger' href='javascript:deleteDisplayField(".$id.")'><i class='fa fa-trash'></i></a>";

				$entries[] = array($modulelabel,$UserName,$display_fieldlabel,$fieldtype,$deleteButton);
			}

			$html = json_encode($entries);

			$response = new Vtiger_Response();
			$response->setEmitType(Vtiger_Response::$EMIT_JSON);
			$response->setResult($html);
			$response->emit();

		}else if($mode == 'getlistAssetsTracking'){
			$getAssetSQL = $adb->pquery("SELECT * FROM ctmobile_asset_field",array());
			$totalRows = $adb->num_rows($getAssetSQL);
			$entries = array();
			for ($i=0; $i < $totalRows; $i++) { 
				$id = $adb->query_result($getAssetSQL,$i,'id');
				$module = $adb->query_result($getAssetSQL,$i,'module');
				$modulelabel = vtranslate($module,$module);
				$asset_fieldname = $adb->query_result($getAssetSQL,$i,'fieldname');
				
				$field_array = explode(':',$asset_fieldname);
				$AssetField = $field_array[2];

				$asset_fieldlabel = $adb->pquery("SELECT columnname,fieldname,fieldlabel,tabid FROM vtiger_field WHERE columnname = ? and tabid= ?",array($AssetField,getTabid($module)));
				$fieldlabel = $adb->query_result($asset_fieldlabel,0,'fieldlabel');
				$fieldlabel = vtranslate($fieldlabel,$module);
				
				$deleteButton= "<a class='btn btn-danger' href='javascript:deleteAssetsTracking(".$id.")'><i class='fa fa-trash'></i></a>";

				$entries[] = array($modulelabel,$fieldlabel,$deleteButton);
			}

			$html = json_encode($entries);

			$response = new Vtiger_Response();
			$response->setEmitType(Vtiger_Response::$EMIT_JSON);
			$response->setResult($html);
			$response->emit();

		}else if($mode == 'deleteSignature'){
			$id = $request->get('id');
			$result = $adb->pquery("DELETE FROM ctmobile_signature_fields WHERE id = ?",array($id));
			if($result){
				$response = new Vtiger_Response();
				$response->setEmitType(Vtiger_Response::$EMIT_JSON);
				$response->setResult(true);
				$response->emit();
			}
		}else if($mode == 'deleteDisplayField'){
			$id = $request->get('id');
			$result = $adb->pquery("DELETE FROM ctmobile_display_fields WHERE id = ?",array($id));
			if($result){
				$response = new Vtiger_Response();
				$response->setEmitType(Vtiger_Response::$EMIT_JSON);
				$response->setResult(true);
				$response->emit();
			}
		}else if($mode == 'deleteAssetsTracking'){
			$id = $request->get('id');
			$result = $adb->pquery("DELETE FROM ctmobile_asset_field WHERE id = ?",array($id));
			if($result){
				$response = new Vtiger_Response();
				$response->setEmitType(Vtiger_Response::$EMIT_JSON);
				$response->setResult(true);
				$response->emit();
			}
		}else if($mode == 'deleteAutoSearch'){
			$id = $request->get('id');
			$result = $adb->pquery("DELETE FROM ctmobile_address_autofields WHERE id = ?",array($id));
			if($result){
				$response = new Vtiger_Response();
				$response->setEmitType(Vtiger_Response::$EMIT_JSON);
				$response->setResult(true);
				$response->emit();
			}
		}else if($mode == 'deleteRoute'){
			$routeid = $request->get('routeid');
			if($routeid){
				$recordModel = Vtiger_Record_Model::getInstanceById($routeid,'CTRoutePlanning');
				$result = $recordModel->delete();
			}
			if($result){
				$response = new Vtiger_Response();
				$response->setEmitType(Vtiger_Response::$EMIT_JSON);
				$response->setResult(true);
				$response->emit();
			}
		}else if($mode == 'getlist'){
			$listdaterange = $request->get('listdaterange');
			$listUsers = $request->get('listUsers');
			$recordid = $request->get('recordid');
			$date = explode(' - ',$listdaterange);
			$startdate = date("Y-m-d", strtotime($date[0]));
			$enddate = date("Y-m-d", strtotime($date[1]));
			$sql = "SELECT vtiger_ctrouteplanning.ctrouteplanningid,vtiger_ctrouteplanning.ctroutename,vtiger_ctrouteplanning.ctroute_date,vtiger_ctrouteplanning.ctroute_status,entity2.crmid,
			vtiger_users.first_name, vtiger_users.last_name, entity2.label,entity2.setype,entity3.createdtime as check_in_time,entity3.modifiedtime as check_out_time,vtiger_ctrouteattendance.ctroute_attendance_status,vtiger_ctrouteattendance.check_in_address,vtiger_ctrouteattendance.check_out_address,vtiger_ctrouteattendance.check_in_location,vtiger_ctrouteattendance.check_out_location FROM vtiger_ctrouteplanning INNER JOIN vtiger_crmentity entity1 ON entity1.crmid =  vtiger_ctrouteplanning.ctrouteplanningid INNER JOIN vtiger_users ON vtiger_users.id = entity1.smownerid
				INNER JOIN vtiger_ctrouteplanrel ON vtiger_ctrouteplanrel.ctrouteplanningid = vtiger_ctrouteplanning.ctrouteplanningid INNER JOIN vtiger_crmentity entity2 ON
	 			entity2.crmid = vtiger_ctrouteplanrel.ctroute_realtedto LEFT JOIN  vtiger_ctrouteattendance ON vtiger_ctrouteattendance.related_to = vtiger_ctrouteplanrel.ctroute_realtedto AND vtiger_ctrouteattendance.ctroute_planning = vtiger_ctrouteplanning.ctrouteplanningid LEFT JOIN vtiger_crmentity entity3 ON entity3.crmid = vtiger_ctrouteattendance.ctrouteattendanceid WHERE entity1.deleted = 0 AND entity2.deleted = 0";
	 		if($listUsers != '' && $listUsers != 'all'){
	 			$sql.=" AND entity1.smownerid = '$listUsers'";
	 		}
	 		if($startdate != '' && $enddate != ''){
	 			$sql.=" AND vtiger_ctrouteplanning.ctroute_date BETWEEN '$startdate' AND '$enddate' ";
	 		}
	 		if(!empty($recordid)){
	 			$sql.=" AND entity2.crmid = '$recordid' ";
	 		}
	 		
	 		$result = $adb->pquery($sql,array());
	 		$numofrows = $adb->num_rows($result);
	 		if($numofrows){
	 			$entries = array();
	 			for($i=0;$i<$numofrows;$i++){
	 				$row = $adb->query_result_rowdata($result,$i);
	 				if($row['ctroute_attendance_status'] == 'check_in'){
	 					$row['check_out_time'] = '';
	 				}
	 				if($row['check_in_time'] != ''){
	 					$row['check_in_time'] = Vtiger_Util_Helper::convertDateTimeIntoUsersDisplayFormat($row['check_in_time']);
	 				}
	 				if($row['check_out_time'] != ''){
	 					$row['check_out_time'] = Vtiger_Util_Helper::convertDateTimeIntoUsersDisplayFormat($row['check_out_time']);
	 				}
	 				$row['ctroute_date'] = Vtiger_Date_UIType::getDisplayValue($row['ctroute_date']);
	 				$row['setype'] = vtranslate($row['setype'],$row['setype']);
	 				$ctroute_status = $row['ctroute_status'];
	 				if(Users_Privileges_Model::isPermitted('CTRoutePlanning', 'DetailView', $row['ctrouteplanningid'])){
	 					$deleteButton = '';
	 					if($CurrentUserModel->get('is_admin') == 'on'){
	 						$deleteButton = "<a class='btn btn-danger' href='javascript:deleteRoute(".$row['ctrouteplanningid'].")'><i class='fa fa-trash'></i></a>&nbsp;&nbsp;";
	 					}
	 					if($row['check_in_location'] !='' && $row['check_in_location'] != ','){
	 						$window = 'window.open("https://www.google.com/maps/search/?api=1&query='.$row['check_in_location'].'");return false;';
	 						$deleteButton.="<a onclick='".$window."'><i class='fa fa-map-marker' style='color:green;font-size:20px;' title='Click to see Check-in Location on Map'></i></a>&nbsp;&nbsp;";
	 					}else{
	 						$deleteButton.='<a onclick="return false;"><i class="fa fa-map-marker" style="font-size:20px;" title="No Check-in Location"></i></a>&nbsp;&nbsp;';
	 					}

	 					if($row['check_out_location'] !='' && $row['check_out_location'] != ','){
	 						$window = 'window.open("https://www.google.com/maps/search/?api=1&query='.$row['check_out_location'].'");return false;';
	 						$deleteButton.="<a onclick='".$window."'><i class='fa fa-map-marker' style='color:red;font-size:20px;' title='Click to see Check-out Location on Map'></i></a>&nbsp;&nbsp;";
	 					}else{
	 						$deleteButton.='<a onclick="return false;"><i class="fa fa-map-marker" style="font-size:20px;" title="No Check-out Location"></i></a>&nbsp;&nbsp;';
	 					}
	 					
	 					$detailLink = '<a href="index.php?module=CTRoutePlanning&view=Detail&record='.$row['ctrouteplanningid'].'" target="_blank">'.vtranslate('LBL_VIEW_DETAILS').'</a>';
	 					$entries[] = array($deleteButton,$row['ctroutename'],$row['ctroute_date'],decode_html(decode_html($row['first_name'])).' '.decode_html(decode_html($row['last_name'])),$row['setype'],decode_html(decode_html($row['label'])),$ctroute_status,'',$row['check_in_time'],$row['check_out_time'],$row['check_in_address'],$row['check_out_address'],$detailLink);
	 				}
	 			}
	 			$html = json_encode($entries);
	 		}else{
	 			$entries = array();
	 			$html = json_encode($entries);
	 		}
	 		$response = new Vtiger_Response();
			$response->setEmitType(Vtiger_Response::$EMIT_JSON);
			$response->setResult($html);
			$response->emit();
		}else if($mode == 'ExportData'){
			$listdaterange = $request->get('listdaterange');
			$listUsers = $request->get('listUsers');
	
			$type = $request->get('type');
			$date = explode(' - ',$listdaterange);
			$startdate = date("Y-m-d", strtotime($date[0]));
			$enddate = date("Y-m-d", strtotime($date[1]));
			$sql = "SELECT vtiger_ctrouteplanning.ctrouteplanningid,vtiger_ctrouteplanning.ctroutename,vtiger_ctrouteplanning.ctroute_date,entity2.crmid,vtiger_users.first_name, vtiger_users.last_name, entity2.label,entity2.setype,entity3.createdtime as check_in_time,entity3.modifiedtime as check_out_time,vtiger_ctrouteattendance.check_in_address,vtiger_ctrouteattendance.check_out_address FROM vtiger_ctrouteplanning INNER JOIN vtiger_crmentity entity1 ON entity1.crmid =  vtiger_ctrouteplanning.ctrouteplanningid INNER JOIN vtiger_users ON vtiger_users.id = entity1.smownerid
				INNER JOIN vtiger_ctrouteplanrel ON vtiger_ctrouteplanrel.ctrouteplanningid = vtiger_ctrouteplanning.ctrouteplanningid INNER JOIN vtiger_crmentity entity2 ON
	 			entity2.crmid = vtiger_ctrouteplanrel.ctroute_realtedto LEFT JOIN  vtiger_ctrouteattendance ON vtiger_ctrouteattendance.related_to = vtiger_ctrouteplanrel.ctroute_realtedto AND vtiger_ctrouteattendance.ctroute_planning = vtiger_ctrouteplanning.ctrouteplanningid LEFT JOIN vtiger_crmentity entity3 ON entity3.crmid = vtiger_ctrouteattendance.ctrouteattendanceid WHERE entity1.deleted = 0 AND entity2.deleted = 0";
	 		if($listUsers != '' && $listUsers != 'all'){
	 			$sql.=" AND entity1.smownerid = '$listUsers'";
	 		}
	 		if($startdate != '' && $enddate != ''){
	 			$sql.=" AND vtiger_ctrouteplanning.ctroute_date BETWEEN '$startdate' AND '$enddate' ";
	 		}
	 		
	 		$result = $adb->pquery($sql,array());
	 		$numofrows = $adb->num_rows($result);
	 		if($numofrows){
	 			$headers = array('Route','Date of Route','Assigned To','Record Type','Name','Notes','Check-in Time','Check-out Time','Check-in Location','Check-out Location');
	 			$entries = array();
	 			for($i=0;$i<$numofrows;$i++){
	 				$row = $adb->query_result_rowdata($result,$i);
	 				if($row['check_in_time'] != ''){
	 					$row['check_in_time'] = Vtiger_Util_Helper::convertDateTimeIntoUsersDisplayFormat($row['check_in_time']);
	 				}
	 				if($row['check_out_time'] != ''){
	 					$row['check_out_time'] = Vtiger_Util_Helper::convertDateTimeIntoUsersDisplayFormat($row['check_out_time']);
	 				}
	 				$row['ctroute_date'] = Vtiger_Date_UIType::getDisplayValue($row['ctroute_date']);
	 				$row['setype'] = vtranslate($row['setype'],$row['setype']);
	 				if(Users_Privileges_Model::isPermitted('CTRoutePlanning', 'DetailView', $row['ctrouteplanningid'])){
	 					$entries[] = array($row['ctroutename'],$row['ctroute_date'],decode_html(decode_html($row['first_name'])).' '.decode_html(decode_html($row['last_name'])),$row['setype'],decode_html(decode_html($row['label'])),'',$row['check_in_time'],$row['check_out_time'],$row['check_in_address'],$row['check_out_address']);
	 				}
	 			}
	 			$this->output($headers,$entries,$type);
	 			
	 		}
		}else if($mode == 'listRoute'){
			$mapdaterange = $request->get('mapdaterange');
			$mapUsers = $request->get('mapUsers');
			$date = explode(' - ',$mapdaterange);
			$startdate = date("Y-m-d", strtotime($date[0]));
			$enddate = date("Y-m-d", strtotime($date[1]));
			$sql = "SELECT vtiger_ctrouteplanning.ctrouteplanningid,vtiger_ctrouteplanning.ctroutename FROM vtiger_ctrouteplanning INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_ctrouteplanning.ctrouteplanningid WHERE vtiger_crmentity.deleted = 0";
			if($mapUsers != '' && $mapUsers != 'all'){
	 			$sql.=" AND vtiger_crmentity.smownerid = '$mapUsers'";
	 		}
	 		if($startdate != '' && $enddate != ''){
	 			$sql.=" AND vtiger_ctrouteplanning.ctroute_date BETWEEN '$startdate' AND '$enddate' ";
	 		}
	 		$result = $adb->pquery($sql,array());
	 		$numofrows = $adb->num_rows($result);
	 		if($numofrows){
	 			$html = '<option value="">'.vtranslate('Select Routes','CTMobileSettings').'</option>';
	 			for($i=0;$i<$numofrows;$i++){
	 				$row = $adb->query_result_rowdata($result,$i);
	 				if(Users_Privileges_Model::isPermitted('CTRoutePlanning', 'DetailView', $row['ctrouteplanningid'])){
	 					$html.= '<option value="'.$row['ctrouteplanningid'].'">'.decode_html(decode_html($row['ctroutename'])).'</option>';
	 				}
	 			}
	 		}else{
	 			$html = '<option value="">'.vtranslate('Select Routes','CTMobileSettings').'</option>';
	 		}
	 		$response = new Vtiger_Response();
			$response->setEmitType(Vtiger_Response::$EMIT_JSON);
			$response->setResult($html);
			$response->emit();

		}else if($mode == 'listRecord'){
			$listdaterange = $request->get('listdaterange');
			$listUsers = $request->get('listUsers');
			$searchvalue = 	$request->get('searchtext');
			$date = explode(' - ',$listdaterange);
			$startdate = date("Y-m-d", strtotime($date[0]));
			$enddate = date("Y-m-d", strtotime($date[1]));

			$sql = "SELECT entity2.label,entity2.crmid FROM vtiger_ctrouteplanning INNER JOIN vtiger_crmentity entity1 ON entity1.crmid =  vtiger_ctrouteplanning.ctrouteplanningid INNER JOIN vtiger_users ON vtiger_users.id = entity1.smownerid
				INNER JOIN vtiger_ctrouteplanrel ON vtiger_ctrouteplanrel.ctrouteplanningid = vtiger_ctrouteplanning.ctrouteplanningid INNER JOIN vtiger_crmentity entity2 ON
	 			entity2.crmid = vtiger_ctrouteplanrel.ctroute_realtedto LEFT JOIN  vtiger_ctrouteattendance ON vtiger_ctrouteattendance.related_to = vtiger_ctrouteplanrel.ctroute_realtedto AND vtiger_ctrouteattendance.ctroute_planning = vtiger_ctrouteplanning.ctrouteplanningid LEFT JOIN vtiger_crmentity entity3 ON entity3.crmid = vtiger_ctrouteattendance.ctrouteattendanceid WHERE entity1.deleted = 0 AND entity2.deleted = 0";
	 		if($listUsers != '' && $listUsers != 'all'){
	 			$sql.=" AND entity1.smownerid = '$listUsers'";
	 		}
	 		if($startdate != '' && $enddate != ''){
	 			$sql.=" AND vtiger_ctrouteplanning.ctroute_date BETWEEN '$startdate' AND '$enddate' ";
	 		}
	 		if(!empty($searchvalue))
				$sql.= " AND entity2.label LIKE '%".$searchvalue."%'";
	 		
	 		$result = $adb->pquery($sql,array());
			$data = array();
			for($i=0;$i<$adb->num_rows($result);$i++){
				$id = $adb->query_result($result,$i,'crmid');
				$label = $adb->query_result($result,$i,'label');
				$action = $adb->query_result($result,$i,'action');
				$data[] = array('id' => $id ,'text' =>$label);
			}
			
			$response = new Vtiger_Response();
			$response->setResult($data);
			$response->emit();

		}else if($mode == 'getRoutePoint'){
			$routeid = $request->get('routeid');
			$sql = "SELECT vtiger_ctrouteplanrel.*,vtiger_ctrouteplanning.*,vtiger_users.*,e2.setype,e2.label,e2.createdtime,e2.crmid,entity3.createdtime as check_in_time,entity3.modifiedtime as check_out_time,vtiger_ctrouteattendance.ctroute_attendance_status FROM vtiger_ctrouteplanrel INNER JOIN vtiger_ctrouteplanning ON vtiger_ctrouteplanning.ctrouteplanningid = vtiger_ctrouteplanrel.ctrouteplanningid INNER JOIN vtiger_crmentity e1 ON e1.crmid = vtiger_ctrouteplanrel.ctrouteplanningid INNER JOIN vtiger_crmentity e2 ON e2.crmid = vtiger_ctrouteplanrel.ctroute_realtedto INNER JOIN vtiger_users ON vtiger_users.id = e1.smownerid LEFT JOIN  vtiger_ctrouteattendance ON vtiger_ctrouteattendance.related_to = vtiger_ctrouteplanrel.ctroute_realtedto AND vtiger_ctrouteattendance.ctroute_planning = vtiger_ctrouteplanning.ctrouteplanningid LEFT JOIN vtiger_crmentity entity3 ON entity3.crmid = vtiger_ctrouteattendance.ctrouteattendanceid WHERE e2.deleted = 0 AND vtiger_ctrouteplanrel.ctrouteplanningid = '$routeid'";
			$result = $adb->pquery($sql,array());
			$data = array('marker'=>array());
	 		$numofrows = $adb->num_rows($result);
	 		if($numofrows){
	 			for($i=0;$i<$numofrows;$i++){
	 				$ctroutename = decode_html(decode_html($adb->query_result($result,$i,'ctroutename')));
	 				$recordid = $adb->query_result($result,$i,'crmid');
	 				$ctroute_date = Vtiger_Date_UIType::getDisplayValue($adb->query_result($result,$i,'ctroute_date'));
					$setype = $adb->query_result($result,$i,'setype');

					if(in_array($setype,array('HelpDesk','Invoice','Quotes','SalesOrder','PurchaseOrder'))){
						$latlongData = $this->getLatLongFromRelatedRecord($recordid,$setype);
					}else{
						$latlongData = $this->getLatLongOfRecord($recordid);
					}

					$ctroute_attendance_status = $adb->query_result($result,$i,'ctroute_attendance_status');
					$check_in_time = $adb->query_result($result,$i,'check_in_time');
					$check_out_time = $adb->query_result($result,$i,'check_out_time');

					if($ctroute_attendance_status == 'check_in'){
	 					$check_out_time = '';
	 				}
	 				if($check_in_time != ''){
	 					$check_in_time = Vtiger_Util_Helper::convertDateTimeIntoUsersDisplayFormat($check_in_time);
	 				}
	 				if($check_out_time != ''){
	 					$check_out_time = Vtiger_Util_Helper::convertDateTimeIntoUsersDisplayFormat($check_out_time);
	 				}

					$latitude = $latlongData['lat'];
					$longitude = $latlongData['long'];

					$module = vtranslate($setype,$setype);
					$label = decode_html(decode_html($adb->query_result($result,$i,'label')));
					
					$createdtime = $adb->query_result($result,$i,'createdtime');
					$DetaiViewurl = 'index.php?module='.$module.'&view=Detail&record='.$recordid;
					$createdtime = Vtiger_Util_Helper::formatDateDiffInStrings($createdtime);
					$username = decode_html(decode_html($adb->query_result($result,$i,'first_name'))).' '.decode_html(decode_html($adb->query_result($result,$i,'last_name')));
					if(in_array($setype,array('HelpDesk','Invoice','Quotes','SalesOrder','PurchaseOrder'))){
						$locationModule = vtranslate($latlongData['setype'],$latlongData['setype']);
						$locationRecord = decode_html($latlongData['label']);
						$entitylabel = '<div id="bodyContent"><table class="table table-bordered"><tr><td> Name </td><td><a href="'.$DetaiViewurl.'" target="_blank"><b>'.$label.' </b></a></td></tr><tr><td> Record Type </td><td><b>'.$module.'</b></td></tr><tr><td> Route Name </td><td><b>'.$ctroutename.'</b></td></tr><tr><td>Assigned To </td><td><b>'.$username.'</b></td></tr><tr><td> Check-in time </td><td><b>'.$check_in_time.'</b></td></tr><tr><td> Check-out time </td><td><b>'.$check_out_time.'</b></td></tr><tr><td> Location Module </td><td><b>'.$locationModule.'</b></td></tr><tr><td> Location Record </td><td><b>'.$locationRecord.'</b></td></tr></div>';
					}else{
						$entitylabel = '<div id="bodyContent"><table class="table table-bordered"><tr><td> Name </td><td><a href="'.$DetaiViewurl.'" target="_blank"><b>'.$label.' </b></a></td></tr><tr><td> Record Type </td><td><b>'.$module.'</b></td></tr><tr><td> Route Name </td><td><b>'.$ctroutename.'</b></td></tr><tr><td>Assigned To </td><td><b>'.$username.'</b></td></tr><tr><tr><td> Check-in time </td><td><b>'.$check_in_time.'</b></td></tr><tr><td> Check-out time </td><td><b>'.$check_out_time.'</b></td></tr></div>';
					}
					$data['marker'][] = array('lat'=>(double)$latitude,'lng'=>(double)$longitude,'label'=>$entitylabel);
				}
	 		}
	 		$response = new Vtiger_Response();
			$response->setEmitType(Vtiger_Response::$EMIT_JSON);
			$response->setResult($data);
			$response->emit();
		}

	}

	function output($headers, $entries,$type) {
		// for content disposition header comma should not be there in filename 
		if($type == 'csv'){
			$fileName = 'RouteData.csv';
			$exportType = 'text/csv';
		}else{
			$fileName = 'RouteData.xls';
			$exportType = 'application/x-msexcel';
		}
		header("Content-Disposition:attachment;filename=$fileName");
		header("Content-Type:$exportType;charset=UTF-8");
		header("Expires: Mon, 31 Dec 2000 00:00:00 GMT" );
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT" );
		header("Cache-Control: post-check=0, pre-check=0", false );

		if($type == 'csv'){
			$header = implode("\", \"", $headers);
			$header = "\"" .$header;
			$header .= "\"\r\n";
			echo $header;
			foreach($entries as $row) {
				foreach ($row as $key => $value) {
					/* To support double quotations in CSV format
					 * To review: http://creativyst.com/Doc/Articles/CSV/CSV01.htm#EmbedBRs
					 */
					$row[$key] = str_replace('"', '""', $value);
				}
				$line = implode("\",\"",$row);
				$line = "\"" .$line;
				$line .= "\"\r\n";
				echo $line;
			}
		}else{
			$header =  implode("\t", $headers) . "\n";
			echo $header;
			foreach($entries as $row) {
				echo implode("\t", array_values($row)) . "\n";
			}

		}
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
				$result  = $adb->pquery("SELECT * FROM `ct_address_lat_long` INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = ct_address_lat_long.recordid WHERE recordid = ? ",array($record1));
				if($adb->num_rows($result) > 0){
					$data['lat'] = $adb->query_result($result,0,'latitude');
					$data['long'] = $adb->query_result($result,0,'longitude');
					$data['setype'] = $adb->query_result($result,0,'setype');
					$data['label'] = $adb->query_result($result,0,'label');
				}

			}
			if($record2 != "" && $data['lat'] == "" && $data['long'] == ""){
				$result  = $adb->pquery("SELECT * FROM `ct_address_lat_long` INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = ct_address_lat_long.recordid WHERE recordid = ? ",array($record2));
				if($adb->num_rows($result) > 0){
					$data['lat'] = $adb->query_result($result,0,'latitude');
					$data['long'] = $adb->query_result($result,0,'longitude');
					$data['setype'] = $adb->query_result($result,0,'setype');
					$data['label'] = $adb->query_result($result,0,'label');
				}
			}
		}

		return $data;
	}
}
?>
