<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

include_once 'include/Webservices/Create.php';
include_once 'include/Webservices/Update.php';

class CTMobile_WS_SaveRecord1 extends CTMobile_WS_Controller {
	
	protected $recordValues = false;
	
	function process(CTMobile_API_Request $request) {
		global $adb,$current_user; // Required for vtws_update API
		$current_user = $this->getActiveUser();
		$refrenceUitypes = array(10,51,57,58,59,66,73,75,76,78,80,81,101);
		$module = trim($request->get('module'));

		if($module == ''){
			$message = $this->CTTranslate('Required fields not found');
			throw new WebServiceException(404,$message);
		}

		//relation Operation Pramaters
		$parentModuleName = trim($request->get('sourceModule'));
		$sourceRecord = explode('x',$request->get('sourceRecord'));
		$parentRecordId = $sourceRecord[1];
		
		//start validation for module & fields
		if(!getTabid($module)){
			$message = vtranslate($module,$module)." ".$this->CTTranslate('Module does not exists');
			throw new WebServiceException(404,$message);
		}
		if(!empty($parentModuleName) && !getTabid($parentModuleName)){
			$message = vtranslate($parentModuleName,$parentModuleName)." ".$this->CTTranslate('Module does not exists');
			throw new WebServiceException(404,$message);
		}
		
		$recordid = trim($request->get('record'));
		$is_duplicate = trim($request->get('is_duplicate'));
		$imageurl = $request->get('imageurl');
		//$valuesJSONString =  $request->get('values');
		$recurringJSONString =  $request->get('recurring_value');
		$recordModel = Vtiger_Record_Model::getCleanInstance($module);
		$moduleModel = $recordModel->getModule();
		$fieldList = $moduleModel->getFields();

		/*$values = "";
		if(!empty($valuesJSONString) && is_string($valuesJSONString)) {
			$values = Zend_Json::decode($valuesJSONString);
		} else {
			$values = $valuesJSONString; // Either empty or already decoded.
		}*/
		
		$recurringvalues = "";
		if(!empty($recurringJSONString) && is_string($recurringJSONString)) {
			$recurringvalues = Zend_Json::decode($recurringJSONString);
		} else {
			$recurringvalues = $recurringJSONString; // Either empty or already decoded.
		}

		//Pass TaxType in Inventory Modules
		$lineItemsModules = array('Quotes','Invoice','SalesOrder','PurchaseOrder');
		if(in_array($module,$lineItemsModules)){
			if($values['hdnTaxType'] == ''){
				$values['hdnTaxType'] = 'group';
			}
			$taxes = Inventory_TaxRecord_Model::getProductTaxes();
			$lineItems = $values['LineItems'];
			$values['productid'] = $values['LineItems'][0]['productid'];
			if($module == 'SalesOrder'){
				$values['enable_recurring'] = 0;
				$values['invoicestatus'] = "Created";
			}

			$ChargeTaxes = Inventory_TaxRecord_Model::getChargeTaxes();
			$ChargeTaxesList = Inventory_Charges_Model::getChargeTaxesList();
			$InventoryCharges = Inventory_Charges_Model::getInventoryCharges();
			foreach ($InventoryCharges as $chargesid => $chargesTax) {
				$chargesTaxes = $ChargeTaxesList[$chargesid];
				$chargename = decode_html(decode_html($InventoryCharges[$chargesid]->get('name')));
				$chargename = strtolower(str_replace(' ','_', $chargename));
				//$chargesFields[] = $chargename;
				$charges[$chargesid]['value'] = (double)$values[$chargename];
				$charges[$chargesid]['format'] = $InventoryCharges[$chargesid]->get('format');
				unset($values[$chargename]);
				foreach($ChargeTaxes as $taxid => $tax){
					if(in_array($taxid, array_keys($chargesTaxes))){
						$chargesTaxesFields = $chargename.'_'.$tax->get('taxname');
						$charges[$chargesid]['taxes'][$taxid] = (double)$values[$chargesTaxesFields];
						unset($values[$chargesTaxesFields]);
					}
				}
			}
			
			foreach ($values['LineItems'] as $key => $value) {
				if($values['hdnTaxType'] == 'individual'){
					foreach($taxes as $keys =>$taxValues){
						$taxname = $taxValues->get('taxname');
						$method = $taxValues->get('method');
						if($method == 'Deducted'){
							$values['LineItems'][$key][$taxname] = "-".$values[$taxname];
						}
					}
				}else{
					foreach($taxes as $keys =>$taxValues){
						$taxname = $taxValues->get('taxname');
						$method = $taxValues->get('method');
						if($method == 'Deducted'){
							$values['LineItems'][$key][$taxname] = "-".$values[$taxname];
						}else{
							$values['LineItems'][$key][$taxname] = $values[$taxname];
						}
						unset($values[$taxname]);
					}
				}
			}

		}
		
		$response = new CTMobile_API_Response();
		
		try {
			// Retrieve or Initalize
			if (!empty($recordid)) {
				$arrRecordId = explode('x',$recordid);
				$recordModel = Vtiger_Record_Model::getInstanceById($arrRecordId[1], $module);
				$recordModel->set('id', $arrRecordId[1]);
				$recordModel->set('mode', 'edit');
			}else {
				$recordModel = Vtiger_Record_Model::getCleanInstance($module);
				$recordModel->set('mode', '');
			}

			if($module == 'Calendar' || $module == 'Events'){
				$reminder_time = $request->get('reminder_time');
				$invite_user = $request->get('reminder_time');
			}

			if($module == 'SalesOrder'){
				$recordModel->set('enable_recurring',0);
				$recordModel->set('invoicestatus',"Created");
			}

			$EventsParentModule = array('Accounts','Campaigns','HelpDesk','Leads','Potentials');
			if(in_array($parentModuleName,$EventsParentModule) && $module == 'Events'){
				$recordModel->set('parent_id',$parentRecordId);
			}
			if($parentModuleName == 'Contacts' && $module == 'Events'){
				$recordModel->set('contact_id',$parentRecordId);
			}

			//if ($module == 'Events')
				//$module = 'Calendar';
			$moduleModel = Vtiger_Module_Model::getInstance($module);

			$fieldModelList = $moduleModel->getFields();
			foreach ($fieldModelList as $fieldName => $fieldModel) {
				if($fieldName != 'invite_user' || $fieldName != 'LineItems'){
					$fieldValue = $request->get($fieldName, null);
					$uitype = $fieldModel->get('uitype');
					$fieldDataType = $fieldModel->getFieldDataType();
					if(in_array($uitype,array(10,51,53,57,58,59,66,73,75,76,78,80,81,101))){
						if($fieldValue != ''){
							$tmp_value = explode('x',$fieldValue);
							$fieldValue = $tmp_value[1];
						}
					}
					if($fieldDataType == 'time'){
						$fieldValue = Vtiger_Time_UIType::getTimeValueWithSeconds($fieldValue);
					}
					if($fieldValue !== null) {
						if(!is_array($fieldValue) && $fieldDataType != 'currency') {
							$fieldValue = trim($fieldValue);
						}
						$recordModel->set($fieldName, $fieldValue);
					}
				}
			}
			$recordModel->save();
			$moduleWSId = CTMobile_WS_Utils::getEntityModuleWSId($module);
			$lastInsertId = $recordModel->getId();
			$lastInsertWSId= $moduleWSId.'x'.$lastInsertId;

			if($parentModuleName && $parentRecordId){
				
				$parentModuleModel = Vtiger_Module_Model::getInstance($parentModuleName);
				$recordModel = Vtiger_Record_Model::getInstanceById($lastInsertId,$module);
				$relatedModule = $recordModel->getModule();
				$relatedRecordId = $recordModel->getId();
				if($relatedModule->getName() == 'Events'){
					$relatedModule = Vtiger_Module_Model::getInstance('Calendar');
				}
				$relationModel = Vtiger_Relation_Model::getInstance($parentModuleModel, $relatedModule);
				$relationModel->addRelation($parentRecordId, $relatedRecordId);
				
				//To store the relationship between Products/Services and PriceBooks
				if ($parentRecordId && ($parentModuleName === 'Products' || $parentModuleName === 'Services') && $module == 'PriceBooks') {
					$parentRecordModel = Vtiger_Record_Model::getInstanceById($parentRecordId, $parentModuleName);
					$sellingPricesList = $parentModuleModel->getPricesForProducts($recordModel->get('currency_id'), array($parentRecordId));
					$recordModel->updateListPrice($parentRecordId, $sellingPricesList[$parentRecordId]);
				}
			}
			
			if(in_array($module,$lineItemsModules)){
				//$ID = explode('x', $this->recordValues['id']);
				$recordModel = Vtiger_Record_Model::getInstanceById($lastInsertId);
				$currencyId = $recordModel->get('currency_id');
				$currencies = Inventory_Module_Model::getAllCurrencies();
				foreach ($currencies as $currencyInfo) {
					if ($currencyId == $currencyInfo['curid']) {
						$conversionRateForPurchaseCost = $currencyInfo['conversionrate'];
						$currencysymbol = $currencyInfo['currencysymbol'];
						$conversionRate = $currencyInfo['conversionrate'];
						break;
					}
				}
				$basetable = $recordModel->getModule()->get('basetable');
				$basetableid = $recordModel->getModule()->get('basetableid');
				$lineItems = $values['LineItems'];
				$discountTotal = 0;
				$s_h_amount = 0;
				$total_shipping_tax = 0;
				$s_h_percent = 0;
				if($charges){
					$hdnSubTotal = $values['hdnSubTotal'];
					foreach($charges as $chargeid => $charge){
						if($charge['format'] == 'Percent'){
							$charges[$chargeid]['percent'] = $charge['value'];
							$charges[$chargeid]['value'] = ($charge['value']*$hdnSubTotal)/100;
						}
						foreach ($charge['taxes'] as $key => $value) {
							$total_shipping_tax = $total_shipping_tax + (($charges[$chargeid]['value']*$value)/100);
						}
						$s_h_amount = $s_h_amount + $charges[$chargeid]['value'];
						unset($charges[$chargeid]['format']);
					}
					$s_h_percent = $s_h_amount;
					$insertShipQuery = "INSERT INTO vtiger_inventorychargesrel(recordid,charges) VALUES(?,?)";
					$result = $adb->pquery($insertShipQuery,array($lastInsertId,json_encode($charges)));
				}
				if($module == 'Invoice'){
					$total = $values['hdnGrandTotal'];
					$subtotal = $values['hdnSubTotal'];
					$pre_tax_total = $values['pre_tax_total'];
					$received = $values['received'];
					$balance = $total - $received;
					$query = "UPDATE ".$basetable." SET subtotal = ?, total = ?,s_h_amount=?,s_h_percent=?,pre_tax_total=?,balance=?,conversion_rate=? WHERE ".$basetableid."=?";
					$result = $adb->pquery($query,array($subtotal,$total,$s_h_amount,$s_h_percent,$pre_tax_total,$balance,$conversionRate,$lastInsertId));
				}else if ($module == 'PurchaseOrder'){
					$total = $values['hdnGrandTotal'];
					$subtotal = $values['hdnSubTotal'];
					$pre_tax_total = $values['pre_tax_total'];
					$paid = $values['paid'];
					$balance = $total - $paid;
					$query = "UPDATE ".$basetable." SET subtotal = ?,total = ?,s_h_amount=?,s_h_percent=?,pre_tax_total=?,balance=?,conversion_rate=? WHERE ".$basetableid."=?";
					$result = $adb->pquery($query,array($subtotal,$total,$s_h_amount,$s_h_percent,$pre_tax_total,$balance,$conversionRate,$lastInsertId));
				}else{
					$total = $values['hdnGrandTotal'];
					$subtotal = $values['hdnSubTotal'];
					$pre_tax_total = $values['pre_tax_total'];
					$query = "UPDATE ".$basetable." SET subtotal = ?,total = ?,s_h_amount=?,s_h_percent=?,pre_tax_total=?,conversion_rate=? WHERE ".$basetableid."=?";
					$result = $adb->pquery($query,array($subtotal,$total,$s_h_amount,$s_h_percent,$pre_tax_total,$conversionRate,$lastInsertId));
				}
				
			}
			if($module == 'Contacts' || $module == 'Products'){
				if($is_duplicate == '1' && !empty($imageurl)){
					$this->SaveImageAsDuplicateRecord($imageurl,$lastInsertId,$module);
				}else{
					$this->uploadAndSaveFiles($_FILES['imagename'],$lastInsertId,$module);
				}
			}
			if($module == 'Documents' || $module == 'ModComments'){
				//$ID = explode('x', $this->recordValues['id']);
				if(!empty($_FILES['filename']) && $module == 'Documents'){
					$query = "UPDATE vtiger_notes SET filestatus = '1' WHERE notesid = ?";
					$result = $adb->pquery($query,array($lastInsertId));
				}
				if($module == 'ModComments'){
					$adb->pquery("UPDATE vtiger_modcomments SET userid = ? WHERE modcommentsid = ?",array($current_user->id,$lastInsertId));
					$uploadedFileNames = array();
					foreach ($_FILES as $key => $files) {
						$uploadedFileNames[] = $this->uploadAndSaveFiles($files,$lastInsertId,$module);
					}
					if(count($uploadedFileNames)){
						$filename = implode(',',$uploadedFileNames);
						$adb->pquery("UPDATE vtiger_modcomments SET filename = ? WHERE modcommentsid = ?",array($filename,$lastInsertId));
					}
				}else{
					$this->uploadAndSaveFiles($_FILES['filename'],$lastInsertId,$module);
				}
			}

			if($module == 'Events' || $module == 'Calendar'){
				//$recordId = explode('x', $this->recordValues['id']);
				if($recordid){
					$delete = $adb->pquery("DELETE FROM vtiger_invitees WHERE activityid=?",array($lastInsertId));
					foreach ($invite_user as $value) {
						$result = $adb->pquery('INSERT INTO vtiger_invitees (activityid,inviteeid,status) values(?,?,?)',array($lastInsertId,$value,'sent'));
					}
				}else{
					foreach ($invite_user as $value) {
						$result = $adb->pquery('INSERT INTO vtiger_invitees (activityid,inviteeid,status) values(?,?,?)',array($lastInsertId,$value,'sent'));
					}
				}

				//code added to send mail to the vtiger_invitees
		        $selectUsers = $invite_user;
		        if(!empty($selectUsers)){
		            $invities = implode(';',$selectUsers);
		            $recordModel = Vtiger_Record_Model::getInstanceById($lastInsertId,'Events');
		            $mail_contents = $this->getInviteUserMailData($recordModel);
		            $activityMode = ($recordModel->getModuleName()=='Calendar') ? 'Task' : 'Events';
		            sendInvitation($invities,$activityMode,$recordModel,$mail_contents);
		        }

				if(!empty($recurringvalues)){
					$adb->pquery('DELETE FROM vtiger_activity_recurring_info WHERE activityid = ?',array($lastInsertId));
					$adb->pquery('DELETE FROM vtiger_recurringevents WHERE activityid = ?',array($lastInsertId));
					$recurringdate = Vtiger_Date_UIType::getDBInsertedValue($recurringvalues['recurringdate']);
					$recurringtype = $recurringvalues['recurringtype'];
					$recurringfreq = $recurringvalues['recurringfreq'];
					//$recurringinfo = $recurringvalues['recurringinfo'];
					if($recurringvalues['recurringtype'] == 'Monthly'){
						$recurringMonthType = $recurringvalues['recurringMonthType'];
						if($recurringMonthType == "1"){
							$recurringDayOfMonth = $recurringvalues['recurringDayOfMonth'];
							$recurringinfo = $recurringtype.'::date::'.$recurringDayOfMonth;
						}else{
							$recurringDayOfMonth = $recurringvalues['recurringDayOfMonth'];
							$recurringDayType = $recurringvalues['recurringDayType'];
							if($recurringDayType == '1'){
								$recurringDayType = 'first';
							}else{
								$recurringDayType = 'last';
							}
							$recurringDayOfWeek = $recurringvalues['recurringDayOfWeek'];
							$recurringinfo = $recurringtype.'::day::'.$recurringDayType.'::'.$recurringDayOfWeek;
						}
					}else if($recurringvalues['recurringtype'] == 'Weekly'){
						$recurringWeekDay = Zend_Json::decode($recurringvalues['recurringWeekDay']);
						$recurringinfo = $recurringtype;
						foreach($recurringWeekDay as $keys => $values){
							$recurringinfo = $recurringinfo.'::'.$values;
						}
					}else{
						$recurringinfo = $recurringtype;
					}
					$recurringenddate = Vtiger_Date_UIType::getDBInsertedValue($recurringvalues['recurringenddate']);
					$adb->pquery('INSERT INTO vtiger_recurringevents(activityid,recurringdate,recurringtype,recurringfreq,recurringinfo,recurringenddate) VALUES(?,?,?,?,?,?)',array($lastInsertId,$recurringdate,$recurringtype,$recurringfreq,$recurringinfo,$recurringenddate));	
				}
				
				if($reminder_time != ''){
					$recurringQuery = $adb->pquery('SELECT * FROM vtiger_recurringevents WHERE activityid =?',array($lastInsertId));
					if($adb->num_rows($recurringQuery) > 0){
						$recurringid = $adb->query_result($recurringQuery,0,'recurringid');
					}else{
						$recurringid = '0';
					}
					
					if($recordid){
						$reminderquery = $adb->pquery("SELECT * FROM vtiger_activity_reminder WHERE activity_id = ? ",array($lastInsertId));
						if($adb->num_rows($reminderquery) > 0){
							$result = $adb->pquery('UPDATE vtiger_activity_reminder SET reminder_time = ? WHERE activity_id = ?',array($reminder_time,$lastInsertId));
						}else{
							$result = $adb->pquery('INSERT INTO vtiger_activity_reminder (activity_id,reminder_time,reminder_sent,recurringid) values(?,?,?,?)',array($lastInsertId,$reminder_time,'0',$recurringid));
						}
					}else{
						$reminderquery = $adb->pquery("SELECT * FROM vtiger_activity_reminder WHERE activity_id = ? ",array($lastInsertId));
						if($adb->num_rows($reminderquery) > 0){
							$result = $adb->pquery('UPDATE vtiger_activity_reminder SET reminder_time = ? WHERE activity_id = ?',array($reminder_time,$lastInsertId));
						}else{
							$result = $adb->pquery('INSERT INTO vtiger_activity_reminder (activity_id,reminder_time,reminder_sent,recurringid) values(?,?,?,?)',array($lastInsertId,$reminder_time,'0',$recurringid));
						}
					}
				}else{
					$reminderquery = $adb->pquery("SELECT * FROM vtiger_activity_reminder WHERE activity_id = ? ",array($lastInsertId));
					if($adb->num_rows($reminderquery) > 0){
						$result = $adb->pquery('UPDATE vtiger_activity_reminder SET reminder_time = ? WHERE activity_id = ?',array('0',$lastInsertId));
					}else{
						$result = $adb->pquery('INSERT INTO vtiger_activity_reminder (activity_id,reminder_time,reminder_sent,recurringid) values(?,?,?,?)',array($lastInsertId,'0','0','0'));
					}
				}
			}
			
			// Update the record id
			$request->set('record', $lastInsertWSId);
			//$recordId = explode('x', $lastInsertId);
			if($request->get('user_lat')!='' && $request->get('user_long')!='' && $request->get('user_id')!=''){
				if($lastInsertId !=''){
					$date_var = date("Y-m-d H:i:s");
					$userId = explode('x', $request->get('user_id'));
					$createdtime = $adb->formatDate($date_var, true);
					$query = $adb->pquery("INSERT INTO ctmobile_userderoute (userid, latitude, longitude, createdtime,action,record) VALUES (?,?,?,?,?,?)", array($userId[1], $request->get('user_lat'), $request->get('user_long'), $createdtime,$mode,$lastInsertId));
					
				}
				
			}

			$response = new CTMobile_API_Response();
			$getLabelQuery = $adb->pquery("SELECT label from vtiger_crmentity where crmid = ?", array($lastInsertId));
			$recordLabel = decode_html(trim($adb->query_result($getLabelQuery, 0, 'label')));
			$message = $this->CTTranslate('Record save successfully');
			$result = array('id'=>$lastInsertWSId,'recordLabel'=>$recordLabel,'module'=>$module,'message'=>$message);

			// Gather response with full details
			$response->setResult($result);
			
		} catch(Exception $e) {
			$response->setError($e->getCode(), $e->getMessage());
		}
		return $response;
	}

