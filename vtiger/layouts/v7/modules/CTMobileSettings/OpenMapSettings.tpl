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

<link rel="stylesheet" type="text/css" href="layouts/v7/modules/CTMobileSettings/dataTables.bootstrap.min.css" />
<script type="text/javascript" src="layouts/v7/modules/CTMobileSettings/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="layouts/v7/modules/CTMobileSettings/dataTables.bootstrap.min.js"></script>

<div class="container-fluid">
    <div class="widget_header row-fluid">
        <button type="button" class="btn btn-info pull-right" style="background:#287DF2 !important;" onclick='window.location.href="{CTMobileSettings_Module_Model::$CTMOBILE_DETAILVIEW_URL}"'>{vtranslate('Go To CRMTiger Settings',$MODULE)}</button>
        <h3>{vtranslate('MODULE_LBL', 'CTMobileSettings')}</h3>
    </div>
    <hr>
      <div class="clearfix"></div>
      <div class="summaryWidgetContainer" id="global_search_settings">
       <button type="button" class="btn btn-primary pull-right" id="geocodingReport" data-url='?module=CTMobileSettings&parent=Settings&view=GeocodingReport'>{vtranslate('LBL_VIEW_REPORT','CTMobileSettings')}</button>
       <br/>
       <div><h4>{vtranslate('Open Street Map','CTMobileSettings')}</h4></div>

          <div class="tab-content massEditContent">
              
              <div class="summaryWidgetContainer" id="global_search_settings">
                <ul class="nav nav-tabs massEditTabs">
                   <li class="active">
                        <a href="#ofields" data-toggle="tab">
                            <strong>
                                {vtranslate('Set Address Fields', 'CTMobileSettings')}
                            </strong>
                        </a>
                    </li> 
                    <li class="">
                      <a href="#autofields" data-toggle="tab">
                      <strong>
                      {vtranslate('Set Auto Address Finder Fields', 'CTMobileSettings')}
                      </strong>
                      </a>
                    </li>   
                </ul>
                <div class="tab-content massEditContent">
                    <div class="tab-pane active" id="ofields">
                
                        <div class="row-fluid">
                            <div style="margin-top:15px;">
                                {vtranslate('This feature find latitude and longitude of your customer\'s record to properly display customer on map inside CRMTiger Mobile apps.',{$MODULE})}<br/>
                                {vtranslate('Please use "Sync Now" to sync all records.',{$MODULE})}

                                 <ul>
                                     <li>{vtranslate('To Dispaly proper location from address in google please define key fields which will cover your complete address',{$MODULE})}</li>
                                     <br>
                                     {vtranslate('Example',{$MODULE})} :
                                     <br>
                                     {vtranslate('Address',{$MODULE})} : 268 Elizabeth St
                                     <br>
                                     {vtranslate('City',{$MODULE})} : New York
                                     <br>
                                     {vtranslate('State',{$MODULE})} : NY
                                     <br>
                                     {vtranslate('ZipCode',{$MODULE})} : 10012
                                     <br>
                                     {vtranslate('Country',{$MODULE})} : USA
                                  </ul>
                            </div>
                            <div class="select-search" style="margin-top:15px;">
                                <select class="inputElement select2 {if $OCCUPY_COMPLETE_WIDTH} row {/if}" id="search_module" name="search_module" style="width: 300px;">
                                    <option value="">{vtranslate('LBL_SELECT_MODULE', 'CTMobileSettings')}</option>
                                    {foreach from=$ALL_MODULE item=MODULE}
                                        <option value="{$MODULE}">{vtranslate($MODULE, $MODULE)}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <br/>
                        <div id="selectedFields">

                        </div>
                        <br/>
                    </div>


                   <div class="tab-pane" id="autofields">
                      <div class="row-fluid">
                         <div style="margin-top:15px;">
                            {vtranslate('This feature automatically find address from the few characters you will type on selected field. On selection of the address it will automatically copy Street, City, State, Country or Zip to respected field as per order you\'ve set.',{$MODULE})}
                            <ul>
                               <li>{vtranslate('To Dispaly proper location from address in google please define key fields which will cover your complete address',{$MODULE})}</li>
                               <br>
                               {vtranslate('Example',{$MODULE})} :
                               <br>
                               {vtranslate('Address',{$MODULE})} : 268 Elizabeth St
                               <br>
                               {vtranslate('City',{$MODULE})} : New York
                               <br>
                               {vtranslate('State',{$MODULE})} : NY
                               <br>
                               {vtranslate('ZipCode',{$MODULE})} : 10012
                               <br>
                               {vtranslate('Country',{$MODULE})} : USA
                            </ul>
                         </div>
                         <div class="select-search" style="margin-top:15px;">
                            <select class="inputElement select2 {if $OCCUPY_COMPLETE_WIDTH} row {/if}" id="autosearch_module" name="autosearch_module" style="width: 300px;">
                               <option value="">{vtranslate('LBL_SELECT_MODULE', 'CTMobileSettings')}</option>
                               {foreach from=$ALL_MODULE item=MODULE}
                               <option value="{$MODULE}">{vtranslate($MODULE, $MODULE)}</option>
                               {/foreach}
                            </select>
                         </div>
                      </div>
                      <br/>
                      <div id="autoSelectedFields">
                      </div>
                      <br/>
                      <div class="container-fluid" id="listautoSelectedFields">
                        <table id="example3" class='table table-bordered table-striped'>
                            <thead>
                                <tr><th> {vtranslate('Module Name','CTMobileSettings')} </th>
                                <th> {vtranslate('Auto Address Finder Field','CTMobileSettings')} </th>
                                <th> {vtranslate('Street','CTMobileSettings')} </th>
                                <th> {vtranslate('City','CTMobileSettings')} </th>
                                <th> {vtranslate('State','CTMobileSettings')} </th>
                                <th> {vtranslate('PostalCode','CTMobileSettings')} </th>
                                <th> {vtranslate('Country','CTMobileSettings')} </th>
                                <th>  </th></tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                      </div>
                   </div>

                </div>
              </div>   
            </div>
      </div>
</div>

