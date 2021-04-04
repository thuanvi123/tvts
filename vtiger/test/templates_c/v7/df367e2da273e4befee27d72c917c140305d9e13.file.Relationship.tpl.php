<?php /* Smarty version Smarty-3.1.7, created on 2021-03-27 17:16:16
         compiled from "/home/hmvtkebv/public_html/vtigercrm/vtiger/includes/runtime/../../layouts/v7/modules/MailManager/Relationship.tpl" */ ?>
<?php /*%%SmartyHeaderCode:68033070605f686061f340-98480465%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'df367e2da273e4befee27d72c917c140305d9e13' => 
    array (
      0 => '/home/hmvtkebv/public_html/vtigercrm/vtiger/includes/runtime/../../layouts/v7/modules/MailManager/Relationship.tpl',
      1 => 1602660960,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '68033070605f686061f340-98480465',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'LINKEDTO' => 0,
    'moduleName' => 0,
    'LINK_TO_AVAILABLE_ACTIONS' => 0,
    'MODULE' => 0,
    'LOOKUPS' => 0,
    'RECORDS' => 0,
    'RECORD' => 0,
    'SINGLE_MODLABEL' => 0,
    'LOOKRECATLEASTONE' => 0,
    'ALLOWED_MODULES' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_605f68607b093',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_605f68607b093')) {function content_605f68607b093($_smarty_tpl) {?>
<?php if ($_smarty_tpl->tpl_vars['LINKEDTO']->value){?><div class='col-lg-12 padding0px'><div class="col-lg-7 padding0px recordScroll" ><span class="col-lg-12 padding0px"><span class="col-lg-1 padding0px"><input type="radio" name="_mlinkto" value="<?php echo $_smarty_tpl->tpl_vars['LINKEDTO']->value['record'];?>
"></span><span class="col-lg-11 padding0px mmRelatedRecordDesc textOverflowEllipsis" title="<?php echo $_smarty_tpl->tpl_vars['LINKEDTO']->value['detailviewlink'];?>
">&nbsp;&nbsp;<?php echo $_smarty_tpl->tpl_vars['LINKEDTO']->value['detailviewlink'];?>
&nbsp;&nbsp;(<?php echo vtranslate($_smarty_tpl->tpl_vars['LINKEDTO']->value['module'],$_smarty_tpl->tpl_vars['moduleName']->value);?>
)</span></span></div><div class="pull-left col-lg-5 "><?php if (count($_smarty_tpl->tpl_vars['LINK_TO_AVAILABLE_ACTIONS']->value)!=0){?><select name="_mlinktotype"  id="_mlinktotype" data-action='associate'style="background: #FFFFFF url('layouts/v7/skins/images/arrowdown.png') no-repeat 95% 40%;"><option value=""><?php echo vtranslate('LBL_ACTIONS',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</option><?php  $_smarty_tpl->tpl_vars['moduleName'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['moduleName']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['LINK_TO_AVAILABLE_ACTIONS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['moduleName']->key => $_smarty_tpl->tpl_vars['moduleName']->value){
$_smarty_tpl->tpl_vars['moduleName']->_loop = true;
?><?php if ($_smarty_tpl->tpl_vars['moduleName']->value=='Calendar'){?><option value="<?php echo $_smarty_tpl->tpl_vars['moduleName']->value;?>
"><?php echo vtranslate("LBL_ADD_CALENDAR",'MailManager');?>
</option><option value="Events"><?php echo vtranslate("LBL_ADD_EVENTS",'MailManager');?>
</option><?php }else{ ?><option value="<?php echo $_smarty_tpl->tpl_vars['moduleName']->value;?>
"><?php echo vtranslate("LBL_MAILMANAGER_ADD_".($_smarty_tpl->tpl_vars['moduleName']->value),'MailManager');?>
</option><?php }?><?php } ?></select><?php }?></div></div><?php }?><?php if ($_smarty_tpl->tpl_vars['LOOKUPS']->value){?><?php $_smarty_tpl->tpl_vars["LOOKRECATLEASTONE"] = new Smarty_variable(false, null, 0);?><?php  $_smarty_tpl->tpl_vars['RECORDS'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['RECORDS']->_loop = false;
 $_smarty_tpl->tpl_vars['MODULE'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['LOOKUPS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['RECORDS']->key => $_smarty_tpl->tpl_vars['RECORDS']->value){
$_smarty_tpl->tpl_vars['RECORDS']->_loop = true;
 $_smarty_tpl->tpl_vars['MODULE']->value = $_smarty_tpl->tpl_vars['RECORDS']->key;
?><?php  $_smarty_tpl->tpl_vars['RECORD'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['RECORD']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['RECORDS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['RECORD']->key => $_smarty_tpl->tpl_vars['RECORD']->value){
$_smarty_tpl->tpl_vars['RECORD']->_loop = true;
?><?php $_smarty_tpl->tpl_vars["LOOKRECATLEASTONE"] = new Smarty_variable(true, null, 0);?><?php } ?><?php } ?><div class="col-lg-12 padding0px"><div class="col-lg-7 padding0px recordScroll" ><?php  $_smarty_tpl->tpl_vars['RECORDS'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['RECORDS']->_loop = false;
 $_smarty_tpl->tpl_vars['MODULE'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['LOOKUPS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['RECORDS']->key => $_smarty_tpl->tpl_vars['RECORDS']->value){
$_smarty_tpl->tpl_vars['RECORDS']->_loop = true;
 $_smarty_tpl->tpl_vars['MODULE']->value = $_smarty_tpl->tpl_vars['RECORDS']->key;
?><?php  $_smarty_tpl->tpl_vars['RECORD'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['RECORD']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['RECORDS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['RECORD']->key => $_smarty_tpl->tpl_vars['RECORD']->value){
$_smarty_tpl->tpl_vars['RECORD']->_loop = true;
?><span class="col-lg-12 padding0px"><span class="col-lg-1 padding0px"><input type="radio" name="_mlinkto" value="<?php echo $_smarty_tpl->tpl_vars['RECORD']->value['id'];?>
"></span><span class="textOverflowEllipsis col-lg-11 padding0px mmRelatedRecordDesc ">&nbsp;&nbsp;<a target="_blank" href='index.php?module=<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
&view=Detail&record=<?php echo $_smarty_tpl->tpl_vars['RECORD']->value['id'];?>
' title="<?php echo $_smarty_tpl->tpl_vars['RECORD']->value['label'];?>
"><?php echo textlength_check($_smarty_tpl->tpl_vars['RECORD']->value['label']);?>
</a>&nbsp;&nbsp;<?php $_smarty_tpl->tpl_vars["SINGLE_MODLABEL"] = new Smarty_variable("SINGLE_".($_smarty_tpl->tpl_vars['MODULE']->value), null, 0);?>(<?php echo vtranslate($_smarty_tpl->tpl_vars['SINGLE_MODLABEL']->value,$_smarty_tpl->tpl_vars['MODULE']->value);?>
)</span></span><br><?php } ?><?php } ?></div><div class="pull-left col-lg-5 "><?php if ($_smarty_tpl->tpl_vars['LOOKRECATLEASTONE']->value){?><?php if (count($_smarty_tpl->tpl_vars['LINK_TO_AVAILABLE_ACTIONS']->value)!=0){?><select name="_mlinktotype"  id="_mlinktotype" data-action='associate'style="background: #FFFFFF url('layouts/v7/skins/images/arrowdown.png') no-repeat 95% 40%;"><option value=""><?php echo vtranslate('LBL_ACTIONS',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</option><?php  $_smarty_tpl->tpl_vars['moduleName'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['moduleName']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['LINK_TO_AVAILABLE_ACTIONS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['moduleName']->key => $_smarty_tpl->tpl_vars['moduleName']->value){
$_smarty_tpl->tpl_vars['moduleName']->_loop = true;
?><?php if ($_smarty_tpl->tpl_vars['moduleName']->value=='Calendar'){?><option value="<?php echo $_smarty_tpl->tpl_vars['moduleName']->value;?>
"><?php echo vtranslate("LBL_ADD_CALENDAR",'MailManager');?>
</option><option value="Events"><?php echo vtranslate("LBL_ADD_EVENTS",'MailManager');?>
</option><?php }else{ ?><option value="<?php echo $_smarty_tpl->tpl_vars['moduleName']->value;?>
"><?php echo vtranslate("LBL_MAILMANAGER_ADD_".($_smarty_tpl->tpl_vars['moduleName']->value),'MailManager');?>
</option><?php }?><?php } ?></select><?php }?><?php }else{ ?><?php if (count($_smarty_tpl->tpl_vars['ALLOWED_MODULES']->value)!=0){?><select name="_mlinktotype"  id="_mlinktotype" data-action='create'style="background: #FFFFFF url('layouts/v7/skins/images/arrowdown.png') no-repeat 95% 40%;"><option value=""><?php echo vtranslate('LBL_ACTIONS','MailManager');?>
</option><?php  $_smarty_tpl->tpl_vars['moduleName'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['moduleName']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['ALLOWED_MODULES']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['moduleName']->key => $_smarty_tpl->tpl_vars['moduleName']->value){
$_smarty_tpl->tpl_vars['moduleName']->_loop = true;
?><?php if ($_smarty_tpl->tpl_vars['moduleName']->value=='Calendar'){?><option value="<?php echo $_smarty_tpl->tpl_vars['moduleName']->value;?>
"><?php echo vtranslate("LBL_ADD_CALENDAR",'MailManager');?>
</option><option value="Events"><?php echo vtranslate("LBL_ADD_EVENTS",'MailManager');?>
</option><?php }else{ ?><option value="<?php echo $_smarty_tpl->tpl_vars['moduleName']->value;?>
"><?php echo vtranslate("LBL_MAILMANAGER_ADD_".($_smarty_tpl->tpl_vars['moduleName']->value),'MailManager');?>
</option><?php }?><?php } ?></select><?php }?><?php }?></div></div><?php }else{ ?><?php if ($_smarty_tpl->tpl_vars['LINKEDTO']->value==''){?><div class="col-lg-12 padding0px"><div class="col-lg-7 padding0px recordScroll" >&nbsp;</div><div class="pull-left col-lg-5"><?php if (count($_smarty_tpl->tpl_vars['ALLOWED_MODULES']->value)!=0){?><select name="_mlinktotype"  id="_mlinktotype" data-action='create'style="background: #FFFFFF url('layouts/v7/skins/images/arrowdown.png') no-repeat 95% 40%;"><option value=""><?php echo vtranslate('LBL_ACTIONS','MailManager');?>
</option><?php  $_smarty_tpl->tpl_vars['moduleName'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['moduleName']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['ALLOWED_MODULES']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['moduleName']->key => $_smarty_tpl->tpl_vars['moduleName']->value){
$_smarty_tpl->tpl_vars['moduleName']->_loop = true;
?><?php if ($_smarty_tpl->tpl_vars['moduleName']->value=='Calendar'){?><option value="<?php echo $_smarty_tpl->tpl_vars['moduleName']->value;?>
"><?php echo vtranslate("LBL_ADD_CALENDAR",'MailManager');?>
</option><option value="Events"><?php echo vtranslate("LBL_ADD_EVENTS",'MailManager');?>
</option><?php }else{ ?><option value="<?php echo $_smarty_tpl->tpl_vars['moduleName']->value;?>
"><?php echo vtranslate("LBL_MAILMANAGER_ADD_".($_smarty_tpl->tpl_vars['moduleName']->value),'MailManager');?>
</option><?php }?><?php } ?></select><?php }?></div></div><?php }?><?php }?><?php }} ?>