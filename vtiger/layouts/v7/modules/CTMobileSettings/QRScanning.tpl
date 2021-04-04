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
<div class="container-fluid">
    <div class="widget_header row-fluid">
        <h3>{vtranslate('CTMobileSettings', 'CTMobileSettings')}</h3>
    </div>
    <hr>
      <div class="clearfix"></div>
      <div class="summaryWidgetContainer" id="qr_scanning_settings">
       <button type="button" class="btn btn-primary pull-right" id="geocodingReport" data-url='?module=CTMobileSettings&parent=Settings&view=GeocodingReport'>{vtranslate('LBL_VIEW_REPORT','CTMobileSettings')}</button>
       
         <div class="summaryWidgetContainer" id="qr_scanning_settings">
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
                        <div class="select-search" style="margin-left:100px;margin-top:15px;">
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
                </div>
            </div>
        </div>
  </div>
</div>
                            

