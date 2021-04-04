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
.ctblocks{
  border:1px solid black;
  margin-bottom:10px;
}
</style>
{/literal}

<link rel="stylesheet" type="text/css" href="layouts/v7/modules/CTMobileSettings/dataTables.bootstrap.min.css" />
<script type="text/javascript" src="layouts/v7/modules/CTMobileSettings/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="layouts/v7/modules/CTMobileSettings/dataTables.bootstrap.min.js"></script>

<div class="container-fluid">
    <div class="widget_header row-fluid">
        <button type="button" class="btn btn-info pull-right" style="background:#287DF2 !important;" onclick='window.location.href="{CTMobileSettings_Module_Model::$CTMOBILE_DETAILVIEW_URL}"'>{vtranslate('Go To CRMTiger Settings',$MODULE)}</button>
        <h3>{vtranslate('CRMTiger Mobile Apps - Fields Configuration', 'CTMobileSettings')}</h3>
    </div>

<div class="container-fluid ctblocks" id="vcard_block">
    <div class="widget_header row-fluid">
        <h3>{vtranslate('VCard Configuration', 'CTMobileSettings')}</h3>
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


<div class="container-fluid ctblocks" id="barcode_block">
    <div class="widget_header row-fluid">
        <h3>{vtranslate('Barcode Field Configuration', 'CTMobileSettings')}</h3>
    </div>
    <hr>
      <div class="clearfix"></div>
      <div class="summaryWidgetContainer" id="barcode_fields_settings">

          <div class="row-fluid">
              <div style="margin-top:15px;">
                 {vtranslate('Select field to scan barcode, This will helpful while adding Inventory in Quotes, Invoice, Sales order or Purchase order',$MODULE)}
              </div>
              <div class="select-search" style="margin-top:15px;">
                  <form action="index.php" method="post" id="barcode_Settings" class="form-horizontal">
                      <input type="hidden" name="module" value="CTMobileSettings">
                      <input type="hidden" name="action" value="SaveBarcodeAjax">
                      <input type="hidden" name="barcode_module" value="{$BARCODE_SOURCE_MODULE}">

                      <select class="inputElement select2 {if $OCCUPY_COMPLETE_WIDTH} row {/if}" id="barcode_fields" name="barcode_fields" style="width: 300px;">
                          
                          {foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$BARCODE_RECORD_STRUCTURE}
                            <optgroup label='{vtranslate($BLOCK_LABEL, $BARCODE_SOURCE_MODULE)}'>
                                {foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS}
                                    <option value="{$FIELD_MODEL->getCustomViewColumnName()}" data-field-name="{$FIELD_NAME}"
                                            {if in_array($FIELD_MODEL->getCustomViewColumnName(), $BARCODE_SELECTED_FIELDS)}
                                                selected
                                            {/if}
                                            >{vtranslate($FIELD_MODEL->get('label'), $BARCODE_SOURCE_MODULE)}
                                    </option>
                                {/foreach}
                            </optgroup>
                          {/foreach}
                          {*Required to include event fields for columns in calendar module advanced filter*}
                          {foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$EVENT_RECORD_STRUCTURE}
                            <optgroup label='{vtranslate($BLOCK_LABEL, 'Events')}'>
                                {foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS}
                                    <option value="{$FIELD_MODEL->getCustomViewColumnName()}" data-field-name="{$FIELD_NAME}"
                                            {if in_array($FIELD_MODEL->getCustomViewColumnName(), $BARCODE_SELECTED_FIELDS)}
                                                selected
                                            {/if}
                                            >{vtranslate($FIELD_MODEL->get('label'), $BARCODE_SOURCE_MODULE)}
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
              </div>
          </div>
   
      </div>
     
</div>


<div class="container-fluid ctblocks" id="assettracking_block">
    <div class="widget_header row-fluid">
        <h3>{vtranslate('Asset-tracking Field Configuration', 'CTMobileSettings')}</h3>
    </div>
    <hr>
      <div class="clearfix"></div>
      <div class="summaryWidgetContainer" id="asset_fields_settings">

          <div class="row-fluid">
              <div style="margin-top:15px;">
                {vtranslate('Setup field to track asset from asset module, This will help to scan asset against field specified in asset tracking field',$MODULE)}
              </div>
              <div class="select-search" style="margin-top:15px;">
                  <select class="inputElement select2 {if $OCCUPY_COMPLETE_WIDTH} row {/if}" id="asset_module" name="asset_module" style="width: 300px;">
                      <option value="">{vtranslate('LBL_SELECT_MODULE', 'CTMobileSettings')}</option>
                      {foreach from=$ASSET_ALL_MODULE item=MODULE}
                          <option value="{$MODULE}">{vtranslate($MODULE, $MODULE)}</option>
                      {/foreach}
                  </select>
              </div>
          </div>
          <br/>
          <div id="AssetSelectedFields">

          </div>
          <br/>
   
      </div>

      <div class="container-fluid" id="listAssetTracking">
            <table id="example2" class='table table-bordered table-striped'>
                <thead>
                    <tr><th> {vtranslate('Module Name','CTMobileSettings')} </th><th> {vtranslate('Field Name','CTMobileSettings')} </th><th>  </th></tr>
                </thead>
                <tbody>
                </tbody>
            </table>
      </div>
     
