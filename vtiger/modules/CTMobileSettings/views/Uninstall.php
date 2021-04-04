<?php
/*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

include_once 'vtlib/Vtiger/Module.php';
class CTMobileSettings_Uninstall_View extends Settings_Vtiger_Index_View {

    function process (Vtiger_Request $request) {
        global $adb;
        echo '<div class="container-fluid">
                <div class="widget_header row-fluid">
                    <h3>CTMobileSettings</h3>
                </div>
                <hr>';
        // Uninstall module
        $module = Vtiger_Module::getInstance('CTMobileSettings');
        if ($module) $module->delete();
        // drop tables
        $sql = "DROP TABLE `ctmobile_address_modules`, `ctmobile_address_fields`;";
        $result = $adb->pquery($sql,array());
        echo "&nbsp;&nbsp;- Delete CTMobile Settings tables";
        if($result) echo " - DONE"; else echo " - <b>ERROR</b>";
        echo '<br>';
        // remove directory
        $res_template = $this->delete_folder('layouts/vlayout/modules/CTMobileSettings');
        echo "&nbsp;&nbsp;- Delete CTMobileAddress template folder";
        if($res_template) echo " - DONE"; else echo " - <b>ERROR</b>";
        echo '<br>';

        $res_module = $this->delete_folder('modules/CTMobileSettings');
        echo "&nbsp;&nbsp;- Delete CTMobile Settings module folder";
        if($res_module) echo " - DONE"; else echo " - <b>ERROR</b>";
        echo '<br>';
        // Remove module from other settings
        $adb->pquery("DELETE FROM vtiger_settings_field WHERE `name` = ?",array('CTMobileSettings'));
        echo "Module was Uninstalled.";
        echo '</div>';
    }

    function delete_folder($tmp_path){
        // check and set folder access
        if(!is_writeable($tmp_path) && is_dir($tmp_path)) {
            chmod($tmp_path,0777);
        }
        $handle = opendir($tmp_path);
        while($tmp=readdir($handle)) {
            if($tmp!='..' && $tmp!='.' && $tmp!=''){
                // check and set file access before delete file
                if(is_writeable($tmp_path.DS.$tmp) && is_file($tmp_path.DS.$tmp)) {
                    unlink($tmp_path.DS.$tmp);
                } elseif(!is_writeable($tmp_path.DS.$tmp) && is_file($tmp_path.DS.$tmp)){
                    chmod($tmp_path.DS.$tmp,0666);
                    unlink($tmp_path.DS.$tmp);
                }

                // check and set folder access before delete folder
                if(is_writeable($tmp_path.DS.$tmp) && is_dir($tmp_path.DS.$tmp)) {
                    $this->delete_folder($tmp_path.DS.$tmp);
                } elseif(!is_writeable($tmp_path.DS.$tmp) && is_dir($tmp_path.DS.$tmp)){
                    chmod($tmp_path.DS.$tmp,0777);
                    $this->delete_folder($tmp_path.DS.$tmp);
                }
            }
        }
        closedir($handle);
        rmdir($tmp_path);
        if(!is_dir($tmp_path)) {
            return true;
        } else {
            return false;
        }
    }
}
