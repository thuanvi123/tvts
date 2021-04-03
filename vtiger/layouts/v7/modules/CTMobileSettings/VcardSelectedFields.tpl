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
<form action="index.php" method="post" id="vcard_Settings" class="form-horizontal">
    <input type="hidden" name="module" value="CTMobileSettings">
    <input type="hidden" name="action" value="SaveVCardAjax">
    <input type="hidden" name="vcard_module" value="{$SOURCE_MODULE}">
    <table class="" style="width: 500px;">
        <tr>
            <td colspan="2" class="fieldValue medium">
                <select class="select2" multiple="true" id="moduleFields" name="fields[]" data-placeholder="Select fields" style="width: 800px;margin-bottom: 25px;">
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
            </td>
        </tr>
    </table>
    <br />  
    <div class="row-fluid">
        <button class="btn btn-success btnSaveSettings" type="button">{vtranslate('LBL_SAVE', 'CTMobileSettings')}</button>
        <a class="cancelLink" type="reset" onclick="javascript:window.history.back();">{vtranslate('Cancel','CTMobileSettings')}</a>
    </div>
</form>
