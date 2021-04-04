<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

include_once dirname(__FILE__) . '/Block.php';

class CTMobile_UI_ModuleRecordModel {
	private $_id;
	private $_blocks = array();
	
	function initData($recordData) {
		$this->data = $recordData;
		if (isset($recordData['blocks'])) {
			$blocks = CTMobile_UI_BlockModel::buildModelsFromResponse($recordData['blocks']);
			foreach($blocks as $block) {
				$this->_blocks[$block->label()] = $block;
			}
		}
	}
	
	function setId($newId) {
		$this->_id = $newId;
	}
	
	function id() {
		return $this->data['id'];
	}
	
	function label() {
		return $this->data['label'];
	}
	
	function blocks() {
		return $this->_blocks;
	}
	
	static function buildModelFromResponse($recordData) {
		$instance = new self();
		$instance->initData($recordData);
		return $instance;
	}
	
	static function buildModelsFromResponse($records) {
		$instances = array();
		foreach($records as $recordData) {
			$instance = new self();
			$instance->initData($recordData);
			$instances[] = $instance;
		}
		return $instances;
	}
	
}
