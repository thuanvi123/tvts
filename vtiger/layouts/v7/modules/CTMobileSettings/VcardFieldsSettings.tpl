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
        <h3>{vtranslate('CRMTiger Mobile Apps - VCard Configuration', 'CTMobileSettings')}</h3>
    </div>
    <hr>
      <div class="clearfix"></div>
      <div class="summaryWidgetContainer" id="vcard_fields_settings">

          <div class="row-fluid">
              <div style="margin-top:15px;">
                 {vtranslate('Select module and fields to be export to vCard, Save contact to mobile phone or share records to any other users or contacts from Mobile Apps',$MODULE)}
              </div>
              <div class="select-search" style="margin-top:15px;">
                  <select class="inputElement select2 {if $OCCUPY_COMPLETE_WIDTH} row {/if}" id="vcard_module" name="vcard_module" style="width: 300px;">
                      <option value="">{vtranslate('LBL_SELECT_MODULE', 'CTMobileSettings')}</option>
                      {foreach from=$ALL_MODULE item=MODULE}
                          <option value="{$MODULE}">{vtranslate($MODULE, $MODULE)}</option>
                      {/foreach}
                  </select>
              </div>
          </div>
          <br/>
          <div id="vcardselectedFields">

          </div>
          <br/>
                                 
                              
              
              
      </div>
     
</div>

