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
    <input type="hidden" name="action" value="SaveAjax">
    <input type="hidden" name="search_module" value="{$SOURCE_MODULE}">
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
        <tr>
            <td class="table table-bordered" style="padding: 10px;">
                <label>{vtranslate('Apply selected filter to sync records', 'CTMobileSettings')}</label>
                <br/>
                <select class="inputElement select2 {if $OCCUPY_COMPLETE_WIDTH} row {/if}" id="module_filter" name="module_filter" style="width: 300px;margin-bottom: 25px;">
                 {foreach from=$CUSTOM_VIEWS key=Group item=filter}
                  <optgroup label="{vtranslate($Group,$MODULE)}">
                    {foreach from=$filter item=view}
                        <option value="{$view->get('cvid')}" {if $view->get('cvid') eq $VIEWID}selected="selected"{/if}>{$view->get('viewname')}</option>
                    {/foreach}
                  </optgroup>
                 {/foreach}
                </select>
                <div>{vtranslate('Note : Records with selected filter only sync to capture correct Lattitude and Longitude for selected address from the record.',$MODULE)}</div>
            </td>
        </tr>
        <tr>
            <td id="geo_record_summary" class="table table-bordered" style="padding: 10px;">
                <div><label>{vtranslate('Record Summary (GEOCoding)',$MODULE)}</label></div>
                <div id="totalRecords">{vtranslate('# of records',$MODULE)} : <span style="font-weight: bold;">{$noOfEntries}</span></div>
                <div id="addressRecords">{vtranslate('# of records with correct address (Address supported by GEO API)',$MODULE)} : <span style="font-weight: bold;text-decoration: underline;""><a id="addressPopup" href="#" > {$AddressRecords}</a></span></div>
                <div id="nonAddressRecords">{vtranslate('# of records without correct address (Address not supported by GEO API)',$MODULE)} : <span style="font-weight: bold;text-decoration: underline;"><a id="nonAddressPopup" href="#" > {$nonAddressRecords} </a></span><div>
            </td>
        </tr>
    </table>
    <br />  
    <div class="row-fluid">
        <button class="btn btn-success btnSaveSettings" type="button">{vtranslate('LBL_SAVE', 'CTMobileSettings')}</button>
        <button class="btn btn-info" style="background:#287DF2 !important;" id="btnsyncNow" type="button" title="{vtranslate('Sync only first 500 records. Wait for automatic process to sync all records.','CTMobileSettings')}">{vtranslate('Sync Now', 'CTMobileSettings')}</button>
		<a class="cancelLink" type="reset" onclick="javascript:window.history.back();">{vtranslate('Cancel','CTMobileSettings')}</a>
    </div>
</form>
