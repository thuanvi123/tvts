<?php /* Smarty version Smarty-3.1.7, created on 2021-03-27 16:22:53
         compiled from "/home/hmvtkebv/public_html/vtigercrm/vtiger/includes/runtime/../../layouts/v7/modules/CTMobileSettings/CTMobileLicenseDetail.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1385066936605f5bdd4bfa96-99118657%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd68e042c531b86e8a848f9b1774a10154c2e3769' => 
    array (
      0 => '/home/hmvtkebv/public_html/vtigercrm/vtiger/includes/runtime/../../layouts/v7/modules/CTMobileSettings/CTMobileLicenseDetail.tpl',
      1 => 1616862048,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1385066936605f5bdd4bfa96-99118657',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'MODULE' => 0,
    'LICENCE_KEY' => 0,
    'WIDTHTYPE' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_605f5bdd4f3e6',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_605f5bdd4f3e6')) {function content_605f5bdd4f3e6($_smarty_tpl) {?>
<div class="container-fluid" id="EditConfigEditor"><div class="widget_header row-fluid"><button type="button" class="btn btn-info pull-right" style="background:#287DF2 !important;" onclick='window.location.href="<?php echo CTMobileSettings_Module_Model::$CTMOBILE_DETAILVIEW_URL;?>
"'><?php echo vtranslate('Go To CRMTiger Settings',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</button><div class="span8"><h3><?php echo vtranslate('CTMobile License Configuration',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</h3></div><hr><div class="span4"><div class="pull-right"><?php if ($_smarty_tpl->tpl_vars['LICENCE_KEY']->value!=''){?><button class="btn btn-danger" id="deactivateLicense" type="button" title="<?php echo vtranslate('LBL_DEACTIVATE',$_smarty_tpl->tpl_vars['MODULE']->value);?>
"><strong><?php echo vtranslate('LBL_DEACTIVATE',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</strong></button>&nbsp;<?php }?><button class="btn btn-success editButton" data-url='?module=CTMobileSettings&parent=Settings&view=LicenseEdit' type="button" title="<?php echo vtranslate('LBL_EDIT',$_smarty_tpl->tpl_vars['MODULE']->value);?>
"><strong><?php echo vtranslate('LBL_EDIT',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</strong></button><a class="cancelLink" type="reset" onclick="javascript:window.history.back();"><?php echo vtranslate('Cancel',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</a></div></div></div><br/><div id="successMessage"></div><div class="contents"><table class="table table-bordered table-condensed themeTableColor"><tbody><tr><th colspan="2"><?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
<?php $_tmp1=ob_get_clean();?><?php echo vtranslate('License Key Configuration',$_tmp1);?>
</th></tr><tr class="fieldLabel medium"><td width="30%" class="<?php echo $_smarty_tpl->tpl_vars['WIDTHTYPE']->value;?>
"><label class="muted pull-right marginRight10px"><?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
<?php $_tmp2=ob_get_clean();?><?php echo vtranslate('License Key',$_tmp2);?>
</label></td><td  class="<?php echo $_smarty_tpl->tpl_vars['WIDTHTYPE']->value;?>
 fieldValue medium"><label class="muted marginRight10px"><?php echo $_smarty_tpl->tpl_vars['LICENCE_KEY']->value;?>
</label></td></tr></tbody></table></div><div><strong><?php echo vtranslate('Note : If you\'re experience any problem in installation of above extensions.','CTMobileSettings');?>
</strong><br/><strong><a href="https://kb.crmtiger.com/knowledge-base/error-code-solutions/" target="_blank" style="color: rgb(17, 85, 204);" onmouseover="this.style.color='#00008b'" onmouseout="this.style.color='#15c'"><?php echo vtranslate('Click here','CTMobileSettings');?>
</a> <?php echo vtranslate('to download and install manually one by one all updated extensions related to CRMTiger Mobile Apps','CTMobileSettings');?>
</strong></div></div>
<?php }} ?>