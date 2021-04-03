  {*<!--
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
-->*}

{literal}
<style type="text/css">
.widget_header.row-fluid {
  border: 1px solid rgb(44 59 73 / 0.15);
  border-bottom: 0;
  padding: 15px 15px;
}
.widget_header.row-fluid h3 {
  color: #333333;
  font-size: 20px;
  font-weight: 600;
  margin: 0 0 10px;
}
.widget_header.row-fluid button.btn.btn-info.pull-right {
  background: #287DF2 !important;
  border-radius: 4px;
  padding: 9px 10px;
  border: 0;
}

#notification_settings {
  background: #f4f5f6;
  border: 1px solid rgb(44 59 73 / 0.15);
  padding: 15px 15px;
}

#notification_settings h4 {
  color: #333;
  font-size: 16px;
  line-height: 22px;
  font-weight: 600;
  margin: 0;
}
#notification_settings h5 {
  color: #333;
  font-size: 14px;
  font-weight: 600;
  margin:0 0 10px;
  text-transform: capitalize;
}
#notification_settings h6 {
  color: #333;
  font-size: 13px;
  line-height: 20px;
  font-weight: 600;
  margin:0 0 10px;
}

.select2-container{
  width: 100%;
}

.select2-drop.select2-drop-above.select2-drop-active {
    border-top: 1px solid #5897fb;
}



.greendata{
  margin-left:24px !important;
}

</style>
{/literal}

<div class="container-fluid">
    

