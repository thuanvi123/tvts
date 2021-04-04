<?php /* Smarty version Smarty-3.1.7, created on 2021-03-27 17:16:15
         compiled from "/home/hmvtkebv/public_html/vtigercrm/vtiger/includes/runtime/../../layouts/v7/modules/MailManager/MailOpen.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1742142748605f685f8cf782-99427797%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd5afa0d1807b667d1a859862566b4b156fe5770b' => 
    array (
      0 => '/home/hmvtkebv/public_html/vtigercrm/vtiger/includes/runtime/../../layouts/v7/modules/MailManager/MailOpen.tpl',
      1 => 1602660960,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1742142748605f685f8cf782-99427797',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'MAIL' => 0,
    'FOLDER' => 0,
    'USERNAME' => 0,
    'ATTACHMENTS' => 0,
    'INLINE_ATT' => 0,
    'ATTACHMENT_COUNT' => 0,
    'NAME' => 0,
    'FIRST_CHAR' => 0,
    'MODULE' => 0,
    'FROM' => 0,
    'TO_VAL' => 0,
    'CC_VAL' => 0,
    'BCC_VAL' => 0,
    'BODY' => 0,
    'ATTACHVALUE' => 0,
    'ATTACHNAME' => 0,
    'ATTACHID' => 0,
    'DOWNLOAD_LINK' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_605f685f99b6d',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_605f685f99b6d')) {function content_605f685f99b6d($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/hmvtkebv/public_html/vtigercrm/vtiger/libraries/Smarty/libs/plugins/modifier.escape.php';
?>
<div class="container-fluid padding0px"><input type="hidden" id="mmFrom" value='<?php echo implode(',',$_smarty_tpl->tpl_vars['MAIL']->value->from());?>
'><input type="hidden" id="mmSubject" value='<?php echo Vtiger_Functions::jsonEncode($_smarty_tpl->tpl_vars['MAIL']->value->subject());?>
'><input type="hidden" id="mmMsgNo" value="<?php echo $_smarty_tpl->tpl_vars['MAIL']->value->msgNo();?>
"><input type="hidden" id="mmMsgUid" value="<?php echo $_smarty_tpl->tpl_vars['MAIL']->value->uniqueid();?>
"><input type="hidden" id="mmFolder" value="<?php echo $_smarty_tpl->tpl_vars['FOLDER']->value->name();?>
"><input type="hidden" id="mmTo" value='<?php echo implode(',',$_smarty_tpl->tpl_vars['MAIL']->value->to());?>
'><input type="hidden" id="mmCc" value='<?php echo implode(',',$_smarty_tpl->tpl_vars['MAIL']->value->cc());?>
'><input type="hidden" id="mmDate" value="<?php echo $_smarty_tpl->tpl_vars['MAIL']->value->date();?>
"><input type="hidden" id="mmUserName" value="<?php echo $_smarty_tpl->tpl_vars['USERNAME']->value;?>
"><?php $_smarty_tpl->tpl_vars['ATTACHMENT_COUNT'] = new Smarty_variable((count($_smarty_tpl->tpl_vars['ATTACHMENTS']->value)-count($_smarty_tpl->tpl_vars['INLINE_ATT']->value)), null, 0);?><input type="hidden" id="mmAttchmentCount" value="<?php echo $_smarty_tpl->tpl_vars['ATTACHMENT_COUNT']->value;?>
"><div class="row" id="mailManagerActions"><div class="col-lg-12"><div class="col-lg-8 padding0px" id="relationBlock"></div><div class="col-lg-4 padding0px"><span class="pull-right"><button type="button" class="btn btn-default mailPagination marginRight0px" <?php if ($_smarty_tpl->tpl_vars['MAIL']->value->msgno()<$_smarty_tpl->tpl_vars['FOLDER']->value->count()){?>data-folder='<?php echo $_smarty_tpl->tpl_vars['FOLDER']->value->name();?>
' data-msgno='<?php echo $_smarty_tpl->tpl_vars['MAIL']->value->msgno(1);?>
'<?php }else{ ?>disabled="disabled"<?php }?>><i class="fa fa-caret-left"></i></button><button type="button" class="btn btn-default mailPagination" <?php if ($_smarty_tpl->tpl_vars['MAIL']->value->msgno()>1){?>data-folder='<?php echo $_smarty_tpl->tpl_vars['FOLDER']->value->name();?>
' data-msgno='<?php echo $_smarty_tpl->tpl_vars['MAIL']->value->msgno(-1);?>
'<?php }else{ ?>disabled="disabled"<?php }?>><i class="fa fa-caret-right"></i></button></span></div></div></div><div class="row marginTop15px"><div class="col-lg-12 "><h5 class="marginTop0px"><?php echo $_smarty_tpl->tpl_vars['MAIL']->value->subject();?>
</h5></div></div><hr><div class="row"><div class="col-lg-2"><div class="mmFirstNameChar"><center><?php $_smarty_tpl->tpl_vars['NAME'] = new Smarty_variable($_smarty_tpl->tpl_vars['MAIL']->value->from(), null, 0);?><?php $_smarty_tpl->tpl_vars['FIRST_CHAR'] = new Smarty_variable(strtoupper(substr($_smarty_tpl->tpl_vars['NAME']->value[0],0,1)), null, 0);?><?php if ($_smarty_tpl->tpl_vars['FOLDER']->value->isSentFolder()){?><?php $_smarty_tpl->tpl_vars['NAME'] = new Smarty_variable($_smarty_tpl->tpl_vars['MAIL']->value->to(), null, 0);?><?php $_smarty_tpl->tpl_vars['FIRST_CHAR'] = new Smarty_variable(strtoupper(substr($_smarty_tpl->tpl_vars['NAME']->value[0],0,1)), null, 0);?><?php }?><strong><?php echo $_smarty_tpl->tpl_vars['FIRST_CHAR']->value;?>
</strong></center></div></div><div class="col-lg-6"><span class="mmDisplayName"><?php if ($_smarty_tpl->tpl_vars['FOLDER']->value->isSentFolder()){?><?php echo implode(', ',$_smarty_tpl->tpl_vars['MAIL']->value->to());?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['NAME']->value[0];?>
<?php }?></span><?php if ($_smarty_tpl->tpl_vars['ATTACHMENT_COUNT']->value){?>&nbsp;&nbsp;<i class="fa fa-paperclip fontSize20px"></i><?php }?><span><?php $_smarty_tpl->tpl_vars['FROM'] = new Smarty_variable($_smarty_tpl->tpl_vars['MAIL']->value->from(), null, 0);?>&nbsp;&nbsp;<a href="javascript:void(0)" class="emailDetails" role="tooltip" data-toggle="popover" data-trigger="focus" title="<strong><?php echo vtranslate('LBL_DETAILS',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</strong>"data-content="<table><tr><td class='muted input-info-addon'><?php echo vtranslate('LBL_FROM',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</td><td class='displayEmailValues'><?php echo $_smarty_tpl->tpl_vars['FROM']->value[0];?>
</td></tr><tr><td>&nbsp;</td></tr><tr><td class='muted input-info-addon'><?php echo vtranslate('LBL_TO',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</td><td class='displayEmailValues'><?php  $_smarty_tpl->tpl_vars['TO_VAL'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['TO_VAL']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['MAIL']->value->to(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['TO_VAL']->key => $_smarty_tpl->tpl_vars['TO_VAL']->value){
$_smarty_tpl->tpl_vars['TO_VAL']->_loop = true;
?><?php echo $_smarty_tpl->tpl_vars['TO_VAL']->value;?>
<br><?php } ?></td></tr><tr><td>&nbsp;</td></tr><?php if ($_smarty_tpl->tpl_vars['MAIL']->value->cc()){?><tr><td class='muted input-info-addon'><?php echo vtranslate('LBL_CC_SMALL',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</td><td class='displayEmailValues'><?php  $_smarty_tpl->tpl_vars['CC_VAL'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['CC_VAL']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['MAIL']->value->cc(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['CC_VAL']->key => $_smarty_tpl->tpl_vars['CC_VAL']->value){
$_smarty_tpl->tpl_vars['CC_VAL']->_loop = true;
?><?php echo $_smarty_tpl->tpl_vars['CC_VAL']->value;?>
<br><?php } ?></td></tr><tr><td>&nbsp;</td></tr><?php }?><?php if ($_smarty_tpl->tpl_vars['MAIL']->value->bcc()){?><tr><td class='muted input-info-addon'><?php echo vtranslate('LBL_BCC_SMALL',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</td><td class='displayEmailValues'><?php  $_smarty_tpl->tpl_vars['BCC_VAL'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['BCC_VAL']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['MAIL']->value->bcc(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['BCC_VAL']->key => $_smarty_tpl->tpl_vars['BCC_VAL']->value){
$_smarty_tpl->tpl_vars['BCC_VAL']->_loop = true;
?><?php echo $_smarty_tpl->tpl_vars['BCC_VAL']->value;?>
<br><?php } ?></td></tr><tr><td>&nbsp;</td></tr><?php }?></table>"><i class="fa fa-caret-down" title="<?php echo vtranslate('LBL_SHOW_FULL_DETAILS',$_smarty_tpl->tpl_vars['MODULE']->value);?>
" style='border: 1px solid #AAA; padding: 0 2px; color: #AAA;'></i></a></span></div><div class="col-lg-4"><span class="pull-right mmDetailDate"><?php echo Vtiger_Util_Helper::formatDateTimeIntoDayString($_smarty_tpl->tpl_vars['MAIL']->value->date(),true);?>
</span></div></div><div class="clearfix"><div class="pull-right"><span class="cursorPointer mmDetailAction" id='mmPrint' title='<?php echo vtranslate('LBL_Print',$_smarty_tpl->tpl_vars['MODULE']->value);?>
'><i class="fa fa-print"></i></span><span class="cursorPointer mmDetailAction" id='mmReply' title='<?php echo vtranslate('LBL_Reply',$_smarty_tpl->tpl_vars['MODULE']->value);?>
'><i class="fa fa-reply"></i></span><span class="cursorPointer mmDetailAction" id='mmReplyAll' title='<?php echo vtranslate('LBL_Reply_All',$_smarty_tpl->tpl_vars['MODULE']->value);?>
'><i class="fa fa-reply-all"></i></span><span class="cursorPointer mmDetailAction" id='mmForward' title='<?php echo vtranslate('LBL_Forward',$_smarty_tpl->tpl_vars['MODULE']->value);?>
'><i class="fa fa-share"></i></span><span class="cursorPointer mmDetailAction" id='mmDelete' title='<?php echo vtranslate('LBL_Delete',$_smarty_tpl->tpl_vars['MODULE']->value);?>
' style="border-right: 1px solid #BBBBBB;"><i class="fa fa-trash-o"></i></span></div></div><br><div class="row"><div class="col-lg-12 mmEmailContainerDiv"><div id='mmBody'><?php echo $_smarty_tpl->tpl_vars['BODY']->value;?>
</div></div></div><?php if ($_smarty_tpl->tpl_vars['ATTACHMENT_COUNT']->value){?><br><hr class="mmDetailHr"><br><div class='col-lg-12 padding0px'><span><strong><?php echo vtranslate('LBL_Attachments',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</strong></span><span>&nbsp;&nbsp;(<?php echo count($_smarty_tpl->tpl_vars['ATTACHMENTS']->value)-count($_smarty_tpl->tpl_vars['INLINE_ATT']->value);?>
&nbsp;<?php echo vtranslate('LBL_FILES',$_smarty_tpl->tpl_vars['MODULE']->value);?>
)</span><br><br><?php  $_smarty_tpl->tpl_vars['ATTACHVALUE'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['ATTACHVALUE']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['ATTACHMENTS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['ATTACHVALUE']->key => $_smarty_tpl->tpl_vars['ATTACHVALUE']->value){
$_smarty_tpl->tpl_vars['ATTACHVALUE']->_loop = true;
?><?php $_smarty_tpl->tpl_vars['ATTACHNAME'] = new Smarty_variable($_smarty_tpl->tpl_vars['ATTACHVALUE']->value['filename'], null, 0);?><?php if ($_smarty_tpl->tpl_vars['INLINE_ATT']->value[$_smarty_tpl->tpl_vars['ATTACHNAME']->value]==null){?><?php $_smarty_tpl->tpl_vars['DOWNLOAD_LINK'] = new Smarty_variable(rawurlencode($_smarty_tpl->tpl_vars['ATTACHNAME']->value), null, 0);?><?php $_smarty_tpl->tpl_vars['ATTACHID'] = new Smarty_variable($_smarty_tpl->tpl_vars['ATTACHVALUE']->value['attachid'], null, 0);?><span><i class="fa <?php echo $_smarty_tpl->tpl_vars['MAIL']->value->getAttachmentIcon($_smarty_tpl->tpl_vars['ATTACHVALUE']->value['path']);?>
"></i>&nbsp;&nbsp;<a href="index.php?module=<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
&view=Index&_operation=mail&_operationarg=attachment_dld&_muid=<?php echo $_smarty_tpl->tpl_vars['MAIL']->value->muid();?>
&_atid=<?php echo $_smarty_tpl->tpl_vars['ATTACHID']->value;?>
&_atname=<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['DOWNLOAD_LINK']->value, 'htmlall', 'UTF-8');?>
"><?php echo $_smarty_tpl->tpl_vars['ATTACHNAME']->value;?>
</a><span>&nbsp;&nbsp;(<?php echo $_smarty_tpl->tpl_vars['ATTACHVALUE']->value['size'];?>
)</span><a href="index.php?module=<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
&view=Index&_operation=mail&_operationarg=attachment_dld&_muid=<?php echo $_smarty_tpl->tpl_vars['MAIL']->value->muid();?>
&_atid=<?php echo $_smarty_tpl->tpl_vars['ATTACHID']->value;?>
&_atname=<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['DOWNLOAD_LINK']->value, 'htmlall', 'UTF-8');?>
">&nbsp;&nbsp;<i class="fa fa-download"></i></a></span><br><?php }?><?php } ?></div><?php }?></div>
<?php }} ?>