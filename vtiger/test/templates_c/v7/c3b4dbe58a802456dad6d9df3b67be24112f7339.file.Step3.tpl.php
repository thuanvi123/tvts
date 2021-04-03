<?php /* Smarty version Smarty-3.1.7, created on 2021-03-27 17:11:48
         compiled from "/home/hmvtkebv/public_html/vtigercrm/vtiger/includes/runtime/../../layouts/v7/modules/Settings/MailConverter/Step3.tpl" */ ?>
<?php /*%%SmartyHeaderCode:416683608605f6754e88a69-21312783%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c3b4dbe58a802456dad6d9df3b67be24112f7339' => 
    array (
      0 => '/home/hmvtkebv/public_html/vtigercrm/vtiger/includes/runtime/../../layouts/v7/modules/Settings/MailConverter/Step3.tpl',
      1 => 1602616594,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '416683608605f6754e88a69-21312783',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'MODULE_NAME' => 0,
    'SCANNER_ID' => 0,
    'RECORD_ID' => 0,
    'MODULE_MODEL' => 0,
    'FIELDS' => 0,
    'FIELD_MODEL' => 0,
    'QUALIFIED_MODULE' => 0,
    'FIELD_DATA_TYPE' => 0,
    'FIELD_NAME' => 0,
    'PICKLIST_VALUES' => 0,
    'PICKLIST_KEY' => 0,
    'RECORD_MODEL' => 0,
    'PICKLIST_VALUE' => 0,
    'RADIO_OPTIONS' => 0,
    'RADIO_NAME' => 0,
    'DEFAULT_MATCH' => 0,
    'RADIO_VALUE' => 0,
    'USER_MODEL' => 0,
    'USERS' => 0,
    'OWNER_ID' => 0,
    'OWNER_NAME' => 0,
    'ASSIGNED_USER' => 0,
    'GROUPS' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_605f67550aac3',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_605f67550aac3')) {function content_605f67550aac3($_smarty_tpl) {?>

<div class="block fieldBlockContainer"><form class="form-horizontal" id="ruleSave" method="post" name="step3"><input type="hidden" name="module" value="<?php echo $_smarty_tpl->tpl_vars['MODULE_NAME']->value;?>
" /><input type="hidden" name="parent" value="Settings" /><input type="hidden" name="action" value="SaveRule" /><input type="hidden" name="scannerId" value="<?php echo $_smarty_tpl->tpl_vars['SCANNER_ID']->value;?>
" /><input type="hidden" name="record" value="<?php echo $_smarty_tpl->tpl_vars['RECORD_ID']->value;?>
" /><div class="addMailBoxStep"><div class="row"><table class="table editview-table no-border"><tbody><?php $_smarty_tpl->tpl_vars['FIELDS'] = new Smarty_variable($_smarty_tpl->tpl_vars['MODULE_MODEL']->value->getSetupRuleFields(), null, 0);?><?php  $_smarty_tpl->tpl_vars['FIELD_MODEL'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['FIELD_MODEL']->_loop = false;
 $_smarty_tpl->tpl_vars['FIELD_NAME'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['FIELDS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['FIELD_MODEL']->key => $_smarty_tpl->tpl_vars['FIELD_MODEL']->value){
$_smarty_tpl->tpl_vars['FIELD_MODEL']->_loop = true;
 $_smarty_tpl->tpl_vars['FIELD_NAME']->value = $_smarty_tpl->tpl_vars['FIELD_MODEL']->key;
?><tr><td class="fieldLabel control-label" style="width:25%; padding-right:20px;"><label><?php echo vtranslate($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->get('label'),$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</label><td style="word-wrap:break-word;"><?php $_smarty_tpl->tpl_vars['FIELD_DATA_TYPE'] = new Smarty_variable($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getFieldDataType(), null, 0);?><?php if ($_smarty_tpl->tpl_vars['FIELD_DATA_TYPE']->value=='picklist'){?><?php $_smarty_tpl->tpl_vars['PICKLIST_VALUES'] = new Smarty_variable($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getPickListValues(), null, 0);?><?php if ($_smarty_tpl->tpl_vars['FIELD_NAME']->value=='subject'){?><select name="subjectop" class="select2 fieldValue inputElement"><option value=""><?php echo vtranslate('LBL_SELECT_OPTION',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</option><?php  $_smarty_tpl->tpl_vars['PICKLIST_VALUE'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['PICKLIST_VALUE']->_loop = false;
 $_smarty_tpl->tpl_vars['PICKLIST_KEY'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['PICKLIST_VALUES']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['PICKLIST_VALUE']->key => $_smarty_tpl->tpl_vars['PICKLIST_VALUE']->value){
$_smarty_tpl->tpl_vars['PICKLIST_VALUE']->_loop = true;
 $_smarty_tpl->tpl_vars['PICKLIST_KEY']->value = $_smarty_tpl->tpl_vars['PICKLIST_VALUE']->key;
?><option value="<?php echo $_smarty_tpl->tpl_vars['PICKLIST_KEY']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['RECORD_MODEL']->value->get('subjectop')==$_smarty_tpl->tpl_vars['PICKLIST_KEY']->value){?> selected <?php }?> ><?php echo $_smarty_tpl->tpl_vars['PICKLIST_VALUE']->value;?>
</option><?php } ?></select>&nbsp;&nbsp;<input type="text" class="fieldValue inputElement" name="<?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getName();?>
" value="<?php echo $_smarty_tpl->tpl_vars['RECORD_MODEL']->value->get($_smarty_tpl->tpl_vars['FIELD_NAME']->value);?>
" style="margin-left: 10px;" /><?php }elseif($_smarty_tpl->tpl_vars['FIELD_NAME']->value=='body'){?><select name="bodyop" class="select2 fieldValue inputElement"><option value="" <?php if ($_smarty_tpl->tpl_vars['RECORD_MODEL']->value->get('bodyop')==''){?>selected<?php }?>><?php echo vtranslate('LBL_SELECT_OPTION',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</option><?php  $_smarty_tpl->tpl_vars['PICKLIST_VALUE'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['PICKLIST_VALUE']->_loop = false;
 $_smarty_tpl->tpl_vars['PICKLIST_KEY'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['PICKLIST_VALUES']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['PICKLIST_VALUE']->key => $_smarty_tpl->tpl_vars['PICKLIST_VALUE']->value){
$_smarty_tpl->tpl_vars['PICKLIST_VALUE']->_loop = true;
 $_smarty_tpl->tpl_vars['PICKLIST_KEY']->value = $_smarty_tpl->tpl_vars['PICKLIST_VALUE']->key;
?><option value="<?php echo $_smarty_tpl->tpl_vars['PICKLIST_KEY']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['RECORD_MODEL']->value->get('bodyop')==$_smarty_tpl->tpl_vars['PICKLIST_KEY']->value){?> selected <?php }?> ><?php echo $_smarty_tpl->tpl_vars['PICKLIST_VALUE']->value;?>
</option><?php } ?></select><br><br><textarea name="<?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getName();?>
" class="boxSizingBorderBox fieldValue inputElement" style="width:416px;padding: 3px 8px;"><?php echo $_smarty_tpl->tpl_vars['RECORD_MODEL']->value->get($_smarty_tpl->tpl_vars['FIELD_NAME']->value);?>
</textarea><?php }else{ ?><select id="actions" name="action1" class="select2 fieldValue inputElement" style="min-width:220px"><?php  $_smarty_tpl->tpl_vars['PICKLIST_VALUE'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['PICKLIST_VALUE']->_loop = false;
 $_smarty_tpl->tpl_vars['PICKLIST_KEY'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['PICKLIST_VALUES']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['PICKLIST_VALUE']->key => $_smarty_tpl->tpl_vars['PICKLIST_VALUE']->value){
$_smarty_tpl->tpl_vars['PICKLIST_VALUE']->_loop = true;
 $_smarty_tpl->tpl_vars['PICKLIST_KEY']->value = $_smarty_tpl->tpl_vars['PICKLIST_VALUE']->key;
?><option value="<?php echo $_smarty_tpl->tpl_vars['PICKLIST_KEY']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['RECORD_MODEL']->value->get($_smarty_tpl->tpl_vars['FIELD_NAME']->value)==$_smarty_tpl->tpl_vars['PICKLIST_KEY']->value){?> selected <?php }?> ><?php echo $_smarty_tpl->tpl_vars['PICKLIST_VALUE']->value;?>
</option><?php } ?></select><?php }?><?php }elseif($_smarty_tpl->tpl_vars['FIELD_DATA_TYPE']->value=='radio'){?><?php $_smarty_tpl->tpl_vars['RADIO_OPTIONS'] = new Smarty_variable($_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getRadioOptions(), null, 0);?><?php  $_smarty_tpl->tpl_vars['RADIO_VALUE'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['RADIO_VALUE']->_loop = false;
 $_smarty_tpl->tpl_vars['RADIO_NAME'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['RADIO_OPTIONS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['RADIO_VALUE']->key => $_smarty_tpl->tpl_vars['RADIO_VALUE']->value){
$_smarty_tpl->tpl_vars['RADIO_VALUE']->_loop = true;
 $_smarty_tpl->tpl_vars['RADIO_NAME']->value = $_smarty_tpl->tpl_vars['RADIO_VALUE']->key;
?><label class="radioOption inline"><input class="radioOption" type="radio" name="<?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getName();?>
" value="<?php echo $_smarty_tpl->tpl_vars['RADIO_NAME']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['DEFAULT_MATCH']->value==$_smarty_tpl->tpl_vars['RADIO_NAME']->value){?> checked <?php }?> /><?php echo $_smarty_tpl->tpl_vars['RADIO_VALUE']->value;?>
</label>&nbsp;&nbsp;&nbsp;&nbsp;<?php } ?><?php }elseif($_smarty_tpl->tpl_vars['FIELD_DATA_TYPE']->value=='email'){?><input type="text" class="fieldValue inputElement" name="<?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getName();?>
" value="<?php echo $_smarty_tpl->tpl_vars['RECORD_MODEL']->value->get($_smarty_tpl->tpl_vars['FIELD_NAME']->value);?>
" data-validation-engine="validate[funcCall[Vtiger_Email_Validator_Js.invokeValidation]]"/><?php }else{ ?><input type="text" class="fieldValue inputElement" name="<?php echo $_smarty_tpl->tpl_vars['FIELD_MODEL']->value->getName();?>
" value="<?php echo $_smarty_tpl->tpl_vars['RECORD_MODEL']->value->get($_smarty_tpl->tpl_vars['FIELD_NAME']->value);?>
"/><?php }?></td></tr><?php } ?><tr id="assignedToBlock"><td class="fieldLabel control-label" style="width:25%; padding-right:20px;"><label><?php echo vtranslate('Assigned To');?>
</label></td><td style="word-wrap:break-word;"><select class="select2 fieldValue inputElement" id="assignedTo" name="assignedTo"><optgroup label="<?php echo vtranslate('LBL_USERS');?>
"><?php $_smarty_tpl->tpl_vars['USERS'] = new Smarty_variable($_smarty_tpl->tpl_vars['USER_MODEL']->value->getAccessibleUsersForModule($_smarty_tpl->tpl_vars['MODULE_NAME']->value), null, 0);?><?php  $_smarty_tpl->tpl_vars['OWNER_NAME'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['OWNER_NAME']->_loop = false;
 $_smarty_tpl->tpl_vars['OWNER_ID'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['USERS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['OWNER_NAME']->key => $_smarty_tpl->tpl_vars['OWNER_NAME']->value){
$_smarty_tpl->tpl_vars['OWNER_NAME']->_loop = true;
 $_smarty_tpl->tpl_vars['OWNER_ID']->value = $_smarty_tpl->tpl_vars['OWNER_NAME']->key;
?><option value="<?php echo $_smarty_tpl->tpl_vars['OWNER_ID']->value;?>
" data-picklistvalue= '<?php echo $_smarty_tpl->tpl_vars['OWNER_NAME']->value;?>
' <?php if ($_smarty_tpl->tpl_vars['ASSIGNED_USER']->value==$_smarty_tpl->tpl_vars['OWNER_ID']->value){?> selected <?php }?>><?php echo $_smarty_tpl->tpl_vars['OWNER_NAME']->value;?>
</option><?php } ?></optgroup><optgroup label="<?php echo vtranslate('LBL_GROUPS');?>
"><?php $_smarty_tpl->tpl_vars['GROUPS'] = new Smarty_variable($_smarty_tpl->tpl_vars['USER_MODEL']->value->getAccessibleGroups(), null, 0);?><?php  $_smarty_tpl->tpl_vars['OWNER_NAME'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['OWNER_NAME']->_loop = false;
 $_smarty_tpl->tpl_vars['OWNER_ID'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['GROUPS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['OWNER_NAME']->key => $_smarty_tpl->tpl_vars['OWNER_NAME']->value){
$_smarty_tpl->tpl_vars['OWNER_NAME']->_loop = true;
 $_smarty_tpl->tpl_vars['OWNER_ID']->value = $_smarty_tpl->tpl_vars['OWNER_NAME']->key;
?><option value="<?php echo $_smarty_tpl->tpl_vars['OWNER_ID']->value;?>
" data-picklistvalue= '<?php echo $_smarty_tpl->tpl_vars['OWNER_NAME']->value;?>
' <?php if ($_smarty_tpl->tpl_vars['ASSIGNED_USER']->value==$_smarty_tpl->tpl_vars['OWNER_ID']->value){?> selected <?php }?>><?php echo $_smarty_tpl->tpl_vars['OWNER_NAME']->value;?>
</option><?php } ?></optgroup></select></td></tr></tbody></table></div></div><div class="border1px modal-overlay-footer clearfix"><div class="row clearfix"><div class="textAlignCenter col-lg-12 col-md-12 col-lg-12 "><button class="btn btn-danger backStep" type="button" onclick="javascript:window.history.back();"><strong><?php echo vtranslate('LBL_BACK',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</strong></button>&nbsp;&nbsp;<button class="btn btn-success" onclick="javascript:Settings_MailConverter_Edit_Js.thirdStep()"><strong><?php echo vtranslate('LBL_FINISH',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</strong></button><a class="cancelLink" type="reset" onclick="javascript:window.history.go(-3);"><?php echo vtranslate('LBL_CANCEL',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</a></div></div></div></form></div></div></div><?php }} ?>