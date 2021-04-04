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
 <form action="index.php" method="post" id="asset_Settings" class="form-horizontal">
      <input type="hidden" name="module" value="CTMobileSettings">
      <input type="hidden" name="action" value="SaveAssetAjax">
      <input type="hidden" name="asset_module" value="{$ASSET_SOURCE_MODULE}">

      <select class="inputElement select2 {if $OCCUPY_COMPLETE_WIDTH} row {/if}" id="asset_fields" name="asset_fields" style="width: 300px;">
          
          {foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$ASSET_RECORD_STRUCTURE}
            <optgroup label='{vtranslate($BLOCK_LABEL, $ASSET_SOURCE_MODULE)}'>
                {foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS}
                    <option value="{$FIELD_MODEL->getCustomViewColumnName()}" data-field-name="{$FIELD_NAME}"
                            {if in_array($FIELD_MODEL->getCustomViewColumnName(), $ASSET_SELECTED_FIELDS)}
                                selected
                            {/if}
                            >{vtranslate($FIELD_MODEL->get('label'), $ASSET_SOURCE_MODULE)}
                    </option>
                {/foreach}
            </optgroup>
          {/foreach}
          {*Required to include event fields for columns in calendar module advanced filter*}
          {foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$EVENT_RECORD_STRUCTURE}
            <optgroup label='{vtranslate($BLOCK_LABEL, 'Events')}'>
                {foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS}
                    <option value="{$FIELD_MODEL->getCustomViewColumnName()}" data-field-name="{$FIELD_NAME}"
                            {if in_array($FIELD_MODEL->getCustomViewColumnName(), $ASSET_SELECTED_FIELDS)}
                                selected
                            {/if}
                            >{vtranslate($FIELD_MODEL->get('label'), $ASSET_SOURCE_MODULE)}
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
  </form>
