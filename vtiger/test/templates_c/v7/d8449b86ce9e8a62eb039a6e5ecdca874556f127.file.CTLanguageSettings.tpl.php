<?php /* Smarty version Smarty-3.1.7, created on 2021-03-27 17:26:53
         compiled from "/home/hmvtkebv/public_html/vtigercrm/vtiger/includes/runtime/../../layouts/v7/modules/CTMobileSettings/CTLanguageSettings.tpl" */ ?>
<?php /*%%SmartyHeaderCode:965728269605f6addcfa070-23603963%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd8449b86ce9e8a62eb039a6e5ecdca874556f127' => 
    array (
      0 => '/home/hmvtkebv/public_html/vtigercrm/vtiger/includes/runtime/../../layouts/v7/modules/CTMobileSettings/CTLanguageSettings.tpl',
      1 => 1616862048,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '965728269605f6addcfa070-23603963',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'MODULE' => 0,
    'OCCUPY_COMPLETE_WIDTH' => 0,
    'ALL_LANGUAGES' => 0,
    'lang' => 0,
    'languages' => 0,
    'ALL_SECTIONS' => 0,
    'id' => 0,
    'name' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_605f6addd2def',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_605f6addd2def')) {function content_605f6addd2def($_smarty_tpl) {?>
<div class="container-fluid">
    <div class="widget_header row-fluid">
        <button type="button" class="btn btn-info pull-right" style="background:#287DF2 !important;" onclick='window.location.href="<?php echo CTMobileSettings_Module_Model::$CTMOBILE_DETAILVIEW_URL;?>
"'><?php echo vtranslate('Go To CRMTiger Settings',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</button>
        <h3><?php echo vtranslate('CRMTiger Mobile Apps-Language Settings','CTMobileSettings');?>
</h3>
    </div>
    <hr>
    <h5 style="margin-left:20px;"><?php echo vtranslate('This section allow you to setup text display in CRMTiger mobile apps according to your language preference.','CTMobileSettings');?>
<br/><?php echo vtranslate('you have an ability to change language text as per the needs.','CTMobileSettings');?>
</h5>
      <div class="clearfix"></div>
      <div class="summaryWidgetContainer" id="ct_language_settings">                
        <div class="row-fluid">
             <table class="table table-bordered blockContainer showInlineTable equalSplit" >
                <tr>
                    <td class="fieldLabel alignMiddle">
                    <?php echo vtranslate('Please choose your language','CTMobileSettings');?>
<span class="redColor">*</span>:
                    </td>
                    <td class="fieldLabel alignMiddle">
                        <select class="inputElement select2 <?php if ($_smarty_tpl->tpl_vars['OCCUPY_COMPLETE_WIDTH']->value){?> row <?php }?>" id="ct_language" name="ct_language" style="width: 300px;">
                        <option value=""><?php echo vtranslate('Select Language','CTMobileSettings');?>
</option>
                        <?php  $_smarty_tpl->tpl_vars['languages'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['languages']->_loop = false;
 $_smarty_tpl->tpl_vars['lang'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['ALL_LANGUAGES']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['languages']->key => $_smarty_tpl->tpl_vars['languages']->value){
$_smarty_tpl->tpl_vars['languages']->_loop = true;
 $_smarty_tpl->tpl_vars['lang']->value = $_smarty_tpl->tpl_vars['languages']->key;
?>
                            <option value="<?php echo $_smarty_tpl->tpl_vars['lang']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['languages']->value;?>
</option>
                        <?php } ?>
                        </select>
                    </td>
                    <td class="fieldLabel alignMiddle">
                     <?php echo vtranslate('Please choose your section','CTMobileSettings');?>
<span class="redColor">*</span>:
                    </td>
                    <td class="fieldValue">
                        <select class="inputElement select2 <?php if ($_smarty_tpl->tpl_vars['OCCUPY_COMPLETE_WIDTH']->value){?> row <?php }?>" id="ct_section" name="ct_section" style="width: 300px;">
                            <option value=""><?php echo vtranslate('Select Section','CTMobileSettings');?>
</option>
                            <?php  $_smarty_tpl->tpl_vars['name'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['name']->_loop = false;
 $_smarty_tpl->tpl_vars['id'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['ALL_SECTIONS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['name']->key => $_smarty_tpl->tpl_vars['name']->value){
$_smarty_tpl->tpl_vars['name']->_loop = true;
 $_smarty_tpl->tpl_vars['id']->value = $_smarty_tpl->tpl_vars['name']->key;
?>
                                <option value="<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['name']->value;?>
</option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
            </table>
        </div>
        <br/>
        <div id="selectedFields">

        </div>              
    </div>
</div>
              
             <?php }} ?>