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
class CTTimeControl_Field_Model extends Vtiger_Field_Model {

	/**
	 * Customize the display value for detail view.
	 */
	public function getDisplayValue($value, $record = false, $recordInstance = false) {
		if ($recordInstance) {
			if ($this->getName() == 'date_start') {
				$dateTimeValue = $value . ' '. $recordInstance->get('time_start');
				$value = $this->getUITypeModel()->getDisplayValue($dateTimeValue);
				list($startDate, $startTime) = explode(' ', $value);

				$currentUser = Users_Record_Model::getCurrentUserModel();
				if($currentUser->get('hour_format') == '12')
					$startTime = Vtiger_Time_UIType::getTimeValueInAMorPM($startTime);

				return $startDate;
			} else if ($this->getName() == 'date_end') {
				$dateTimeValue = $value . ' '. $recordInstance->get('time_end');
				$value = $this->getUITypeModel()->getDisplayValue($dateTimeValue);
				list($startDate, $startTime) = explode(' ', $value);

				$currentUser = Users_Record_Model::getCurrentUserModel();
				if($currentUser->get('hour_format') == '12')
					$startTime = Vtiger_Time_UIType::getTimeValueInAMorPM($startTime);

				return $startDate;
			} else if($this->getName() == 'time_start'){
				$dateTimeValue = $recordInstance->get('date_start').' '.$value;
				$value = Vtiger_Datetime_UIType::getDisplayDateTimeValue($dateTimeValue);
				list($startDate, $startTime) = explode(' ', $value);

				$currentUser = Users_Record_Model::getCurrentUserModel();
				if($currentUser->get('hour_format') == '12')
					$startTime = Vtiger_Time_UIType::getTimeValueInAMorPM($startTime);
				return $startTime;
			} else if($this->getName() == 'time_end'){
				$dateTimeValue = $recordInstance->get('date_end').' '.$value;
				$value = Vtiger_Datetime_UIType::getDisplayDateTimeValue($dateTimeValue);
				list($startDate, $startTime) = explode(' ', $value);

				$currentUser = Users_Record_Model::getCurrentUserModel();
				if($currentUser->get('hour_format') == '12')
					$startTime = Vtiger_Time_UIType::getTimeValueInAMorPM($startTime);
				return $startTime;
			}
		}
		return parent::getDisplayValue($value, $record, $recordInstance);
	}
	
}
