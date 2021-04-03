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
        <h3>{vtranslate("MODULE_LBL",$MODULE)}</h3>
    </div>
    <hr>
      <h4 style="margin-left:20px;">{vtranslate('Select Mobile app users','CTMobileSettings')}</h4>
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
								<option value="selectAll" {if in_array('selectAll',$SELECTED_FIELDS)} selected {/if}>{vtranslate('LBL_ALL_USERS','CTMobileSettings')}</option>
							</optgroup>
							<optgroup label="{vtranslate('LBL_USERS')}">
								{foreach key=FIELD_NAME item=FIELD_MODEL from=$USER_MODEL}
									<option value="{$FIELD_MODEL['userid']}" data-field-name="{$FIELD_MODEL['username']}"
											 {if in_array($FIELD_MODEL['userid'], $SELECTED_FIELDS)}
                                               selected
                                             {/if}
											>{$FIELD_MODEL['username']}
									</option>
								{/foreach}
								</optgroup>
								<optgroup label="{vtranslate('LBL_GROUPS')}">
								{foreach key=FIELD_NAME item=FIELD_MODEL from=$GROUPS_MODEL}
									<option value="{$FIELD_MODEL['userid']}" data-field-name="{$FIELD_MODEL['username']}"
									{if in_array($FIELD_MODEL['userid'], $SELECTED_FIELDS)}
                                               selected
                                             {/if}
											>{$FIELD_MODEL['username']}
									</option>
								{/foreach}
								</optgroup>
							</select>
						</td>
					</tr>
				</table>
				<br />
				<div class="row-fluid">
					<button class="btn btn-success btnSaveAccessUser" type="button">{vtranslate('LBL_SAVE', 'CTMobileSettings')}</button>
					<a class="cancelLink" type="reset" onclick="javascript:window.history.back();">{vtranslate('Cancel','CTMobileSettings')}</a>
				</div>
			</form>
      </div>
</div>
