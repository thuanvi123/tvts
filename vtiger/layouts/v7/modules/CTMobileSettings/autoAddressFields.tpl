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
<form action="index.php" method="post" id="Settings" class="form-horizontal">
    <input type="hidden" name="module" value="CTMobileSettings">
    <input type="hidden" name="mode" value="SaveAddressFields">
    <input type="hidden" name="action" value="SaveAjax">
    <input type="hidden" name="search_module" value="{$SOURCE_MODULE}">
    <table class="" style="width: 500px;">
        
        {foreach item=FIELD from=$SELECTED_ADDRESS_FIELDS}

        <tr>
            <td class="fieldValue medium">
                {if $FIELD eq 'Auto-Search'}
                     <label>{vtranslate('Auto Address Finder Field', "CTMobileSettings")}</label>
                {else}
                     <label>{vtranslate($FIELD, "CTMobileSettings")}</label>
                {/if}
            </td>
            <td class="fieldValue medium">
                <select class="select2" id="moduleFields" name="fields_{$FIELD}" data-placeholder="Select fields" style="width: 300px;margin-bottom: 5px;">
                    {foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE}
                        <optgroup label='{vtranslate($BLOCK_LABEL, $SOURCE_MODULE)}'>
                            {foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS}
				{if in_array($FIELD_MODEL->get('uitype'),array('1','2','19','21'))}
                                <option value="{$FIELD_NAME}" data-field-name="{$FIELD_NAME}"
                                    {if ($FIELD_NAME eq $SELECTED_FIELDS[$FIELD])}
                                        selected
                                    {/if}
                                    >{vtranslate($FIELD_MODEL->get('label'), $SOURCE_MODULE)}
                                </option>
				{/if}
                            {/foreach}
                        </optgroup>
                    {/foreach}
                    {*Required to include event fields for columns in calendar module advanced filter*}
                    {foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$EVENT_RECORD_STRUCTURE}
                        <optgroup label='{vtranslate($BLOCK_LABEL, 'Events')}'>
                            {foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS}
				{if in_array($FIELD_MODEL->get('uitype'),array('1','2','19','21'))}
                                <option value="{$FIELD_NAME}" data-field-name="{$FIELD_NAME}"
                                    {if in_array($FIELD_NAME, $SELECTED_FIELDS)}
                                        selected
                                    {/if}
                                    >{vtranslate($FIELD_MODEL->get('label'), $SOURCE_MODULE)}
                                </option>
				{/if}
                            {/foreach}
                        </optgroup>
                    {/foreach}
                </select>
            </td>    
        </tr>
        {if $FIELD eq 'Auto-Search'}
            <tr>
            <td style="padding-top:20px;"><label>{vtranslate('GEO API Field',$SOURCE_MODULE)}</label></td>
            <td style="padding-top:20px;"><label>{vtranslate('Module Field',$SOURCE_MODULE)}</label></td>
            </tr>
          {/if}
        {/foreach}
    </table>
    <br />  
    <div class="row-fluid">
        <button class="btn btn-success btnSaveSettings" type="button">{vtranslate('LBL_SAVE', 'CTMobileSettings')}</button>
        <a class="cancelLink" type="reset" onclick="javascript:window.history.back();">{vtranslate('Cancel','CTMobileSettings')}</a>
    </div>
</form>
