<?php
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
include_once 'include/Webservices/Utils.php';
include_once 'modules/CTMobile/CTMobile.php';
include_once dirname(__FILE__) . '/Utils.php';

class CTMobile_WS_Controller {
	function requireLogin() {
		return true;
	}
	private $activeUser = false;
	public function initActiveUser($user) {
		$this->activeUser = $user;
	}
	protected function setActiveUser($user) {
		$this->sessionSet('_authenticated_user_id', $user->id);
		$this->initActiveUser($user);
	}
	protected function getActiveUser() {
		if($this->activeUser === false) {
			$userid = $this->sessionGet('_authenticated_user_id');
			if(!empty($userid)) {
				$this->activeUser = CRMEntity::getInstance('Users');
				$this->activeUser->retrieveCurrentUserInfoFromFile($userid);
			}
		}
		return $this->activeUser;
	}
	function hasActiveUser() {
		$user = $this->getActiveUser();
		return ($user !== false);
	}
	function sessionGet($key, $defvaule = '') {
		return CTMobile_API_Session::get($key, $defvalue);
	}
	function sessionSet($key, $value) {
		CTMobile_API_Session::set($key, $value);
	}

	protected function CTTranslate($keyword){
		global $adb;
		$user = $this->getActiveUser();
		$language = $user->language;
		$default_charset = VTWS_PreserveGlobal::getGlobal('default_charset');
		$checkLangSQL = "SELECT language_keyword FROM ctmobile_language_keyword WHERE keyword = ? AND keyword_lang = ?";
		$resultLang = $adb->pquery($checkLangSQL,array($keyword,$language));
		if($adb->num_rows($resultLang) > 0){
			return html_entity_decode($adb->query_result($resultLang,0,'language_keyword'),ENT_QUOTES,$default_charset);
		}else{
			$checkdefaultLangSQL = "SELECT language_keyword FROM ctmobile_language_keyword WHERE keyword = ? AND keyword_lang = ?";
			$resultDefaultLang = $adb->pquery($checkdefaultLangSQL,array($keyword,'en_us'));
			if($adb->num_rows($resultLang) > 0){
				return html_entity_decode($adb->query_result($resultDefaultLang,0,'language_keyword'),ENT_QUOTES,$default_charset);
			}else{
				return $keyword;
			}
		}
	}
}
