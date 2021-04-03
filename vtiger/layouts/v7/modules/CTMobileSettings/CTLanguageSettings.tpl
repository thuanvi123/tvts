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
        <h3>{vtranslate('CRMTiger Mobile Apps-Language Settings', 'CTMobileSettings')}</h3>
    </div>
    <hr>
    <h5 style="margin-left:20px;">{vtranslate('This section allow you to setup text display in CRMTiger mobile apps according to your language preference.','CTMobileSettings')}<br/>{vtranslate('you have an ability to change language text as per the needs.','CTMobileSettings')}</h5>
      <div class="clearfix"></div>
      <div class="summaryWidgetContainer" id="ct_language_settings">                
        <div class="row-fluid">
             <table class="table table-bordered blockContainer showInlineTable equalSplit" >
                <tr>
                    <td class="fieldLabel alignMiddle">
                    {vtranslate('Please choose your language','CTMobileSettings')}<span class="redColor">*</span>:
                    </td>
                    <td class="fieldLabel alignMiddle">
                        <select class="inputElement select2 {if $OCCUPY_COMPLETE_WIDTH} row {/if}" id="ct_language" name="ct_language" style="width: 300px;">
                        <option value="">{vtranslate('Select Language', 'CTMobileSettings')}</option>
                        {foreach from=$ALL_LANGUAGES key=lang item=languages}
                            <option value="{$lang}">{$languages}</option>
                        {/foreach}
                        </select>
                    </td>
                    <td class="fieldLabel alignMiddle">
                     {vtranslate('Please choose your section','CTMobileSettings')}<span class="redColor">*</span>:
                    </td>
                    <td class="fieldValue">
                        <select class="inputElement select2 {if $OCCUPY_COMPLETE_WIDTH} row {/if}" id="ct_section" name="ct_section" style="width: 300px;">
                            <option value="">{vtranslate('Select Section', 'CTMobileSettings')}</option>
                            {foreach from=$ALL_SECTIONS key=id item=name}
                                <option value="{$id}">{$name}</option>
                            {/foreach}
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
              
             