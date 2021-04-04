<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

include_once dirname(__FILE__) . '/ModuleRecord.php';

class CTMobile_UI_ModuleModel {
	private $data;
	
	function initData($moduleData) {
		$this->data = $moduleData;
	}
	
	function id() {
		return $this->data['id'];
	}
	
	function name() {
		return $this->data['name'];
	}
	
	function label() {
		return $this->data['label'];
	}
	
	static function buildModelsFromResponse($modules) {
		$instances = array();
		foreach($modules as $moduleData) {
			$instance = new self();
			$instance->initData($moduleData);
			$instances[] = $instance;
		}
		return $instances;
	}
	
}
