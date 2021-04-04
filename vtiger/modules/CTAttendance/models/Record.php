<?php

class CTAttendance_Record_Model extends Vtiger_Record_Model {

	function getCheckInLocation(){
		$recordId = $this->getId();
		$check_in_location = "";
		if($recordId){
			$recordModel = self::getInstanceById($recordId,'CTAttendance');
			$check_in_location = $recordModel->get('check_in_location');
		}
		return $check_in_location;
	}

	function getCheckOutLocation(){
		$recordId = $this->getId();
		$check_out_location = "";
		if($recordId){
			$recordModel = self::getInstanceById($recordId,'CTAttendance');
			$check_out_location = $recordModel->get('check_out_location');
		}
		return $check_out_location;
	}

}