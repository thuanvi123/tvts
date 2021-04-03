<?php
class CTMobileSettings_chkPermission_Action extends Vtiger_Action_Controller {
	public function checkPermission(Vtiger_Request $request) {
	}
	public function process(Vtiger_Request $request) {
		$mode = $request->get('mode');
		$response = new Vtiger_Response();
		if($mode == 'GetRequirement'){
			$count = CTMobileSettings_Module_Model::GetRequirement();
			$CurrentUserModel = Users_Record_Model::getCurrentUserModel();
			$data = array('count'=>$count,'is_admin'=>$CurrentUserModel->get('is_admin')); 

			$response->setEmitType(Vtiger_Response::$EMIT_JSON);
			$response->setResult($data);
			$response->emit();
			exit;
		}
		if($mode == 'GetPermission'){
			$ExtensionsName = array();
			$Extensions_status = array();
			$install_guide =  array();
			if(extension_loaded('zip')){
				$ExtensionsName[] = "<a href='https://www.luminanetworks.com/docs-lsc-610/Topics/SDN_Controller_Software_Installation_Guide/Appendix/Installing_Zip_and_Unzip_for_Ubuntu_1.html' style='color:#15c;'>Zip</a>";
				$Extensions_status[] = "True";
				$install_guide[] = "sudo apt-get install zip";
			}else{
				$ExtensionsName[] = "<a href='https://www.luminanetworks.com/docs-lsc-610/Topics/SDN_Controller_Software_Installation_Guide/Appendix/Installing_Zip_and_Unzip_for_Ubuntu_1.html' style='color:#15c;'>Zip</a>";
				$Extensions_status[] = "False";
				$install_guide[] = "sudo apt-get install zip";
			}
			if(extension_loaded('unzip')){
				$ExtensionsName[] = "<a href='https://www.luminanetworks.com/docs-lsc-610/Topics/SDN_Controller_Software_Installation_Guide/Appendix/Installing_Zip_and_Unzip_for_Ubuntu_1.html' style='color:#15c;'>Unzip</a>";
				$Extensions_status[] = "True";
				$install_guide[] = "sudo apt-get install unzip";
			}else{
				$ExtensionsName[] = "<a href='https://www.luminanetworks.com/docs-lsc-610/Topics/SDN_Controller_Software_Installation_Guide/Appendix/Installing_Zip_and_Unzip_for_Ubuntu_1.html' style='color:#15c;'>Unzip</a>";
				$Extensions_status[] = "False";
				$install_guide[] = "sudo apt-get install unzip";
			}
			if(extension_loaded('gd')){
				$ExtensionsName[] = "<a href='https://www.digitalocean.com/community/questions/installing-the-gd-image-library' style='color:#15c;'>GD</a>";
				$Extensions_status[] = "True";
				$install_guide[] = "sudo apt-get install php5-gd <br/>sudo service apache2 restart";
			}else{
				$ExtensionsName[] = "<a href='https://www.digitalocean.com/community/questions/installing-the-gd-image-library' style='color:#15c;'>GD</a>";
				$Extensions_status[] = "False";
				$install_guide[] = "sudo apt-get install php5-gd <br/>sudo service apache2 restart";
			}
			if(extension_loaded('Zlib')){
				$ExtensionsName[] = "<a href='https://www.digitalocean.com/community/questions/php-7-0-ziparchive-library-is-missing-or-disabled' style='color:#15c;'>Zlib</a>";
				$Extensions_status[] = "True";
				$install_guide[] = "https://www.digitalocean.com/community/questions/php-7-0-ziparchive-library-is-missing-or-disabled";
			}else{
				$ExtensionsName[] = "<a href='https://www.digitalocean.com/community/questions/php-7-0-ziparchive-library-is-missing-or-disabled' style='color:#15c;'>Zlib</a>";
				$Extensions_status[] = "False";
				$install_guide[] = "https://www.digitalocean.com/community/questions/php-7-0-ziparchive-library-is-missing-or-disabled";
			}
			if(extension_loaded('Curl')){
				$ExtensionsName[] = "<a href='https://www.digitalocean.com/community/questions/curl-is-not-installed-in-your-php-installation' style='color:#15c;'>Curl</a>";
				$Extensions_status[] = "True";
				$install_guide[] = "sudo apt-get install php5-curl";
			}else{
				$ExtensionsName[] = "<a href='https://www.digitalocean.com/community/questions/curl-is-not-installed-in-your-php-installation' style='color:#15c;'>Curl</a>";
				$Extensions_status[] = "False";
				$install_guide[] = "sudo apt-get install php5-curl";
			}
			if(extension_loaded('mbstring')){
				$ExtensionsName[] = "<a href='https://www.digitalocean.com/community/questions/php-curl-and-mbstring-extensions-enabled' style='color:#15c;'>Mbstring</a>";
				$Extensions_status[] = "True";
				$install_guide[] = "yum install php-mbstring";
			}else{
				$ExtensionsName[] = "<a href='https://www.digitalocean.com/community/questions/php-curl-and-mbstring-extensions-enabled' style='color:#15c;'>Mbstring</a>";
				$Extensions_status[] = "False";
				$install_guide[] = "yum install php-mbstring";
			}
			$extensions = array('ExtensionsName'=>$ExtensionsName,'Extensions_status'=>$Extensions_status,'install_guide'=>$install_guide);
			$filenames = array();
			$file_permission = array();
			global $root_directory;
			foreach (glob($root_directory."vtlib/thirdparty/dZip.inc.php") as $filename) {
				$filenames[] = $filename;
				$file_permission[] = substr(sprintf('%o',fileperms($filename)),-4);
			}
			foreach (glob($root_directory."vtlib/thirdparty/dUnzip2.inc.php") as $filename) {
				$filenames[] = $filename;
				$file_permission[] = substr(sprintf('%o',fileperms($filename)),-4);
			}
			foreach (glob($root_directory."test/") as $filename) {
				$filenames[] = $filename;
				$file_permission[] = substr(sprintf('%o',fileperms($filename)),-4);
			}
			foreach (glob($root_directory."modules/CTMobile/") as $filename) {
				$filenames[] = $filename;
				$file_permission[] = substr(sprintf('%o',fileperms($filename)),-4);
			}
			foreach (glob($root_directory."modules/CTMobile/api/ws/*.*") as $filename) {
				$filenames[] = $filename;
				$file_permission[] = substr(sprintf('%o',fileperms($filename)),-4);
			}
			$folder_permission = substr(sprintf('%o', fileperms('/var/www/html')), -4);
			$html['permission'] = array('filename' =>$filenames, 'file_permission'=> $file_permission, 'folder_permission' => $folder_permission);
			$html['extensions'] = $extensions;
			
			$response->setEmitType(Vtiger_Response::$EMIT_JSON);
			$response->setResult($html);
			$response->emit();
		}
		if($mode == 'GetLicense'){
			global $adb;
			$getLicenseQuery=$adb->pquery("SELECT * FROM ctmobile_license_settings");
			$numOfLicense = $adb->num_rows($getLicenseQuery);
			if($numOfLicense > 0){
				$license_key = $adb->query_result($getLicenseQuery,0,'license_key');
			}else{
				$license_key = 0;
			}
			
			$response->setEmitType(Vtiger_Response::$EMIT_JSON);
			$response->setResult($license_key);
			$response->emit();
		}
	}
}
?>
