<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
class CTMobileSettings_updateModuleAjax_Action extends Vtiger_BasicAjax_Action {
    public function process(Vtiger_Request $request) {	
       	$moduleName = $request->get('updateModName');
		global $adb,$root_directory;
		$url = CTMobileSettings_Module_Model::$CTMOBILE_VERSION_URL;
        $ch = curl_init($url);
		$data = array( "vt_version"=>'7.x');
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$results = curl_exec($ch);
		curl_close($ch);
		$jason_result = json_decode($results,true);
		$zip_url = $jason_result['ext_path'];
		$ext_version = $jason_result['ext_version'];
		mkdir($root_directory."/test/".$ext_version, 0777);
		$destination_path = $root_directory."/test/".$ext_version."/CTMobileupgrade.zip";
		file_put_contents($destination_path, fopen($zip_url, 'r'));
		chmod($root_directory."/test/".$ext_version."/CTMobileupgrade.zip",0755);
		$zip = new ZipArchive;
		$res = $zip->open($root_directory."/test/".$ext_version."/CTMobileupgrade.zip");
		if ($res === TRUE) {
			$zip->extractTo($root_directory."/test/".$ext_version."/");
			$zip->close();
		}
		$package = new Vtiger_Package();
		if(!getTabid($moduleName)){
			$package->import($root_directory."/test/".$ext_version.'/'.$moduleName.'.zip',true);
		}else{
			$package->update(Vtiger_Module::getInstance($moduleName),$root_directory."/test/".$ext_version.'/'.$moduleName.'.zip');
		}

		$checkModulePresence = $adb->pquery("SELECT * FROM vtiger_tab WHERE tabid = ? AND presence = '1' ",array(getTabid($moduleName)));
		if($adb->num_rows($checkModulePresence) > 0){
			$moduleManagerModel = new Settings_ModuleManager_Module_Model();
			$moduleManagerModel->enableModule($moduleName);
		}
		
		$path  = $root_directory.'modules/'.$moduleName;
    	chmod($path, 0755);
    	$path  = $root_directory.'layouts/v7/modules/'.$moduleName;
    	chmod($path, 0755);
		$response = new Vtiger_Response();
		$response->setResult(true);
		$response->emit();

    }
}
