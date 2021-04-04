<?php /* Smarty version Smarty-3.1.7, created on 2021-03-27 16:23:07
         compiled from "/home/hmvtkebv/public_html/vtigercrm/vtiger/includes/runtime/../../layouts/v7/modules/CTMobileSettings/CTMobileLicenseEdit.tpl" */ ?>
<?php /*%%SmartyHeaderCode:320423146605f5beb48f519-11082816%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '329526de2bbcbc51c7f245c0f6df73b720f2102c' => 
    array (
      0 => '/home/hmvtkebv/public_html/vtigercrm/vtiger/includes/runtime/../../layouts/v7/modules/CTMobileSettings/CTMobileLicenseEdit.tpl',
      1 => 1616862048,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '320423146605f5beb48f519-11082816',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'MODULE' => 0,
    'WIDTHTYPE' => 0,
    'LICENCE_KEY' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_605f5beb4bef4',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_605f5beb4bef4')) {function content_605f5beb4bef4($_smarty_tpl) {?>
<div class="container-fluid" id="EditConfigEditor"><div class="widget_header row-fluid"><button type="button" class="btn btn-info pull-right" style="background:#287DF2 !important;" onclick='window.location.href="<?php echo CTMobileSettings_Module_Model::$CTMOBILE_DETAILVIEW_URL;?>
"'><?php echo vtranslate('Go To CRMTiger Settings',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</button><div class="span6"><h3><?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
<?php $_tmp1=ob_get_clean();?><?php echo vtranslate('CTMobile License Configuration',$_tmp1);?>
</h3></div></div><div class="contents"><table class="table table-bordered table-condensed themeTableColor"><tbody><tr><th colspan="2"><?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
<?php $_tmp2=ob_get_clean();?><?php echo vtranslate('License Key Configuration',$_tmp2);?>
</th></tr><tr class="fieldLabel medium"><td width="30%" class="<?php echo $_smarty_tpl->tpl_vars['WIDTHTYPE']->value;?>
"><label class="muted pull-right marginRight10px"><span class="redColor">*</span><?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
<?php $_tmp3=ob_get_clean();?><?php echo vtranslate('Enter License Key',$_tmp3);?>
</label></td><td  class="<?php echo $_smarty_tpl->tpl_vars['WIDTHTYPE']->value;?>
 fieldValue medium"><input class="inputElement" type="text" name="License_Key" id="License_Key" value="<?php echo $_smarty_tpl->tpl_vars['LICENCE_KEY']->value;?>
"/></td></tr></tbody></table><br><div class="row-fluid"><div><strong><?php echo vtranslate('Note : If you\'re experience any problem in installation of above extensions.','CTMobileSettings');?>
</strong><br/><strong><a href="https://kb.crmtiger.com/knowledge-base/error-code-solutions/" target="_blank" style="color: rgb(17, 85, 204);" onmouseover="this.style.color='#00008b'" onmouseout="this.style.color='#15c'"><?php echo vtranslate('Click here','CTMobileSettings');?>
</a> <?php echo vtranslate('to download and install manually one by one all updated extensions related to CRMTiger Mobile Apps','CTMobileSettings');?>
</strong></div><div class="pull-right"><button type="button" class="btn btn-success saveButton" name="save_license_settings" id="save_license_settings"><strong><?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
<?php $_tmp4=ob_get_clean();?><?php echo vtranslate('Save',$_tmp4);?>
</strong></button><a class="cancelLink" type="reset" onclick="javascript:window.history.back();"><?php echo vtranslate('Cancel',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</a></div></div></div></div>
<?php }} ?>