<div class="container-fluid main_notification_settings" id="notification_settings_block">
    <div class="widget_header row-fluid">
        <button type="button" class="btn btn-info pull-right" onclick='window.location.href="{CTMobileSettings_Module_Model::$CTMOBILE_DETAILVIEW_URL}"'>{vtranslate('Go To CRMTiger Settings',$MODULE)}</button>
        <h3>{vtranslate('CRMTiger Mobile Apps - Notification Settings', 'CTMobileSettings')}</h3>

        {vtranslate('Default notification to users on various action on records, if you wish to setup your own push notification','CTMobileSettings')}
        <br/>
        <a href="{CTMobileSettings_Module_Model::$CTMOBILE_WORKFLOW_URL}" style="color: #15c !important;
    text-decoration: underline !important;" target="_blank">{vtranslate('Click here','CTMobileSettings')}</a> {vtranslate('to setup from Settings->Workflow.(Add "CRMTiger In App notification" as an Action)','CTMobileSettings')}
    </div>
    
      <div class="clearfix"></div>
      <div class="summaryWidgetContainer" id="notification_settings">
          <div class="row-fluid pull-right" style="margin-top:10px;">
            <button class="btn btn-success btnSaveSettings" type="button">{vtranslate('LBL_SAVE', 'CTMobileSettings')}</button>
            <a class="cancelLink" type="reset" onclick="javascript:window.history.back();">{vtranslate('Cancel','CTMobileSettings')}</a>
          </div>
          <table class="table table-borderless">
                <tr>
                  <td style="width:62%"><h4><b>{vtranslate('Notification Settings','CTMobileSettings')}</b></h4></td>
                  <td style="width:28%">
                    <label class="switch">
                    <input type="checkbox" name="allow_notification" id="allow_notification" value="1" data-on-color="success" {if $ALLOW_NOTIFICATION_SETTINGS } checked {/if}>
                    <div class="slider"></div>
                    </label>
                  </td>
                </tr>
          </table>
          <form action="index.php" method="post" id="notification_settings_form" class="form-horizontal">
          <input type="hidden" name="module" value="CTMobileSettings">
          <input type="hidden" name="action" value="SaveNotificationSettings">
             <table class="table table-borderless">
                 
                  <tbody>
                    <tr>
                      <td colspan="3" style="width:100%;"><h5><b>{vtranslate('Events','CTMobileSettings')}</b></h5></td>
                    </tr>
                    <tr>
                      <td></td>
                      <td style="width:68%;"><h6><b style="margin-left:20px;">{vtranslate('Event Invitation','CTMobileSettings')}</b></h6></td>
                      <td style="width:32%;">
                        <label class="switch">
                        <input type="checkbox" name="event_invitation" value="1" {if $SELECTED_NOTIFICATION['event_invitation'] eq '1'}checked{/if} data-on-color="success">
                        <div class="slider"></div>
                        </label>
                      </td>
                    </tr>
                    <tr class="event_invitation_div">
                      <td></td>
                      <td colspan="2"> 
                        <div style="width:30%;display: inline-block;"><span style="margin-left:150px;"> {vtranslate('Notification Title','CTMobileSettings')} </span> <span class="redColor">*</span>
                        </div>
                        <div style="width:30%;display: inline-block;">
                          <input class="inputElement" type="text" name="event_invitation_title" placeholder="{vtranslate('Notification Title','CTMobileSettings')}" value="{$SELECTED_NOTIFICATION_TITLE_MESSAGE['event_invitation']['notification_title']}">
                        </div>
                      </td>
                    </tr>
                    <tr class="event_invitation_div">
                      <td></td>
                      <td colspan="2">
                        <div style="width:30%;display: inline-block;"><span style="margin-left:150px;">{vtranslate('LBL_ADD_FIELD','Settings:Workflows')}</span>
                        </div>
                        <div style="width:30%;display: inline-block;">
                            <select id="events-invitation-fieldnames" class="select2" data-placeholder="{vtranslate('LBL_SELECT_OPTIONS','Settings:Workflows')}"">
                              <option></option>
                              {foreach key=FILEDKEY item=FIELDS from=$EVENTS_FIELD_OPTIONS}
                                  <option value="{$FIELDS[1]}">{$FIELDS[0]}</option>
                              {/foreach}
                            </select>
                          </div>
                      </td>
                    </tr>
                    <tr class="event_invitation_div">
                      <td></td>
                      <td colspan="2"> 
                       <div style="width:30%;display: inline-block;position:relative;top:-75px;"> <span style="margin-left:130px;">{vtranslate('Notification Message','CTMobileSettings')} </span> <span class="redColor">*</span>
                       </div>
                       <div style="width:30%;display: inline-block;">
                       <textarea rows="5" class="inputElement" name="event_invitation_message" id="event_invitation_message" placeholder="{vtranslate('Notification Message','CTMobileSettings')}" style="height:100%;">{$SELECTED_NOTIFICATION_TITLE_MESSAGE['event_invitation']['notification_message']}</textarea>
                       </div>
                      </td>
                    </tr>

                    <tr>
                      <td></td>
                      <td><h6><b style="margin-left:20px;">{vtranslate('Event Reminder','CTMobileSettings')}</b></h6></td>
                      <td>
                        <label class="switch">
                        <input type="checkbox" name="event_reminder" value="1" {if $SELECTED_NOTIFICATION['event_reminder'] eq '1'}checked{/if} data-on-color="success">
                        <div class="slider"></div>
                        </label>
                      </td>
                    </tr>
                    <tr class="event_reminder_div">
                      <td></td>
                      <td colspan="2"> 
                        <div style="width:30%;display: inline-block;"><span style="margin-left:150px;"> {vtranslate('Notification Title','CTMobileSettings')} </span> <span class="redColor">*</span>
                        </div>
                        <div style="width:30%;display: inline-block;">
                          <input class="inputElement" type="text" name="event_reminder_title" placeholder="{vtranslate('Notification Title','CTMobileSettings')}" value="{$SELECTED_NOTIFICATION_TITLE_MESSAGE['event_reminder']['notification_title']}">
                        </div>
                      </td>
                    </tr>
                    <tr class="event_reminder_div">
                      <td></td>
                      <td colspan="2">
                        <div style="width:30%;display: inline-block;"><span style="margin-left:150px;">{vtranslate('LBL_ADD_FIELD','Settings:Workflows')}</span>
                        </div>
                        <div style="width:30%;display: inline-block;">
                            <select id="events-reminder-fieldnames" class="select2" data-placeholder="{vtranslate('LBL_SELECT_OPTIONS','Settings:Workflows')}"">
                              <option></option>
                              {foreach key=FILEDKEY item=FIELDS from=$EVENTS_FIELD_OPTIONS}
                                  <option value="{$FIELDS[1]}">{$FIELDS[0]}</option>
                              {/foreach}
                            </select>
                          </div>
                      </td>
                    </tr>
                    <tr class="event_reminder_div">
                      <td></td>
                      <td colspan="2"> 
                       <div style="width:30%;display: inline-block;position:relative;top:-75px;"> <span style="margin-left:130px;">{vtranslate('Notification Message','CTMobileSettings')} </span> <span class="redColor">*</span>
                       </div>
                       <div style="width:30%;display: inline-block;">
                       <textarea rows="5" class="inputElement" name="event_reminder_message" id="event_reminder_message" placeholder="{vtranslate('Notification Message','CTMobileSettings')}" style="height:100%;">{$SELECTED_NOTIFICATION_TITLE_MESSAGE['event_reminder']['notification_message']}</textarea>
                       </div>
                      </td>
                    </tr>
                    <tr class="event_reminder_div">
                      <td colspan="3">

                        <div id="reminder-controls" style="margin-left:37px;visibility:{if $SELECTED_NOTIFICATION['event_reminder'] eq '1'}visible{else}collapse{/if};">
                          <div id="reminder-selections" style="float:left;">
                            <span class="fieldLabel" style="float:left;margin: 5px 40px 5px 0;">{vtranslate('Send Reminder Before','CTMobileSettings')}</span>
                            <div style="float:left;margin-left: 92px;">
                              <div style="float:left">
                                <select class="select2" name="event_reminder_time">
                                    <option value="1" {if $REMINDER_VALUES eq '1'}selected{/if}>{vtranslate('1 Minute','CTMobileSettings')}</option>
                                    <option value="5" {if $REMINDER_VALUES eq '5'}selected{/if}>{vtranslate('5 Minutes','CTMobileSettings')}</option>
                                    <option value="15" {if $REMINDER_VALUES eq '15'}selected{/if}>{vtranslate('15 Minutes','CTMobileSettings')}</option>
                                    <option value="30" {if $REMINDER_VALUES eq '30'}selected{/if}>{vtranslate('30 Minutes','CTMobileSettings')}</option>
                                    <option value="45" {if $REMINDER_VALUES eq '45'}selected{/if}>{vtranslate('45 Minutes','CTMobileSettings')}</option>
                                    <option value="60" {if $REMINDER_VALUES eq '60'}selected{/if}>{vtranslate('1 Hour','CTMobileSettings')}</option>
                                </select>
                              </div>
                              <div class="clearfix"></div>
                            </div>
                          </div>
                          <div class="clearfix"></div>
                        </div>
                      </td>
                    </tr>

                    <tr>
                      <td colspan="3"><h5><b>{vtranslate('Conversions','CTMobileSettings')}</b></h5></td>
                    </tr>
                    <tr>
                      <td></td>
                      <td><h6><b style="margin-left:20px;">{vtranslate('When Record Assigned','CTMobileSettings')}</b></h6></td>
                      <td>
                        <label class="switch">
                        <input type="checkbox" name="record_assigned" value="1" {if $SELECTED_NOTIFICATION['record_assigned'] eq '1'}checked{/if} data-on-color="success">
                        <div class="slider"></div>
                        </label>
                      </td>
                    </tr>
                    <tr class="record_assigned_div">
                      <td></td>
                      <td colspan="2"> 
                        <div style="width:30%;display: inline-block;"><span style="margin-left:150px;"> {vtranslate('Notification Title','CTMobileSettings')} </span> <span class="redColor">*</span>
                        </div>
                        <div style="width:30%;display: inline-block;">
                          <input class="inputElement" type="text" name="record_assigned_title" placeholder="{vtranslate('Notification Title','CTMobileSettings')}" value="{$SELECTED_NOTIFICATION_TITLE_MESSAGE['record_assigned']['notification_title']}">
                        </div>
                      </td>
                    </tr>

                    <tr class="record_assigned_div">
                      <td></td>
                      <td colspan="2"> 
                       <div style="width:30%;display: inline-block;position:relative;top:-75px;"> <span style="margin-left:130px;">{vtranslate('Notification Message','CTMobileSettings')} </span> <span class="redColor">*</span>
                       </div>
                       <div style="width:30%;display: inline-block;">
                       <textarea rows="5" class="inputElement" name="record_assigned_message" placeholder="{vtranslate('Notification Message','CTMobileSettings')}" style="height:100%;">{$SELECTED_NOTIFICATION_TITLE_MESSAGE['record_assigned']['notification_message']}</textarea>
                       </div>
                      </td>
                    </tr>  
                    <tr class="record_assigned_div">
                        <td colspan="3">
                            <div class="container-fluid" id="record_assigned_div">
                              <div class="clearfix"></div>
                              <div class="summaryWidgetContainer" id="record_assigned_module_settings">

                                  <div class="row-fluid">
                                      <div style="margin-top:15px;">
                                          {vtranslate('Select module to allow to send notification when record assigned',$MODULE)}
                                      </div>
                                      <div class="select-search" style="margin-top:15px;">
                                          <select class="select2" multiple="true" id="record_assigned_module" name="record_assigned_module[]" data-placeholder="Select Modules">
                                              {foreach item=MODULE_MODEL key=TAB_ID from=$ALL_MODULE}
                                                      <option value="{$MODULE_MODEL->getName()}" {if in_array($MODULE_MODEL->getName(), $ASSIGNED_RECORD_MODULES)} selected {/if}>{vtranslate($MODULE_MODEL->getName(),$MODULE_MODEL->getName())}</option>
                                              {/foreach}
                                          </select>
                                      </div>
                                  </div>
                              </div>  
                          </div>
                        </td>
                    </tr>
                    <tr>
                      <td></td>
                      <td><h6><b style="margin-left:20px;">{vtranslate('You were mentioned in comments','CTMobileSettings')}</b></h6></td>
                      <td>
                        <label class="switch">
                        <input type="checkbox" name="comment_mentioned" value="1" {if $SELECTED_NOTIFICATION['comment_mentioned'] eq '1'}checked{/if} data-on-color="success">
                        <div class="slider"></div>
                        </label>
                      </td>
                    </tr>
                    <tr class="comment_mentioned_div">
                      <td></td>
                      <td colspan="2"> 
                        <div style="width:30%;display: inline-block;"><span style="margin-left:150px;"> {vtranslate('Notification Title','CTMobileSettings')} </span> <span class="redColor">*</span>
                        </div>
                        <div style="width:30%;display: inline-block;">
                          <input class="inputElement" type="text" name="comment_mentioned_title" placeholder="{vtranslate('Notification Title','CTMobileSettings')}" value="{$SELECTED_NOTIFICATION_TITLE_MESSAGE['comment_mentioned']['notification_title']}">
                        </div>
                      </td>
                    </tr>
                    <tr class="comment_mentioned_div">
                      <td></td>
                      <td colspan="2">
                        <div style="width:30%;display: inline-block;"><span style="margin-left:150px;">{vtranslate('LBL_ADD_FIELD','Settings:Workflows')}</span>
                        </div>
                        <div style="width:30%;display: inline-block;">
                            <select id="comment-mentioned-fieldnames" class="select2" data-placeholder="{vtranslate('LBL_SELECT_OPTIONS','Settings:Workflows')}"">
                              <option></option>
                              {foreach key=FILEDKEY item=FIELDS from=$COMMENTS_FIELD_OPTIONS}
                                  <option value="{$FIELDS[1]}">{$FIELDS[0]}</option>
                              {/foreach}
                            </select>
                          </div>
                      </td>
                    </tr>
                    <tr class="comment_mentioned_div">
                      <td></td>
                      <td colspan="2"> 
                       <div style="width:30%;display: inline-block;position:relative;top:-75px;"> <span style="margin-left:130px;">{vtranslate('Notification Message','CTMobileSettings')} </span> <span class="redColor">*</span>
                       </div>
                       <div style="width:30%;display: inline-block;">
                       <textarea rows="5" class="inputElement" name="comment_mentioned_message" id="comment_mentioned_message" placeholder="{vtranslate('Notification Message','CTMobileSettings')}" style="height:100%;">{$SELECTED_NOTIFICATION_TITLE_MESSAGE['comment_mentioned']['notification_message']}</textarea>
                       </div>
                      </td>
                    </tr>
                    <tr>
                      <td></td>
                      <td><h6><b style="margin-left:20px;">{vtranslate('Comments has been added to record assigned to you ','CTMobileSettings')}</b></h6></td>
                      <td>
                        <label class="switch">
                        <input type="checkbox" name="comment_assigned" value="1" {if $SELECTED_NOTIFICATION['comment_assigned'] eq '1'}checked{/if} data-on-color="success">
                        <div class="slider"></div>
                        </label>
                      </td>
                    </tr>
                    <tr class="comment_assigned_div">
                      <td></td>
                      <td colspan="2"> 
                        <div style="width:30%;display: inline-block;"><span style="margin-left:150px;"> {vtranslate('Notification Title','CTMobileSettings')} </span> <span class="redColor">*</span>
                        </div>
                        <div style="width:30%;display: inline-block;">
                          <input class="inputElement" type="text" name="comment_assigned_title" placeholder="{vtranslate('Notification Title','CTMobileSettings')}" value="{$SELECTED_NOTIFICATION_TITLE_MESSAGE['comment_assigned']['notification_title']}">
                        </div>
                      </td>
                    </tr>

                    <tr class="comment_assigned_div">
                      <td></td>
                      <td colspan="2"> 
                       <div style="width:30%;display: inline-block;position:relative;top:-75px;"> <span style="margin-left:130px;">{vtranslate('Notification Message','CTMobileSettings')} </span> <span class="redColor">*</span>
                       </div>
                       <div style="width:30%;display: inline-block;">
                       <textarea rows="5" class="inputElement" name="comment_assigned_message" placeholder="{vtranslate('Notification Message','CTMobileSettings')}" style="height:100%;">{$SELECTED_NOTIFICATION_TITLE_MESSAGE['comment_assigned']['notification_message']}</textarea>
                       </div>
                      </td>
                    </tr>
                    <tr class="comment_assigned_div">
                        <td colspan="3">
                            <div class="container-fluid" id="comment_assigned_div">
                              <div class="clearfix"></div>
                              <div class="summaryWidgetContainer" id="comment_assigned_module_settings">

                                  <div class="row-fluid">
                                      <div style="margin-top:15px;">
                                          {vtranslate('Select module to allow to send notification when comments has been added to record assigned',$MODULE)}
                                      </div>
                                      <div class="select-search" style="margin-top:15px;">
                                          <select class="select2" multiple="true" id="comment_assigned_module" name="comment_assigned_module[]" data-placeholder="Select Modules">
                                              {foreach item=MODULE_MODEL key=TAB_ID from=$ALL_MODULE}
                                                    {if $MODULE_MODEL->isCommentEnabled()}
                                                      <option value="{$MODULE_MODEL->getName()}" {if in_array($MODULE_MODEL->getName(), $ASSIGNED_RECORD_COMMENTS_MODULES)} selected {/if}>{vtranslate($MODULE_MODEL->getName(),$MODULE_MODEL->getName())}</option>
                                                    {/if}
                                              {/foreach}
                                          </select>
                                      </div>
                                  </div>
                              </div>  
                          </div>
                        </td>
                    </tr>
                    <tr>
                      <td colspan="3"><h5><b>{vtranslate('Task','CTMobileSettings')}</b></h5></td>
                    </tr>
                    <tr>
                      <td></td>
                      <td><h6><b style="margin-left:20px;">{vtranslate('Task Reminder','CTMobileSettings')}</b></h6></td>
                      <td>
                        <label class="switch">
                        <input type="checkbox" name="task_reminder" value="1" {if $SELECTED_NOTIFICATION['task_reminder'] eq '1'}checked{/if} data-on-color="success">
                        <div class="slider"></div>
                        </label>
                      </td>
                    </tr>
                    <tr class="task_reminder_div">
                      <td></td>
                      <td colspan="2"> 
                        <div style="width:30%;display: inline-block;"><span style="margin-left:150px;"> {vtranslate('Notification Title','CTMobileSettings')} </span> <span class="redColor">*</span>
                        </div>
                        <div style="width:30%;display: inline-block;">
                          <input class="inputElement" type="text" name="task_reminder_title" placeholder="{vtranslate('Notification Title','CTMobileSettings')}" value="{$SELECTED_NOTIFICATION_TITLE_MESSAGE['task_reminder']['notification_title']}">
                        </div>
                      </td>
                    </tr>
                    <tr class="task_reminder_div">
                      <td></td>
                      <td colspan="2">
                        <div style="width:30%;display: inline-block;"><span style="margin-left:150px;">{vtranslate('LBL_ADD_FIELD','Settings:Workflows')}</span>
                        </div>
                        <div style="width:30%;display: inline-block;">
                            <select id="task-reminder-fieldnames" class="select2" data-placeholder="{vtranslate('LBL_SELECT_OPTIONS','Settings:Workflows')}"">
                              <option></option>
                              {foreach key=FILEDKEY item=FIELDS from=$TASK_FIELD_OPTIONS}
                                  <option value="{$FIELDS[1]}">{$FIELDS[0]}</option>
                              {/foreach}
                            </select>
                          </div>
                      </td>
                    </tr>
                    <tr class="task_reminder_div">
                      <td></td>
                      <td colspan="2"> 
                       <div style="width:30%;display: inline-block;position:relative;top:-75px;"> <span style="margin-left:130px;">{vtranslate('Notification Message','CTMobileSettings')} </span> <span class="redColor">*</span>
                       </div>
                       <div style="width:30%;display: inline-block;">
                       <textarea rows="5" class="inputElement" name="task_reminder_message" id="task_reminder_message" placeholder="{vtranslate('Notification Message','CTMobileSettings')}" style="height:100%;">{$SELECTED_NOTIFICATION_TITLE_MESSAGE['task_reminder']['notification_message']}</textarea>
                       </div>
                      </td>
                    </tr>
                    <tr class="task_reminder_div">
                      <td colspan="3">

                        <div id="task-reminder-controls" style="margin-left:37px;visibility:{if $SELECTED_NOTIFICATION['task_reminder'] eq '1'}visible{else}collapse{/if};">
                          <div id="reminder-selections" style="float:left;">
                            <span class="fieldLabel" style="float:left;margin: 5px 40px 5px 0;">{vtranslate('Send Reminder Before','CTMobileSettings')}</span>
                            <div style="float:left;margin-left: 92px;">
                              <div style="float:left">
                                <select class="select2" name="task_reminder_time">
                                    <option value="1" {if $TASK_REMINDER_VALUES eq '1'}selected{/if}>{vtranslate('1 Minute','CTMobileSettings')}</option>
                                    <option value="5" {if $TASK_REMINDER_VALUES eq '5'}selected{/if}>{vtranslate('5 Minutes','CTMobileSettings')}</option>
                                    <option value="15" {if $TASK_REMINDER_VALUES eq '15'}selected{/if}>{vtranslate('15 Minutes','CTMobileSettings')}</option>
                                    <option value="30" {if $TASK_REMINDER_VALUES eq '30'}selected{/if}>{vtranslate('30 Minutes','CTMobileSettings')}</option>
                                    <option value="45" {if $TASK_REMINDER_VALUES eq '45'}selected{/if}>{vtranslate('45 Minutes','CTMobileSettings')}</option>
                                    <option value="60" {if $TASK_REMINDER_VALUES eq '60'}selected{/if}>{vtranslate('1 Hour','CTMobileSettings')}</option>
                                </select>
                              </div>
                              <div class="clearfix"></div>
                            </div>
                          </div>
                          <div class="clearfix"></div>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="3"><h5><b>{vtranslate('Follow record','CTMobileSettings')}</b></h5></td>
                    </tr>
                    <tr>
                      <td></td>
                      <td><h6><b style="margin-left:20px;">{vtranslate('Notify when any updates to the record you\'re following','CTMobileSettings')}</b></h6></td>
                      <td>
                        <label class="switch">
                        <input type="checkbox" name="follow_record" value="1" {if $SELECTED_NOTIFICATION['follow_record'] eq '1'}checked{/if} data-on-color="success">
                        <div class="slider"></div>
                        </label>
                      </td>
                    </tr>
                    <tr class="follow_record_div">
                      <td></td>
                      <td colspan="2"> 
                        <div style="width:30%;display: inline-block;"><span style="margin-left:150px;"> {vtranslate('Notification Title','CTMobileSettings')} </span> <span class="redColor">*</span>
                        </div>
                        <div style="width:30%;display: inline-block;">
                          <input class="inputElement" type="text" name="follow_record_title" placeholder="{vtranslate('Notification Title','CTMobileSettings')}" value="{$SELECTED_NOTIFICATION_TITLE_MESSAGE['follow_record']['notification_title']}">
                        </div>
                      </td>
                    </tr>

                    <tr class="follow_record_div">
                      <td></td>
                      <td colspan="2"> 
                       <div style="width:30%;display: inline-block;position:relative;top:-75px;"> <span style="margin-left:130px;">{vtranslate('Notification Message','CTMobileSettings')} </span> <span class="redColor">*</span>
                       </div>
                       <div style="width:30%;display: inline-block;">
                       <textarea rows="5" class="inputElement" name="follow_record_message" placeholder="{vtranslate('Notification Message','CTMobileSettings')}" style="height:100%;">{$SELECTED_NOTIFICATION_TITLE_MESSAGE['follow_record']['notification_message']}</textarea>
                       </div>
                      </td>
                    </tr>
                    <tr class="follow_record_div">
                        <td colspan="3">
                            <div class="container-fluid" id="follow_record_div">
                              <div class="clearfix"></div>
                              <div class="summaryWidgetContainer" id="follow_record_module_settings">

                                  <div class="row-fluid">
                                      <div style="margin-top:15px;">
                                          {vtranslate('Select module to allow to send notification when any updates to the record you\'re following',$MODULE)}
                                      </div>
                                      <div class="select-search" style="margin-top:15px;">
                                          <select class="select2" multiple="true" id="follow_record_module" name="follow_record_module[]" data-placeholder="Select Modules">
                                              {foreach item=MODULE_MODEL key=TAB_ID from=$ALL_MODULE}
                                                      <option value="{$MODULE_MODEL->getName()}" {if in_array($MODULE_MODEL->getName(), $ASSIGNED_RECORD_COMMENTS_MODULES)} selected {/if}>{vtranslate($MODULE_MODEL->getName(),$MODULE_MODEL->getName())}</option>
                                              {/foreach}
                                          </select>
                                      </div>
                                  </div>
                              </div>  
                          </div>
                        </td>
                    </tr>
                    <tr>
                      <td colspan="3">
                       <div class="row-fluid pull-right" style="margin-top:10px;">
                        <button class="btn btn-success btnSaveSettings" type="button">{vtranslate('LBL_SAVE', 'CTMobileSettings')}</button>
                        <a class="cancelLink" type="reset" onclick="javascript:window.history.back();">{vtranslate('Cancel','CTMobileSettings')}</a>
                      </div>
                      </td>
                    </tr>
                  </tbody>
             </table>
            
           </form>
      </div>
     
</div>