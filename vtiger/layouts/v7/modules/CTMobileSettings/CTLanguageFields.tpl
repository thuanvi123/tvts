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
    <input type="hidden" name="action" value="SaveLanguageAjax">
    <input type="hidden" name="ctlanguage" value="{$CTLANGAUGE}">
    <input type="hidden" name="ctlanguage_section" value="{$CTLANGUAGE_SECTION}">
    <div class="fieldBlockContainer">
    <table class="table table-bordered blockContainer showInlineTable equalSplit">
    {if count($LANGUAGE_FIELDS) eq '0'}
        <tr>
            <td>
                <div class="emptyRecordsContent">
                {vtranslate('LBL_NO','CTMobileSettings')} {vtranslate('LBL_LABELS','CTMobileSettings')} {vtranslate('LBL_FOUND')} 
                </div>
            </td>
        </tr>
    {else}
        <tr>
        {assign var=COUNTER value=0}
        {foreach from=$LANGUAGE_FIELDS key=key item=FIELDS}
                {if $COUNTER eq 2}
                    </tr><tr>
                    {assign var=COUNTER value=1}
                {else}
                    {assign var=COUNTER value=$COUNTER+1}
                {/if}
                <td class="fieldLabel alignMiddle" >
                    {$FIELDS['keyword_name']}
                </td>
                <td class="fieldValue">
                    <textarea name="field_{$FIELDS['keyword_id']}" class="inputElement" rows="3">{$FIELDS['language_keyword']}</textarea>
                </td>
                {if count($LANGUAGE_FIELDS) eq '1'}
                    <td class="fieldLabel alignMiddle" style="width:200px;"></td><td class="fieldValue"></td>
                {/if}
        {/foreach}
        </tr>
    {/if}
    </table>
    </div>
    {if count($LANGUAGE_FIELDS) neq '0'}
    <br />
    <div class="row-fluid">
        <button class="btn btn-success btnSaveSettings" type="button">{vtranslate('LBL_SAVE', 'CTMobileSettings')}</button>
		<a class="cancelLink" type="reset" onclick="javascript:window.history.back();">{vtranslate('Cancel','CTMobileSettings')}</a>
    </div>
    {/if}
</form>
