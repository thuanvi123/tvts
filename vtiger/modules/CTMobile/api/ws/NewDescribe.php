<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
include_once 'include/Webservices/DescribeObject.php';

class CTMobile_WS_NewDescribe extends CTMobile_WS_Controller {
	
	function process(CTMobile_API_Request $request) {
		$default_charset = VTWS_PreserveGlobal::getGlobal('default_charset');
		global $adb;
		$current_user = $this->getActiveUser();
		$roleid = $current_user->roleid;
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$module = trim($request->get('module'));
		
		$idComponents = explode('x',$request->get('record'));
		$record = $idComponents[1];
		if($module == ''){
			$message =  $this->CTTranslate('Required fields not found');
			throw new WebServiceException(404,$message);
		}
		if($module == 'Users'){
			$current_user = Users::getActiveAdminUser();
		}
		
		$isFilter = trim($request->get('isFilter'));
		$isAssetTracking = trim($request->get('isAssetTracking'));
		$describeInfo = vtws_describe($module, $current_user);
		
		$fields = $describeInfo['fields'];

		$describe = array();
		$moduleModel = Vtiger_Module_Model::getInstance($module);

		//code start of display Fields by suresh
		if($isFilter && $isFilter != 'false'){
			$displayFields = array();
			$getDisplaySQL = $adb->pquery("SELECT * FROM ctmobile_display_fields WHERE module = ?",array($module));
			$totalRows = $adb->num_rows($getDisplaySQL);
			$entries = array();
			for ($i=0; $i < $totalRows; $i++) { 
				$id = $adb->query_result($getDisplaySQL,$i,'id');
				$module = $adb->query_result($getDisplaySQL,$i,'module');
				$modulelabel = vtranslate($module,$module);
				$display_fieldname = $adb->query_result($getDisplaySQL,$i,'fieldname');
				$fieldtype = $adb->query_result($getDisplaySQL,$i,'fieldtype');
				$displayFields[] = array('field'=>$display_fieldname,'display_fieldtype'=>$fieldtype);
			}
		}
		//code end

		
		$taxFields = array();
		$chargesFields = array();
		$chargesTaxesFields = array();
		if(in_array($module,array('Invoice','Quotes','SalesOrder','PurchaseOrder'))){
			$inventoryTaxes = Inventory_TaxRecord_Model::getProductTaxes();
			foreach($inventoryTaxes as $tax){
				$taxFields[] = $tax->get('taxname');	
			}
			$ChargeTaxes = Inventory_TaxRecord_Model::getChargeTaxes();
			$ChargeTaxesList = Inventory_Charges_Model::getChargeTaxesList();
			$InventoryCharges = Inventory_Charges_Model::getInventoryCharges();
			if(!empty($record)){
				$recordModel = Vtiger_Record_Model::getInstanceById($record,$module);
				$productDetails = $recordModel->getProducts();
				foreach ($productDetails['1']['final_details']['chargesAndItsTaxes'] as $chargesid => $value) {
					$productChargesTaxes[$chargesid] = $value['taxes'];
				}

				foreach ($InventoryCharges as $chargesid => $charges) {
					$chargename = decode_html(decode_html($charges->get('name')));
					$chargename = strtolower(str_replace(' ','_', $chargename));
					$chargesFields[] = $chargename;
					foreach($ChargeTaxes as $taxid => $tax){
						$chargeTaxid = $tax->get('taxid');
						if(in_array($chargeTaxid, array_keys($ChargeTaxesList[$chargesid])) || in_array($chargeTaxid, array_keys($productChargesTaxes[$chargesid]))){
							$chargesTaxesFields[] = array('chargeTaxid'=>$chargeTaxid,'chargeTaxname'=>$chargename.'_'.$tax->get('taxname'));	
						}
					}
				}
			}else{
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
			}
			
		 	$lineItemsFields = array('productid','quantity','listprice','comment','discount_amount','discount_percent','txtAdjustment','hdnDiscountPercent','hdnDiscountAmount','hdnTaxType','currency_id','pre_tax_total','received','paid','balance');
		 	$lineItemsFields = array_merge($lineItemsFields,$taxFields,$chargesFields,column_array($chargesTaxesFields,'chargeTaxname'));
		 	$otherhdnFields = array('txtAdjustment','hdnDiscountPercent','hdnDiscountAmount','hdnTaxType');

		 	$currencies = Inventory_Module_Model::getAllCurrencies();
			$currencyId = $current_user->currency_id;
			foreach ($currencies as $currencyInfo) {
				if ($currencyId == $currencyInfo['curid']) {
					$conversionRateForPurchaseCost = $currencyInfo['conversionrate'];
					$currencysymbol = $currencyInfo['currencysymbol'];
					$conversionRate = $currencyInfo['conversionrate'];
					break;
				}
			}

		}

		$fieldModels = $moduleModel->getFields();

		if($module == 'Events'){
			$modulename = 'Calendar';
		}else{
			$modulename = $module;
		}
		// code start for Entity Field By suresh /
		$entityQuery = $adb->pquery("SELECT * FROM vtiger_entityname WHERE modulename = ?",array($modulename));
		$entityField = $adb->query_result($entityQuery,0,'fieldname');
		$entityField_array = explode(',',$entityField);
		$entityField = $entityField_array[0];
		$tabid = getTabid($modulename);

		if($isAssetTracking && $isAssetTracking != 'false'){
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
		$describeInfo['entityField'] = array('label'=>$fieldlabel,'value'=>$entityField);
		
		// code End for Entity Field By suresh /
		foreach($fields as $index=>$field) {
			if($field['name'] == 'terms_conditions'){
				$field['default'] = html_entity_decode(decode_html($field['default']),ENT_QUOTES,$default_charset);
			}
			if($field['name'] == 'reminder_time'){
				$field['label'] = vtranslate($field['label'],'Events');
			}
			if($module == 'Events'){
				if(in_array($field['name'], array('date_start','time_start','due_date','time_end'))){
					$language = $current_user->language;
					$field['label'] = ctTranslate($field['name'],$language);
				}
			}
			if($module == 'Calendar'){
				if(in_array($field['name'], array('date_start','time_start'))){
					$language = $current_user->language;
					$field['label'] = ctTranslate($field['name'],$language);
				}
			}
			if($field['name'] == 'currency_id'){
				$field['type'] = array();
				$query = "SELECT id,currency_name,currency_symbol FROM  `vtiger_currency_info` WHERE currency_status = 'Active' AND deleted = 0";
				$result = $adb->pquery($query,array());
				$numrows = $adb->num_rows($result);
				$query2 = "SELECT id FROM vtiger_ws_entity WHERE name = 'Currency'";
				$resullt2 = $adb->pquery($query2,array());
				$id = $adb->query_result($resullt2,0,'id');
				$currencyPicklistvalues = array();
				$picklistValues = array();
				for($i=0;$i<$numrows;$i++){
					$currency_name = $adb->query_result($result,$i,'currency_name');
					$currency_symbol = html_entity_decode($adb->query_result($result,$i,'currency_symbol'),ENT_QUOTES,$default_charset);
					$currency_name = vtranslate($currency_name,$module);
					$value = $adb->query_result($result,$i,'id');
					if(in_array($module, array('Quotes','Invoice','SalesOrder','PurchaseOrder'))){
						$picklistValues[] = array('label'=>$currency_name,'value'=>$id.'x'.$value);
						$currencyPicklistvalues[] = array('label'=>$currency_name,'value'=>$id.'x'.$value,'symbol'=>$currency_symbol);
					}else{
						$picklistValues[] = array('label'=>$currency_name,'value'=>$id.'x'.$value);
					}
				}
				$field['type']['picklistValues'] = $picklistValues;
				if(in_array($module, array('Quotes','Invoice','SalesOrder','PurchaseOrder'))){
					$field['type']['defaultValue'] = vtws_getWebserviceEntityId('Currency', $current_user->currency_id);
				}else{
					$field['type']['defaultValue'] = trim($picklistValues[0]['value']);
				}
				$field['type']['name'] = 'picklist';
			}
			if($field['name'] == 'folderid' && $module == 'Documents'){
				$field['type'] = array();
				$query = "SELECT folderid,foldername FROM  `vtiger_attachmentsfolder` ORDER BY sequence ASC";
				$result = $adb->pquery($query,array());
				$numrows = $adb->num_rows($result);
				$query2 = "SELECT id FROM vtiger_ws_entity WHERE name = 'DocumentFolders'";
				$resullt2 = $adb->pquery($query2,array());
				$id = $adb->query_result($resullt2,0,'id');
				$picklistValues = array();
				for($i=0;$i<$numrows;$i++){
					$foldername = $adb->query_result($result,$i,'foldername');
					$foldername =  vtranslate($foldername,$module);
					$folderid = $adb->query_result($result,$i,'folderid');
					$picklistValues[] = array('label'=>$foldername,'value'=>$id.'x'.$folderid);
				}
				$field['type']['picklistValues'] = $picklistValues;
				$field['type']['defaultValue'] = trim($picklistValues[0]['value']);
				$field['default'] = trim($picklistValues[0]['value']);
				$field['type']['name'] = 'picklist';
			}
			$fieldModel = $fieldModels[$field['name']];
			$fieldBlock = $fieldModel->block;
			$fieldId = $fieldModel->id;
			
			$restrictedFields = array('sendnotification','duration_hours','isconvertedfromlead','filelocationtype','filestatus','fileversion');
			if(in_array($field['name'],$restrictedFields)){
					unset($field);
					continue;
			}
			if(($field['name'] == 'modifiedby'  ) && ($module == 'Calendar' || $module == 'Events')){
				continue;
			}
			
			if(($module == 'Calendar' || $module == 'Events') && ($field['name'] == 'activitytype')){
				$defaultactivitytype = $current_user->defaultactivitytype;
				if($defaultactivitytype != ''){
					$field['default'] = trim($defaultactivitytype);
				}
			}
			if(($module == 'Calendar' || $module == 'Events') && ($field['name'] == 'eventstatus')){
				if($module == 'Calendar'){
					$field['label'] = vtranslate('Event','Events').' '.$field['label'];
				}
				$defaulteventstatus = $current_user->defaulteventstatus;
				if($defaulteventstatus != ''){
					$field['default'] = trim($defaulteventstatus);
				}
			}
			if($field['name'] != 'currency_id'){
				if($field['default'] != '' && $field['type']['name'] == 'picklist'){
					$field['type']['defaultValue'] = trim($field['default']);
				}else{
					$field['type']['defaultValue'] = trim($field['default']);
				}
			}
			
			if($fieldModel) {
				$displaytype = $fieldModel->get('displaytype');
				$uitype =  $fieldModel->get('uitype');
				if($uitype == 15 || $uitype == 33){
					$picklistValues1 = array();
					if($uitype == 15){
						$picklistValues1[] = array('value'=>"", 'label'=>vtranslate('LBL_SELECT_OPTION',$module));
					}
					$picklistValues = Vtiger_Util_Helper::getRoleBasedPicklistValues($field['name'],$roleid);
					foreach($picklistValues as $pvalue){
						if($pvalue != ''){
							$picklistValues1[] = array('value'=>$pvalue, 'label'=>vtranslate($pvalue,$module));
							$field['type']['picklistValues'] = $picklistValues1;
						}
					}
					if($uitype == 33 && $field['default'] != ''){
						$value = explode(' |##| ', $field['default']);
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
							if($v != ''){
								$multipicklistvalue[] = array('label'=>vtranslate($v,$module),'value'=>$v);
							}
						}
						$field['default'] = $multipicklistvalue;
						$field['type']['defaultValue'] = $multipicklistvalue;
					}
				}else if(in_array($uitype, array(5,6,23)) && $field['default'] != ''){
					$field['default'] = Vtiger_Date_UIType::getDisplayDateValue($field['default']);
					$field['type']['defaultValue'] = $field['default'];
				}
				//start code to remove unwanted fields 
				
				if($isFilter && $isFilter != 'false'){
					$allowedFields = array('time_start','time_end','duration_hours','hdnSubTotal','hdnGrandTotal','txtAdjustment','hdnTaxType','currency_id','pre_tax_total','received','paid','balance');
					$allowedFields[] = 'modifiedtime';
					$allowedFields[] = 'createdtime';
					$isDisplayField = false;
					$display_fieldtype = '';
					if(in_array($field['name'], column_array($displayFields,'field'))){
						$isDisplayField = true;
						$akey = array_search($field['name'],column_array($displayFields,'field'),true);
						$display_fieldtype = $displayFields[$akey]['display_fieldtype'];
					}
					$field['isDisplayField'] = $isDisplayField;
					$field['display_fieldtype'] = $display_fieldtype;
				}else{
					$allowedFields = array('productid','time_start','time_end','duration_hours','quantity','listprice','comment','discount_amount','discount_percent','hdnSubTotal','hdnGrandTotal','txtAdjustment','hdnDiscountPercent','hdnDiscountAmount','hdnTaxType','currency_id','pre_tax_total','received','paid','balance');
					$allowedFields = array_merge($allowedFields,$taxFields,$chargesFields,column_array($chargesTaxesFields,'chargeTaxname'));
				}


				if($displaytype != 1 && !in_array($field['name'],$allowedFields)){
					if($isFilter && $isFilter != 'false' && in_array($field['name'],array('eventstatus','activitytype'))){
					}else{
						unset($field);
						continue;
					}
				}

				if($uitype == 4 && $isFilter != true){
					unset($field);
					continue;
				}
				
				$field['headerfield'] = $fieldModel->get('headerfield');
				$field['summaryfield'] = $fieldModel->get('summaryfield');
				$field['uitype'] = $fieldModel->get('uitype');
				$field['typeofdata'] = $fieldModel->get('typeofdata');
				$field['displaytype'] = $fieldModel->get('displaytype');
				$field['quickcreate'] = $fieldModel->get('quickcreate');
				$field['blockId'] = $fieldBlock->id;
				$field['blockname'] = $fieldBlock->label;
				$field['blocklabel'] = vtranslate($fieldBlock->label, $module);
				$getSequencefieldQuery = $adb->pquery("SELECT sequence from vtiger_field where fieldid = ?", array($fieldId));
				$sequence = $adb->query_result($getSequencefieldQuery, 0, 'sequence');
				$field['sequence'] = $sequence;
				$field['readonly'] = false;
				if($field['name'] == 'hdnTaxType'){
					$picklistValues = $field['type']['picklistValues'];
					foreach ($picklistValues as $pkey => $pvalue) {
						if($pvalue['value'] == 'group'){
							$picklistValues[$pkey]['label'] = vtranslate('LBL_GROUP');
						}else if($pvalue['value'] == 'individual'){
							$picklistValues[$pkey]['label'] = vtranslate('LBL_INDIVIDUAL');
						}
					}
					$field['type']['picklistValues'] = $picklistValues;
					$field['sequence'] = "1";
					$field['type']['defaultValue'] = "group";
				}
				if(in_array($module,array('Invoice','Quotes','SalesOrder','PurchaseOrder'))){
					if($field['name'] == 'currency_id'){
						$field['sequence'] = "2";
					}
				}
				if($field['name'] == 'hdnSubTotal'){
					$field['sequence'] = "3";
					$field['readonly'] = true;
				}
				if($field['name'] == 'pre_tax_total'){
					$field['sequence'] = strval(8+count($chargesFields));
					$field['readonly'] = true;
				}
				if($field['name'] == 'hdnDiscountAmount'){
					$field['sequence'] = "5";
				}
				if($field['name'] == 'hdnDiscountPercent'){
					$field['sequence'] = "6";
				}
				if($field['name'] == 'txtAdjustment'){
					$field['sequence'] = strval(11+ count($chargesFields) + count($taxFields) + count($chargesTaxesFields));
				}
				if($field['name'] == 'hdnGrandTotal'){
					$field['sequence'] = strval(12+ count($chargesFields) + count($taxFields) + count($chargesTaxesFields));
					$field['readonly'] = true;
				}
				if($field['name'] == 'received'){
					$field['sequence'] = strval(13+ count($chargesFields) + count($taxFields) + count($chargesTaxesFields));
				}
				if($field['name'] == 'paid'){
					$field['sequence'] = strval(13+ count($chargesFields) + count($taxFields) + count($chargesTaxesFields));
				}
				if($field['name'] == 'balance'){
					$field['sequence'] = strval(14+ count($chargesFields) + count($taxFields) + count($chargesTaxesFields));
					$field['readonly'] = true;
				}
				
			}

			if(in_array($module,array('Invoice','Quotes','SalesOrder','PurchaseOrder'))){

				if(in_array($field['name'],$chargesFields)){
					$chargesid = array_search($field['name'], $chargesFields) + 1;
					$format = $InventoryCharges[$chargesid]->get('format');
					if($format != 'Percent'){
						$value = ($InventoryCharges[$chargesid]->get('value') * $conversionRate);
					}else{
						$value = $InventoryCharges[$chargesid]->get('value');
					}
					$field['headerfield'] = "";
					$field['summaryfield'] = "0";
					$field['uitype'] = "72";
					$field['typeofdata'] = "N~O";
					$field['displaytype'] = "3";
					$field['quickcreate'] = "1";
					$field['sequence'] = strval(7+$key);
					$field['type']['defaultValue'] =  number_format($value,$current_user->no_of_currency_decimals,'.','');
					$field['type']['format'] = $format;
					$field['chargesid'] = $chargesid;
					$field['blockId'] = "1835";
					$field['blockname'] = 'LBL_CHARGES';
					$field['blocklabel'] = vtranslate('LBL_CHARGES',$module);
				}
				
				if(in_array($field['name'],$taxFields)){
					$taxesid = array_search($field['name'], $taxFields) + 1;
					$format = $inventoryTaxes[$taxesid]->get('method');
					$field['type']['format'] = $format;
					if($format == 'Compound'){
						$compoundon = $inventoryTaxes[$taxesid]->getTaxesOnCompound();
						$field['type']['compoundon'] = $compoundon;
					}
					$field['taxid'] = $taxesid;
					$field['sequence'] = strval(9+count($chargesFields)+$key);
					if($format == 'Deducted'){
						$field['blockId'] = "1837";
						$field['blockname'] = 'LBL_DEDUCTED_TAXES';
						$field['blocklabel'] = vtranslate('LBL_DEDUCTED_TAXES',$module);
					}else{
						$field['blockId'] = "1834";
						$field['blockname'] = 'LBL_TAX';
						$field['blocklabel'] = vtranslate('LBL_TAX',$module);
					}
					
				}
				
				if(in_array($field['name'],column_array($chargesTaxesFields,'chargeTaxname'))){
					$chargesTaxKey = array_search($field['name'], column_array($chargesTaxesFields,'chargeTaxname'));
					$chargesTaxid = $chargesTaxesFields[$chargesTaxKey]['chargeTaxid'];
					$format = $ChargeTaxes[$chargesTaxid]->get('method');
					
					$chargename = '';
					foreach ($chargesFields as $charge) {
						if (strpos($field['name'], $charge.'_') !== false) {
							$chargename = $charge;
						}
					}
					$field['type']['format'] = $format;
					if($format == 'Compound'){
						$compoundon = $ChargeTaxes[$chargesTaxid]->getTaxesOnCompound();
						$field['type']['compoundon'] = $compoundon;
					}
					$field['type']['chargename'] = $chargename;
					$field['headerfield'] = "";
					$field['summaryfield'] = "0";
					$field['uitype'] = "1";
					$field['typeofdata'] = "N~O";
					$field['displaytype'] = "5";
					$field['quickcreate'] = "1";
					$field['taxid'] = $chargesTaxid;
					$field['sequence'] = strval(10+ count($chargesFields) + count($taxFields) + $key);
					$field['blockId'] = "1836";
					$field['blockname'] = 'LBL_TAXES_ON_CHARGES';
					$field['blocklabel'] = vtranslate('LBL_TAXES_ON_CHARGES',$module);
				}
				
			}

			if($module == 'Calendar' && $field['name'] == 'time_end'){
				continue;

			}

			if($field['name'] == 'quantity' || $field['name'] == 'listprice'){
				$field['mandatory'] = true;
			}

			if($field['mandatory'] == true){
				$field['quickcreate'] = "0";
			}

			
			if($fieldModel && $fieldModel->getFieldDataType() == 'owner') {
				$currentUser = Users_Record_Model::getCurrentUserModel();
                $users = $currentUser->getAccessibleUsers();
                $usersWSId = CTMobile_WS_Utils::getEntityModuleWSId('Users');
                foreach ($users as $id => $name) {
                    unset($users[$id]);
                    $users[$usersWSId.'x'.$id] = $name; 
                }
                
                $groups = $currentUser->getAccessibleGroups();
                $groupsWSId = CTMobile_WS_Utils::getEntityModuleWSId('Groups');
                foreach ($groups as $id => $name) {
                    unset($groups[$id]);
                    $groups[$groupsWSId.'x'.$id] = $name; 
                }

				//Special treatment to set default mandatory owner field
				if (!$field['default']) {
					$field['default'] = array("value"=>$usersWSId.'x'.$current_user->id,"label"=>html_entity_decode($current_user->first_name.' '.$current_user->last_name, ENT_QUOTES, $default_charset));
					$field['type']['defaultValue'] = $field['default'];
				}
			}
			if($fieldModel && $fieldModel->get('name') == 'salutationtype') {
				$values = $fieldModel->getPicklistValues();
				$picklistValues = array();
				foreach($values as $value => $label) {
					$picklistValues[] = array('value'=>trim($value), 'label'=>trim($label));
				}
				$field['type']['picklistValues'] = $picklistValues;
			}

			if(in_array($field['name'],column_array($chargesTaxesFields,'chargeTaxname'))){
				$field['type']['defaultValue'] = number_format($field['type']['defaultValue'],$current_user->no_of_currency_decimals,'.','');
			}

			foreach($field as $key => $value){
				if($value == null){
					if(in_array($key, array('nullable','mandatory','editable'))){
						$field[$key] = false;
					}else{
						$field[$key] = "";
					}
				}
			}

			if($field['type']['name'] == 'reference'){
				$refersTo = $field['type']['refersTo'];
				$refModule = array();
				if(!empty($refersTo)){
					foreach ($refersTo as $key => $rModule) {
						if($field['name'] != 'salesorder_id' && $field['name'] != 'assigned_user_id1' && $field['name'] != 'quote_id' && $field['name'] != 'invoiceid' && $field['name'] != 'productid'){
							if($rModule == 'Events'){
								$referenceModuleModel = Vtiger_Module_Model::getInstance('Calendar');
							}else{
								$referenceModuleModel = Vtiger_Module_Model::getInstance($rModule);
							}
							$userPrivModel = Users_Privileges_Model::getInstanceById($current_user->id);
							$createAction = $userPrivModel->hasModuleActionPermission($referenceModuleModel->getId(), 'CreateView');
							if($createAction){
								$isQuickCreate = true;
							}else{
								$isQuickCreate = false;
							}	
						}else{
							$isQuickCreate = false;
						}
						$refModule[] = array('value'=>$rModule,'label'=>vtranslate($rModule,$rModule),'isQuickCreate' => $isQuickCreate);
					}
					$field['type']['refersTo'] = $refModule;
				}
			}

			if($record == '' && in_array($module, array('Invoice','Quotes','SalesOrder','PurchaseOrder')) && $request->get('productid') != ''){
				
				$product_id = explode('x', trim($request->get('productid')));
				$productid = $product_id[1];
				$recordModel = Vtiger_Record_Model::getInstanceById($productid);
				$ModuleName = $recordModel->getModuleName();
				$InventoryCharges = Inventory_Charges_Model::getInventoryCharges();
				$shipping_handling_charge = $InventoryCharges[1]->get('value');
				if($module == 'PurchaseOrder'){
					$pretaxTotal = $recordModel->get('purchase_cost') + $shipping_handling_charge;
				}else{
					$pretaxTotal = $recordModel->get('unit_price') + $shipping_handling_charge;
				}
				$ProductTaxes = Inventory_TaxRecord_Model::getProductTaxes();
				$taxtotal = 0;
				foreach ($ProductTaxes as $key => $taxes) {
					$tax = $taxes->get('percentage');
					//$tax_.''.$key = ($pretaxTotal*$tax)/100;
					if($module == 'PurchaseOrder'){
						$taxtotal+= ($recordModel->get('purchase_cost')*$tax)/100;
					}else{
						$taxtotal+= ($recordModel->get('unit_price')*$tax)/100;
					}
				}
				$chargesTotal = 0;
				$ChargeTaxes = Inventory_TaxRecord_Model::getChargeTaxes();
				foreach ($ChargeTaxes as $key => $taxes) {
					$tax = $taxes->get('percentage');
					//$tax_.''.$key = ($shipping_handling_charge*$tax)/100;
					$chargesTotal+= ($shipping_handling_charge*$tax)/100;
				}
				$hdnGrandTotal = $pretaxTotal + $taxtotal +$chargesTotal;
				if(in_array($field['name'],array('productid','quantity','listprice','comment','discount_amount','discount_percent','hdnSubTotal','hdnGrandTotal','txtAdjustment','hdnDiscountPercent','hdnDiscountAmount','hdnTaxType','balance'))){
					$productName = decode_html(decode_html($recordModel->get('label')));
					$deleted = "0";
					$deletedMessage = vtranslate('LBL_THIS',$ModuleName).' '.vtranslate($ModuleName,$ModuleName).' '.vtranslate('LBL_IS_DELETED_FROM_THE_SYSTEM_PLEASE_REMOVE_OR_REPLACE_THIS_ITEM',$ModuleName);

					$disabledMessage = vtranslate($ModuleName,$ModuleName).''.vtranslate('LBL_MODULE_DISABLED',$ModuleName);
					if($field['name'] == 'productid'){
						$disabledModule = false;
						$WSId = CTMobile_WS_Utils::getEntityModuleWSId($entityType);
						$field['value'][] = array('value'=>$request->get('productid'),'label'=>$productName,'referenceModule'=>$ModuleName,'deleted'=>$deleted,'deletedMessage'=>$deletedMessage,'disabledModule'=>$disabledModule,'disabledMessage'=>$disabledMessage);
					}else if($field['name'] == 'quantity'){
						$field['value'][] = '1';
					}else if($field['name'] == 'listprice'){
						if($module == 'PurchaseOrder'){
							$field['value'][] =  number_format($recordModel->get('purchase_cost'), $currentUserModel->get('no_of_currency_decimals'),'.','');
						}else{
							$field['value'][] =  number_format($recordModel->get('unit_price'), $currentUserModel->get('no_of_currency_decimals'),'.','');
						}
					}else if($field['name'] == 'comment'){
						$field['value'][] = decode_html(decode_html($recordModel->get('description')));
					}else if($field['name'] == 'discount_amount'){
						$field['value'][] = "0";
					}else if($field['name'] == 'discount_percent'){
						$field['value'][] = "0";
					}else if($field['name'] == 'hdnSubTotal'){
						if($module == 'PurchaseOrder'){
							$field['value'] = number_format($recordModel->get('purchase_cost'), $currentUserModel->get('no_of_currency_decimals'),'.','');
						}else{
							$field['value'] = number_format($recordModel->get('unit_price'), $currentUserModel->get('no_of_currency_decimals'),'.','');
						}
					}else if($field['name'] == 'hdnGrandTotal'){
						//$hdnGrandTotal = $recordModel->get('unit_price') - $shipping_handling_charge;
						$field['value'] =  number_format($hdnGrandTotal, $currentUserModel->get('no_of_currency_decimals'),'.','');
					}else if($field['name'] == 'balance'){
						//$hdnGrandTotal = $recordModel->get('unit_price') - $shipping_handling_charge;
						$field['value'] =  number_format($hdnGrandTotal, $currentUserModel->get('no_of_currency_decimals'),'.','');
					}else if($field['name'] == 'hdnTaxType'){
						$field['value'] = 'group';
					}
					if($field['value'] == null){
						$field['value'] = "";
					}
					$field['type']['defaultValue'] = $field['value'];
				}
			}

			if(!empty($record)){
				$recordModel = Vtiger_Record_Model::getInstanceById($record,$module);
				//code start for merge by suresh

				$currencies = Inventory_Module_Model::getAllCurrencies();
				$currencyId = $recordModel->get('currency_id');
				foreach ($currencies as $currencyInfo) {
					if ($currencyId == $currencyInfo['curid']) {
						$conversionRateForPurchaseCost = $currencyInfo['conversionrate'];
						$currencysymbol = $currencyInfo['currencysymbol'];
						$conversionRate = $currencyInfo['conversionrate'];
						break;
					}
				}

				$refrenceUitypes = array(10,51,57,58,59,66,73,75,76,77,78,80,81,101);
				if(in_array($field['uitype'], $refrenceUitypes) && $field['name']!='productid'){
					$recordid = $recordModel->get($field['name']);
					if($recordid){
						if($field['name'] == 'assigned_user_id1'){
							$seQuery = $adb->pquery("SELECT first_name,last_name FROM vtiger_users WHERE id = ?",array($recordid));
							$first_name = $adb->query_result($seQuery,0,'first_name');
							$first_name = html_entity_decode($first_name, ENT_QUOTES, $default_charset);
							$last_name = $adb->query_result($seQuery,0,'last_name');
							$last_name = html_entity_decode($last_name, ENT_QUOTES, $default_charset);
						 	$WSId = CTMobile_WS_Utils::getEntityModuleWSId('Users');
							$field['value'] = array('value'=>$WSId.'x'.$recordid,'label'=>$first_name.' '.$last_name);
							$field['refrerenceModule'] = 'Users';
						}else{
							$seQuery = $adb->pquery("SELECT setype,label FROM vtiger_crmentity WHERE crmid = ?",array($recordid));
							$setype = $adb->query_result($seQuery,0,'setype');
							$label = $adb->query_result($seQuery,0,'label');
							$label = html_entity_decode($label, ENT_QUOTES, $default_charset);
						 	$WSId = CTMobile_WS_Utils::getEntityModuleWSId($setype);
							$field['value'] = array('value'=>$WSId.'x'.$recordid,'label'=>$label);
							$field['refrerenceModule'] = $setype;
						}
					}else{
						$field['value'] = array('value'=>"",'label'=>"");
						$field['refrerenceModule'] = "";
					}
					
				}else if($field['uitype'] == 53){
					$userid = $recordModel->get($field['name']);
					if($userid){
						$seQuery = $adb->pquery("SELECT first_name,last_name FROM vtiger_users WHERE id = ?",array($userid));
						$first_name = $adb->query_result($seQuery,0,'first_name');
						$first_name = html_entity_decode($first_name, ENT_QUOTES, $default_charset);
						$last_name = $adb->query_result($seQuery,0,'last_name');
						$last_name = html_entity_decode($last_name, ENT_QUOTES, $default_charset);
					 	$WSId = CTMobile_WS_Utils::getEntityModuleWSId('Users');
						$field['value'] = array('value'=>$WSId.'x'.$userid,'label'=>$first_name.' '.$last_name);
					}else{
						$field['value'] = array('value'=>"",'label'=>"");
					}
				}else if(in_array($field['name'], $lineItemsFields) && !in_array($field['name'], $otherhdnFields)){
					$productDetails = $recordModel->getProducts();
					foreach ($productDetails as $key => $product) {
						$entityType = $product['entityType'.$key];
						$hdnProductId = $product['hdnProductId'.$key];
						$productName = html_entity_decode($product['productName'.$key], ENT_QUOTES, $default_charset);
						$deleted = $product['productDeleted'.$key];
						if($deleted){
							$deleted = "1";
						}else{
							$deleted = "0";
						}
						$deletedMessage = vtranslate('LBL_THIS',$module).' '.vtranslate($entityType,$entityType).' '.vtranslate('LBL_IS_DELETED_FROM_THE_SYSTEM_PLEASE_REMOVE_OR_REPLACE_THIS_ITEM',$module);

						$disabledMessage = vtranslate($entityType,$entityType).''.vtranslate('LBL_MODULE_DISABLED',$module);
						if($field['name'] == 'productid'){
							$presence = array('0', '2');
							$userPrivModel = Users_Privileges_Model::getInstanceById($current_user->id);
							$ProductmoduleModel = Vtiger_Module_Model::getInstance($entityType);
							if (($userPrivModel->isAdminUser() ||
							$userPrivModel->hasGlobalReadPermission() ||
							$userPrivModel->hasModulePermission($ProductmoduleModel->getId())) && in_array($ProductmoduleModel->get('presence'), $presence)) {
								$disabledModule = false;
							}else{
								$disabledModule = true;
							}
							$WSId = CTMobile_WS_Utils::getEntityModuleWSId($entityType);
							$field['value'][] = array('value'=>$WSId.'x'.$hdnProductId,'label'=>$productName,'referenceModule'=>$entityType,'deleted'=>$deleted,'deletedMessage'=>$deletedMessage,'disabledModule'=>$disabledModule,'disabledMessage'=>$disabledMessage);
						}
						if($field['name'] == 'quantity'){
							$field['value'][] = $product['qty'.$key];
						}
						if($field['name'] == 'listprice'){
							//$field['value'][] = $product['listPrice'.$key] * $conversionRate;
							$field['value'][] = $product['listPrice'.$key];
						}
						if($field['name'] == 'comment'){
							$field['value'][] = decode_html(decode_html($product['comment'.$key]));
						}
						if($field['name'] == 'discount_amount'){
							$field['value'][] = $product['discount_amount'.$key];
						}
						if($field['name'] == 'discount_percent'){
							$field['value'][] = $product['discount_percent'.$key];
						}

						if($recordModel->get('hdnTaxType') == 'individual'){
							foreach ($productDetails[$key]['taxes'] as $keys => $value) {
								$taxname = $value['taxname']; 
								if($field['name'] == $taxname){
									$entityType = $product['entityType'.$key];
									$hdnProductId = $product['hdnProductId'.$key];
									$amount = $value['amount'];
									$percentage = $value['percentage'];
									$field['value'][] = array('key'=>$key,'productid'=>vtws_getWebserviceEntityId($entityType, $hdnProductId),'field_name'=>$taxname,'percentage'=>$percentage,'amount'=>$amount);
								}
							}

							foreach ($taxFields as $keys => $taxname) {
								if($field['name'] == $taxname && !in_array($taxname,column_array($productDetails[$key]['taxes'], 'taxname'))){
									if($field['type']['format'] == 'Deducted'){
										$taxfield = ltrim($field['name'],'tax');
										$field['value'] = $productDetails['1']['final_details']['deductTaxes'][$taxfield]['percentage'];
										$field['value'] = number_format($field['value'],3,'.','');
									}else{
										$field['value'][] = array('key'=>$key,'productid'=>"",'field_name'=>$taxname,'percentage'=>"",'amount'=>"");
									}
								}
							}
						
						}
						
					}
					if($recordModel->get('hdnTaxType') != 'individual'){
						if(in_array($field['name'],$taxFields)){
							$taxfield = ltrim($field['name'],'tax');
							if($field['type']['format'] == 'Deducted'){
								$field['value'] = $productDetails['1']['final_details']['deductTaxes'][$taxfield]['amount'];
							}else{
								$field['value'] = $productDetails['1']['final_details']['taxes'][$taxfield]['amount'];
							}
						}
					}
					if(in_array($field['name'],array('pre_tax_total','received','balance','paid'))){
						$field['value'] = number_format($recordModel->get($field['name']),$current_user->no_of_currency_decimals,'.','');
					}

					if(in_array($field['name'],$chargesFields)){
						if($field['type']['format'] == 'Flat'){
							$chargesid = $field['chargesid'];
							$field['type']['defaultValue'] = (string)$productDetails['1']['final_details']['chargesAndItsTaxes'][$chargesid]['value'];
							$field['value'] = (string)$productDetails['1']['final_details']['chargesAndItsTaxes'][$chargesid]['value'];
						}else{
							$chargesid = $field['chargesid'];
							$field['type']['defaultValue'] = (string)$productDetails['1']['final_details']['chargesAndItsTaxes'][$chargesid]['percent'];
							$field['value'] = (string)$productDetails['1']['final_details']['chargesAndItsTaxes'][$chargesid]['percent'];
						}
					}

					if(in_array($field['name'],column_array($chargesTaxesFields,'chargeTaxname'))){
						$taxid = $field['taxid'];
						$chargername = $field['type']['chargename'];
						foreach ($chargesFields as $ckey => $cvalue) {
							if($cvalue == $chargername){
								$chargesid = $ckey + 1;
							}
						}
						$field['type']['defaultValue'] = (string)$productDetails['1']['final_details']['chargesAndItsTaxes'][$chargesid]['taxes'][$taxid];
						$field['value'] = (string)$productDetails['1']['final_details']['chargesAndItsTaxes'][$chargesid]['taxes'][$taxid];
					}

					if($field['name'] == 'currency_id'){
						$WSId = CTMobile_WS_Utils::getEntityModuleWSId('Currency');
						$field['value'] = $WSId.'x'.$recordModel->get($field['name']);
					}
					
				}else if($field['name'] == 'folderid'){
					$WSId = CTMobile_WS_Utils::getEntityModuleWSId('DocumentFolders');
					$field['value'] = $WSId.'x'.$recordModel->get($field['name']);
				}else if($field['name'] == 'currency_id'){
					$WSId = CTMobile_WS_Utils::getEntityModuleWSId('Currency');
					$field['value'] = $WSId.'x'.$recordModel->get($field['name']);
				}else if($field['name'] == 'recurringtype'){
					$field['value'] = CTMobile_WS_Utils::RecurringDetails($idComponents[1],$module);
					$recurringInfo = $recordModel->getRecurringDetails();
					$recurringInfo['recurringfreq'] = $recurringInfo['repeat_frequency'];
					unset($recurringInfo['repeat_frequency']);
					if($recurringInfo['recurringtype'] == 'Monthly'){
						if($recurringInfo['repeatMonth'] == 'date'){
							$recurringInfo['recurringMonthType'] = "1";
							$recurringInfo['recurringDayOfMonth'] = $recurringInfo['repeatMonth_date'];
							unset($recurringInfo['repeatMonth_date']);
						}else{
							$recurringInfo['recurringMonthType'] = "2";
							if($recurringInfo['repeatMonth_daytype'] == 'first'){
								$recurringInfo['recurringDayType'] = "1";
							}else{
								$recurringInfo['recurringDayType'] = "2";
							}
							$recurringInfo['recurringDayOfWeek'] = $recurringInfo['repeatMonth_day'];
							unset($recurringInfo['repeatMonth_daytype']);
							unset($recurringInfo['repeatMonth_day']);
						}
						unset($recurringInfo['repeatMonth']);
					}
					if($recurringInfo['recurringtype'] == 'Weekly'){
						$repeat_str =explode(' ',$recurringInfo['repeat_str']);
						$weekdays = explode(',',$repeat_str[1]);
						$totalWeek = count($weekdays);
						$weekstr = "";
						for($i=0;$i<$totalWeek;$i++){
							if($i == $totalWeek-1){
								$weekstr.= str_replace("LBL_DAY", "", $weekdays[$i]);
							}else{
								$weekstr.= str_replace("LBL_DAY", "", $weekdays[$i]).',';
							}
						}
						$recurringInfo['recurringWeekDay'] = $weekstr;
					}
					unset($recurringInfo['repeat_str']);
					if($recurringInfo['recurringcheck'] == vtranslate('LBL_YES','Vtiger')){
						$recurringInfo['recurringcheck'] = "1";
					}else{
						$recurringInfo['recurringcheck'] = "0";
					}
					$field['recurringInfo'] = $recurringInfo;
					
				}else if($field['uitype'] == 33){
					$value = $recordModel->get($field['name']);
					if($value){
						$value = explode(' |##| ', $value);
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
					}else{
						$multipicklistvalue = array();
						$values = '';
					}
					$field['value'] = $values;
					$fieldname = $field['name'];
					$field[$fieldname.'_value'] = $multipicklistvalue;
					$field['type']['defaultValue'] = $multipicklistvalue;
				}else if($field['name'] == 'reminder_time'){
					$reminder_time = $recordModel->get($field['name']);
					if($reminder_time == 0){
					    $field['value'] = array('days'=>0,'hours'=>0,'minutes'=>0);
				    }else{
				   	   $reminder = $reminder_time;
					   $minutes = (int)($reminder)%60;
					   $hours = (int)($reminder/(60))%24;
					   $days =  (int)($reminder/(60*24));
					   $field['value'] = array('days'=>$days,'hours'=>$hours,'minutes'=>$minutes); 
				   }
				}else if($field['uitype'] == 69){
					global $adb,$site_URL;
					$AttachmentQuery =$adb->pquery("select vtiger_attachments.attachmentsid, vtiger_attachments.name, vtiger_attachments.subject, vtiger_attachments.path FROM vtiger_seattachmentsrel
									INNER JOIN vtiger_attachments ON vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid  
									WHERE vtiger_seattachmentsrel.crmid = ?", array($record));
									
					$AttachmentQueryCount = $adb->num_rows($AttachmentQuery);
					$document_path = array();
					
					if($AttachmentQueryCount > 0) {
						$name = $adb->query_result($AttachmentQuery, 0, 'name');
						$Path = $adb->query_result($AttachmentQuery, 0, 'path');
						$attachmentsId = $adb->query_result($AttachmentQuery, 0, 'attachmentsid');
						$ImageUrl = $site_URL.$Path.$attachmentsId."_".$name;
						$value = $recordModel->get($field['name']);
					} else {
						$ImageUrl = "";
						$value = "";
					}
					$field['value'] = $value;
					$field['ImageUrl'] = $ImageUrl;
				}else if(in_array($field['uitype'],array('5','6','23'))){
					if($field['name'] == 'date_start'){
						$value = $recordModel->get('date_start').' '.$recordModel->get('time_start');
						$value = Vtiger_Datetime_UIType::getDisplayDateTimeValue($value);
						$DATETIMEVALUE = explode(' ',$value);
						$field['value'] = $DATETIMEVALUE[0];
					}else if($field['name'] == 'due_date'){
						if($recordModel->get('time_end')){
							$value = $recordModel->get('due_date').' '.$recordModel->get('time_end');
							$value = Vtiger_Datetime_UIType::getDisplayDateTimeValue($value);
							$DATETIMEVALUE = explode(' ',$value);
							$field['value'] = $DATETIMEVALUE[0];
						}else{
							$field['value'] = $recordModel->get($field['name']);
							$field['value'] = Vtiger_Date_UIType::getDisplayDateValue($field['value']);
						}	
					}else{

						$field['value'] = $recordModel->get($field['name']);
						if($field['value'] != ''){
							$field['value'] = Vtiger_Date_UIType::getDisplayDateValue($field['value']);
						}
					}
				}else if($field['uitype'] == 70){
					$field['value'] = $recordModel->get($field['name']);
					$field['value'] = Vtiger_Datetime_UIType::getDisplayValue($field['value']);
				}else if($field['name'] == 'terms_conditions'){
					$field['value'] = html_entity_decode(decode_html($recordModel->get($field['name'])),ENT_QUOTES,$default_charset);
				}else if($field['name'] == 'filename'){
						global $adb,$site_URL;
						$query = "SELECT * FROM vtiger_attachments INNER JOIN vtiger_seattachmentsrel ON vtiger_seattachmentsrel.attachmentsid=vtiger_attachments.attachmentsid WHERE vtiger_seattachmentsrel.crmid=?";
						$result = $adb->pquery($query,array($record));
						$filename = $adb->query_result($result,0,'name');
						$attachmentsid = $adb->query_result($result,0,'attachmentsid');
						$path = $adb->query_result($result,0,'path');
						$filepath = $site_URL.$path.$attachmentsid.'_'.$filename;
						if(!empty($filename)){
							$field['filepath'] = $filepath;
							$field['ImageUrl'] = $filepath;
							$field['value'] = $filename;
						}else{
							$field['filepath'] = "";
							$field['ImageUrl'] = "";
							$field['value'] = "";
						}
				}else if($field['name'] == 'time_start' || $field['name'] == 'time_end'){
					$fname = $field['name'];
					if($fname == 'time_start'){
						$value = $recordModel->get('date_start').' '.$recordModel->get('time_start');
						$value = Vtiger_Datetime_UIType::getDisplayValue($value);
						$DATETIMEVALUE = explode(' ',$value);
						if(count($DATETIMEVALUE) > 2){
							$field['value'] = $DATETIMEVALUE[1].' '.$DATETIMEVALUE[2];
						}else{
							$field['value'] = $DATETIMEVALUE[1];
						}
					}else{
						$value = $recordModel->get('due_date').' '.$recordModel->get('time_end');
						$value = Vtiger_Datetime_UIType::getDisplayValue($value);
						$DATETIMEVALUE = explode(' ',$value);
						if(count($DATETIMEVALUE) > 2){
							$field['value'] = $DATETIMEVALUE[1].' '.$DATETIMEVALUE[2];
						}else{
							$field['value'] = $DATETIMEVALUE[1];
						}
					}
				}else if($field['type']['name'] == 'time'){
					$field['value'] = $recordModel->get($field['name']);
					if($field['value']){
						$fieldname = $field['name'];
						$fieldvalue = $field['value'];
						$field['value'] = $fieldModels[$fieldname]->getDisplayValue($fieldvalue);

					}

				}else{
					if($recordModel->get($field['name']) == '--None--'){
						$field['value'] = "";
					}else{
						$field['value'] = $recordModel->get($field['name']);
					}
				}
				
				
				if(!is_array($field['value'])){
					$field['value'] = html_entity_decode($field['value'],ENT_QUOTES,$default_charset);
					$field['value'] = html_entity_decode($field['value'],ENT_QUOTES,$default_charset);
				}
				if($field['name'] == 'recurringtype'){
					$field['type']['defaultValue'] = $field['recurringInfo'];
					unset($field['recurringInfo']);
				}else if($field['uitype'] == 33){
					$fieldname = $field['name'];
					$field['type']['defaultValue'] = $field[$fieldname.'_value'];
				}else{
					$field['type']['defaultValue'] = $field['value'];
				}
				
				//code end for merge
			}
			if(in_array($module,array('Invoice','Quotes','SalesOrder','PurchaseOrder'))){
				$totalblockFields = array('hdnSubTotal','hdnGrandTotal','txtAdjustment','hdnDiscountAmount','hdnDiscountPercent','hdnTaxType','currency_id','pre_tax_total','received','paid','balance');
				if(in_array($field['name'],$totalblockFields)){
					if($field['name'] == 'txtAdjustment'){
						$isAdd =  true;
						if($field['value'] < 0){
							$isAdd =  false;
							$field['value'] = abs($field['value']);
						}
						$field['isAdd'] = $isAdd;
					}
					$field['blockId'] = "1833";
					$field['blockname'] = 'LBL_ITEM_TOTAL';
					$field['blocklabel'] = vtranslate('Item Details Total','CTMobile');
				}
			}
			if(($field['uitype'] == 72 || $field['uitype'] == 71) && !in_array($field['name'],$chargesFields)){
				$fieldname = $field['name'];
				$fieldModel = $fieldModels[$fieldname]; 
				if(is_array($field['value'])){
					
				}else{
					if($field['value']){
						if($field['uitype'] == 72){
							$field['value'] = CurrencyField::convertToUserFormat($field['value'], null, true);
						}else{
							$field['value'] = CurrencyField::convertToUserFormat($field['value']);
						}
						$field['type']['defaultValue'] =$field['value'];
					}
				}
			}
			if($module == 'Documents'){
				if($field['name'] == 'filename'){
					$field['mandatory'] = true;
				}
			}
			if($field['type']['name'] == 'double'){
				if(is_array($field['value'])){
					
				}else{
					if($field['value']){
						$field['value'] = Vtiger_Double_UIType::getDisplayValue($field['value']);
						$field['type']['defaultValue'] = Vtiger_Double_UIType::getDisplayValue($field['type']['defaultValue']);
					}
				}
			}
			if($field['uitype'] == 56){
				if($record){
					if($field['value'] == 'on' || $field['value'] == '1'){
						$field['value'] = "1";
					}else if($field['value'] == "1"){
						
					}else{
						$field['value'] = "0";
					}
					$field['type']['defaultValue'] = $field['value'];
				}else{
					if($field['default']){
						if($field['default'] == 'on' || $field['default'] == '1'){
							$field['default'] = "1";
						}else{
							$field['default'] = "0";
						}
						$field['type']['defaultValue'] = $field['default'];
					}
				}
			}
			if($field['readonly'] == ''){
				$field['readonly'] = false;
			}
			if($field['isDisplayField'] == ''){
				$field['isDisplayField'] = false;
			}
			if($field['name'] == $entityField){
				$entityFieldInfo = $field;
			}
			
			$newFields[] = $field;
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

        		foreach ($newFields as $key => $fields) {
        			if($fields['name'] == $auto_search){
        				$newFields[$key]['isDisplayMap'] = true;
        				$newFields[$key]['GeoFields'] = true;
        			}else{
        				$newFields[$key]['isDisplayMap'] = false;
        			}
        			if($fields['name'] == $street){
        				$newFields[$key]['GeoFields'] = true;
        				$newFields[$key]['GeoFieldsName'] = 'street';
        			}else if($fields['name'] == $city){
        				$newFields[$key]['GeoFields'] = true;
        				$newFields[$key]['GeoFieldsName'] = 'city';
        			}else if($fields['name'] == $state){
        				$newFields[$key]['GeoFields'] = true;
        				$newFields[$key]['GeoFieldsName'] = 'state';
        			}else if($fields['name'] == $postalcode){
        				$newFields[$key]['GeoFields'] = true;
        				$newFields[$key]['GeoFieldsName'] = 'postalcode';
        			}else if($fields['name'] == $country){
        				$newFields[$key]['GeoFields'] = true;
        				$newFields[$key]['GeoFieldsName'] = 'country';
        			}else{
        				$newFields[$key]['GeoFields'] = false;
        			}
        		}
        	}
        }else{
        	foreach ($newFields as $key => $fields) {
        		$newFields[$key]['GeoFields'] = false;
        	}
        }
		//code End

		if($module == 'Events'){
			$USER_MODEL = Users_Record_Model::getCurrentUserModel();
			$AccessibleUsers = array_keys($USER_MODEL->getAccessibleUsers());
			$field = array();
			$query = "SELECT * FROM vtiger_users WHERE status='Active' AND id!='".$USER_MODEL->getId()."' ORDER BY first_name ASC";
			$result = $adb->pquery($query, array());
			$picklistValues = Array();
			if($adb->num_rows($result) > 0) {
				while ($row = $adb->fetch_array($result)) {
					if(in_array($row['id'], $AccessibleUsers)){
						//Need to decode the picklist values twice which are saved from old ui
						$value = $row['first_name'].' '.$row['last_name'];
						$picklistValues[]= array('value'=>decode_html($row['id']), 'label'=>decode_html($value));
					}
				}
			}
			$field['name'] = "invite_user";
			$field['label'] = vtranslate("LBL_INVITE_USERS",$module);
			$field['mandatory'] = false;
			$field['type']['picklistValues'] = $picklistValues;
			$field['type']['defaultValue'] = "";
			$field['type']['name'] = "multipicklist";
			$field['nullable'] = true;
			$field['editable'] = true;
			$field['default'] = "";
			$field['headerfield'] = "";
			$field['summaryfield'] = "0";
			$field['uitype'] = "33";
			$field['typeofdata'] = "V~O";
			$field['displaytype'] = "1";
			$field['quickcreate'] = "1";
			$field['blockId'] = "1844";
			$field['blockname'] = "LBL_INVITE_USER_BLOCK";
			$field['blocklabel'] = vtranslate("LBL_INVITE_USER_BLOCK",$module);
			$field['sequence'] = "1";
			$field['readonly'] = false;
			if(!empty($record)){
				$getInvites = $adb->pquery("SELECT * FROM vtiger_invitees where activityid = ?", array($record));
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

				$field['value'] = $invite_user_value;
				if(!empty($field['value'])){
					$field['type']['defaultValue'] = $field['value'];   
				}
			}
			$newFields[] = $field;
		}
		foreach($newFields as $key=> $fields){
			$sort[$key] = $fields['sequence'];
			$newFields[$key]['label'] = html_entity_decode($fields['label'], ENT_QUOTES, $default_charset);
		}
		array_multisort($sort, SORT_ASC, $newFields);
		
		$inventoryTaxes = Inventory_TaxRecord_Model::getProductTaxes();
		foreach($newFields as $key=> $fields){
				foreach($inventoryTaxes as $taxFields){
					if($newFields[$key]['name'] == $taxFields->get('taxname')){
						$newFields[$key]['default'] = $taxFields->get('percentage');
						if($record == ''){
							$newFields[$key]['default'] = number_format($newFields[$key]['default'],$current_user->no_of_currency_decimals,'.','');
							$newFields[$key]['type']['defaultValue'] = $newFields[$key]['default'];
						}else{
							if($recordModel->get('hdnTaxType') == 'individual'){
								$newFields[$key]['type']['defaultValue'] = $newFields[$key]['value'];
							}else{
								$newFields[$key]['default'] = number_format($newFields[$key]['default'],$current_user->no_of_currency_decimals,'.','');
								$newFields[$key]['type']['defaultValue'] = $newFields[$key]['default'];
							}
						}
					}
			}
			$newFields[$key]['label'] = html_entity_decode($fields['label'], ENT_QUOTES, $default_charset);
		}
		
		$blocks = $moduleModel->getBlocks();
		foreach($blocks as $block){
			$blockId = $block->get('id');
			$blockname = $block->get('label');
			if($module == 'SalesOrder' && $blockname == 'Recurring Invoice Information'){
				continue;
			}
			$blocklabel = vtranslate($block->get('label'),$module);
			$blockfield = array();
			foreach ($newFields as $key => $value) {
				if($value['blockId'] == $blockId){
					unset($value['blockId']);
					unset($value['blockname']);
					unset($value['blocklabel']);
					$blockfield[] = $value;
					unset($newFields[$key]);
				}
			}
			if($blockfield == ''){
				continue;
			}
			$describe['blocks'][] = array('blockId'=>$blockId,'blockname'=>$blockname,'blocklabel'=>decode_html(decode_html($blocklabel)),'fields'=>$blockfield);
		}

		if(in_array($module,array('Invoice','Quotes','SalesOrder','PurchaseOrder'))){
			$totalblockfield = array();
			$taxblockFields = array();
			$chargeblockFields = array();
			$taxonchargeFields = array();
			$deductedTaxesFields = array();
			foreach ($newFields as $key => $value) {
				if($value['blockname'] == 'LBL_ITEM_TOTAL'){
					unset($value['blockId']);
					unset($value['blockname']);
					unset($value['blocklabel']);
					$totalblockfield[] = $value;
					unset($newFields[$key]);
				}else if($value['blockname'] == 'LBL_CHARGES'){
					unset($value['blockname']);
					unset($value['blocklabel']);
					$chargeblockFields[] = $value;
					unset($newFields[$key]);
				}else if($value['blockname'] == 'LBL_TAX'){
					unset($value['blockname']);
					unset($value['blocklabel']);
					$taxblockFields[] = $value;
					unset($newFields[$key]);
				}else if($value['blockname'] == 'LBL_TAXES_ON_CHARGES'){
					unset($value['blockname']);
					unset($value['blocklabel']);
					$taxonchargeFields[] = $value;
					unset($newFields[$key]);
				}else if($value['blockname'] == 'LBL_DEDUCTED_TAXES'){
					unset($value['blockname']);
					unset($value['blocklabel']);
					$deductedTaxesFields[] = $value;
					unset($newFields[$key]);
				}
			}
			if($isFilter && $isFilter != 'false'){
				$describe['blocks'][] = array('blockId'=>'1833','blockname'=>'LBL_ITEM_TOTAL','blocklabel'=>vtranslate('Item Details Total','CTMobile'),'fields'=>$totalblockfield);
			}else{
				$describe['blocks'][] = array('blockId'=>'1834','blockname'=>'LBL_CHARGES','blocklabel'=>vtranslate('LBL_CHARGES',$module),'fields'=>$chargeblockFields);
				$describe['blocks'][] = array('blockId'=>'1835','blockname'=>'LBL_TAX','blocklabel'=>vtranslate('LBL_TAX',$module),'fields'=>$taxblockFields);
				$describe['blocks'][] = array('blockId'=>'1836','blockname'=>'LBL_TAXES_ON_CHARGES','blocklabel'=>vtranslate('LBL_TAXES_ON_CHARGES',$module),'fields'=>$taxonchargeFields);
				$describe['blocks'][] = array('blockId'=>'1837','blockname'=>'LBL_DEDUCTED_TAXES','blocklabel'=>vtranslate('LBL_DEDUCTED_TAXES',$module),'fields'=>$deductedTaxesFields);
				$describe['blocks'][] = array('blockId'=>'1833','blockname'=>'LBL_ITEM_TOTAL','blocklabel'=>vtranslate('Item Details Total','CTMobile'),'fields'=>$totalblockfield);
			}
		}else if($module == 'Events'){
			$blockfield = array();
			foreach ($newFields as $key => $value) {
				if($value['blockname'] == 'LBL_INVITE_USER_BLOCK'){
					unset($value['blockId']);
					unset($value['blockname']);
					unset($value['blocklabel']);
					$blockfield[] = $value;
					unset($newFields[$key]);
				}
			}
			$describe['blocks'][] = array('blockId'=>'1844','blockname'=>'LBL_INVITE_USER_BLOCK','blocklabel'=>vtranslate('LBL_INVITE_USER_BLOCK',$module),'fields'=>$blockfield);
		}


		//code start for barcode field by suresh
		if(in_array($module,array('Invoice','Quotes','SalesOrder','PurchaseOrder','Products'))){
			$moduleName = 'Products';
			$rs_field=$adb->pquery("SELECT * FROM `ctmobile_barcode_fields` WHERE module=?",array($moduleName));
            if($adb->num_rows($rs_field) > 0) {
                while($row=$adb->fetch_array($rs_field)) {
                	$fieldname = explode(':', $row['fieldname']);
                    $selectedFields=$fieldname[1];
                }
            }
            if($selectedFields != ''){
            	$describe['barcode_field'] = $selectedFields;
            }else{
            	$describe['barcode_field'] = 'productcode';
            }

		}else{
			$describe['barcode_field'] = "";
		}
		if(in_array($module, array('Invoice','Quotes','SalesOrder','PurchaseOrder'))){
			if(!empty($record)){
            	$describe['hdnTaxType'] = $recordModel->get('hdnTaxType');
            }
		}
		//code end for barcode field by suresh
		
		$response = new CTMobile_API_Response();
		$QuickCreateAction = $moduleModel->isQuickCreateSupported();
		$describe['QuickCreateAction'] = $QuickCreateAction;
		$describe['label'] = vtranslate($describeInfo['label'],$module);
		$describe['name'] = $describeInfo['name'];
		$describe['createable'] = $describeInfo['createable'];
		$describe['updateable'] = $describeInfo['updateable'];
		$describe['deleteable'] = $describeInfo['deleteable'];
		$describe['retrieveable'] = $describeInfo['retrieveable'];
		$describe['idPrefix'] = $describeInfo['idPrefix'];
		$describe['isEntity'] = $describeInfo['isEntity'];
		$describe['allowDuplicates'] = $describeInfo['allowDuplicates'];
		$describe['labelFields'] = $describeInfo['labelFields'];
		$describe['entityField'] = $entityFieldInfo;
		if(!empty($record)){
			if(in_array($module, array('Invoice','Quotes','SalesOrder','PurchaseOrder'))){
				$WSId = CTMobile_WS_Utils::getEntityModuleWSId('Currency');
				$currency_id = $WSId.'x'.$recordModel->get('currency_id');
				foreach ($currencyPicklistvalues as $key => $currency) {
					if($currency_id == $currency['value']){
						$currency_symbol = $currency['symbol'];
					}
				}
				$describe['currency_symbol'] = decode_html(decode_html($currency_symbol));
			}else{
				$currency_symbol = html_entity_decode($current_user->currency_symbol, ENT_QUOTES, $default_charset);
				$describe['currency_symbol'] = $currency_symbol;
			}
		}else{
			$currency_symbol = html_entity_decode($current_user->currency_symbol, ENT_QUOTES, $default_charset);
			$describe['currency_symbol'] = $currency_symbol;
		}
		$response->setResult(array('describe'=>$describe));
		
		return $response;
	}

}

function ctTranslate($keyword,$language){
	global $adb;
	$default_charset = VTWS_PreserveGlobal::getGlobal('default_charset');
	$checkLangSQL = "SELECT language_keyword FROM ctmobile_language_keyword WHERE keyword = ? AND keyword_lang = ?";
	$resultLang = $adb->pquery($checkLangSQL,array($keyword,$language));
	if($adb->num_rows($resultLang) > 0){
		return html_entity_decode($adb->query_result($resultLang,0,'language_keyword'),ENT_QUOTES,$default_charset);
	}else{
		$checkdefaultLangSQL = "SELECT language_keyword FROM ctmobile_language_keyword WHERE keyword = ? AND keyword_lang = ?";
		$resultDefaultLang = $adb->pquery($checkdefaultLangSQL,array($keyword,'en_us'));
		if($adb->num_rows($resultLang) > 0){
			return html_entity_decode($adb->query_result($resultDefaultLang,0,'language_keyword'),ENT_QUOTES,$default_charset);
		}else{
			return $keyword;
		}
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
