<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

class CTMobile_WS_GetInventoryData extends CTMobile_WS_Controller {
	
	function process(CTMobile_API_Request $request) {
		global $adb, $current_user,$site_URL;
		$current_user = $this->getActiveUser();
		$decimalPlace = getCurrencyDecimalPlaces();
		$currencies = Inventory_Module_Model::getAllCurrencies();
		$conversionRate = $conversionRateForPurchaseCost = 1;
		$module = trim($request->get('module'));
		$taxtype = trim($request->get('taxtype'));
		$currency = explode('x',$request->get('currency_id'));
		$old_currency = $request->get('old_currency_id');
		$hdnDiscountAmount = $request->get('hdnDiscountAmount');
		$hdnDiscountPercent = $request->get('hdnDiscountPercent');
		$AdjustmentType = $request->get('AdjustmentType');
		$txtAdjustment = $request->get('txtAdjustment');
		$received = $request->get('received');
		$paid = $request->get('paid');
		$currencyId = $currency[1];
		$idList = $request->get('idlist');

		if (!$idList) {
			$idList = array();
		}else{
			$idList = Zend_Json::decode($idList);

		}

		$namesList = $purchaseCostsList = $taxesList = $listPricesList = $listPriceValuesList = array();
		$descriptionsList = $quantitiesList = $imageSourcesList = $productIdsList = $baseCurrencyIdsList = array();
		$totalListPrice = 0;
		foreach($idList as $key => $value) {
			$recordid = explode('x',$value['productid']);
			$id = $recordid[1];
			$recordModel = Vtiger_Record_Model::getInstanceById($id);
			$taxes = $recordModel->getTaxes();
			foreach ($taxes as $key => $taxInfo) {
				$taxInfo['compoundOn'] = json_encode($taxInfo['compoundOn']);
				$taxes[$key] = $taxInfo;
			}

			//$taxesList[$id]				= $taxes;
			$namesList[$id]				= decode_html(decode_html($recordModel->getName()));
			$quantitiesList[$id]		= $recordModel->get('qtyinstock');
			$descriptionsList[$id]		= decode_html($recordModel->get('description'));
			$listPriceValuesList[$id]	= $recordModel->getListPriceValues($recordModel->getId());

			$priceDetails = $recordModel->getPriceDetails();
			foreach ($priceDetails as $currencyDetails) {
				if ($currencyId == $currencyDetails['curid']) {
					$currencysymbol = $currencyDetails['currencysymbol'];
					$conversionRate = $currencyDetails['conversionrate'];
				}
			}

			if(!empty($old_currency)){
				$old_currency_id = explode('x', $old_currency);
				$prev_currency_id = $old_currency_id[1];
				foreach ($priceDetails as $currencyDetails) {
					if ($prev_currency_id == $currencyDetails['curid']) {
						$prev_currencysymbol = $currencyDetails['currencysymbol'];
						$prev_conversionRate = $currencyDetails['conversionrate'];
					}
				}
			}
			if($module == 'PurchaseOrder'){
				$listPricesList[$id] = (float)$recordModel->get('purchase_cost') * (float)$conversionRate;
			}else{
				$listPricesList[$id] = (float)$recordModel->get('unit_price') * (float)$conversionRate;
			}
			$netListPrice[$id] = 0;
			if($value['quantity'] != ''){
				if($value['listprice'] != ''){
					if(!empty($prev_currency_id)){
						$value['listprice'] = $value['listprice'] / $prev_conversionRate;
					}
					$listPricesList[$id] = (float)$value['listprice'] * (float)$conversionRate;
					$totalListPrice = $totalListPrice + $listPricesList[$id] * $value['quantity'];
					$netListPrice[$id] = $listPricesList[$id] * $value['quantity'];
				}else{
					$totalListPrice = $totalListPrice + $listPricesList[$id] * $value['quantity'];
					$netListPrice[$id] = $listPricesList[$id] * $value['quantity'];
				}
			}else{
				if($value['listprice'] != ''){
					if(!empty($prev_currency_id)){
						$value['listprice'] = $value['listprice'] / $prev_conversionRate;
					}
					$listPricesList[$id] = (float)$value['listprice'] * (float)$conversionRate;
					$totalListPrice = $totalListPrice + $listPricesList[$id];
					$netListPrice[$id] = $listPricesList[$id];
				}else{
					$totalListPrice = $totalListPrice + $listPricesList[$id];
					$netListPrice[$id] = $listPricesList[$id];
				}
			}
			 
			foreach ($currencies as $currencyInfo) {
				if ($currencyId == $currencyInfo['curid']) {
					$conversionRateForPurchaseCost = $currencyInfo['conversionrate'];
					break;
				}
			}

			$discount_amount[$id] = 0;
			if($value['discount_amount'] != ''){
				$discount_amount[$id] = $value['discount_amount'];
				if(!empty($prev_currency_id)){
					$discount_amount[$id] = $discount_amount[$id] / $prev_conversionRate;
				}
				$discount_amount[$id] = (float)$discount_amount[$id] * (float)$conversionRate;
			}

			$purchaseCostsList[$id] = round((float)$recordModel->get('purchase_cost') * (float)$conversionRateForPurchaseCost, $decimalPlace);
			$baseCurrencyIdsList[$id] = getProductBaseCurrency($id, $recordModel->getModuleName());

			if ($recordModel->getModuleName() == 'Products') {
				$productIdsList[] = $id;
			}

			$imageSourcesList[$id] = "";

			$productTaxes[$id] = array();
			$productTaxesTotal[$id] = 0;
			if ($taxtype == 'individual') {
				$taxDetails = getTaxDetailsForProduct($id, 'all');
				$taxCount = count($taxDetails);
				for($j=0; $j<$taxCount; $j++) {	
					$method = $taxDetails[$j]['method'];
					$taxid = $taxDetails[$j]['taxid'];
					if($method == 'Simple'){
						$taxname = $taxDetails[$j]['taxname'];
						$taxlabel = vtranslate($taxDetails[$j]['taxlabel'],$recordModel->getModuleName());
						$taxValue = $taxDetails[$j]['percentage'];
						$taxAmount = $netListPrice[$id] * $taxValue / 100;
						$productTaxesTotal[$id] =  $productTaxesTotal[$id] + $taxAmount;
						$taxAmount = number_format($taxAmount,$decimalPlace,'.','');
						$productTaxes[$id][] = array('taxid'=>$taxid,'taxname'=>$taxname,'taxlabel'=>$taxlabel,'method'=>$method,'percentage'=>$taxValue,'amount'=>$taxAmount);
					}elseif ($method == 'Compound') {
						$taxname = $taxDetails[$j]['taxname'];
						$taxlabel = vtranslate($taxDetails[$j]['taxlabel'],$recordModel->getModuleName());
						$taxValue = $taxDetails[$j]['percentage'];
						$taxAmount = $netListPrice[$id] * $taxValue / 100;
						$compoundTaxes = $taxDetails[$j]['compoundon'];
						$compoundOn = $taxAmount;
						foreach ($compoundTaxes as $key => $ctaxValue) {
							$compoundOn+= $taxAmount * $taxDetails[$ctaxValue-1]['percentage'] / 100;
						}
						$productTaxesTotal[$id] =  $productTaxesTotal[$id] + $compoundOn;
						$compoundOn = number_format($compoundOn,$decimalPlace,'.','');
						$productTaxes[$id][] = array('taxid'=>$taxid,'taxname'=>$taxname,'taxlabel'=>$taxlabel,'method'=>$method,'compoundTaxes'=>$compoundTaxes,'percentage'=>$taxValue,'amount'=>$compoundOn);
					}
				}
			}

			$netPrice[$id] = $netListPrice[$id] + $productTaxesTotal[$id] - $discount_amount[$id];
			$totalListPrice = $totalListPrice + $productTaxesTotal[$id] - $discount_amount[$id];
			$discount_amount[$id] = number_format($discount_amount[$id], $decimalPlace,'.','');
			$productTaxesTotal[$id] = number_format($productTaxesTotal[$id],$decimalPlace,'.','');
			$netPrice[$id] = number_format($netPrice[$id],$decimalPlace,'.','');
			
		}

		foreach ($currencies as $currencyInfo) {
			if ($currencyId == $currencyInfo['curid']) {
				$conversionRateForPurchaseCost = $currencyInfo['conversionrate'];
				$currencysymbol = $currencyInfo['currencysymbol'];
				$conversionRate = $currencyInfo['conversionrate'];
				break;
			}
		}

		if(!empty($old_currency)){
			$old_currency_id = explode('x', $old_currency);
			$prev_currency_id = $old_currency_id[1];
			foreach ($currencies as $currencyInfo) {
				if ($prev_currency_id == $currencyInfo['curid']) {
					$prev_currencysymbol = $currencyInfo['currencysymbol'];
					$prev_conversionRate = $currencyInfo['conversionrate'];
				}
			}
		}

		$discountAmount = 0;
		if($hdnDiscountAmount != ''){
			if(!empty($prev_currency_id)){
				$hdnDiscountAmount = $hdnDiscountAmount / $prev_conversionRate;
			}
			$discountAmount = (float)$hdnDiscountAmount * (float)$conversionRate;
		}else if($hdnDiscountPercent != ''){
			$discountAmount = ( $hdnDiscountPercent * $totalListPrice ) / 100;
		}

		
		$InventoryCharges = Inventory_Charges_Model::getInventoryCharges();
		$chargesAmount = 0;
		foreach($InventoryCharges as $InventoryCharge){
			$chargeid = $InventoryCharge->get('chargeid');
			if($InventoryCharge->get('format') == 'Flat'){
				$amount = ($InventoryCharge->get('value') * (float)$conversionRate);
			}else if($InventoryCharge->get('format') == 'Percent'){
				if($discountAmount != 0){
					$amount = ( ($totalListPrice - $discountAmount) * $InventoryCharge->get('value'))/100;
				}else{
					$amount = ($totalListPrice * $InventoryCharge->get('value'))/100;
				}
			}else{
				$amount = $InventoryCharge->get('value');
			}
			$chargesAmount+=$amount;
			$chargesName = strtolower(str_replace(' ','_', $InventoryCharge->get('name')));
			$amount = number_format($amount,$decimalPlace,'.','');
			$charges[] = array('chargeid'=>$chargeid,'name'=>decode_html(decode_html($chargesName)),'label' =>decode_html(decode_html($InventoryCharge->get('name'))) ,'format'=>$InventoryCharge->get('format'),'value'=>$InventoryCharge->get('value'),'amount'=>$amount);
		}

		if($taxtype == 'individual'){
			$pre_tax_Total = $totalListPrice;
			$grand_pre_tax_Total = $totalListPrice + $chargesAmount;
			foreach($idList as $idkey => $idvalue) {
				$record__id = explode('x',$idvalue['productid']);
				$recid = $record__id[1];
				$indiTaxTotal = $productTaxesTotal[$recid];
				$pre_tax_Total = $pre_tax_Total - $indiTaxTotal;
			}
			$pre_tax_Total = $pre_tax_Total + $chargesAmount - $discountAmount;
		}else{
			$pre_tax_Total = $totalListPrice + $chargesAmount - $discountAmount;
		}
		$ProductTax = Inventory_TaxRecord_Model::getProductTaxes();
		$taxestotal = 0;
		$taxesList = array();
		$deductedTaxesList = array();
		$taxesOnCharges = array();
		
		foreach ($ProductTax as $key => $taxes) {
			$method = $taxes->get('method');
			$taxid = $taxes->get('taxid');
			if($method == 'Simple'){
				if ($taxtype != 'individual') {
					$taxname = $taxes->get('taxname');
					$taxlabel = vtranslate($taxes->get('taxlabel'),'Products');
					$taxValue = $taxes->get('percentage');
					if($discountAmount != 0){
						$taxAmount = (($totalListPrice - $discountAmount) * $taxValue) / 100;
					}else{
						$taxAmount = ($totalListPrice * $taxValue) / 100;
					}
					$taxestotal =  $taxestotal + $taxAmount;
					$taxAmount = number_format($taxAmount,$decimalPlace,'.','');
					$taxesList[] = array('taxid'=>$taxid,'taxname'=>decode_html(decode_html($taxname)),'taxlabel'=>$taxlabel,'method'=>$method,'percentage'=>$taxValue,'amount'=>$taxAmount);
				}
			}else if($method == 'Compound'){
				if ($taxtype != 'individual') {
					$taxname = $taxes->get('taxname');
					$percentage = $taxes->get('percentage');
					$taxValue = $taxes->get('percentage');
					if($discountAmount != 0){
						$taxAmount = ($totalListPrice - $discountAmount) * $percentage / 100;
					}else{
						$taxAmount = $totalListPrice * $percentage / 100;
					}
					$taxlabel = vtranslate($taxes->get('taxlabel'),'Products');
					$compoundTaxes = $taxes->getTaxesOnCompound();
					$compoundOn = $taxAmount;
					if($taxAmount != 0){
						foreach ($compoundTaxes as $key => $ctaxValue) {
							$compoundOn+= $taxAmount * $ProductTax[$ctaxValue]->get('percentage') / 100;
						}
					}
					$taxestotal =  $taxestotal + $compoundOn;
					$compoundOn = number_format($compoundOn,$decimalPlace,'.','');
					$taxesList[] = array('taxid'=>$taxid,'taxname'=>decode_html(decode_html($taxname)),'taxlabel'=>$taxlabel,'method'=>$method,'compoundTaxes'=>$compoundTaxes,'percentage'=>$percentage,'amount'=>$compoundOn);
				}
			}else if($method == 'Deducted'){
				$taxname = $taxes->get('taxname');
				$taxlabel = vtranslate($taxes->get('taxlabel'),'Products');
				$taxValue = $taxes->get('percentage');
				if($discountAmount != 0){
					$taxAmount = ($totalListPrice - $discountAmount) * $taxValue / 100;
				}else{
					$taxAmount = $totalListPrice * $taxValue / 100;
				}
				$taxAmount = number_format($taxAmount,$decimalPlace,'.','');
				$deductedTaxesList[] = array('taxid'=>$taxid,'taxname'=>decode_html(decode_html($taxname)),'taxlabel'=>$taxlabel,'method'=>$method,'percentage'=>$taxValue,'amount'=>$taxAmount);
			}

		}

		$chargesTotal = 0;
		$ChargeTaxesList = Inventory_Charges_Model::getChargeTaxesList();
		$ChargeTaxes = Inventory_TaxRecord_Model::getChargeTaxes();
		foreach ($ChargeTaxesList as $chargesid => $chargesTax) {
			foreach ($ChargeTaxes as $key => $taxes) {
				if(in_array($key, array_keys($chargesTax))){
					$taxid = $taxes->get('taxid');
					$method = $taxes->get('method');
					if($method == 'Simple'){
						$chargesname = $charges[$chargesid-1]['name'];
						$chargesAmountValue = $charges[$chargesid-1]['amount'];
						$taxname = $taxes->get('taxname');
						$taxlabel = vtranslate($taxes->get('taxlabel'),'Products');
						$taxValue = $taxes->get('percentage');
						$taxAmount = ($chargesAmountValue *$taxValue)/100;
						$chargesTotal = $chargesTotal + $taxAmount;
						$taxAmount = number_format($taxAmount,$decimalPlace,'.','');
						$taxfieldName = $chargesname.'_'.decode_html(decode_html($taxname));
						$taxesOnCharges[] = array('taxid'=>$taxid,'charges'=>$chargesname,'taxname'=>decode_html(decode_html($taxname)),'taxfieldName'=>$taxfieldName,'taxlabel'=>$taxlabel,'percentage'=>$taxValue,'amount'=>$taxAmount);
					}else if($method == 'Compound'){
						$chargesname = $charges[$chargesid-1]['name'];
						$chargesAmountValue = $charges[$chargesid-1]['amount'];
						$taxname = $taxes->get('taxname');
						$taxlabel = vtranslate($taxes->get('taxlabel'),'Products');
						$taxValue = $taxes->get('percentage');
						$taxAmount = ($chargesAmountValue *$taxValue)/100;
						//$chargesTotal = $chargesTotal + $taxAmount;
						$compoundTaxes = $taxes->getTaxesOnCompound();
						$compoundOn = $taxAmount;
						foreach ($compoundTaxes as $key => $ctaxValue) {
							if(in_array($ctaxValue, array_keys($chargesTax))){
								$compoundOn+= $taxAmount * $ChargeTaxes[$ctaxValue]->get('percentage') / 100;
							}
						}
						$chargesTotal = $chargesTotal + $compoundOn;
						$compoundOn = number_format($compoundOn,$decimalPlace,'.','');
						$taxAmount = number_format($taxAmount,$decimalPlace,'.','');
						$taxfieldName = $chargesname.'_'.decode_html(decode_html($taxname));
						$taxesOnCharges[] = array('taxid'=>$taxid,'charges'=>$chargesname,'taxname'=>decode_html(decode_html($taxname)),'taxfieldName'=>$taxfieldName,'taxlabel'=>$taxlabel,'percentage'=>$taxValue,'amount'=>$compoundOn);
					}
				}else{
					//$taxesOnCharges[] = array();
				}	
			}
		}

		if($hdnDiscountPercent != ''){
			$discountAmount = 0;
		}
		$discountAmount = number_format($discountAmount, $decimalPlace,'.','');

		if($taxtype == 'individual'){
			$Total = $grand_pre_tax_Total + $taxestotal + $chargesTotal;
		}else{
			$Total = $pre_tax_Total + $taxestotal + $chargesTotal;
		}
		foreach ($deductedTaxesList as $key => $deductedTaxes) {
			$Total = $Total - $deductedTaxes['amount'];
		}

		$adjustAmount = 0;
		if($txtAdjustment != ''){
			$adjustAmount = $txtAdjustment;
			if(!empty($prev_currency_id)){
				$adjustAmount = $adjustAmount / $prev_conversionRate;
			}
			$adjustAmount = (float)$adjustAmount * (float)$conversionRate;

			if($AdjustmentType == ''){
				$AdjustmentType = 'add';
			}
			if($AdjustmentType == 'add'){
				$Total = $Total + $adjustAmount;
			}else{
				$Total = $Total - $adjustAmount;
			}
		}

		$adjustAmount = number_format($adjustAmount, $decimalPlace,'.','');

		$taxestotal = number_format($taxestotal,$decimalPlace,'.','');
		$chargesTotal = number_format($chargesTotal,$decimalPlace,'.','');
		$pretaxTotal = number_format($pre_tax_Total,$decimalPlace,'.','');
		$hdnSubTotal = number_format($totalListPrice, $decimalPlace,'.','');
		$hdnGrandTotal = number_format($Total, $decimalPlace,'.','');

		if($module == 'PurchaseOrder'){
			if($paid != ''){
				if(!empty($prev_currency_id)){
					$paid = $paid / $prev_conversionRate;
				}
				$paid = (float)$paid * (float)$conversionRate;
				$balance = $Total - $paid;
			}else{
				$paid = 0;
				$balance = $Total;
			}
		}
		if($module == 'Invoice'){
			if($received != ''){
				if(!empty($prev_currency_id)){
					$received = $received / $prev_conversionRate;
				}
				$received = (float)$received * (float)$conversionRate;
				$balance = $Total - $received;
			}else{
				$received = 0;
				$balance = $Total;
			}
		}

		$paid = number_format($paid,$decimalPlace,'.','');
		$received = number_format($received,$decimalPlace,'.','');
		$balance = number_format($balance,$decimalPlace,'.','');

		if ($productIdsList) {
			$imageDetailsList = Products_Record_Model::getProductsImageDetails($productIdsList);
			foreach ($imageDetailsList as $productId => $imageDetails) {
				$imageSourcesList[$productId] = $site_URL.$imageDetails[0]['path'].'_'.$imageDetails[0]['orgname'];
			}
		}

		$info['products'] = array();
		foreach($idList as $key => $value) {
			$recordid = explode('x',$value['productid']);
			$id = $recordid[1];
			$resultData = array(
						'id'					=> $value['productid'],
						'name'					=> $namesList[$id],
						//'taxes'					=> $taxesList[$id],
						'listprice'				=> $listPricesList[$id],
						'listpricevalues'		=> $listPriceValuesList[$id],
						'purchaseCost'			=> $purchaseCostsList[$id],
						'baseCurrencyId'		=> $baseCurrencyIdsList[$id],
						'quantityInStock'		=> $quantitiesList[$id],
						'imageSource'			=> $imageSourcesList[$id],
						'productTaxes'			=> $productTaxes[$id],
						'productTaxesTotal'     => $productTaxesTotal[$id],
						'discount_amount'	    => $discount_amount[$id],
						'netprice'				=> $netPrice[$id],
					);

			$info['products'][] = $resultData;
		}
		$info['Total'] = array(
						'currencysymbol'        => decode_html(decode_html($currencysymbol)),
						'discountAmount'        => $discountAmount,
						'hdnSubTotal'			=> $hdnSubTotal,
						'charges'				=> $charges,
						'pretaxTotal'			=> $pretaxTotal,
						'taxes'					=> $taxesList,
						'taxtotal'				=> $taxestotal,
						'taxesOnCharges'		=> $taxesOnCharges,
						'deductedTaxesList'     => $deductedTaxesList,
						'taxesOnChargesTotal'	=> $chargesTotal,
						'adjustAmount'			=> $adjustAmount,
						'hdnGrandTotal'			=> $hdnGrandTotal);
		if($module == 'PurchaseOrder'){
			$info['Total']['paid'] = $paid;
			$info['Total']['balance'] = $balance;
		}
		if($module == 'Invoice'){
			$info['Total']['received'] = $received;
			$info['Total']['balance'] = $balance;
		}
		$response = new CTMobile_API_Response();
		$response->setResult($info);
		return $response;
	}
}

?>