	function SaveImageAsDuplicateRecord($imageUrl,$id,$module){
		$contents=file_get_contents($imageUrl);
		$name = basename($imageUrl);
		$imagename = explode('_',$name);
		foreach($imagename as $key => $value){
			if($key == 1){
				$image = $value;
			}
			if($key > 1){
				$image.= "_".$value;
			}
			
		}
		global $adb,$site_URL,$root_directory;
		$typeQuery = $adb->pquery('SELECT type FROM vtiger_attachments WHERE attachmentsid = ?',array($imagename[0]));
		$type = $adb->query_result($typeQuery,0,'type');
        $docID = $id;
       
        $current_user = $this->getActiveUser();
        $moduleName = $module;
        $storagePath = 'storage/';
        $year  = date('Y');
        $month = date('F');
        $day   = date('j');
        $week  = '';
        
		$date_var = date("Y-m-d H:i:s");
		
        if (!is_dir($root_directory.$storagePath . $year)) {
            mkdir($root_directory.$storagePath . $year);
            chmod($root_directory.$storagePath . $year, 0777);
        }

        if (!is_dir($root_directory.$storagePath . $year . "/" . $month)) {
            mkdir($root_directory.$storagePath . "$year/$month");
            chmod($root_directory.$storagePath . "$year/$month", 0777);
        }

        if ($day > 0 && $day <= 7){
            $week = 'week1';
        }elseif ($day > 7 && $day <= 14){
            $week = 'week2';
        }elseif ($day > 14 && $day <= 21){
            $week = 'week3';
        }elseif ($day > 21 && $day <= 28){
            $week = 'week4';
        }else{
            $week = 'week5'; 
        }
        
        if (!is_dir($root_directory.$storagePath . $year . "/" . $month . "/" . $week)) {
            mkdir($root_directory.$storagePath . "$year/$month/$week");
            chmod($root_directory.$storagePath . "$year/$month/$week", 0777);
        }
        $interior = $storagePath . $year . "/" . $month . "/" . $week . "/";
        $crm_id = $adb->getUniqueID("vtiger_crmentity");
        $save_path = $interior.$crm_id.'_'. $image;
        $upload_status = file_put_contents($save_path,$contents);
        if($upload_status && $moduleName == 'Contacts'){
			$delquery = 'delete from vtiger_seattachmentsrel where crmid = ?';
			$adb->pquery($delquery, array($docID));
			
			$sql1 = "INSERT INTO vtiger_crmentity (crmid,smcreatorid,smownerid,setype,description,createdtime,modifiedtime) VALUES (?, ?, ?, ?, ?, ?, ?)";
			$params1 = array($crm_id, $current_user->id, $current_user->id, $moduleName." Image",'', $adb->formatDate($date_var, true), $adb->formatDate($date_var, true));
			$adb->pquery($sql1, $params1);
			//Add entry to attachments
			$sql2 = "INSERT INTO vtiger_attachments(attachmentsid, name, description, type, path) values(?, ?, ?, ?, ?)";
			$params2 = array($crm_id, $image,'', $type, $interior);
			$adb->pquery($sql2, $params2);
			//Add relation
			$sql3 = 'INSERT INTO vtiger_seattachmentsrel VALUES(?,?)';
			$params3 = array($docID,$crm_id);
			$adb->pquery($sql3, $params3);
			$adb->pquery('UPDATE vtiger_contactdetails SET imagename = ? WHERE contactid = ?',array($image,$docID));
		}else if($upload_status && $moduleName == 'Products'){
			$delquery = 'delete from vtiger_seattachmentsrel where crmid = ?';
			$adb->pquery($delquery, array($docID));
			
			$sql1 = "INSERT INTO vtiger_crmentity (crmid,smcreatorid,smownerid,setype,description,createdtime,modifiedtime) VALUES (?, ?, ?, ?, ?, ?, ?)";
			$params1 = array($crm_id, $current_user->id, $current_user->id, $moduleName." Image",'', $adb->formatDate($date_var, true), $adb->formatDate($date_var, true));
			$adb->pquery($sql1, $params1);
			//Add entry to attachments
			$sql2 = "INSERT INTO vtiger_attachments(attachmentsid, name, description, type, path) values(?, ?, ?, ?, ?)";
			$params2 = array($crm_id, $image,'', $type, $interior);
			$adb->pquery($sql2, $params2);
			//Add relation
			$sql3 = 'INSERT INTO vtiger_seattachmentsrel VALUES(?,?)';
			$params3 = array($docID,$crm_id);
			$adb->pquery($sql3, $params3);	
			$adb->pquery('UPDATE vtiger_products SET imagename = ? WHERE productid = ?',array($image,$docID));
		}       
    }
        
