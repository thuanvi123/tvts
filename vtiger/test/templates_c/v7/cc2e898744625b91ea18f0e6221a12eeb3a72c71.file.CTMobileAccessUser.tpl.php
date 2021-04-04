<?php /* Smarty version Smarty-3.1.7, created on 2021-03-27 16:25:40
         compiled from "/home/hmvtkebv/public_html/vtigercrm/vtiger/includes/runtime/../../layouts/v7/modules/CTMobileSettings/CTMobileAccessUser.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1776710270605f5c843db8a9-83954689%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'cc2e898744625b91ea18f0e6221a12eeb3a72c71' => 
    array (
      0 => '/home/hmvtkebv/public_html/vtigercrm/vtiger/includes/runtime/../../layouts/v7/modules/CTMobileSettings/CTMobileAccessUser.tpl',
      1 => 1616862048,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1776710270605f5c843db8a9-83954689',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'MODULE' => 0,
    'SELECTED_FIELDS' => 0,
    'USER_MODEL' => 0,
    'FIELD_MODEL' => 0,
    'GROUPS_MODEL' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_605f5c8443257',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_605f5c8443257')) {function content_605f5c8443257($_smarty_tpl) {?>
<div class="container-fluid">
    <div class="widget_header row-fluid">
    	<button type="button" class="btn btn-info pull-right" style="background:#287DF2 !important;" onclick='window.location.href="<?php echo CTMobileSettings_Module_Model::$CTMOBILE_DETAILVIEW_URL;?>
"'><?php echo vtranslate('Go To CRMTiger Settings',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</button>
        <h3><?php echo vtranslate("MODULE_LBL",$_smarty_tpl->tpl_vars['MODULE']->value);?>
</h3>
    </div>
    <hr>
      <h4 style="margin-left:20px;"><?php echo vtranslate('Select Mobile app users','CTMobileSettings');?>
</h4>
      <div class="clearfix"></div>
      <div class="summaryWidgetContainer" id="global_search_settings">
		   <form action="index.php" method="post" id="Settings" class="form-horizontal">
				<input type="hidden" name="module" value="CTMobileSettings">
				<input type="hidden" name="action" value="SaveAjaxMAccessUser">
				<table class="table table-bordered blockContainer showInlineTable equalSplit" style="width: 500px;">
					<tr>
						<td colspan="2" class="fieldValue medium">
							<select class="select2" multiple="true" id="moduleFields" name="fields[]" data-placeholder="Select fields" style="width: 800px">
							<optgroup label="">
								<option value="selectAll" <?php if (in_array('selectAll',$_smarty_tpl->tpl_vars['SELECTED_FIELDS']->value)){?> selected <?php }?>><?php echo vtranslate('LBL_ALL_USERS','CTMobileSettings');?>
</option>
							</optgroup>
							<optgroup label="<?php echo vtranslate('LBL_USERS');?>
">
								<?php  $_smarty_tpl->tpl_vars['FIELD_MODEL'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['FIELD_MODEL']->_loop = false;
 $_smarty_tpl->tpl_vars['FIELD_NAME'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['USER_MODEL']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['FIELD_MODEL']->key => $_smarty_tpl->tpl_vars['FIELD_MODEL']->value){
$_smarty_tpl->tpl_vars['FIELD_MODEL']->_loop = true;
 $_smarty_tpl->tpl_vars['FIELD_NAME']->value = $_smarty_tpl->tpl_vars['FIELD_MODEL']->key;
?>
									<option value="<?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value['userid'];?>
" data-field-name="<?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value['username'];?>
"
											 <?php if (in_array($_smarty_tpl->tpl_vars['FIELD_MODEL']->value['userid'],$_smarty_tpl->tpl_vars['SELECTED_FIELDS']->value)){?>
                                               selected
                                             <?php }?>
											><?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value['username'];?>

									</option>
								<?php } ?>
								</optgroup>
								<optgroup label="<?php echo vtranslate('LBL_GROUPS');?>
">
								<?php  $_smarty_tpl->tpl_vars['FIELD_MODEL'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['FIELD_MODEL']->_loop = false;
 $_smarty_tpl->tpl_vars['FIELD_NAME'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['GROUPS_MODEL']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['FIELD_MODEL']->key => $_smarty_tpl->tpl_vars['FIELD_MODEL']->value){
$_smarty_tpl->tpl_vars['FIELD_MODEL']->_loop = true;
 $_smarty_tpl->tpl_vars['FIELD_NAME']->value = $_smarty_tpl->tpl_vars['FIELD_MODEL']->key;
?>
									<option value="<?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value['userid'];?>
" data-field-name="<?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value['username'];?>
"
									<?php if (in_array($_smarty_tpl->tpl_vars['FIELD_MODEL']->value['userid'],$_smarty_tpl->tpl_vars['SELECTED_FIELDS']->value)){?>
                                               selected
                                             <?php }?>
											><?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value['username'];?>

									</option>
								<?php } ?>
								</optgroup>
							</select>
						</td>
					</tr>
				</table>
				<br />
				<div class="row-fluid">
					<button class="btn btn-success btnSaveAccessUser" type="button"><?php echo vtranslate('LBL_SAVE','CTMobileSettings');?>
</button>
					<a class="cancelLink" type="reset" onclick="javascript:window.history.back();"><?php echo vtranslate('Cancel','CTMobileSettings');?>
</a>
				</div>
			</form>
      </div>
</div>
<?php }} ?>