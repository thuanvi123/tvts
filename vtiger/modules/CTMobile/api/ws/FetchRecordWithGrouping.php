<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
include_once 'include/Webservices/Retrieve.php';
include_once dirname(__FILE__) . '/FetchRecord.php';
include_once 'include/Webservices/DescribeObject.php';

class CTMobile_WS_FetchRecordWithGrouping extends CTMobile_WS_FetchRecord {
	
	private $_cachedDescribeInfo = false;
	private $_cachedDescribeFieldInfo = false;
	
	protected function cacheDescribeInfo($describeInfo) {
		$this->_cachedDescribeInfo = $describeInfo;
		$this->_cachedDescribeFieldInfo = array();
		if(!empty($describeInfo['fields'])) {
			foreach($describeInfo['fields'] as $describeFieldInfo) {
				$this->_cachedDescribeFieldInfo[$describeFieldInfo['name']] = $describeFieldInfo;
			}
		}
	}
	
	protected function cachedDescribeInfo() {
		return $this->_cachedDescribeInfo;
	}
	
	protected function cachedDescribeFieldInfo($fieldname) {
		if ($this->_cachedDescribeFieldInfo !== false) {
			if(isset($this->_cachedDescribeFieldInfo[$fieldname])) {
				return $this->_cachedDescribeFieldInfo[$fieldname];
			}
		}
		return false;
	}
	
	protected function cachedEntityFieldnames($module) {
		$describeInfo = $this->cachedDescribeInfo();
		$labelFields = $describeInfo['labelFields'];
		switch($module) {
			case 'HelpDesk': $labelFields = 'ticket_title'; break;
			case 'Documents': $labelFields = 'notes_title'; break;
		}
		return explode(',', $labelFields);
	}
	
	protected function isTemplateRecordRequest(CTMobile_API_Request $request) {
		$recordid = $request->get('record');
		return (preg_match("/([0-9]+)x0/", $recordid));
	}
	
	protected function processRetrieve(CTMobile_API_Request $request) {
		$recordid = $request->get('record');

		// Create a template record for use 
		if ($this->isTemplateRecordRequest($request)) {
			global $current_user;
			$current_user = $this->getActiveUser();
			
			$module = $this->detectModuleName($recordid);
		 	$describeInfo = vtws_describe($module, $current_user);
		 	CTMobile_WS_Utils::fixDescribeFieldInfo($module, $describeInfo);

		 	$this->cacheDescribeInfo($describeInfo);

			$templateRecord = array();
			foreach($describeInfo['fields'] as $describeField) {
				$templateFieldValue = '';
				if (isset($describeField['type']) && isset($describeField['type']['defaultValue'])) {
					$templateFieldValue = trim($describeField['type']['defaultValue']);
				} else if (isset($describeField['default'])) {
					$templateFieldValue = trim($describeField['default']);
				}
				$templateRecord[$describeField['name']] = $templateFieldValue;
			}
			if (isset($templateRecord['assigned_user_id'])) {
				$templateRecord['assigned_user_id'] = sprintf("%sx%s", CTMobile_WS_Utils::getEntityModuleWSId('Users'), $current_user->id);
			} 
			// Reset the record id
			$templateRecord['id'] = $recordid;
			
			return $templateRecord;
		}
		
		// Or else delgate the action to parent
		return parent::processRetrieve($request);
	}
	
	function process(CTMobile_API_Request $request) {
		$recordid = trim($request->get('record'));
		if(empty($recordid)){
			$message =  $this->CTTranslate('Required fields not found');
			throw new WebServiceException(404,$message);
		}
		$module = $this->detectModuleName($recordid);
		global $adb;
		if($module == 'Calendar' || $module == 'Events'){
			$calendarmodule = explode('x', $request->get('record'));
			$activityid = $calendarmodule[1];
			$EventTaskQuery = $adb->pquery("SELECT * FROM  `vtiger_activity` WHERE activitytype = ? AND activityid = ?",array('Task',$activityid)); 
		    if($adb->num_rows($EventTaskQuery) > 0){
				$wsid = CTMobile_WS_Utils::getEntityModuleWSId('Calendar');
				$recordid = $wsid.'x'.$activityid;
				$recordModule = 'Calendar';
			}else{
				$wsid = CTMobile_WS_Utils::getEntityModuleWSId('Events');
				$recordid = $wsid.'x'.$activityid;
				$recordModule = 'Events';
			}
			$request->set('record',$recordid);
		}
		$response = parent::process($request);
		
		return $this->processWithGrouping($request, $response);
	}
	
	protected function processWithGrouping(CTMobile_API_Request $request, $response) {
		global $adb;
		$getTimeTrackerQuery = $adb->pquery("SELECT * FROM ctmobile_timetracking_modules");
		$timeTrackerArray = array();
		for ($i=0; $i < $adb->num_rows($getTimeTrackerQuery); $i++) {
			$timeTrackerArray[] = $adb->query_result($getTimeTrackerQuery,$i,'module');
		}

		$isTemplateRecord = $this->isTemplateRecordRequest($request);
		$result = $response->getResult();
		
		$resultRecord = $result['record'];
		$module = $this->detectModuleName($resultRecord['id']);
		$tracking_status = false;
		$cttimetrackerid = "";
		$tracking_time = '';
		$isTimeTrackingSameRecord = false;
		if(in_array($module,$timeTrackerArray)){
			$isTimeTrackerModule = true;
			$record = explode('x', $resultRecord['id']);
			$recordid = $record[1];
			$current_user = $this->getActiveUser();
			$tracking_user = $current_user->id;
			$time_tracking_record = "";
			$isTimeTracking = false;

			$nowInDBFormat = date('Y-m-d H:i:s');
	   		 // calulate the difference in seconds
	    	list($date_end, $time_end) = explode(' ', $nowInDBFormat);
		
			$getTimeQuery = "SELECT * FROM vtiger_cttimetracker INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_cttimetracker.cttimetrackerid WHERE vtiger_crmentity.deleted = 0 AND vtiger_cttimetracker.tracking_user = '$tracking_user' AND vtiger_cttimetracker.tracking_status = 'Start' ";

			$getTimeQuery2 = "SELECT * FROM vtiger_cttimetracker INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_cttimetracker.cttimetrackerid WHERE vtiger_crmentity.deleted = 0 AND vtiger_cttimetracker.tracking_user = '$tracking_user' AND vtiger_cttimetracker.related_to = '$recordid'";
			$resultTime2 = $adb->pquery($getTimeQuery2,array());
			$nooftimetracking = $adb->num_rows($resultTime2);
			if($adb->num_rows($resultTime2) > 0){
				$isTimeTracking = true;
				$time_tracking_record = $adb->query_result($resultTime2,$nooftimetracking-1,'cttimetrackerid');
				$time_tracking_records	= $time_tracking_record;
				$time_tracking_record = vtws_getWebserviceEntityId('CTTimeTracker',$time_tracking_record);
				$tracking_statuss = $adb->query_result($resultTime2,$nooftimetracking-1,'tracking_status');
				if($tracking_statuss == 'Start'){
					$timeControlQuery = "SELECT * FROM vtiger_cttimecontrol INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_cttimecontrol.cttimecontrolid WHERE vtiger_crmentity.deleted = 0 AND vtiger_cttimecontrol.related_tracking = ? ";
					$timeControlResult = $adb->pquery($timeControlQuery,array($time_tracking_records));
					$num_rows = $adb->num_rows($timeControlResult);
					
					$difference = 0;
					for($i=0;$i<$num_rows;$i++) {
						$start_date = $adb->query_result($timeControlResult,$i,'date_start');
						$start_time = $adb->query_result($timeControlResult,$i,'time_start');
						$end_date = $adb->query_result($timeControlResult,$i,'date_end');
						$end_time = $adb->query_result($timeControlResult,$i,'time_end');
						if($end_date == '' && $end_time == ''){
							$end_date = $date_end;
							$end_time = $time_end;
						}

						$startdatetime = strtotime($start_date.' '.$start_time);
					    // calculate the end timestamp
					    $enddatetime = strtotime($end_date.' '.$end_time);
					    // calulate the difference in seconds
					    $difference = $difference + ($enddatetime - $startdatetime);
					}
					$total_time = $difference;
				}else{
					$total_time = $adb->query_result($resultTime2,$nooftimetracking-1,'total_time');
				}
				
			}

			$resultTime = $adb->pquery($getTimeQuery,array());
			if($adb->num_rows($resultTime) > 0){
				$tracking_status = true;
				$cttimetrackerid = $adb->query_result($resultTime,0,'cttimetrackerid');
				$related_to = $adb->query_result($resultTime,0,'related_to');
				$cttimetrackerid = vtws_getWebserviceEntityId('CTTimeTracker',$cttimetrackerid);
				if($related_to == $recordid){
					$isTimeTrackingSameRecord = true;
				}

			}

			$hours = floor($total_time / 3600);
			$minutes = floor(($total_time / 60) % 60);
			$seconds = $total_time % 60;

			//$user_total_time = gmdate("H:i:s", $total_time_user);
			$tracking_time = "$hours:$minutes:$seconds";
		}else{
			$isTimeTrackerModule = false;
		}
		if($module == 'Emails'){
			$resultRecord['recordLabel'] = trim($resultRecord['subject']);
		}else if($module == 'CTTimeTracker'){
			$resultRecord['recordLabel'] = decode_html(decode_html($resultRecord['tracking_title']));
		}
		$default_charset = VTWS_PreserveGlobal::getGlobal('default_charset');
		$resultRecord['recordLabel'] = html_entity_decode($resultRecord['recordLabel'], ENT_QUOTES, $default_charset);
		$modifiedRecord = $this->transformRecordWithGrouping($resultRecord, $module, $isTemplateRecord);
		$modifiedRecord['isTimeTrackerModule'] = $isTimeTrackerModule;
		$modifiedRecord['isTimeTracking'] = $isTimeTracking;
		$modifiedRecord['tracking_status'] = $tracking_status;
		$modifiedRecord['cttimetrackerid'] = $cttimetrackerid;
		$modifiedRecord['time_tracking_record'] = $time_tracking_record;
		$modifiedRecord['tracking_time'] = $tracking_time;
		$modifiedRecord['isTimeTrackingSameRecord'] = $isTimeTrackingSameRecord;

		//for timetracking permission to use
		$checkPermtime = $adb->pquery("SELECT user_setting_type,user_setting_value FROM ctmobile_user_settings WHERE user_setting_type = ? AND user_setting_value = ?",array('time_tracker','1'));
		if($adb->num_rows($checkPermtime) > 0){
			$modifiedRecord['time_tracker_access'] = true;
		}else{
			$modifiedRecord['time_tracker_access'] = false;
		}

		$response->setResult(array('record' => $modifiedRecord));
		
		return $response;
	}
	
