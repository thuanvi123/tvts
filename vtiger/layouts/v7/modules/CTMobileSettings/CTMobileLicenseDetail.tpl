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
{strip}
	<div class="container-fluid" id="EditConfigEditor">
		<div class="widget_header row-fluid">
			<button type="button" class="btn btn-info pull-right" style="background:#287DF2 !important;" onclick='window.location.href="{CTMobileSettings_Module_Model::$CTMOBILE_DETAILVIEW_URL}"'>{vtranslate('Go To CRMTiger Settings',$MODULE)}</button>
			<div class="span8"><h3>{vtranslate('CTMobile License Configuration', $MODULE)}</h3></div>
			<hr>
			<div class="span4"><div class="pull-right">
			{if $LICENCE_KEY neq ''}<button class="btn btn-danger" id="deactivateLicense" type="button" title="{vtranslate('LBL_DEACTIVATE', $MODULE)}"><strong>{vtranslate('LBL_DEACTIVATE', $MODULE)}</strong></button>&nbsp;{/if}
			<button class="btn btn-success editButton" data-url='?module=CTMobileSettings&parent=Settings&view=LicenseEdit' type="button" title="{vtranslate('LBL_EDIT', $MODULE)}"><strong>{vtranslate('LBL_EDIT', $MODULE)}</strong></button>
			<a class="cancelLink" type="reset" onclick="javascript:window.history.back();">{vtranslate('Cancel', $MODULE)}</a>
			</div>
			</div>	
		</div>
		<br/>
		<div id="successMessage">
		</div>
		<div class="contents">
					<table class="table table-bordered table-condensed themeTableColor">
						<tbody>
								<tr>
									<th colspan="2">
										{vtranslate('License Key Configuration',{$MODULE})}
									</th>
								</tr>
								<tr class="fieldLabel medium">
									<td width="30%" class="{$WIDTHTYPE}">
										<label class="muted pull-right marginRight10px"> 
										{vtranslate('License Key',{$MODULE})}</label>
									</td>
									<td  class="{$WIDTHTYPE} fieldValue medium">
										<label class="muted marginRight10px">{$LICENCE_KEY}</label>
									</td>
								</tr>
						</tbody>
					</table>
				   
		</div>
		<div><strong>{vtranslate('Note : If you\'re experience any problem in installation of above extensions.','CTMobileSettings')}</strong> 
		<br/><strong><a href="https://kb.crmtiger.com/knowledge-base/error-code-solutions/" target="_blank" style="color: rgb(17, 85, 204);" onmouseover="this.style.color='#00008b'" onmouseout="this.style.color='#15c'">{vtranslate('Click here','CTMobileSettings')}</a> {vtranslate('to download and install manually one by one all updated extensions related to CRMTiger Mobile Apps','CTMobileSettings')}</strong>
		</div>
	</div>
{/strip}
