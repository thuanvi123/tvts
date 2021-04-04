{*+***********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*************************************************************************************}
{strip}
{literal}

{/literal}
<pre>
    {include file='InstallerHeader.tpl'|@vtemplate_path:$QUALIFIED_MODULE}
    <div class="workFlowContents" style="padding-left: 3%;padding-right: 3%;font-family:Arial, Helvetica, sans-serif;">
        <div class="padding1per" style="border:1px solid #ccc; padding-left: 10px;">
			<label style="font-weight:100;">{vtranslate('Welcome to the CRMTiger Mobile Extensions Installation Wizard.',$QUALIFIED_MODULE)}</label>
			<br/>
			<label style="font-weight:100;">{vtranslate('Thank you for Installing',$QUALIFIED_MODULE)}<strong> “{vtranslate('CRMTiger Mobile Extensions',$QUALIFIED_MODULE)}” </strong>{vtranslate('For your vTiger Instance.',$QUALIFIED_MODULE)}</label>
			<br/>
			<label style="font-weight:100;">{vtranslate('The Product requires Certain PHP Libraries to be enabled  and other to function properly.',$QUALIFIED_MODULE)}</label>
			<br/>
			<label style="font-weight:100;">{vtranslate('Following is the status of Library which required to be installed if not installed on Your server.',$QUALIFIED_MODULE)}</label><br/>
			<label style="font-weight:100;">{vtranslate('Please contact your server administrator to install it properly for you.',$QUALIFIED_MODULE)}</label>
			<br/>
            <div class="control-group">
                <table width="100%" cellspacing="2px" cellpadding="2px" class="table table-bordered">
						<tr>
							<td width="200"><strong>{vtranslate('Library & Others',$QUALIFIED_MODULE)}</strong></td>
							<td width="150"><strong>{vtranslate('Status',$QUALIFIED_MODUL)}</strong></td>
						</tr>
						{foreach key=KEY item=VALUE from=$EXTENSIONS}
						<tr style="color:{if $VALUE['Extensions_status'] eq '1'}green{else}red{/if};">
							<td>{$VALUE['ExtensionsName']}</td>
							<td>{if $VALUE['Extensions_status'] eq '1'}{vtranslate('Installed',$QUALIFIED_MODULE)}{else}{vtranslate('Not Installed',$QUALIFIED_MODULE)}{/if}</td>
						</tr>
						{/foreach}
                </table>
            </div>

             <div class="control-group">
                <div><strong><span><br>{vtranslate('PHP.ini Settings',$QUALIFIED_MODULE)}<br></span></strong></div>
                <div><span>{vtranslate('It is recommended to have php.ini values set as below. You still proceed with the installation if php.ini requirements are not met. This can be adjusted later.',$QUALIFIED_MODULE)}</span></div>
                <div><span>{vtranslate('If you face any issues with CTMobile extension installation/activation - you should then update your php.ini to recommended settings.',$QUALIFIED_MODULE)}<u><a href="http://crmtiger.com/vtiger-php-ini-requirement.html" target="_blank">{vtranslate('Click here for instructions',$QUALIFIED_MODULE)}</a></u></span></div>
                <div >
                    <table cellspacing="2px" cellpadding="2px" class="table table-bordered">
                        <tr>
                            <td width="200"></td>
                            <td width="150"><strong>{vtranslate('Current Value',$QUALIFIED_MODULE)}</strong></td>
                            <td width="200"><strong>{vtranslate('Minimum Requirement',$QUALIFIED_MODULE)}</strong></td>
                            <td><strong>{vtranslate('Recommended Value',$QUALIFIED_MODULE)}</strong></td>
                        </tr>
                        <tr style="color: {if $default_socket_timeout>=60}#009900{else}#ff8000{/if}">
                            <td>default_socket_timeout</td>
                            <td>{$default_socket_timeout}</td>
                            <td>60</td>
                            <td style="color: {if $default_socket_timeout<600}#ff8000{else}#009900{/if}">600</td>
                        </tr>
                        <tr style="color: {if $max_execution_time>=60}#009900{else}#ff8000{/if}">
                            <td>max_execution_time</td>
                            <td>{$max_execution_time}</td>
                            <td>60</td>
                            <td style="color: {if $max_execution_time<600}#ff8000{else}#009900{/if}">600</td>
                        </tr>
                        <tr style="color: {if $max_input_time>=60 || $max_input_time==-1}#009900{else}#ff8000{/if}">
                            <td>max_input_time</td>
                            <td>{$max_input_time}</td>
                            <td>60</td>
                            <td style="color: {if $max_input_time<600 && $max_input_time!=-1}#ff8000{else}#009900{/if}">600</td>
                        </tr>
                        <tr style="color: {if $memory_limit>=256}#009900{else}#ff8000{/if}">
                            <td>memory_limit</td>
                            <td>{$memory_limit}M</td>
                            <td>256M</td>
                            <td style="color: {if $memory_limit<1024}#ff8000{else}#009900{/if}">1024M</td>
                        </tr>
                        <tr style="color: {if $post_max_size>=12}#009900{else}#ff8000{/if}">
                            <td>post_max_size</td>
                            <td>{$post_max_size}M</td>
                            <td>12M</td>
                            <td style="color: {if $post_max_size<50}#ff8000{else}#009900{/if}">50M</td>
                        </tr>
                        <tr style="color: {if $upload_max_filesize>=12}#009900{else}#ff8000{/if}">
                            <td>upload_max_filesize</td>
                            <td>{$upload_max_filesize}M</td>
                            <td>12M</td>
                            <td style="color: {if $upload_max_filesize<50}#ff8000{else}#009900{/if}">50M</td>
                        </tr>
                        <tr style="color: {if $max_input_vars>=10000}#009900{else}#ff8000{/if}">
                            <td>max_input_vars</td>
                            <td>{$max_input_vars}</td>
                            <td>10000</td>
                            <td style="color: {if $max_input_vars<10000}#ff8000{else}#009900{/if}">10000</td>
                        </tr>
                    </table>
                    <br>
                </div>
            </div>
            <center>
				<label>{vtranslate('Once you have change recommended settings please press button below.',$QUALIFIED_MODULE)}</label>
				<br/>
				<button class="btn btn-info" id="refreshRequirement" onClick="window.location.reload();" title="{vtranslate('Refresh',$QUALIFIED_MODULE)}"><i class="fa fa-refresh"></i>&nbsp;<span>{vtranslate('Refresh',$QUALIFIED_MODULE)}</span></button>
			</center>
			<label style="font-weight:100;">{vtranslate('We offer free support enabling php library/permission issues and others for',$QUALIFIED_MODULE)}<strong> {vtranslate('Premium customers',$QUALIFIED_MODULE)}. </strong> <br/>
			{vtranslate('If you are not sure what to do next, please contact us below and we`ll assist with the process.',$QUALIFIED_MODULE)}</label>
            <div class="control-group">
                <ul style="padding-left: 10px;">
                    <li>{vtranslate('LBL_EMAIL',$QUALIFIED_MODULE)}: &nbsp;&nbsp;<a href="mailto:support@crmtiger.com">support@CRMTiger.com</a></li>
                    <li>{vtranslate('LBL_PHONE',$QUALIFIED_MODULE)}: &nbsp;&nbsp;<span>+1 (630) 861-8263</span></li>
                    <li>{vtranslate('LBL_CHAT',$QUALIFIED_MODULE)}: &nbsp;&nbsp;{vtranslate('LBL_AVAILABLE_ON',$QUALIFIED_MODULE)} <a href="http://www.crmtiger.com" target="_blank">http://www.CRMTiger.com</a></li>
                </ul>
            </div>
            
        </div>
    </div>
    <div class="clearfix"></div>
</div>
{/strip}