	function uploadAndSaveFiles($files,$id,$module){
		if (!empty($files)) {
            $docID = $id;
            global $adb,$site_URL,$root_directory;
            $current_user = $this->getActiveUser();
            $moduleName = $module;
            $storagePath = 'storage/';
            $year  = date('Y');
            $month = date('F');
            $day   = date('j');
            $week  = '';
            
			$date_var = date("Y-m-d H:i:s");
			
            if (!is_dir($root_directory.$storagePath . $year)) {
                mkdir($root_directory.$storagePath . $year);
                chmod($root_directory.$storagePath . $year, 0777);
            }

            if (!is_dir($root_directory.$storagePath . $year . "/" . $month)) {
                mkdir($root_directory.$storagePath . "$year/$month");
                chmod($root_directory.$storagePath . "$year/$month", 0777);
            }

            if ($day > 0 && $day <= 7){
                $week = 'week1';
            }elseif ($day > 7 && $day <= 14){
                $week = 'week2';
            }elseif ($day > 14 && $day <= 21){
                $week = 'week3';
            }elseif ($day > 21 && $day <= 28){
                $week = 'week4';
            }else{
                $week = 'week5'; 
            }
            
            if (!is_dir($root_directory.$storagePath . $year . "/" . $month . "/" . $week)) {
                mkdir($root_directory.$storagePath . "$year/$month/$week");
                chmod($root_directory.$storagePath . "$year/$month/$week", 0777);
            }
            $interior = $storagePath . $year . "/" . $month . "/" . $week . "/";
            $crm_id = $adb->getUniqueID("vtiger_crmentity");
            $upload_status = move_uploaded_file($files['tmp_name'],$interior.$crm_id.'_'. $files['name']);
            if($upload_status && $moduleName == 'Documents'){
	            $delquery = 'delete from vtiger_seattachmentsrel where crmid = ?';
				$adb->pquery($delquery, array($docID));
				
	            $lastInsertedId = $adb->pquery("select attachmentsid from vtiger_attachments order by attachmentsid DESC limit 0,1");
	            $attachmentsid = $adb->query_result($lastInsertedId, 0, 'attachmentsid');
	            $query1 = $adb->pquery("insert into vtiger_crmentity (`crmid`,`setype`) VALUES(?,?)",array($crm_id,'Documents Attachment'));
	            $query2 = $adb->pquery("insert into vtiger_attachments (`attachmentsid`,`name`,`type`,`path`) VALUES(?,?,?,?)",array($crm_id,$files['name'],$files['type'],$interior));
	            $grtLastInserted = $adb->pquery("select attachmentsid,subject from vtiger_attachments where attachmentsid > ".$attachmentsid);
	            $total = $adb->num_rows($grtLastInserted);
	            for ($i=0; $i < $total; $i++) { 
	                $grtAttachmentsId = $adb->query_result($grtLastInserted, $i, 'attachmentsid');
	                $subject = $adb->query_result($grtLastInserted, $i, 'subject');
	                $adb->pquery("insert into vtiger_seattachmentsrel (`crmid`,`attachmentsid`) VALUES(?,?)",array($docID,$grtAttachmentsId));
	            }
	            $adb->pquery("UPDATE vtiger_notes SET filename = '".$files['name']."', filetype = '".$files['type']."', filelocationtype = 'I', filesize = '".$files['size']."' WHERE notesid = ".$docID);
            }if($upload_status && $moduleName == 'ModComments'){
	            
	            $lastInsertedId = $adb->pquery("select attachmentsid from vtiger_attachments order by attachmentsid DESC limit 0,1");
	            $attachmentsid = $adb->query_result($lastInsertedId, 0, 'attachmentsid');
	            $query1 = $adb->pquery("insert into vtiger_crmentity (`crmid`,`setype`) VALUES(?,?)",array($crm_id,'ModComments Attachment'));
	            $query2 = $adb->pquery("insert into vtiger_attachments (`attachmentsid`,`name`,`type`,`path`) VALUES(?,?,?,?)",array($crm_id,$files['name'],$files['type'],$interior));
	            $grtLastInserted = $adb->pquery("select attachmentsid,subject from vtiger_attachments where attachmentsid > ".$attachmentsid);
	            $total = $adb->num_rows($grtLastInserted);
	            for ($i=0; $i < $total; $i++) { 
	                $grtAttachmentsId = $adb->query_result($grtLastInserted, $i, 'attachmentsid');
	                $subject = $adb->query_result($grtLastInserted, $i, 'subject');
	                $adb->pquery("insert into vtiger_seattachmentsrel (`crmid`,`attachmentsid`) VALUES(?,?)",array($docID,$grtAttachmentsId));
	            }
	            $adb->pquery("UPDATE vtiger_modcomments SET filename = '".$grtAttachmentsId."' where modcommentsid = ".$docID);
            }else if($upload_status && $moduleName == 'Contacts'){
				$delquery = 'delete from vtiger_seattachmentsrel where crmid = ?';
				$adb->pquery($delquery, array($docID));
				
				$sql1 = "INSERT INTO vtiger_crmentity (crmid,smcreatorid,smownerid,setype,description,createdtime,modifiedtime) VALUES (?, ?, ?, ?, ?, ?, ?)";
				$params1 = array($crm_id, $current_user->id, $current_user->id, $moduleName." Image",'', $adb->formatDate($date_var, true), $adb->formatDate($date_var, true));
				$adb->pquery($sql1, $params1);
				//Add entry to attachments
				$sql2 = "INSERT INTO vtiger_attachments(attachmentsid, name, description, type, path) values(?, ?, ?, ?, ?)";
				$params2 = array($crm_id, $files['name'],'', $files['type'], $interior);
				$adb->pquery($sql2, $params2);
				//Add relation
				$sql3 = 'INSERT INTO vtiger_seattachmentsrel VALUES(?,?)';
				$params3 = array($docID,$crm_id);
				$adb->pquery($sql3, $params3);
				$adb->pquery('UPDATE vtiger_contactdetails SET imagename = ? WHERE contactid = ?',array($files['name'],$docID));
			}else if($upload_status && $moduleName == 'Products'){
				$delquery = 'delete from vtiger_seattachmentsrel where crmid = ?';
				$adb->pquery($delquery, array($docID));
				
				$sql1 = "INSERT INTO vtiger_crmentity (crmid,smcreatorid,smownerid,setype,description,createdtime,modifiedtime) VALUES (?, ?, ?, ?, ?, ?, ?)";
				$params1 = array($crm_id, $current_user->id, $current_user->id, $moduleName." Image",'', $adb->formatDate($date_var, true), $adb->formatDate($date_var, true));
				$adb->pquery($sql1, $params1);
				//Add entry to attachments
				$sql2 = "INSERT INTO vtiger_attachments(attachmentsid, name, description, type, path) values(?, ?, ?, ?, ?)";
				$params2 = array($crm_id, $files['name'],'', $files['type'], $interior);
				$adb->pquery($sql2, $params2);
				//Add relation
				$sql3 = 'INSERT INTO vtiger_seattachmentsrel VALUES(?,?)';
				$params3 = array($docID,$crm_id);
				$adb->pquery($sql3, $params3);	
				$adb->pquery('UPDATE vtiger_products SET imagename = ? WHERE productid = ?',array($files['name'],$docID));
			}  
			return $crm_id;     
        }
	}

