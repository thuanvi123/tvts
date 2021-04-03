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
        <h3>{vtranslate('MODULE_LBL', 'CTMobileSettings')}</h3>
    </div>
    <hr>
      <div class="clearfix"></div>
      <div class="summaryWidgetContainer" id="global_search_settings">
       <button type="button" class="btn btn-primary pull-right" id="geocodingReport" data-url='?module=CTMobileSettings&parent=Settings&view=GeocodingReport'>{vtranslate('LBL_VIEW_REPORT','CTMobileSettings')}</button>
       
          <ul class="nav nav-tabs massEditTabs">
             <li class="active">
                  <a href="#openMap" data-toggle="tab" >
                      <strong>
                          {vtranslate('Open Street Map', 'CTMobileSettings')}
                      </strong>
                  </a>
              </li>
              <li >
                  <a href="#GoogleMap" data-toggle="tab">
                      <strong>
                          {vtranslate('Google Map', 'CTMobileSettings')}
                      </strong>
                  </a>
              </li>   
          </ul>
         
          <div class="tab-content massEditContent">
              <div class="tab-pane active" id="openMap">
                       <div class="summaryWidgetContainer" id="global_search_settings">
                          <ul class="nav nav-tabs massEditTabs">
                             <li class="active">
                                  <a href="#ofields" data-toggle="tab">
                                      <strong>
                                          {vtranslate('Set Address Fields', 'CTMobileSettings')}
                                      </strong>
                                  </a>
                              </li>    
                          </ul>
                          <div class="tab-content massEditContent">
                              <div class="tab-pane active" id="ofields">
                          
                                  <div class="row-fluid">
                                      <div style="margin-top:15px;">
                                          This feature find latitude and longitude of your customer's record to properly display customer on map inside CRMTiger Mobile apps.<br/>
                                          Please use "Sync Now" to sync all records.
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
                                   <b>{vtranslate('In following notes of Fields add following text.',{$MODULE})}</b>
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
                          </div>
                      </div>
              </div>
              
              <div class="tab-pane" id="GoogleMap" style="">
                         <div class="summaryWidgetContainer" id="global_search_settings">
                            <ul class="nav nav-tabs massEditTabs">
                               <li class="active">
                                    <a href="#apikeys" data-toggle="tab" >
                                        <strong>
                                            {vtranslate('Google API Key', 'CTMobileSettings')}
                                        </strong>
                                    </a>
                                </li>
                                <li >
                                    <a href="#fields" data-toggle="tab">
                                        <strong>
                                            {vtranslate('Set Address Fields', 'CTMobileSettings')}
                                        </strong>
                                    </a>
                                </li>    
                            </ul>
                            <div class="tab-content massEditContent">
                                <div class="tab-pane" id="fields">
                            
                                    <div class="row-fluid">
                                        <div style="margin-top:15px;">
                                          This feature find latitude and longitude of your customer's record to properly display customer on map inside CRMTiger Mobile apps.<br/>
                                          Please use "Sync Now" to sync all records.
                                        </div>
                                        <div class="select-search" style="margin-top:15px;">
                                            <select class="inputElement select2 {if $OCCUPY_COMPLETE_WIDTH} row {/if}" id="search_module2" name="search_module2" style="width: 300px;">
                                                <option value="">{vtranslate('LBL_SELECT_MODULE', 'CTMobileSettings')}</option>
                                                {foreach from=$ALL_MODULE item=MODULE}
                                                    <option value="{$MODULE}">{vtranslate($MODULE, $MODULE)}</option>
                                                {/foreach}
                                            </select>
                                        </div>
                                    </div>
                                    <br/>
                                    <div id="selectedFields2">

                                    </div>
                                    <br/>
                                    <b>{vtranslate('In following notes of Fields add following text.',{$MODULE})}</b>
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
                                
                                <div class="tab-pane active" id="apikeys" style="">
                                     <div class="container-fluid" id="EditConfigEditor">
                                        <div class="widget_header row-fluid">
                                           <div class="span6"><h3>{vtranslate('Google Map Api Key',{$MODULE})}</h3> </div>
                                        </div>

                                           <div class="contents">
                                              <table class="table table-bordered table-condensed themeTableColor">
                                                <tbody>
                                                    <tr>
                                                      <th colspan="2">
                                                        {vtranslate('Google Maps Key Configuration',{$MODULE})}
                                                      </th>
                                                    </tr>
                                                    <tr class="fieldLabel medium">
                                                      <td width="30%" class="{$WIDTHTYPE}">
                                                        <label class="muted pull-right marginRight10px"> 
                                                        <span class="redColor">*</span>{vtranslate('Enter Google API Key',{$MODULE})}</label>
                                                      </td>
                                                      <td  class="{$WIDTHTYPE} fieldValue medium">
                                                        <input class="inputElement" type="text" name="api_Key" id="api_Key" value="{$API_KEY}"/>
                                                        <a style="color:blue;margin-left:20px;" href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_new">{vtranslate('Get an API key',{$MODULE})}</a>
                                                      </td>
                                                    </tr>
                                                    
                                                </tbody>
                                              </table>
                                               <br/>
                                               <p style="color:red;"><b>{vtranslate('Only for Premium Plan User',{$MODULE})}</b></p>
                                               <br/>
                                               <b>{vtranslate('In Notes of google API add following text.',{$MODULE})}</b>
                                               <br/>
                                               <ul>
                                                <li>{vtranslate('To deliver these features, CRMTiger converts street address in Contact, Organization, and Lead records to lat/long value (ie. Geocode).',{$MODULE})}</li>
                                                <li>{vtranslate('By default, CRMTiger user their Own API Key to to get lat/long value. If you would like to use the Your own Google Maps API Key, you can configure on this page.',{$MODULE})}</li>
                                                <li>{vtranslate('Daily limits apply for Geocoding (10,000 per day with OSM, 2500 per day with Google free plan, over 100,000 per day with Google paid plan).',{$MODULE})}</li>
                                                <li>{vtranslate('Addresses should be valid to get a lat/long value',{$MODULE})}
                                                    <ul>
                                                    <li>{vtranslate('Address should not have non UTF-8 characters',{$MODULE})}</li>
                                                    <li>{vtranslate('Address should have a known location.',{$MODULE})}</li>
                                                    </ul>
                                                    </li>
                                              </ul>
                                              <div class="row-fluid">
                                                <div class="pull-right">
                                                  <button type="button" class="btn btn-success saveButton" name="save_api_Key" id="save_api_Key"><strong>{vtranslate('Save',{$MODULE})}</strong></button>
                                                  <a class="cancelLink" type="reset" onclick="javascript:window.history.back();">{vtranslate('Cancel',$MODULE)}</a>
                                                </div>
                                              </div>
                                        </div>
                                     </div>
                                </div>
                            </div>
                        </div>
              </div>
          </div>
      </div>
     
</div>

