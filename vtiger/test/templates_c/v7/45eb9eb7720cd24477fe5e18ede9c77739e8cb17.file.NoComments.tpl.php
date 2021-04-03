<?php /* Smarty version Smarty-3.1.7, created on 2021-03-27 18:25:32
         compiled from "/home/hmvtkebv/public_html/vtigercrm/vtiger/includes/runtime/../../layouts/v7/modules/Vtiger/NoComments.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1984752515605f789ce62686-52593444%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '45eb9eb7720cd24477fe5e18ede9c77739e8cb17' => 
    array (
      0 => '/home/hmvtkebv/public_html/vtigercrm/vtiger/includes/runtime/../../layouts/v7/modules/Vtiger/NoComments.tpl',
      1 => 1602616594,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1984752515605f789ce62686-52593444',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'MODULE_NAME' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_605f789d07984',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_605f789d07984')) {function content_605f789d07984($_smarty_tpl) {?>
<div class="noCommentsMsgContainer noContent"><p class="textAlignCenter"> <?php echo vtranslate('LBL_NO_COMMENTS',$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
</p></div><?php }} ?>