	public function getInviteUserMailData($recordModel) {
            $adb = PearDatabase::getInstance();

            $return_id = $recordModel->getId();
            $cont_qry = "select * from vtiger_cntactivityrel where activityid=?";
            $cont_res = $adb->pquery($cont_qry, array($return_id));
            $noofrows = $adb->num_rows($cont_res);
            $cont_id = array();
            if($noofrows > 0) {
                for($i=0; $i<$noofrows; $i++) {
                    $cont_id[] = $adb->query_result($cont_res,$i,"contactid");
                }
            }
            $cont_name = '';
            foreach($cont_id as $key=>$id) {
                if($id != '') {
                    $contact_name = Vtiger_Util_Helper::getRecordName($id);
                    $cont_name .= $contact_name .', ';
                }
            }

			$parentId = $recordModel->get('parent_id');
			$parentName = '';
			if($parentId != '') {
				$parentName = Vtiger_Util_Helper::getRecordName($parentId);
			}
			
            $cont_name  = trim($cont_name,', ');
            $mail_data = Array();
            $mail_data['user_id'] = $recordModel->get('assigned_user_id');
            $mail_data['subject'] = $recordModel->get('subject');
            $moduleName = $recordModel->getModuleName();
            $mail_data['status'] = (($moduleName=='Calendar')?($recordModel->get('taskstatus')):($recordModel->get('eventstatus')));
            $mail_data['activity_mode'] = (($moduleName=='Calendar')?('Task'):('Events'));
            $mail_data['taskpriority'] = $recordModel->get('taskpriority');
            $mail_data['relatedto'] = $parentName;
            $mail_data['contact_name'] = $cont_name;
            $mail_data['description'] = $recordModel->get('description');
            $mail_data['assign_type'] = $recordModel->get('assigntype');
            $mail_data['group_name'] = getGroupName($recordModel->get('assigned_user_id'));
            $mail_data['mode'] = $recordModel->get('mode');
            //TODO : remove dependency on request;
            $value = getaddEventPopupTime($recordModel->get('time_start'),$recordModel->get('time_end'),'24');
            $start_hour = $value['starthour'].':'.$value['startmin'].''.$value['startfmt'];
            if($_REQUEST['activity_mode']!='Task')
                $end_hour = $value['endhour'] .':'.$value['endmin'].''.$value['endfmt'];
            $startDate = new DateTimeField($recordModel->get('date_start')." ".$start_hour);
            $endDate = new DateTimeField($recordModel->get('due_date')." ".$end_hour);
            $mail_data['st_date_time'] = $startDate->getDBInsertDateTimeValue();
            $mail_data['end_date_time'] = $endDate->getDBInsertDateTimeValue();
            $mail_data['location']=$recordModel->get('location');
            return $mail_data;
     }
	
}
