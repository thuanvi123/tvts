<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/**
 * Calendar Field Model Class
 */
class CTAttendance_Field_Model extends Vtiger_Field_Model {

	/**
	 * Customize the display value for detail view.
	 */
	public function getDisplayValue($value, $record = false, $recordInstance = false) {
		if ($recordInstance) {
			if ($this->getName() == 'modifiedtime') {
				if($recordInstance->get('attendance_status') == 'check_in'){
					$value = "";
				}
				return $value;
			}
		}
		return parent::getDisplayValue($value, $record, $recordInstance);
	}

	
}
