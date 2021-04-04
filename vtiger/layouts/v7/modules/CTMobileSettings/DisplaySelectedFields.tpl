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
<form action="index.php" method="post" id="display_Settings" class="form-horizontal">
    <input type="hidden" name="module" value="CTMobileSettings">
    <input type="hidden" name="action" value="SaveDisplayFieldAjax">
    <input type="hidden" name="display_field_module" value="{$SOURCE_MODULE}">
    <input type="hidden" name="userid" value="{$USERID}">
    <table class="" style="width: 500px;">
        <tr>
            <td>{vtranslate('First Field','CTMobileSettings')}</td>
            <td colspan="2" class="fieldValue medium">
                <select class="select2" id="first_field" name="first_field" data-placeholder="Select fields" style="width: 300px;margin-bottom: 25px;">
                    {foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE}
                        <optgroup label='{vtranslate($BLOCK_LABEL, $SOURCE_MODULE)}'>
                            {foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS}
                                <option value="{$FIELD_MODEL->getFieldName()}" data-field-name="{$FIELD_NAME}"
                                        {if $FIELD_MODEL->getFieldName() eq $SELECTED_FIELDS['first_field']}
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
                                <option value="{$FIELD_MODEL->getFieldName()}" data-field-name="{$FIELD_NAME}"
                                        {if $FIELD_MODEL->getFieldName() eq $SELECTED_FIELDS['first_field']}
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
        <tr>
            <td>{vtranslate('Second Field','CTMobileSettings')}</td>
            <td colspan="" class="fieldValue medium">
                <select class="select2" id="second_field" name="second_field" data-placeholder="Select fields" style="width: 300px;margin-bottom: 25px;">
                    {foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE}
                        <optgroup label='{vtranslate($BLOCK_LABEL, $SOURCE_MODULE)}'>
                            {foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS}
                                <option value="{$FIELD_MODEL->getFieldName()}" data-field-name="{$FIELD_NAME}"
                                        {if $FIELD_MODEL->getFieldName() eq $SELECTED_FIELDS['second_field']}
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
                                <option value="{$FIELD_MODEL->getFieldName()}" data-field-name="{$FIELD_NAME}"
                                        {if $FIELD_MODEL->getFieldName() eq $SELECTED_FIELDS['second_field']}
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
        <tr>
            <td>{vtranslate('Third Field','CTMobileSettings')}</td>
            <td colspan="" class="fieldValue medium">
                <select class="select2" id="third_field" name="third_field" data-placeholder="Select fields" style="width: 300px;margin-bottom: 25px;">
                    {foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE}
                        <optgroup label='{vtranslate($BLOCK_LABEL, $SOURCE_MODULE)}'>
                            {foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS}
                                <option value="{$FIELD_MODEL->getFieldName()}" data-field-name="{$FIELD_NAME}"
                                        {if $FIELD_MODEL->getFieldName() eq $SELECTED_FIELDS['third_field']}
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
                                <option value="{$FIELD_MODEL->getFieldName()}" data-field-name="{$FIELD_NAME}"
                                        {if $FIELD_MODEL->getFieldName() eq $SELECTED_FIELDS['third_field']}
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