</div>

<div class="container-fluid ctblocks" id="signature_block">
    <div class="widget_header row-fluid">
        <h3>{vtranslate('Signature/Picture/Document Fields Configuration', 'CTMobileSettings')}</h3>
    </div>
    <hr>
      <div class="clearfix"></div>
      <div class="summaryWidgetContainer" id="signature_fields_settings">

          <div class="row-fluid">
              <div style="margin-top:15px;">
                 {vtranslate('Note : CRMtiger Apps only consider filed with type "TextArea" as Signature/Picture/Document type field. You should convert your existing fields to Signature/Picture/Document field or add field from Field Settings in CRM as "Text Area" field and convert it to mentioned field type.',$MODULE)}
              </div>
              <div class="select-search" style="margin-top:15px;">
                  <select class="inputElement select2 {if $OCCUPY_COMPLETE_WIDTH} row {/if}" id="signature_module" name="signature_module" style="width: 300px;">
                      <option value="">{vtranslate('LBL_SELECT_MODULE', 'CTMobileSettings')}</option>
                      {foreach from=$SIGN_ALL_MODULE item=MODULE}
                          <option value="{$MODULE}">{vtranslate($MODULE, $MODULE)}</option>
                      {/foreach}
                  </select>
              </div>
          </div>
          <br/>
          <div id="SignatureSelectedFields">

          </div>
          <br/>
                                 
                              
              
              
      </div>
     
      <div class="container-fluid" id="listRoute">
            <table id="example1" class='table table-bordered table-striped'>
                <thead>
                    <tr><th> {vtranslate('Module Name','CTMobileSettings')} </th><th> {vtranslate('Field Name','CTMobileSettings')} </th><th> {vtranslate('Document Type','CTMobileSettings')} </th><th>  </th></tr>
                </thead>
                <tbody>
                </tbody>
            </table>
       </div>
</div>


<div class="container-fluid ctblocks" id="displayfields_block">
    <div class="widget_header row-fluid">
        <h3>{vtranslate('Display Fields Configuration', 'CTMobileSettings')}</h3>
    </div>
    <hr>
      <div class="clearfix"></div>
      <div class="summaryWidgetContainer" id="display_fields_settings">

          <div class="row-fluid">
              <div style="margin-top:15px;">
                 {vtranslate('Set default display field for each user which will display in list screen of any module. you can select maximum 3 fields for each module.',$MODULE)}
              </div>
              <div class="select-search" style="margin-top:15px;">
                  <select class="inputElement select2 {if $OCCUPY_COMPLETE_WIDTH} row {/if}" id="display_field_module" name="display_field_module" style="width: 300px;">
                      <option value="">{vtranslate('LBL_SELECT_MODULE', 'CTMobileSettings')}</option>
                      {foreach from=$DISPLAY_ALL_MODULE item=MODULE}
                          <option value="{$MODULE}">{vtranslate($MODULE, $MODULE)}</option>
                      {/foreach}
                  </select>
              </div>

             
              <div class="select-search" style="margin-top:15px;">
                    <select class="select2" id="userid" name="userid" data-placeholder="Select Users" style="width: 300px;margin-bottom: 25px;">
                        {foreach key=FIELD_NAME item=FIELD_MODEL from=$DISPLAY_USER_MODEL}
                            <option value="{$FIELD_MODEL['userid']}" data-field-name="{$FIELD_MODEL['username']}"
                                     {if in_array($FIELD_MODEL['userid'], $ROUTE_USERS)}
                                       selected
                                     {/if}
                                    >{$FIELD_MODEL['username']}
                            </option>
                        {/foreach}
                    </select>
              </div>
            
          </div>
          <br/>
          <div id="displaySelectedFields">

          </div>
          <br/>
                                 
                              
              
              
      </div>
     
      <div class="container-fluid" id="listdisplayfields">
            <table id="example3" class='table table-bordered table-striped'>
                <thead>
                    <tr><th> {vtranslate('Module Name','CTMobileSettings')} </th><th> {vtranslate('User Name','CTMobileSettings')} </th><th> {vtranslate('Field Name','CTMobileSettings')} </th><th> {vtranslate('Field Type','CTMobileSettings')} </th><th>  </th></tr>
                </thead>
                <tbody>
                </tbody>
            </table>
       </div>
</div>


</div>

