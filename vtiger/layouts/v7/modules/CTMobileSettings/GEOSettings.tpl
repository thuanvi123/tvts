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
        <button type="button" class="btn btn-info pull-right" style="background:#287DF2 !important;" onclick='window.location.href="{CTMobileSettings_Module_Model::$CTMOBILE_DETAILVIEW_URL}"'>{vtranslate('Go To CRMTiger Settings',$MODULE)}</button>
        <h3>{vtranslate('GEO_LOCATION_CONF',$MODULE)}</h3>
    </div>
    <hr>
      <div><span>{vtranslate('GEO_LOCATION_SECTION_MESSAGE', $MODULE)}</span></div>
      <div><span>{vtranslate('GEO_LOCATION_SECTION_MESSAGE2' ,$MODULE)}</span></div>
      <br/>
      <div class="clearfix"></div>
      <div class="row-fluid">
         <table class="table" frame="box" width="100%">
             <thead>
               <tr>
                  <th>{vtranslate('MAP_TYPE' ,$MODULE)}</th>
                  <th>{vtranslate('LBL_AVAILABLE_ON' ,$MODULE)}</th>
                  <th>{vtranslate('Paid/Free' ,$MODULE)}</th>
                  <th>{vtranslate('Active' ,$MODULE)}</th>
                  <th>{vtranslate('Action' ,$MODULE)}</th>
               </tr>
             </thead>
             <tbody>
               <tr>
                 <td>{vtranslate('Open Street Map' ,'CTMobileSettings')}</td>
                 <td>{vtranslate('Premium' ,$MODULE)}</td>
                 <td>{vtranslate('FREE_TYPE' ,$MODULE)}</td>
                 <td>
                   {if $API_KEY neq ''}
                      {vtranslate('LBL_NO' ,$MODULE)}
                   {else}
                      {vtranslate('LBL_YES' ,$MODULE)}
                   {/if}
                 </td>
                 <td><button class="btn btn-info" id="ctopenstreetmap" style="background:#287DF2 !important;" data-url="{CTMobileSettings_Module_Model::$CTMOBILE_OPENSTREETMAP_EDIT_URL}"><span title="{vtranslate('LBL_EDIT',$MODULE)}"><i class="fa fa-edit"></i></span></button></span></td>
               </tr>
               <tr>
                 <td>{vtranslate('Google Map' ,$MODULE)}</td>
                 <td>{vtranslate('Premium' ,$MODULE)}</td>
                 <td>{vtranslate('PAID_TYPE' ,$MODULE)}
                    <a style="color:blue;margin-left:20px;" href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_new">{vtranslate('Get API key',$MODULE)}</a>
                 </td>
                 <td>
                   {if $API_KEY neq ''}
                      {vtranslate('LBL_YES' ,$MODULE)}
                   {else}
                      {vtranslate('LBL_NO' ,$MODULE)}
                   {/if}
                 </td>
                 <td><button class="btn btn-info" id="ctgooglemap" style="background:#287DF2 !important;" data-url="{CTMobileSettings_Module_Model::$CTMOBILE_GOOGLEMAP_EDIT_URL}"><span title="{vtranslate('LBL_EDIT',$MODULE)}"><i class="fa fa-edit"></i></span></button></td>
               </tr>
             </tbody>
         </table>
      </div>
        
</div>

