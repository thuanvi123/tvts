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
        <h3>{vtranslate('CRMTiger Mobile Apps - Asset-tracking Field Configuration', 'CTMobileSettings')}</h3>
    </div>
    <hr>
      <div class="clearfix"></div>
      <div class="summaryWidgetContainer" id="asset_fields_settings">

          <div class="row-fluid">
              <div style="margin-top:15px;">
                {vtranslate('Setup field to track asset from asset module, This will help to scan asset against field specified in asset tracking field',$MODULE)}
              </div>
              <div class="select-search" style="margin-top:15px;">
                  <form action="index.php" method="post" id="Settings" class="form-horizontal">
                      <input type="hidden" name="module" value="CTMobileSettings">
                      <input type="hidden" name="action" value="SaveAssetAjax">
                      <input type="hidden" name="asset_module" value="{$SOURCE_MODULE}">

                      <select class="inputElement select2 {if $OCCUPY_COMPLETE_WIDTH} row {/if}" id="fields" name="fields" style="width: 300px;">
                          
                          {foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE}
                            <optgroup label='{vtranslate($BLOCK_LABEL, $SOURCE_MODULE)}'>
                                {foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS}
                                    <option value="{$FIELD_MODEL->getCustomViewColumnName()}" data-field-name="{$FIELD_NAME}"
                                            {if in_array($FIELD_MODEL->getCustomViewColumnName(), $SELECTED_FIELDS)}
                                                selected
                                            {/if}
                                            >{vtranslate($FIELD_MODEL->get('label'), $SOURCE_MODULE)}
                                    </option>
                                {/foreach}
                            </optgroup>
                          {/foreach}
                          {*Required to include event fields for columns in calendar module advanced filter*}
                          {foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$EVENT_RECORD_STRUCTURE}
                            <optgroup label='{vtranslate($BLOCK_LABEL, 'Events')}'>
                                {foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS}
                                    <option value="{$FIELD_MODEL->getCustomViewColumnName()}" data-field-name="{$FIELD_NAME}"
                                            {if in_array($FIELD_MODEL->getCustomViewColumnName(), $SELECTED_FIELDS)}
                                                selected
                                            {/if}
                                            >{vtranslate($FIELD_MODEL->get('label'), $SOURCE_MODULE)}
                                    </option>
                                {/foreach}
                            </optgroup>
                          {/foreach}
                      </select>
                      <br/>
                      <br/>
                      <div class="row-fluid">
                        <button class="btn btn-success btnSaveSettings" type="button">{vtranslate('LBL_SAVE', 'CTMobileSettings')}</button>
                        <a class="cancelLink" type="reset" onclick="javascript:window.history.back();">{vtranslate('Cancel','CTMobileSettings')}</a>
                    </div>
              </div>
          </div>
   
      </div>
     
</div>