	protected function transformRecordWithGrouping($resultRecord, $module, $isTemplateRecord=false) {
		$default_charset = VTWS_PreserveGlobal::getGlobal('default_charset');
		global $adb,$current_user,$site_URL;
		$current_user = $this->getActiveUser();
		$moduleFieldGroups = CTMobile_WS_Utils::gatherModuleFieldGroupInfo($module);
		$recordid = explode("x",$resultRecord['id']);
		$modifiedResult = array();
		$moduleModel = Vtiger_Module_Model::getInstance($module);
		$duplicateAction = $moduleModel->isDuplicateOptionAllowed('CreateView', $recordid[1]);
		//$userPrivModel = Users_Privileges_Model::getInstanceById($current_user->id);
		$editAction = Users_Privileges_Model::isPermitted($module, 'EditView', $recordid[1]);
		
		$deleteAction = Users_Privileges_Model::isPermitted($module, 'Delete', $recordid[1]);
		$ModulesArray = array('SMSNotifier','PBXManager','CTPushNotification','CTCalllog','CTAttendance','Users','CTTimeTracker');
		if(in_array($module,$ModulesArray)){
			$editAction = false;
			$deleteAction = false;
		}
		if(in_array($module,array('Emails','ModComments'))){
			$editAction = false;
		}
		if(in_array($module,array('Emails','ModComments','CTTimeTracker','CTAttendance'))){
			$deleteAction = false;
			$duplicateAction = false;
		}
		$modCommentsModel = Vtiger_Module_Model::getInstance('ModComments');
		$modCommentsFields = array_keys($modCommentsModel->getFields());
		$isAttachmentSupport = false;
		if(in_array('filename', $modCommentsFields)){
			$isAttachmentSupport = true;
		}
		$commentModuleAccess = $modCommentsModel->isPermitted('CreateView');
		$ActivityModuleModel = Vtiger_Module_Model::getInstance('Calendar');
		$ActivityModuleAccess = $ActivityModuleModel->isPermitted('CreateView');

		$newblocks = array();
		$moduleblocks = array_keys($moduleModel->getBlocks());
		foreach ($moduleblocks as $key => $value) {
			$newblocks[$value] = vtranslate($value,$module);
		}
		
		$fieldModels = $moduleModel->getFields();

		if($module == 'Calendar' || $module == 'Events'){
			$recordModel = Vtiger_Record_Model::getInstanceById($recordid[1]);
			$activityType = $recordModel->getType();
			if($activityType == 'Events'){
				$moduleName = 'Events';
			}else{
				$moduleName = 'Calendar';
			}
			$recordModel = Vtiger_Record_Model::getInstanceById($recordid[1],$moduleName);
		}else{
			if($module == 'CTTimeTracker'){
				$IS_AJAX_ENABLED = false;
			}else{
				$recordModel = Vtiger_Record_Model::getInstanceById($recordid[1],$module);
				$IS_AJAX_ENABLED = $recordModel->isEditable();
			}
		}

		$blocks = array(); $labelFields = false;
		if(array_key_exists('filename',$resultRecord)){
		}else{
			if($module == 'Emails'){
				$query = "SELECT * FROM  `vtiger_attachments` INNER JOIN vtiger_seattachmentsrel ON vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid WHERE vtiger_seattachmentsrel.crmid = ?";
				$result = $adb->pquery($query,array($recordid[1]));
				$filename = $adb->query_result($result,0,'name');
				$resultRecord['filename'] = $filename;
			}else{
				$query = "SELECT * FROM  `vtiger_notes` WHERE notesid = ?";
				$result = $adb->pquery($query,array($recordid[1]));
				$filename = $adb->query_result($result,0,'filename');
				$resultRecord['filename'] = $filename;
			}
		}
		$LineItemsFields = array();
		$lineItemsTotalFieldGroup = array();
		$lineItemsTotalFields = array();
		if(in_array($module,array('Invoice','Quotes','SalesOrder','PurchaseOrder'))){
			$taxFields = array();
			$DeductedTaxFields = array();
			$chargesFields = array();
			$deductedFieldGroup = array();
			$inventoryTaxes = Inventory_TaxRecord_Model::getProductTaxes();
			foreach($inventoryTaxes as $tax){
				$taxid = $tax->get('taxid');
				$taxname = $tax->get('taxname');
				if($tax->get('method') == 'Deducted'){
					$DeductedTaxFields[] = array('taxid'=>$taxid,'taxname'=>$taxname);
				}else{
					$taxFields[] = array('taxid'=>$taxid,'taxname'=>$taxname);	
				}
			}
			$ChargeTaxes = Inventory_TaxRecord_Model::getChargeTaxes();
			$ChargeTaxesList = Inventory_Charges_Model::getChargeTaxesList();
			$InventoryCharges = Inventory_Charges_Model::getInventoryCharges();
			foreach ($InventoryCharges as $chargesid => $charges) {
				$chargename = decode_html(decode_html($charges->get('name')));
				$chargename = strtolower(str_replace(' ','_', $chargename));
				$chargesFields[] = $chargename;
				foreach($ChargeTaxes as $taxid => $tax){
					$chargeTaxid = $tax->get('taxid');
					if(in_array($chargeTaxid, array_keys($ChargeTaxesList[$chargesid]))){
						$chargesTaxesFields[] = array('chargeTaxid'=>$chargeTaxid,'chargeTaxname'=>$chargename.'_'.$tax->get('taxname'));	
					}
				}
			}

			$LineItemsFields = array('productid','quantity','listprice','netprice','comment','discount_amount','discount_percent','hdnSubTotal','txtAdjustment','hdnDiscountPercent','hdnDiscountAmount','hdnTaxType','currency_id','hdnS_H_Amount','hdnS_H_Percent','pre_tax_total','hdnGrandTotal','received','paid','balance');
		 	$LineItemsFields = array_merge($LineItemsFields,column_array($taxFields,'taxname'),column_array($DeductedTaxFields,'taxname'),$chargesFields,column_array($chargesTaxesFields,'chargeTaxname'));

			$lineItemsTotalFields = array('hdnSubTotal','txtAdjustment','hdnDiscountPercent','hdnDiscountAmount','hdnTaxType','currency_id','hdnS_H_Amount','hdnS_H_Percent','pre_tax_total','hdnGrandTotal','received','paid','balance');
			$lineItemsTotalFields = array_merge($lineItemsTotalFields,column_array($DeductedTaxFields,'taxname'),$chargesFields,column_array($chargesTaxesFields,'chargeTaxname'));
			if($resultRecord['hdnTaxType'] == 'group'){
				$lineItemsTotalFields = array_merge($lineItemsTotalFields,column_array($taxFields,'taxname'),column_array($DeductedTaxFields,'taxname'),$chargesFields,column_array($chargesTaxesFields,'chargeTaxname'));
			}
		}

		$DocumentsModuleModel = Vtiger_Module_Model::getInstance('Documents');
		if(in_array($DocumentsModuleModel->get('presence'), array('0', '2'))){
			$signFields = array();
			$sign_query = "SELECT * FROM ctmobile_signature_fields WHERE module = ?";
			$sign_result = $adb->pquery($sign_query,array($module));
			$num_rows = $adb->num_rows($sign_result);
			for($i=0;$i<$num_rows;$i++){
				$signatureFields = array();
				$sign_fieldname = $adb->query_result($sign_result,$i,'fieldname');
				$doc_type = $adb->query_result($sign_result,$i,'doc_type');
				$sign_field_array = explode(':',$sign_fieldname);
				$signFields[] = $sign_field_array[2];
			}
		}

		foreach($moduleFieldGroups as $blocklabel => $fieldgroups) {
			$fields = array();
			/* Start: Added by Vijay Bhavsar */
			$query = "SELECT * FROM vtiger_smsnotifier_servers WHERE isactive='1'";
			$result = $adb->pquery($query,array());
			$totalRecords = $adb->num_rows($result);
			if($blocklabel == vtranslate('LBL_ITEM_DETAILS',$module)){
				$fieldgroups['netprice'] = array('label'=>vtranslate('LBL_NET_PRICE'),'uitype'=>'7','summaryfield'=>0,'typeofdata'=>'N~O');
			}
			foreach($fieldgroups as $fieldname => $fieldinfo) {
					if (in_array($fieldname,$signFields)) {
						continue;
					}
					if(in_array($fieldname, $LineItemsFields)){
					if(in_array($fieldname,$lineItemsTotalFields)){
						if($fieldinfo['uitype'] == 15 ||$fieldinfo['uitype'] == 16){
							$values = $resultRecord[$fieldname];
							if($values){
								$values = vtranslate($values,$module);
							}
						}else if($fieldinfo['uitype'] == 72){
							$values = $resultRecord[$fieldname];
							if($values){
								$fieldModel = $fieldModels[$fieldname];
								$values = $fieldModel->getDisplayValue($values);
							}
						}else if($fieldname == 'hdnS_H_Percent' || $fieldname == 'hdnDiscountPercent'){
							$values = $resultRecord[$fieldname];
							if($values){
								$values = Vtiger_Double_UIType::getDisplayValue($values);
							}
						}else if($fieldinfo['uitype'] == 83){
							$values = $resultRecord['LineItems'][0][$fieldname];
						}else{
							$values = $resultRecord[$fieldname];
						}
					}else{
						$values = array();
						foreach($resultRecord['LineItems'] as $key => $value) {
								if($fieldname == 'productid'){
									$productid = explode('x',$value[$fieldname]);
									$proModel = Vtiger_Record_Model::getInstanceById($productid[1]);
									$product_name = $proModel->get('label');
									$deleted = $proModel->get('deleted');
									$deletedMessage = vtranslate('LBL_THIS',$module).' '.vtranslate($value['entity_type'],$value['entity_type']).' '.vtranslate('LBL_IS_DELETED_FROM_THE_SYSTEM_PLEASE_REMOVE_OR_REPLACE_THIS_ITEM',$module);
									$values[] = array('value'=>$value[$fieldname],'label'=>html_entity_decode($product_name, ENT_QUOTES, $default_charset),'refrerenceModule'=>$proModel->getModuleName(),'deleted'=>$value['deleted'],'deletedMessage'=>$deletedMessage);
								}else{
									if($fieldinfo['uitype'] == 71 || $fieldinfo['uitype'] == 72){
										$valuess = $value[$fieldname];
										if($valuess){
											$fieldModel = $fieldModels[$fieldname];
											$values[] = $fieldModel->getDisplayValue($valuess);
										}else{
											$values[] = $valuess;
										}
									}else if($fieldinfo['uitype'] == 83){
										$totalamount = ($value[$fieldname] * $resultRecord['LineItems'][$key]['listprice'])/100;
										$values[] = "".$value[$fieldname]." of ".number_format($resultRecord['LineItems'][$key]['listprice'],$current_user->no_of_currency_decimals,'.','')." = ".number_format($totalamount,$current_user->no_of_currency_decimals,'.','');
									}else if($fieldinfo['uitype'] == 7){
										if($fieldname == 'netprice'){
											$productDetails = $recordModel->getProducts();
											$newKey = $key+1;
											$values[] = $productDetails[$newKey]['netPrice'.$newKey];
										}else{
											$valuess = $value[$fieldname];
											if($valuess){
												$values[] = Vtiger_Double_UIType::getDisplayValue($valuess);
											}else{
												$values[] = $valuess;
											}
										}
									}else{

										$values[] = decode_html(decode_html($value[$fieldname]));
									}
								}
						}
					}
					$field = array(
						'name'  => $fieldname,
						'value' => $values,
						'label' => $fieldinfo['label'],
						'uitype'=> $fieldinfo['uitype'],
						'summaryfield' => $fieldinfo['summaryfield'],
						'typeofdata' => $fieldinfo['typeofdata']
					);

					//code start for isAjaxEdit by suresh
					if($fieldname != 'netprice'){
						$fieldModel = $fieldModels[$fieldname];
						
						$field['is_Ajaxedit'] = false;
						$fieldModelinfo = $fieldModel->getFieldInfo();
						$field['type']['name'] = $fieldModelinfo['type'];
					}else{
						$field['type']['name'] = 'currency';
					}
					
					// code end for isAjaxEdit by suresh
					$fields[] = $field;
				}else{
					// Pickup field if its part of the result
					if(isset($resultRecord[$fieldname])) {
						$fieldModel = $fieldModels[$fieldname];
						$displayType = $fieldModel->get('displaytype');
						$uitypes = $fieldModel->get('uitype');
						$allowedFields = array('time_start','time_end');
						$restrictedDisplayTypes = array(1,2);
						//remove fields if invisible from CRM
						if($fieldModels[$fieldname]->isViewEnabled() != 1){
							continue;
						}
						if(!in_array($displayType,$restrictedDisplayTypes) && !in_array($fieldname,$allowedFields)){
							continue;
						}
						if($module == 'Calendar' && $fieldname == 'time_end'){
							continue;
						}
						$typeofdataArray = array('N~O','N~M','NN~O','NN~M');
						if(($fieldinfo['uitype'] == 72 || $fieldinfo['uitype'] == 1) && in_array($fieldinfo['typeofdata'],$typeofdataArray)) {
							$recordModel = Vtiger_Record_Model::getInstanceById($recordid[1],$module);
							$value = $fieldModel->getDisplayValue($resultRecord[$fieldname], $recordid[1], $recordModel);
							$field = array(
								'name'  => $fieldname,
								'value' => $value,
								'label' => $fieldinfo['label'],
								'uitype'=> $fieldinfo['uitype'],
								'summaryfield' => $fieldinfo['summaryfield'],
								'typeofdata' => $fieldinfo['typeofdata']
							);
						} else {
							if($fieldinfo['uitype'] == 33){
								$value = explode(' |##| ', $resultRecord[$fieldname]);
								$values = '';
								foreach($value as $key => $v){
									if($key+1 == count($value)){
										$values.= $v;
									}else{
										$values.= $v.',';
									}
								}
								$multipicklistvalue = array();
								foreach($value as $v){
									$multipicklistvalue[] = array('label'=>vtranslate($v,$module),'value'=>$v);
								}
								$field = array(
								'name'  => $fieldname,
								'value' => $values,
								'label' => $fieldinfo['label'],
								'uitype'=> $fieldinfo['uitype'],
								'summaryfield' => $fieldinfo['summaryfield'],
								'typeofdata' => $fieldinfo['typeofdata']
								);
								$field['type']['defaultValue'] = $multipicklistvalue;
							}else if(($fieldname =='time_start' || $fieldname =='time_end') && ($module == 'Events' || $module == 'Calendar')){
								if($fieldname == 'time_start'){
									$value = $resultRecord['date_start'].' '.$resultRecord['time_start'];
									$value = Vtiger_Datetime_UIType::getDisplayValue($value);
									$DATETIMEVALUE = explode(' ',$value);
									if(count($DATETIMEVALUE) > 2){
										$values = $DATETIMEVALUE[1].' '.$DATETIMEVALUE[2];
									}else{
										$values = $DATETIMEVALUE[1];
									}
								}else{
									if(!empty($resultRecord['due_date'])){
										$value = $resultRecord['due_date'].' '.$resultRecord['time_end'];
									}else{
										$value = $date->format('Y-m-d').' '.$resultRecord['time_end'];
									}
									$value = Vtiger_Datetime_UIType::getDisplayValue($value);
									$DATETIMEVALUE = explode(' ',$value);
									if(count($DATETIMEVALUE) > 2){
										$values = $DATETIMEVALUE[1].' '.$DATETIMEVALUE[2];
									}else{
										$values = $DATETIMEVALUE[1];
									}
								}
								$field = array(
								'name'  => $fieldname,
								'value' => $values,
								'label' => $fieldinfo['label'],
								'uitype'=> (string)$fieldinfo['uitype'],
								'summaryfield' => $fieldinfo['summaryfield'],
								'typeofdata' => $fieldinfo['typeofdata']
							   );
							}else if($fieldinfo['uitype'] == 71 || $fieldinfo['uitype'] == 30){
								if($fieldinfo['uitype'] == 71){
									$value = CurrencyField::convertToUserFormat($resultRecord[$fieldname]);
								}else{
									$value = CurrencyField::convertToUserFormat($resultRecord[$fieldname],null, true);
								}
								$field = array(
								'name'  => $fieldname,
								'value' => $value,
								'label' => $fieldinfo['label'],
								'uitype'=> (string)$fieldinfo['uitype'],
								'summaryfield' => $fieldinfo['summaryfield'],
								'typeofdata' => $fieldinfo['typeofdata']
							   );
							  
							   if($fieldname =='reminder_time' && $resultRecord['reminder_time'] == 0){
								  $field['reminder_value'] = array('days'=>0,'hours'=>0,'minutes'=>0);
							   }else{
							   	   $reminder = $resultRecord['reminder_time'];
								   $minutes = (int)($reminder)%60;
								   $hours = (int)($reminder/(60))%24;
								   $days =  (int)($reminder/(60*24));
								   $field['reminder_value'] = array('days'=>$days,'hours'=>$hours,'minutes'=>$minutes);
								   
							   }
							}else if($fieldinfo['uitype'] == 69){
								$AttachmentQuery =$adb->pquery("select vtiger_attachments.attachmentsid, vtiger_attachments.name, vtiger_attachments.subject, vtiger_attachments.path FROM vtiger_seattachmentsrel
												INNER JOIN vtiger_attachments ON vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid  
												WHERE vtiger_seattachmentsrel.crmid = ?", array($recordid[1]));
												
								$AttachmentQueryCount = $adb->num_rows($AttachmentQuery);
								$document_path = array();
								
								if($AttachmentQueryCount > 0) {
									$name = $adb->query_result($AttachmentQuery, 0, 'name');
									$Path = $adb->query_result($AttachmentQuery, 0, 'path');
									$attachmentsId = $adb->query_result($AttachmentQuery, 0, 'attachmentsid');
									$ImageUrl = $site_URL.$Path.$attachmentsId."_".$name;
									$value = $name;
								} else {
									$ImageUrl = "";
									$value = "";
								}
								$field = array(
								'name'  => $fieldname,
								'value' => $value,
								'ImageUrl'=>$ImageUrl,
								'label' => $fieldinfo['label'],
								'uitype'=> (string)$fieldinfo['uitype'],
								'summaryfield' => $fieldinfo['summaryfield'],
								'typeofdata' => $fieldinfo['typeofdata']
							   );
							}else if($fieldinfo['typeofdata'] == 'T~O' || $fieldinfo['typeofdata'] == 'T~M'){
								$field = array(
								'name'  => $fieldname,
								'value' => $resultRecord[$fieldname],
								'label' => $fieldinfo['label'],
								'uitype'=> $fieldinfo['uitype'],
								'summaryfield' => $fieldinfo['summaryfield'],
								'typeofdata' => $fieldinfo['typeofdata']
							   );
							   $field['value'] = Vtiger_Util_Helper::convertTimeIntoUsersDisplayFormat($resultRecord[$fieldname]);
							}else{
								$refrenceUitypes = array(10,51,57,58,59,66,73,75,76,78,80,81,101);
								$field = array(
								'name'  => $fieldname,
								'value' => $resultRecord[$fieldname],
								'label' => $fieldinfo['label'],
								'uitype'=> $fieldinfo['uitype'],
								'summaryfield' => $fieldinfo['summaryfield'],
								'typeofdata' => $fieldinfo['typeofdata']
							   );
							   if(in_array($fieldinfo['uitype'],$refrenceUitypes)){
								   if($resultRecord[$fieldname]['value']){
										$refrerenceModule = CTMobile_WS_Utils::detectModulenameFromRecordId($resultRecord[$fieldname]['value']);
										if($refrerenceModule == 'Calendar'){
											$relatedCRMid = substr($resultRecord[$fieldname]['value'], stripos($resultRecord[$fieldname]['value'], 'x')+1);
											$EventTaskQuery = $adb->pquery("SELECT * FROM  `vtiger_activity` WHERE activitytype = ? AND activityid = ?",array('Task',$relatedCRMid)); 
											if($adb->num_rows($EventTaskQuery) > 0){
												$wsid = CTMobile_WS_Utils::getEntityModuleWSId('Calendar');
												$resultRecord[$fieldname]['value'] = $wsid.'x'.$relatedCRMid;
												$field['refrerenceModule'] = 'Calendar';
											}else{
												$wsid = CTMobile_WS_Utils::getEntityModuleWSId('Events');
												$resultRecord[$fieldname]['value'] = $wsid.'x'.$relatedCRMid;
												$field['refrerenceModule'] = 'Events';
											}
											$field['value'] = $resultRecord[$fieldname];
										}else{
											$field['refrerenceModule'] = $refrerenceModule;
										}
								   }else{
									   $field['refrerenceModule'] = "";
								   }
							   }
							   
							}
							
						}
						if($fieldinfo['uitype'] == 15 || $fieldinfo['uitype'] == 16){
							$field['value'] =  vtranslate($resultRecord[$fieldname],$module);
						}
						if($fieldname == 'recurringtype'){
							$field['value'] = CTMobile_WS_Utils::RecurringDetails($recordid[1],$module);
						}
						if($fieldname == 'filename'){
							if($module == 'Emails'){
								$ImageUrl = array();
								$query = "SELECT * FROM vtiger_attachments INNER JOIN vtiger_seattachmentsrel ON vtiger_seattachmentsrel.attachmentsid=vtiger_attachments.attachmentsid WHERE vtiger_seattachmentsrel.crmid=?";
								$result = $adb->pquery($query,array($recordid[1]));
								for($i=0;$i<$adb->num_rows($result);$i++){
									$filename = $adb->query_result($result,$i,'name');
									$attachmentsid = $adb->query_result($result,$i,'attachmentsid');
									$path = $adb->query_result($result,$i,'path');
									$filepath = $site_URL.$path.$attachmentsid.'_'.$filename;
									if(!empty($filename)){
										$file_URL = $site_URL.'modules/CTMobile/api/ws/DownloadUrl.php?record='.$attachmentsid;
										$ImageUrl[] = array('fileid'=>$attachmentsid,'filename'=>$filename,'filepath'=>$filepath,'Download_URL'=>$file_URL);
									}
								}
								$field['EmailAttachmentList'] = $ImageUrl;
								$field['value'] = "";
							}else{
								$query = "SELECT * FROM vtiger_attachments INNER JOIN vtiger_seattachmentsrel ON vtiger_seattachmentsrel.attachmentsid=vtiger_attachments.attachmentsid WHERE vtiger_seattachmentsrel.crmid=?";
								$result = $adb->pquery($query,array($recordid[1]));
								$filename = $adb->query_result($result,0,'name');
								$attachmentsid = $adb->query_result($result,0,'attachmentsid');
								$path = $adb->query_result($result,0,'path');
								//$filepath = $site_URL.'modules/CTMobile/api/ws/DownloadUrl.php?record='.$attachmentsid;
								$filepath = $site_URL.$path.$attachmentsid.'_'.$filename;
								if(!empty($filename)){
									$field['ImageUrl'] = $filepath;	
									$field['value'] = $filename;
									$field['Download_URL'] = $site_URL.'modules/CTMobile/api/ws/DownloadUrl.php?record='.$attachmentsid;
								}else{
									$field['ImageUrl'] = "";  
									$field['value'] = "";
									$field['Download_URL'] = "";
								}
							}
						}
						
						
						// Template record requested send more details if available
						if ($isTemplateRecord) {
							$describeFieldInfo = $this->cachedDescribeFieldInfo($fieldname);
							if ($describeFieldInfo) {
								foreach($describeFieldInfo as $k=>$v) {
									if (isset($field[$k])) continue;
									$field[$k] = $v;
								}
							}
							// Entity fieldnames
							$labelFields = $this->cachedEntityFieldnames($module);
						}
						// Fix the assigned to uitype
						if ($field['uitype'] == '53') {
							$field['type']['defaultValue'] = array('value' => "19x{$current_user->id}", 'label' => $current_user->column_fields['last_name']);
						} else if($field['uitype'] == '117') {
							$field['type']['defaultValue'] = trim($field['value']);
						}
	               		// Special case handling to pull configured Terms & Conditions given through webservices.
						else if($field['name'] == 'terms_conditions' && in_array($module, array('Quotes','Invoice', 'SalesOrder', 'PurchaseOrder'))){ 
	   						$field['type']['defaultValue'] = trim($field['value']); 
	                    }else if($field['name'] == 'date_start' && ($module == 'Events' || $module == 'Calendar')){
								$startDateTime = Vtiger_Datetime_UIType::getDisplayDateTimeValue($resultRecord['date_start'].' '.$resultRecord['time_start']);
								$DateTime = explode(' ', $startDateTime);
								$field['value'] = $DateTime[0];
						}else if($field['name'] == 'due_date' && ($module == 'Events' || $module == 'Calendar')){
								$endDateTime = Vtiger_Datetime_UIType::getDisplayDateTimeValue($resultRecord['due_date'].' '.$resultRecord['time_end']);
								$DateTime = explode(' ', $endDateTime);
								$field['value'] = $DateTime[0];
						}else if($field['uitype'] == '70' ) {
							if($field['value']!=''){
								$recordModel = Vtiger_Record_Model::getInstanceById($recordid[1],$module);
								$userDateTimeString = $fieldModel->getDisplayValue($resultRecord[$fieldname], $recordid[1], $recordModel);
								$field['value'] = $userDateTimeString;
								
							}
							
						}else if($field['uitype'] == '9'){
							if($field['value']!=''){
								$field['value'] = Vtiger_Double_UIType::getDisplayValue($field['value']);
								
							}
						}else if($field['uitype'] == '5'  ) {
							if($field['value']!=''){
								$field['value'] = Vtiger_Date_UIType::getDisplayDateValue($field['value']);
								
							}
							
						}else if( $field['uitype'] == '6' ) {
							if($field['value']!=''){
								$field['value'] = Vtiger_Date_UIType::getDisplayDateValue($field['value']);
								
							}
							
						}else if($field['uitype'] == '23' ) {
							if($field['value']!=''){
								$field['value'] = Vtiger_Date_UIType::getDisplayDateValue($field['value']);
								
							}
							
						}
						if(array_key_exists('label',$field['value'])){
							if($field['value']['label']){
								$field['value']['label'] = html_entity_decode($field['value']['label'], ENT_QUOTES, $default_charset);
							}
						}else{
							if($field['name'] == 'description'){
								$field['value'] =  decode_html(decode_html(trim(strip_tags($field['value']))));
							}else{
								$field['value'] = html_entity_decode($field['value'], ENT_QUOTES, $default_charset);
							}
						}

						//code start for isAjaxEdit by suresh
						if($IS_AJAX_ENABLED && $fieldModel->isEditable() == 'true' && $fieldModel->isAjaxEditable() == 'true' && $field['name'] != 'imagename' && !in_array($module,array('Documents','Emails','SMSNotifier','PBXManager','CTTimeTracker','CTRoutePlanning','CTTimeControl','CTRouteAttendance','CTAttendance','CTPushNotification'))){
							$field['is_Ajaxedit'] = true;
							$fieldModelinfo = $fieldModel->getFieldInfo();
							$field['type']['name'] = $fieldModelinfo['type'];
							if($field['type']['name'] == 'salutation'){
								$field['type']['name'] = "string";
							}
							if($fieldModelinfo['type'] == 'picklist' || $fieldModelinfo['type'] == 'multipicklist') {
								$roleid = $current_user->roleid;
								$picklistValues1 = array();
								if($field['uitype'] == 15 || $field['uitype'] == 16){
									$picklistValues1[] = array('value'=>"", 'label'=>vtranslate('LBL_SELECT_OPTION',$module));
								}
								if($fieldModel->isRoleBased()){
									$picklistValues = Vtiger_Util_Helper::getRoleBasedPicklistValues($field['name'],$roleid);
								}else{
									$picklistValues = Vtiger_Util_Helper::getPickListValues($field['name']);
								}
								foreach($picklistValues as $pvalue){
									if($pvalue != ''){
										$picklistValues1[] = array('value'=>$pvalue, 'label'=>vtranslate($pvalue,$module));
									}
								}
								$field['type']['picklistValues'] = $picklistValues1;
							}
							if($fieldModelinfo['type'] == 'reference'){
								$refModules = $fieldModel->getReferenceList();
								$refModule = array();
								foreach ($refModules as $key => $value) {
									$refModule[] = array('value'=>$value,'label'=>vtranslate($value,$value));
								}
								$field['type']['refersTo'] = $refModule;
							}
						}else{
							$field['is_Ajaxedit'] = false;
							$fieldModelinfo = $fieldModel->getFieldInfo();
							$field['type']['name'] = $fieldModelinfo['type'];
							if($field['type']['name'] == 'salutation'){
								$field['type']['name'] = "string";
							}
							if($fieldModelinfo['type'] == 'picklist' || $fieldModelinfo['type'] == 'multipicklist') {
								$roleid = $current_user->roleid;
								$picklistValues1 = array();
								if($field['uitype'] == 15 || $field['uitype'] == 16){
									$picklistValues1[] = array('value'=>"", 'label'=>vtranslate('LBL_SELECT_OPTION',$module));
								}
								if($fieldModel->isRoleBased()){
									$picklistValues = Vtiger_Util_Helper::getRoleBasedPicklistValues($field['name'],$roleid);
								}else{
									$picklistValues = Vtiger_Util_Helper::getPickListValues($field['name']);
								}
								foreach($picklistValues as $pvalue){
									if($pvalue != ''){
										$picklistValues1[] = array('value'=>$pvalue, 'label'=>vtranslate($pvalue,$module));
									}
								}
								$field['type']['picklistValues'] = $picklistValues1;
							}
							if($fieldModelinfo['type'] == 'reference'){
								$refModules = $fieldModel->getReferenceList();
								$refModule = array();
								foreach ($refModules as $key => $value) {
									$refModule[] = array('value'=>$value,'label'=>vtranslate($value,$value));
								}
								$field['type']['refersTo'] = $refModule;
							}

						}
						$field['mandatory'] = $fieldModel->isMandatory();
						// code end for isAjaxEdit by suresh
						$field['label'] = decode_html(decode_html($field['label']));
						$fields[] = $field;
					}
				}
				
			}
			$permittedFields = array();
			foreach ($fields as $key => $field) {
				if(in_array($field['name'], $lineItemsTotalFields)){
					if($field['name'] == 'hdnTaxType'){
						$field['value'] = vtranslate('LBL_'.strtoupper($resultRecord['hdnTaxType']),$module);
						$count = 0;
					}
					if($field['name'] == 'currency_id'){
						$field['type']['name'] = 'picklist';
						$field['value'] = vtranslate($resultRecord['currency_id']['label'],$module);
						$count = 1;
					}
					if($field['name'] == 'hdnSubTotal'){
						$count = 2;
					}
					if($field['name'] == 'hdnDiscountAmount'){
						$count = 3;
					}
					if($field['name'] == 'hdnDiscountPercent'){
						$count = 4;
					}
					if($field['name'] == 'hdnS_H_Amount'){
						$count = 5;
					}
					if($field['name'] == 'pre_tax_total'){
						$count = 6;
					}
					
					if($resultRecord['hdnTaxType'] == 'group'){
						foreach ($taxFields as $keys => $taxes) {
							$taxid = $taxes['taxid'];
							$taxname = $taxes['taxname'];
							if($field['name'] == $taxname){
								$count = 7 + $keys;
								$field['value'] = $resultRecord['LineItems_FinalDetails'][1]['final_details']['taxes'][$taxid]['percentage']." of ".number_format($resultRecord['hdnSubTotal'],$current_user->no_of_currency_decimals,'.','')." = ".$resultRecord['LineItems_FinalDetails'][1]['final_details']['taxes'][$taxid]['amount'];
							}
						}
					}
					foreach ($DeductedTaxFields as $keys => $taxes) {
						$taxid = $taxes['taxid'];
						$taxname = $taxes['taxname'];
						if($field['name'] == $taxname){
							$field['value'] = $resultRecord['LineItems_FinalDetails'][1]['final_details']['deductTaxes'][$taxid]['percentage']." of ".number_format($resultRecord['hdnSubTotal'],$current_user->no_of_currency_decimals,'.','')." = ".$resultRecord['LineItems_FinalDetails'][1]['final_details']['deductTaxes'][$taxid]['amount'];
							$deductedFieldGroup[] = $field;
							unset($field[$key]);
							unset($field);
						}

					}
					if($field['name'] == 'hdnS_H_Percent'){
						$count = 8 + count($taxFields);
						$field['value'] = $resultRecord['LineItems_FinalDetails'][1]['final_details']['shtax_totalamount'];
					}
					foreach ($chargesFields as $keys => $taxname) {
						if($field['name'] == $taxname){
							$count = 9 + count($taxFields) + $keys;
						}
					}
					if($field['name'] == 'txtAdjustment'){
						$count = 9 + count($taxFields) + count($chargesFields) +1;
					}
					if($field['name'] == 'hdnGrandTotal'){
						$count = 9 + count($taxFields) + count($chargesFields) +2;
					}
					if($field['name'] == 'received'){
						$count = 9 + count($taxFields) + count($chargesFields) +3;
					}
					if($field['name'] == 'paid'){
						$count = 9 + count($taxFields) + count($chargesFields) +3;
					}
					if($field['name'] == 'balance'){
						$count = 9 + count($taxFields) + count($chargesFields) +4;
					}
					if($field != null){
						$lineItemsTotalFieldGroup[$count] = $field;
						unset($fields[$key]);
					}
				}else{
					$permittedFields[] = $field;
				}

			}

			$blockname = array_search($blocklabel,$newblocks);
			$blocklabel = html_entity_decode($blocklabel, ENT_QUOTES, $default_charset);
			$blocks[] = array('name'=>$blockname,'label' => $blocklabel, 'fields' => $permittedFields );
		}



		//code start for GEO Auto Fill Field
        if(in_array($module, array('Contacts','Leads','Accounts','Calendar','Events'))){
        	if($module == 'Events'){
 				$GeoModule = 'Calendar';
        	}else{
        		$GeoModule = $module;
        	}
        	$GeoQuery = "SELECT * FROM `ctmobile_address_autofields` WHERE module = ?";
        	$GeoResult = $adb->pquery($GeoQuery,array($GeoModule));
        	if($adb->num_rows($GeoResult) > 0){
        		$auto_search = $adb->query_result($GeoResult,0,'auto_search');
        		$street = $adb->query_result($GeoResult,0,'street');
        		$city = $adb->query_result($GeoResult,0,'city');
        		$state = $adb->query_result($GeoResult,0,'state');
        		$postalcode = $adb->query_result($GeoResult,0,'postalcode');
        		$country = $adb->query_result($GeoResult,0,'country');

        		if($auto_search == ''){
        			$auto_search = $street;
        		}

        		foreach ($blocks as $key => $block) {
        			foreach ($block['fields'] as $keys => $fields) {
	        			if($fields['name'] == $auto_search){
	        				$blocks[$key]['fields'][$keys]['isDisplayMap'] = true;
	        			}else{
	        				$blocks[$key]['fields'][$keys]['isDisplayMap'] = false;
	        			}
	        			if($fields['name'] == $street){
	        				$blocks[$key]['fields'][$keys]['GeoFields'] = true;
	        				$blocks[$key]['fields'][$keys]['GeoFieldsName'] = 'street';
	        			}else if($fields['name'] == $city){
	        				$blocks[$key]['fields'][$keys]['GeoFields'] = true;
	        				$blocks[$key]['fields'][$keys]['GeoFieldsName'] = 'city';
	        			}else if($fields['name'] == $state){
	        				$blocks[$key]['fields'][$keys]['GeoFields'] = true;
	        				$blocks[$key]['fields'][$keys]['GeoFieldsName'] = 'state';
	        			}else if($fields['name'] == $postalcode){
	        				$blocks[$key]['fields'][$keys]['GeoFields'] = true;
	        				$blocks[$key]['fields'][$keys]['GeoFieldsName'] = 'postalcode';
	        			}else if($fields['name'] == $country){
	        				$blocks[$key]['fields'][$keys]['GeoFields'] = true;
	        				$blocks[$key]['fields'][$keys]['GeoFieldsName'] = 'country';
	        			}else{
	        				$blocks[$key]['fields'][$keys]['GeoFields'] = false;
	        			}
	        		}
        		}
        	}
        }else{
        	foreach ($blocks as $key => $block) {
				foreach ($block['fields'] as $keys => $fields) {
        			$blocks[$key]['fields'][$keys]['GeoFields'] = false;
        		}
        	}
        }
		//code End
		
		if(!empty($lineItemsTotalFieldGroup)){
			ksort($lineItemsTotalFieldGroup);
			$totalFields = array();
			foreach ($lineItemsTotalFieldGroup as $key => $value) {
				$totalFields[] = $value;
			}
			$blocks[] = array('name'=>'ITEMS_DETAILS_TOTAL','label' => vtranslate('Items Detail Total','CTMobile'), 'fields' => $totalFields );
		}
		if(in_array($module,array('Invoice','Quotes','SalesOrder','PurchaseOrder'))){
			$totalFields = array();
			foreach ($deductedFieldGroup as $key => $value) {
				$totalFields[] = $value;
			}
			$blocks[] = array('name'=>'LBL_DEDUCTED_TAXES','label' => vtranslate('LBL_DEDUCTED_TAXES',$module), 'fields' => $totalFields );
		}

		if($module == 'Emails'){
			foreach($blocks as $key => $value){
				if($value['label'] == 'Emails_Block1' || $value['label'] == 'Emails_Block2' || $value['label'] == 'Emails_Block3'){
					foreach($value['fields'] as $keys => $field){
						$blocks[0]['fields'][] = $field;
					}
					unset($blocks[$key]);
				}
			}
		}
		
		$sections = array();
		$moduleFieldGroupKeys = array_keys($moduleFieldGroups);
		foreach($moduleFieldGroupKeys as $blocklabel) {
			// Eliminate empty blocks
			if(isset($groups[$blocklabel]) && !empty($groups[$blocklabel])) {
				$sections[] = array( 'label' => $blocklabel, 'count' => count($groups[$blocklabel]) );
			}
		}
		
		$recordLabel = html_entity_decode($resultRecord['recordLabel'], ENT_QUOTES, $default_charset);

		//code start for signature and Documents
		$userPrivModel = Users_Privileges_Model::getInstanceById($current_user->id);
		$DocumentsModuleModel = Vtiger_Module_Model::getInstance('Documents');
		$DocumentsCreateAction = $userPrivModel->hasModuleActionPermission($DocumentsModuleModel->getId(), 'CreateView');

		if(in_array($DocumentsModuleModel->get('presence'), array('0', '2'))){
			$sign_query = "SELECT * FROM ctmobile_signature_fields WHERE module = ?";
			$sign_result = $adb->pquery($sign_query,array($module));
			$num_rows = $adb->num_rows($sign_result);
			for($i=0;$i<$num_rows;$i++){
				$signatureFields = array();
				$sign_fieldname = $adb->query_result($sign_result,$i,'fieldname');
				$doc_type = $adb->query_result($sign_result,$i,'doc_type');
				$sign_field_array = explode(':',$sign_fieldname);
				$SignField = $sign_field_array[2];

				$fieldModel = $fieldModels[$SignField];
				$createAction = false;
				$signdeleteAction =  false;
				if($fieldModel->isEditable() == true && $editAction){
					$signdeleteAction = true;
				}
				if($fieldModel->isEditable() == true && $editAction && $DocumentsCreateAction){
					$createAction = true;
				}

				$sign_fieldlabel = $adb->pquery("SELECT * FROM vtiger_field WHERE columnname = ? and tabid= ?",array($SignField,getTabid($module)));
				$sign_fieldlabel = $adb->query_result($sign_fieldlabel,0,'fieldlabel');
				$sign_fieldlabel = vtranslate($sign_fieldlabel,$module);
				if($doc_type == 'Documents'){
					$value = array();
					$filevalues = explode(',', $resultRecord[$SignField]);
					foreach ($filevalues as $key => $fvalue) {
						$name = basename($fvalue);
						$fileData = explode("_", $name);
						$fileid = $fileData[0];
						if(is_numeric($fileid)){
							$filesdata = $this->getAttachments($fileid,$signdeleteAction);
							if ($filesdata != false) {
								$value[] = $filesdata;
							}
						}
					}
				}else{
					$value = "";
					$fileid = "";
					$fvalue = $resultRecord[$SignField];
					$name = basename($fvalue);
					$fileData = explode("_", $name);
					$fileid = $fileData[0];
					if(is_numeric($fileid)){
						$filesdata = $this->getAttachments($fileid,$signdeleteAction);
						if ($filesdata != false) {
							$value = $filesdata['file_URL'];
							$fileid = $filesdata['fileid'];
						}else{
							$fileid = "";
							$createAction = false;
						}
					}
				}

				$blocklabel = decode_html(decode_html($sign_fieldlabel));
				if($doc_type == 'Documents'){
					$signatureFields[] = array('name'=>$SignField,'label'=>$sign_fieldlabel,'uitype'=>'19','summaryfield'=>'0','typeofdata'=>'V~O','is_Ajaxedit'=>false,'type'=>array('name'=>'text'),'fieldType'=>$doc_type,'value'=>$value);
				}else{
					$signatureFields[] = array('name'=>$SignField,'label'=>$sign_fieldlabel,'uitype'=>'19','summaryfield'=>'0','typeofdata'=>'V~O','is_Ajaxedit'=>false,'type'=>array('name'=>'text'),'fieldType'=>$doc_type,'value'=>$value,'fileid'=>$fileid);
				}
				if($fieldModel->isViewable()){
					$blocks[] = array('name'=>'LBL_SIGNATURE_INFORMATION','label' => $blocklabel,'fieldType'=>$doc_type, 'fields' => $signatureFields,'createAction'=>$createAction);
				}
			}
		}
		

		//code end

		if($module == 'Events') {
			$recordId = explode('x',$resultRecord['id']);
			
			$getInvites = $adb->pquery("SELECT * FROM vtiger_invitees where activityid = ?", array($recordId[1]));
			$countInvities = $adb->num_rows($getInvites);
			$id = ''; // for Detailview
			$invite_user_value = array(); //for Editview
			for($i=0;$i<$countInvities;$i++){
				$inviteId = $adb->query_result($getInvites, $i, 'inviteeid');
				$userRecordModel = Vtiger_Record_Model::getInstanceById($inviteId, 'Users');
				$firstname = $userRecordModel->get('first_name');
				$firstname = html_entity_decode($firstname, ENT_QUOTES, $default_charset);
				$lastname = $userRecordModel->get('last_name');
				$lastname = html_entity_decode($lastname, ENT_QUOTES, $default_charset);
				if($i == 0) {
					$id .= $firstname." ".$lastname;
				} else {
					$id .= ", ".$firstname." ".$lastname;
				}
				$invite_user_value[] = array('value'=>$inviteId,'label'=>$firstname." ".$lastname);
			}
			
			$invitefields[] = array('name'=>'invite_user', 'value'=>$id,'invite_user_value'=>$invite_user_value, 'label' => vtranslate('LBL_INVITE_USERS',$module), 'uitype' => '33', 'summaryfield' => '0', 'typeofdata' => 'V~O','is_Ajaxedit' => false);
			$blocks[] = array('name'=>'INVITE_USER','label' => vtranslate("LBL_INVITE_USER_BLOCK",$module), 'fields'=> $invitefields);
		}
		
		if($module == 'Leads' || $module == 'Contacts'){

			if($totalRecords > 0){
				$sms_notifier = true;
				$sms_status_message = '';
			}else{
				$sms_notifier = false;
				$sms_status_message = vtranslate('You do not configure SMS Notifier in CRM. Please configure SMS Notifier in your CRM to use this feature.','CTMobile');
			}	
			$modifiedResult = array('blocks' => $blocks, 'id' => $resultRecord['id'], 'recordLabel' => $recordLabel,'sms_notifier'=>$sms_notifier,'sms_status_message'=>$sms_status_message,'editAction'=>$editAction,'deleteAction'=>$deleteAction,'duplicateAction'=>$duplicateAction,'commentModuleAccess'=>$commentModuleAccess,'ActivityModuleAccess'=>$ActivityModuleAccess);
			if($module == 'Leads'){
				$recordModel = Vtiger_Record_Model::getInstanceById($recordid[1],$module);
				if(Users_Privileges_Model::isPermitted($moduleModel->getName(), 'ConvertLead', $recordModel->getId()) && Users_Privileges_Model::isPermitted($moduleModel->getName(), 'EditView', $recordModel->getId()) && !$recordModel->isLeadConverted()){
					$ConvertLead = true;
				}else{
					$ConvertLead = false;
				}
				$modifiedResult['ConvertLead'] = $ConvertLead;
			}
		}else{
			$modifiedResult = array('blocks' => $blocks, 'id' => $resultRecord['id'], 'recordLabel' => $recordLabel,'editAction'=>$editAction,'deleteAction'=>$deleteAction,'duplicateAction'=>$duplicateAction,'commentModuleAccess'=>$commentModuleAccess,'ActivityModuleAccess'=>$ActivityModuleAccess);
		}
		//code for image url
		$parentRecordModel = Vtiger_Record_Model::getInstanceById($recordid[1], $module);
		$imageDetails = $parentRecordModel->getImageDetails();
		if(!empty($imageDetails)){
			$modifiedResult['ImageUrl'] = $site_URL.$imageDetails[0]['path'].'_'.$imageDetails[0]['name'];
		}else{
			$modifiedResult['ImageUrl'] = "";
		}
		$modifiedResult['isAttachmentSupport'] = $isAttachmentSupport;
		
		$checkShortcut = $adb->pquery("SELECT shortcutid FROM ctmobile_record_shortcut WHERE recordid = ? AND userid = ? AND module = ? ",array($recordid[1],$current_user->id,$module));
		if($adb->num_rows($checkShortcut) == 0){
			$modifiedResult['recordShortcut'] = true;
		}else{
			$modifiedResult['recordShortcut'] = false;
		}

		$modifiedResult['modulename'] = $module;
		$modifiedResult['modulelabel'] = vtranslate($module,$module);
		
		//code start for check in checkout for Events
		if($module == 'Events'){
			$attendance_data = $this->attendance_status($recordid[1]);
			$modifiedResult['ctattendance_status'] = $attendance_data['ctattendance_status'];
			$modifiedResult['attendance_status'] = $attendance_data['attendance_status'];
			if($attendance_data['ctattendanceid'] != ''){
				$modifiedResult['ctattendanceid'] = CTMobile_WS_Utils::getEntityModuleWSId('CTAttendance').'x'.$attendance_data['ctattendanceid'];
			}else{
				$modifiedResult['ctattendanceid'] = $attendance_data['ctattendanceid'];
			}

			$latlongData = $this->getLatLongOfRecord($recordid[1]);
			$modifiedResult['latitude'] = $latlongData['lat'];
			$modifiedResult['longitude'] = $latlongData['long'];
		}
		//code End for check in checkout for Events

		//Code start for PDF Maker
		$isDownloadPDF = false;
		$isEmailPDF = false;
		$PDFType = array();
		$checkPDFMaker = $adb->pquery("SELECT * FROM vtiger_pdfmaker WHERE module = ? AND deleted = 0",array($module));
		if($adb->num_rows($checkPDFMaker) > 0){
			$isDownloadPDF = true;
			$isEmailPDF = true;
			$PDFType[] = array("value"=>'PDFMaker',"label"=>vtranslate('PDFMaker'));
			if(in_array($module, array('Invoice','Quotes','PurchaseOrder','SalesOrder'))){
				$PDFType[] = array("value"=>'Default PDF',"label"=>vtranslate('Default PDF'));
			}
		}else if(in_array($module, array('Invoice','Quotes','PurchaseOrder','SalesOrder'))){
			$isDownloadPDF = true;
			$isEmailPDF = true;
			$PDFType[] = array("value"=>'Default PDF',"label"=>vtranslate('Default PDF'));
		}
		$modifiedResult['toMailNamesList'] = $this->getEmailRecieptent($recordid[1],$module);
		$modifiedResult['isDownloadPDF'] = $isDownloadPDF;
		$modifiedResult['isEmailPDF'] = $isEmailPDF;
		$modifiedResult['PDFType'] = $PDFType;
		//Code End
		if($labelFields) $modifiedResult['labelFields'] = $labelFields;
		
		return $modifiedResult;
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

	function attendance_status($recordid){
		global $current_user,$adb, $site_URL;
		$current_user = $this->getActiveUser();
		$employee_name = $current_user->id;

		$user =  Users::getActiveAdminUser();
		$recentEvent_data = array();
		$generator = new QueryGenerator('CTAttendance', $user);
		$generator->setFields(array('employee_name','attendance_status','createdtime','modifiedtime','id'));
		//$generator->addCondition('attendance_status', 'check_in', 'e');
		$eventQuery = $generator->getQuery();
		$eventQuery .= " AND vtiger_ctattendance.employee_name = '$employee_name' AND vtiger_ctattendance.eventid = '$recordid'";
		
		$query = $adb->pquery($eventQuery);
		$num_rows = $adb->num_rows($query);
		if( $num_rows > 0){
			$ctattendanceid = $adb->query_result($query,$num_rows-1,'ctattendanceid');
			$ctattendance_status = $adb->query_result($query,$num_rows-1,'attendance_status');
			$attendance_status = true;
		} else {
			$ctattendance_status = "";
			$attendance_status = false;
			$ctattendanceid = '';
		}
		$data = array();
		$data['attendance_status'] = vtranslate($ctattendance_status,'CTAttendance');
		$data['ctattendance_status'] = $attendance_status;
		$data['ctattendanceid'] = $ctattendanceid;
		if($ctattendance_status == 'check_out'){
			$data['ctattendance_status'] = false;
			$data['ctattendanceid'] = "";
		}
		return $data;
	}

	function getAttachments($fileid,$signdeleteAction){
		global $adb,$site_URL,$current_user;
		$current_user = $this->getActiveUser();
		$query = "SELECT * FROM vtiger_attachments INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_attachments.attachmentsid INNER JOIN vtiger_seattachmentsrel ON vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid WHERE vtiger_crmentity.deleted = 0 AND vtiger_attachments.attachmentsid = ?";
		$params = array($fileid);
		$result = $adb->pquery($query,$params);
		$data['fileid'] = "";
		$data['file_URL'] = "";
		$data['filename'] = "";
		$data["ext"] = "";
		$data['deleteAction'] = false;
		$deleteAction = false;
		if($adb->num_rows($result) > 0){
			$crmid = $adb->query_result($result,0,'crmid');
			if(Users_Privileges_Model::isPermitted('Documents', 'DetailView', $crmid)){
				$name = $adb->query_result($result,0,'name');
				$path = $adb->query_result($result,0,'path');
				$data['fileid'] = $fileid;
				$data['file_URL'] = $site_URL.$path.$fileid.'_'.$name;
				$data['filename'] = $name;
				$data['ext'] = $ext = pathinfo($data['file_URL'], PATHINFO_EXTENSION);
				$DocumentsdeleteAction = Users_Privileges_Model::isPermitted('Documents', 'Delete', $crmid);
				if($signdeleteAction && $DocumentsdeleteAction){
					$deleteAction = true;
				}
				$data['deleteAction'] = $deleteAction;
			}else{
				return false;
			}
		}
		return $data;
	}

	function getEmailRecieptent($record,$module){
		$recordModel = Vtiger_Record_Model::getInstanceById($record, $module);
		$inventoryModule = $recordModel->getModule();
		$inventotyfields = $inventoryModule->getFields();

		$toEmailConsiderableFields = array('contact_id','account_id','vendor_id');
		$db = PearDatabase::getInstance();
		$to = array();
		$to_info = array();
		$toMailNamesList = array();
		foreach($toEmailConsiderableFields as $fieldName){
			if(!array_key_exists($fieldName, $inventotyfields)){
				continue;
			}
			$fieldModel = $inventotyfields[$fieldName];
			if(!$fieldModel->isViewable()) {
				continue;
			}
			$fieldValue = $recordModel->get($fieldName);
			if(empty($fieldValue)) {
				continue;
			}
			$referenceModule = Vtiger_Functions::getCRMRecordType($fieldValue);
			$fieldLabel = decode_html(Vtiger_Util_Helper::getRecordName($fieldValue));
			$referenceModuleModel = Vtiger_Module_Model::getInstance($referenceModule);
			if (!$referenceModuleModel) {
				continue;
			}
			if(isRecordExists($fieldValue)) {
				$referenceRecordModel = Vtiger_Record_Model::getInstanceById($fieldValue, $referenceModule);
				if ($referenceRecordModel->get('emailoptout')) {
					continue;
				}
			}
			$emailFields = $referenceModuleModel->getFieldsByType('email');
			if(count($emailFields) <= 0) {
				continue;
			}

			$current_user = Users_Record_Model::getCurrentUserModel();
			$queryGenerator = new QueryGenerator($referenceModule, $current_user);
			$queryGenerator->setFields(array_keys($emailFields));
			$query = $queryGenerator->getQuery();
			$query .= ' AND crmid = ' . $fieldValue;

			$result = $db->pquery($query, array());
			$num_rows = $db->num_rows($result);
			if($num_rows <= 0) {
				continue;
			}
			foreach($emailFields as $fieldName => $emailFieldModel) {
				$emailValue = $db->query_result($result,0,$fieldName);
				if(!empty($emailValue)){
					$to = $emailValue;
					$record = CTMobile_WS_Utils::getEntityModuleWSId($referenceModule).'x'.$fieldValue;
					$toMailNamesList[] = array('to'=>$emailValue,'label' => decode_html($fieldLabel), 'record' => $record,'referenceModule'=>$referenceModule);
					
				}
			}
			/*if(!empty($to)) {
				break;
			}*/
		}
		return $toMailNamesList;
	}
}

function searcharray($value, $key, $array) {
   foreach ($array as $k => $val) {
       if ($val[$key] == $value) {
           return $k;
       }
   }
   return null;
}

function column_array($array,$key) {
   $column_array = array();
   foreach ($array as $k => $val) {
       $column_array[] = $val[$key];
   }
   return $column_array;
}

?>
