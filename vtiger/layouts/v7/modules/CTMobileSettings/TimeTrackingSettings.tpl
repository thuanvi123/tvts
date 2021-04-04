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
.nav-tabs > li.active > a, .nav-tabs > li.active > a:hover, .nav-tabs > li.active > a:focus {
    color: #333;
    border-bottom: 3px solid #555;
}
</style>
{/literal}

<div class="container-fluid">
    <div class="widget_header row-fluid">
        <h3>{vtranslate('CRMTiger Mobile Apps - Time Tracker Module Configuration', 'CTMobileSettings')}</h3>
    </div>
    <hr>
    <div class="clearfix"></div>
    <div class="summaryWidgetContainer" id="timetracker_module_settings">

        <div class="row-fluid">
            <div style="margin-top:15px;">
                {vtranslate('Select module',$MODULE)}
            </div>
            <div class="select-search" style="margin-top:15px;">
                <select class="select2" multiple="true" id="moduleFields" name="module[]" data-placeholder="Select Modules" style="width: 800px">
                    {foreach item=MODULE_MODEL key=TAB_ID from=$ALL_MODULE}
                        {if $MODULE_MODEL->customized eq 0 && $MODULE_MODEL->getName() neq 'SMSNotifier' && $MODULE_MODEL->getName() neq 'ModComments'}
                            <option value="{$MODULE_MODEL->getName()}" {if in_array($MODULE_MODEL->getName(), $TIMETRACKEMODULES)} selected {/if}>{vtranslate($MODULE_MODEL->getName(),$MODULE_MODEL->getName())}</option>
                        {/if}
                    {/foreach}
                </select>
            </div>
        </div>
        <br/>
        <div class="row-fluid">
            <button class="btn btn-success btnSaveModule" type="button">{vtranslate('LBL_SAVE', 'CTMobileSettings')}</button>
            <a class="cancelLink" type="reset" onclick="javascript:window.history.back();">{vtranslate('Cancel','CTMobileSettings')}</a>
        </div>  
    </div>  
</div>

