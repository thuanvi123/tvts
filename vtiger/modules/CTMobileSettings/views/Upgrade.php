<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once('vtlib/Vtiger/Unzip.php');
class CTMobileSettings_Upgrade_View extends Settings_Vtiger_Index_View {
    
    public function process(Vtiger_Request $request){
		global $adb,$root_directory;
        $viewer = $this->getViewer($request);
        $qualifiedName = $request->getModule(false);
        $doc_root = $_SERVER['DOCUMENT_ROOT'];
        $url = CTMobileSettings_Module_Model::$CTMOBILE_VERSION_URL;
        //delete all user session
        $unsetSesion = CTMobileSettings_Module_Model::destroyAllUserSession();

        $ch = curl_init($url);
		$data = array( "vt_version"=>'7.x');
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($ch);
		curl_close($ch);
		$jason_result = json_decode($result);
		$zip_url = $jason_result->ext_path;
		$ext_version = $jason_result->ext_version;
		mkdir($root_directory."/test/".$ext_version, 0777);
		$destination_path = $root_directory."/test/".$ext_version."/CTMobileupgrade.zip";
		file_put_contents($destination_path, fopen($zip_url, 'r'));
		chmod($root_directory."/test/".$ext_version."/CTMobileupgrade.zip",0755);
		
		chmod($root_directory."/test/".$ext_version."/",0777);
		$unzip = new Vtiger_Unzip($root_directory."/test/".$ext_version."/CTMobileupgrade.zip");
		$unzip->unzipAllEx($root_directory."/test/".$ext_version."/");
		
		$package = new Vtiger_Package();
		if(!getTabid('CTMobile')){
			$package->import($root_directory."/test/".$ext_version.'/CTMobile.zip',true);
		}else{
			$package->update(Vtiger_Module::getInstance('CTMobile'),$root_directory."/test/".$ext_version.'/CTMobile.zip');
		}
		if(!getTabid('CTAttendance')){
			$package->import($root_directory."/test/".$ext_version.'/CTAttendance.zip',true);
		}else{
			$package->update(Vtiger_Module::getInstance('CTAttendance'),$root_directory."/test/".$ext_version.'/CTAttendance.zip');
		}
		if(!getTabid('CTMessageTemplate')){
			$package->import($root_directory."/test/".$ext_version.'/CTMessageTemplate.zip',true);
		}else{
			$package->update(Vtiger_Module::getInstance('CTMessageTemplate'),$root_directory."/test/".$ext_version.'/CTMessageTemplate.zip');
		}
		if(!getTabid('CTPushNotification')){
			$package->import($root_directory."/test/".$ext_version.'/CTPushNotification.zip',true);
		}else{
			$package->update(Vtiger_Module::getInstance('CTPushNotification'),$root_directory."/test/".$ext_version.'/CTPushNotification.zip');
		}
		if(!getTabid('CTUserFilterView')){
			$package->import($root_directory."/test/".$ext_version.'/CTUserFilterView.zip',true);
		}else{
			$package->update(Vtiger_Module::getInstance('CTUserFilterView'),$root_directory."/test/".$ext_version.'/CTUserFilterView.zip');
		}
		
		if(!getTabid('CTMobileSettings')){
			$package->import($root_directory."/test/".$ext_version.'/CTMobileSettings.zip',true);
		}else{
			$package->update(Vtiger_Module::getInstance('CTMobileSettings'),$root_directory."/test/".$ext_version.'/CTMobileSettings.zip');
		}

		if(!getTabid('CTRoutePlanning')){
			$package->import($root_directory."/test/".$ext_version.'/CTRoutePlanning.zip',true);
		}else{
			$package->update(Vtiger_Module::getInstance('CTRoutePlanning'),$root_directory."/test/".$ext_version.'/CTRoutePlanning.zip');
		}
		if(!getTabid('CTRouteAttendance')){
			$package->import($root_directory."/test/".$ext_version.'/CTRouteAttendance.zip',true);
		}else{
			$package->update(Vtiger_Module::getInstance('CTRouteAttendance'),$root_directory."/test/".$ext_version.'/CTRouteAttendance.zip');
		}
		if(!getTabid('CTTimeControl')){
			$package->import($root_directory."/test/".$ext_version.'/CTTimeControl.zip',true);
		}else{
			$package->update(Vtiger_Module::getInstance('CTTimeControl'),$root_directory."/test/".$ext_version.'/CTTimeControl.zip');
		}
		if(!getTabid('CTTimeTracker')){
			$package->import($root_directory."/test/".$ext_version.'/CTTimeTracker.zip',true);
		}else{
			$package->update(Vtiger_Module::getInstance('CTTimeTracker'),$root_directory."/test/".$ext_version.'/CTTimeTracker.zip');
		}
			
		$array = array('CTAttendance','CTMessageTemplate','CTMobile','CTPushNotification','CTUserFilterView','CTMobileSettings','CTRoutePlanning','CTRouteAttendance','CTTimeTracker','CTTimeControl');
		foreach ($array as $key => $value) {
			$path  = $root_directory.'modules/'.$value;
    		chmod($path, 0755);
    		$path  = $root_directory.'layouts/v7/modules/'.$value;
    		chmod($path, 0755);
        } 
		$upload_status =  copy($root_directory.'/test/'.$ext_version.'/CTMobileApi.php', $root_directory.'/CTMobileApi.php');

		
		
		//redirect to detail view after update
		$detailUrl = CTMobileSettings_Module_Model::$CTMOBILE_DETAILVIEW_URL;
		header("Location: $detailUrl");										   
        $viewer->view('CTMobileDetails.tpl',$qualifiedName);  
    }
		
   function getHeaderScripts(Vtiger_Request $request) {
		$headerScriptInstances = parent::getHeaderScripts($request);
		$moduleName = $request->getModule();

		$jsFileNames = array(
			"modules.$moduleName.resources.OtherSettings",
		);

		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
    }
}
    
