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
<div class="container-fluid">
    <div class="widget_header row-fluid">
    	<button type="button" class="btn btn-info pull-right" style="background:#287DF2 !important;" onclick='window.location.href="{CTMobileSettings_Module_Model::$CTMOBILE_DETAILVIEW_URL}"'>{vtranslate('Go To CRMTiger Settings',$MODULE)}</button>
        <h3>{vtranslate('Send Push Notification', 'CTMobileSettings')}</h3>
    </div>
    <hr>
       <h5 style="margin-left:20px;">{vtranslate('Simply Select Users/Roles/Groups/ to send Push Notification to Team for Some important messages','CTMobileSettings')}</h5>
      <div class="clearfix"></div>
      <div class="summaryWidgetContainer" id="global_search_settings">
		   <form action="index.php" method="post" id="Settings" class="form-horizontal">
				<input type="hidden" name="module" value="CTMobileSettings"/>
				<input type="hidden" name="action" value="SendPushNotification"/>
				<table class="table table-bordered blockContainer showInlineTable equalSplit" style="width: 500px;">
					<tr>
						<td class="fieldLabel alignMiddle">{vtranslate('Type',$MODULE)}<span class="redColor">*</span></td>
						<td class="fieldValue medium">
								<select class="select2" id="type" name="type" data-placeholder="{vtranslate('Type', $MODULE)}" style="width:100%">
									<option value="normal">Normal Message</option>
									<option value="link">Link Message</option>
								</select>
							<br/>
							{vtranslate("Normal Message : This Type of message display as push notification to the user and click on that just open CRMTiger Apps",$MODULE)}
							<br/>
							<br/>
							{vtranslate("Link Message : This Type of message display as push notification to the user and click on that will open URL in Mobile browser based on URL specified in URL Box.",$MODULE)}
						</td>
					</tr>
					<tr>
						<td class="fieldLabel alignMiddle">{vtranslate('Users And Groups',$MODULE)}<span class="redColor">*</span></td>
						<td class="fieldValue">
							<select class="select2" multiple="true" id="moduleFields" name="Users[]" data-placeholder="{vtranslate('Select Users And Groups', $MODULE)}" style="width:100%">
							    <optgroup label="{vtranslate('LBL_USERS')}">
								{foreach key=FIELD_NAME item=FIELD_MODEL from=$USER_MODEL}
									<option value="{$FIELD_MODEL['userid']}" data-field-name="{$FIELD_MODEL['username']}"
											>{$FIELD_MODEL['username']}
									</option>
								{/foreach}
								</optgroup>
								<optgroup label="{vtranslate('LBL_GROUPS')}">
								{foreach key=FIELD_NAME item=FIELD_MODEL from=$GROUPS_MODEL}
									<option value="{$FIELD_MODEL['userid']}" data-field-name="{$FIELD_MODEL['username']}"
											>{$FIELD_MODEL['username']}
									</option>
								{/foreach}
								</optgroup>
							</select>
						</td>
					</tr>
					<tr>
						<td class="fieldLabel alignMiddle">{vtranslate('Title',$MODULE)}<span class="redColor">*</span></td>
						<td class="fieldValue medium">
							<input class="inputElement" type="text" name="title" id="title">
						</td>
					</tr>
					<tr>
						<td class="fieldLabel alignMiddle">{vtranslate('Message',$MODULE)}<span class="redColor">*</span></td>
						<td class="fieldValue">
							<textarea class="textAreaElement col-lg-12" rows="7" name="message" maxlength="500"></textarea> 
						</td>
					</tr>
					<tr>
						<td class="fieldLabel alignMiddle">{vtranslate('URL',$MODULE)}<span class="redColor">*</span></td>
						<td class="fieldValue">
							<input class="inputElement" type="text" name="notification_url" id="notification_url">
						</td>
					</tr>
				</table>
				<br />
				<div class="row-fluid">
					<button class="btn btn-success btnSendNotification" type="button">{vtranslate('LBL_SEND', $MODULE)}</button>
					<a class="cancelLink" type="reset" onclick="javascript:window.history.back();">{vtranslate('Cancel', $MODULE)}</a>
				</div>
			</form>
      </div>
</div>
