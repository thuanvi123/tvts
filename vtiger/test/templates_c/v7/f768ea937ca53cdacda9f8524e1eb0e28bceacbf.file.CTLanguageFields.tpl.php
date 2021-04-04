<?php /* Smarty version Smarty-3.1.7, created on 2021-03-27 17:27:29
         compiled from "/home/hmvtkebv/public_html/vtigercrm/vtiger/includes/runtime/../../layouts/v7/modules/CTMobileSettings/CTLanguageFields.tpl" */ ?>
<?php /*%%SmartyHeaderCode:847438064605f6b01bbbe53-27127998%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f768ea937ca53cdacda9f8524e1eb0e28bceacbf' => 
    array (
      0 => '/home/hmvtkebv/public_html/vtigercrm/vtiger/includes/runtime/../../layouts/v7/modules/CTMobileSettings/CTLanguageFields.tpl',
      1 => 1616862048,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '847438064605f6b01bbbe53-27127998',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'CTLANGAUGE' => 0,
    'CTLANGUAGE_SECTION' => 0,
    'LANGUAGE_FIELDS' => 0,
    'COUNTER' => 0,
    'FIELDS' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_605f6b01bec83',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_605f6b01bec83')) {function content_605f6b01bec83($_smarty_tpl) {?>
<form action="index.php" method="post" id="Settings" class="form-horizontal">
    <input type="hidden" name="module" value="CTMobileSettings">
    <input type="hidden" name="action" value="SaveLanguageAjax">
    <input type="hidden" name="ctlanguage" value="<?php echo $_smarty_tpl->tpl_vars['CTLANGAUGE']->value;?>
">
    <input type="hidden" name="ctlanguage_section" value="<?php echo $_smarty_tpl->tpl_vars['CTLANGUAGE_SECTION']->value;?>
">
    <div class="fieldBlockContainer">
    <table class="table table-bordered blockContainer showInlineTable equalSplit">
    <?php if (count($_smarty_tpl->tpl_vars['LANGUAGE_FIELDS']->value)=='0'){?>
        <tr>
            <td>
                <div class="emptyRecordsContent">
                <?php echo vtranslate('LBL_NO','CTMobileSettings');?>
 <?php echo vtranslate('LBL_LABELS','CTMobileSettings');?>
 <?php echo vtranslate('LBL_FOUND');?>
 
                </div>
            </td>
        </tr>
    <?php }else{ ?>
        <tr>
        <?php $_smarty_tpl->tpl_vars['COUNTER'] = new Smarty_variable(0, null, 0);?>
        <?php  $_smarty_tpl->tpl_vars['FIELDS'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['FIELDS']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['LANGUAGE_FIELDS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['FIELDS']->key => $_smarty_tpl->tpl_vars['FIELDS']->value){
$_smarty_tpl->tpl_vars['FIELDS']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['FIELDS']->key;
?>
                <?php if ($_smarty_tpl->tpl_vars['COUNTER']->value==2){?>
                    </tr><tr>
                    <?php $_smarty_tpl->tpl_vars['COUNTER'] = new Smarty_variable(1, null, 0);?>
                <?php }else{ ?>
                    <?php $_smarty_tpl->tpl_vars['COUNTER'] = new Smarty_variable($_smarty_tpl->tpl_vars['COUNTER']->value+1, null, 0);?>
                <?php }?>
                <td class="fieldLabel alignMiddle" >
                    <?php echo $_smarty_tpl->tpl_vars['FIELDS']->value['keyword_name'];?>

                </td>
                <td class="fieldValue">
                    <textarea name="field_<?php echo $_smarty_tpl->tpl_vars['FIELDS']->value['keyword_id'];?>
" class="inputElement" rows="3"><?php echo $_smarty_tpl->tpl_vars['FIELDS']->value['language_keyword'];?>
</textarea>
                </td>
                <?php if (count($_smarty_tpl->tpl_vars['LANGUAGE_FIELDS']->value)=='1'){?>
                    <td class="fieldLabel alignMiddle" style="width:200px;"></td><td class="fieldValue"></td>
                <?php }?>
        <?php } ?>
        </tr>
    <?php }?>
    </table>
    </div>
    <?php if (count($_smarty_tpl->tpl_vars['LANGUAGE_FIELDS']->value)!='0'){?>
    <br />
    <div class="row-fluid">
        <button class="btn btn-success btnSaveSettings" type="button"><?php echo vtranslate('LBL_SAVE','CTMobileSettings');?>
</button>
		<a class="cancelLink" type="reset" onclick="javascript:window.history.back();"><?php echo vtranslate('Cancel','CTMobileSettings');?>
</a>
    </div>
    <?php }?>
</form>
<?php }} ?>