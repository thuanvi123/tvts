<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

include_once dirname(__FILE__) . '/Field.php';

class CTMobile_UI_BlockModel {
	private $_label;
	private $_fields = array();
	
	function initData($blockData) {
		$this->_label = $blockData['label'];
		if (isset($blockData['fields'])) {
			$this->_fields = CTMobile_UI_FieldModel::buildModelsFromResponse($blockData['fields']);
		}
	}
	
	function label() {
		return $this->_label;
	}
	
	function fields() {
		return $this->_fields;
	}
	
	static function buildModelsFromResponse($blocks) {
		$instances = array();
		foreach($blocks as $blockData) {
			$instance = new self();
			$instance->initData($blockData);
			$instances[] = $instance;
		}
		return $instances;
	}
	
}
