<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class CTRouteAttendance_ListView_Model extends Vtiger_ListView_Model {

	public function getListViewMassActions($linkParams) {
		$massActionLinks =  array();
		return $massActionLinks;
	}
	
	function getListViewLinks($linkParams) {
		$links = array();
		return $links;
	}

	public function getBasicLinks(){
		$basicLinks = array();
		return $basicLinks;
	}

	public function getAdvancedLinks(){
		$advancedLinks = array();
		return $advancedLinks;
	}
}