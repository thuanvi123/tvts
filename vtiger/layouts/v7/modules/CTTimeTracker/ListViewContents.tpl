{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}
{* modules/Vtiger/views/List.php *}

{literal}
<style>
.listview-table-norecords .table-actions, .listview-table .table-actions {
    width: 150px;
    font-size: 15px;
    color: #555;
    margin-left: 3px;
}

.ctlefthideessentials-toggle,.ctleftshowessentials-toggle,.ctrighthideessentials-toggle,.ctrightshowessentials-toggle{
 	background-color: white;
    font-weight: bold;
    padding: 5px 2px !important;
    /*position: absolute;*/
    top: 89px;
    left: -1px;
    cursor: pointer;
    width: 14px;
    border: 1px solid #DDDDDD;
    /*border-left: 0;*/
    display: inline-block;
}

div#example1_wrapper {
    position: relative;
}
div#example1_wrapper  .ctlefthideessentials-toggle{
	position: absolute;
    top: 40px !important;
    right: 1px;
}
div#example1_wrapper {
    position: relative;
}
.ctlefthideessentials-toggle{
	position: absolute;
    top: 40px !important;
    left :auto !important;
    right: 1px !important;;
}
div#map {
    position: relative !important;
}
.ctrighthideessentials-toggle{
	position: absolute;
    top: 40px !important;
    left: 1px !important;
}

</style>
{/literal}

{if !$smarty.get._pjax}
<script async defer
	src="https://maps.googleapis.com/maps/api/js?key={$API_KEY}&callback=initMap">
	</script>
{/if}

<script type="text/javascript">
	
	jQuery(document).ready(function(){
		jQuery('#table-content').css('height','600px');

		jQuery("#listViewContent").on("click",".ctlefthideessentials-toggle",function(){
			if(jQuery('#mapdiv').css('display') == 'none') {
				jQuery('#listdiv').show();
				jQuery('#mapdiv').show();
				jQuery('.ctrightshowessentials-toggle').removeClass('ctrightshowessentials-toggle').addClass('ctrighthideessentials-toggle');
				jQuery('.ctrighthideessentials-toggle').find('span').removeClass('fa-chevron-left').addClass('fa-chevron-right');
				jQuery('#mapdiv').removeClass('col-lg-12 col-md-12 col-sm-12').addClass('col-lg-6 col-md-6 col-sm-6');
				jQuery('#listdiv').removeClass('col-lg-12 col-md-12 col-sm-12').addClass('col-lg-6 col-md-6 col-sm-6');
			}else{
				jQuery(this).removeClass('ctlefthideessentials-toggle').addClass('ctleftshowessentials-toggle');
				jQuery(this).find('span').removeClass('fa-chevron-left').addClass('fa-chevron-right');
				jQuery('#listdiv').hide();
				jQuery('#mapdiv').removeClass('col-lg-6 col-md-6 col-sm-6').addClass('col-lg-12 col-md-12 col-sm-12');
			}
		});


		jQuery("#listViewContent").on("click",".ctleftshowessentials-toggle", function(){
			jQuery(this).removeClass('ctleftshowessentials-toggle').addClass('ctlefthideessentials-toggle');
			jQuery(this).find('span').removeClass('fa-chevron-right').addClass('fa-chevron-left');
			jQuery('#listdiv').show();
			jQuery('#mapdiv').removeClass('col-lg-12 col-md-12 col-sm-12').addClass('col-lg-6 col-md-6 col-sm-6');
		});

		jQuery("#listViewContent").on("click",".ctrighthideessentials-toggle",function(){
			if(jQuery('#listdiv').css('display') == 'none') {
				jQuery('#listdiv').show();
				jQuery('#mapdiv').show();
				jQuery('.ctleftshowessentials-toggle').removeClass('ctleftshowessentials-toggle').addClass('ctlefthideessentials-toggle');
				jQuery('.ctlefthideessentials-toggle').find('span').removeClass('fa-chevron-right').addClass('fa-chevron-left');
				jQuery('#mapdiv').removeClass('col-lg-12 col-md-12 col-sm-12').addClass('col-lg-6 col-md-6 col-sm-6');
				jQuery('#listdiv').removeClass('col-lg-12 col-md-12 col-sm-12').addClass('col-lg-6 col-md-6 col-sm-6');
			}else{
				jQuery(this).removeClass('ctrighthideessentials-toggle').addClass('ctrightshowessentials-toggle');
				jQuery(this).find('span').removeClass('fa-chevron-right').addClass('fa-chevron-left');
				jQuery('#mapdiv').hide();
				jQuery('#listdiv').removeClass('col-lg-6 col-md-6 col-sm-6').addClass('col-lg-12 col-md-12 col-sm-12');
			}
		});

		jQuery("#listViewContent").on("click",".ctrightshowessentials-toggle", function(){
			jQuery(this).removeClass('ctrightshowessentials-toggle').addClass('ctrighthideessentials-toggle');
			jQuery(this).find('span').removeClass('fa-chevron-left').addClass('fa-chevron-right');
			jQuery('#mapdiv').show();
			jQuery('#listdiv').removeClass('col-lg-12 col-md-12 col-sm-12').addClass('col-lg-6 col-md-6 col-sm-6');
		});
	});
</script>

{if $API_KEY neq ''}
		{literal}
			<script type='text/javascript'>
			var flightPath;
			var map;
			var markers = [];
			var flightPlanCoordinates = [];
			function initMap() {
				map = new google.maps.Map(document.getElementById('map'), {
				  zoom: 3,
				  center: {lat: 0, lng: -180},
				  mapTypeId: 'terrain'
				});

				var flightPlanCoordinates = [
				  {lat: 37.772, lng: -122.214},
				  {lat: 21.291, lng: -157.821},
				  {lat: -18.142, lng: 178.431},
				  {lat: -27.467, lng: 153.027}
				];
				flightPath = new google.maps.Polyline({
				  path: flightPlanCoordinates,
				  geodesic: true,
				  strokeColor: '#FF0000',
				  strokeOpacity: 1.0,
				  strokeWeight: 2
				});
				flightPath.setMap(null);
			}


			jQuery(document).ready(function () {
				loadMapMarker();
				function loadMapMarker(){
					var polilinedata = JSON.parse($("input[name=poliline_data]").val());
					var progressIndicatorElement = jQuery.progressIndicator({
		              'position' : 'html',
		              'blockInfo' : {
		               'enabled' : true
		              }
		            });
					
					progressIndicatorElement.progressIndicator({'mode' : 'hide'});
					var result2 = polilinedata;
					if(result2.length != 0){
						var flightPlanCoordinates2 = [];
						jQuery.each(result2, function(index, item) {
							flightPlanCoordinates2.push([item.label,item.lat,item.lng]);
						});
						initMap();
						map.setCenter({lat:flightPlanCoordinates2[0][1], lng:flightPlanCoordinates2[0][2]});
						map.setZoom(10);
						
						var infowindow = new google.maps.InfoWindow();

						var marker, i;

						for (i = 0; i < flightPlanCoordinates2.length; i++) {  
						  marker = new google.maps.Marker({
							position: new google.maps.LatLng(flightPlanCoordinates2[i][1], flightPlanCoordinates2[i][2]),
							map: map,
						  });
						
						  google.maps.event.addListener(marker, 'mouseover', (function(marker, i) {
							return function() {
							  infowindow.setContent(flightPlanCoordinates2[i][0]);
							  infowindow.open(map, marker);
							}
						  })(marker, i));
						  google.maps.event.addListener(marker, 'click', (function(marker, i) {
							return function() {
								 if (marker.getAnimation() !== null) {
								  marker.setAnimation(null);
								} else {
								  marker.setAnimation(google.maps.Animation.BOUNCE);
								}
							}
						  })(marker, i));
						}
					}else{
						initMap();
					}
				}
				jQuery(".clickDrawPoliline").on("click",".drawPoliline",function(e){
					
					e.preventDefault(); 
					e.stopPropagation();
					initMap();
					var flightPlanCoordinates = [];	
					flightPath = new google.maps.Polyline({
					  	path: flightPlanCoordinates,
					  	geodesic: true,
					  	strokeColor: '#FF0000',
					  	strokeOpacity: 1.0,
					  	strokeWeight: 2
					});
					flightPath.setMap(null);

					var polilinedata = JSON.parse($("input[name=poliline_data]").val());
					var element = jQuery(e.currentTarget);
					var recordid = element.closest('tr').attr('data-id');
					jQuery.each(polilinedata, function(index, item) {
						if(recordid == item.recordId){
							flightPlanCoordinates.push(item);
						}
					});
					
					map.setCenter({lat:flightPlanCoordinates[0].lat, lng:flightPlanCoordinates[0].lng});
					map.setZoom(20);
					var  infowindow = new google.maps.InfoWindow({
					    content: flightPlanCoordinates[0].label,
					});

					var marker = new google.maps.Marker({
    				position: {lat:flightPlanCoordinates[0].lat, lng:flightPlanCoordinates[0].lng},
					    map,
					});
					infowindow.open(map, marker);

					var  infowindow = new google.maps.InfoWindow({
					    content: flightPlanCoordinates[1].label,
					});
					var marker = new google.maps.Marker({
    				position: {lat:flightPlanCoordinates[1].lat, lng:flightPlanCoordinates[1].lng},
					    map,
					});
					infowindow.open(map, marker);
					flightPath = new google.maps.Polyline({
						path: flightPlanCoordinates,
						geodesic: true,
						strokeColor: '#FF0000',
						strokeOpacity: 1.0,
						strokeWeight: 2
					});
					flightPath.setMap(map);
				});
			});
			</script>
		{/literal}
{else}
	<script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js"></script>
	<link href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" rel="stylesheet"/>
	{literal}
		<script type='text/javascript'>
				
					var markers = [];
					var myMap = '';
					var flightPlanCoordinates = [];
					function initOpenMap() {
					 	var element = document.getElementById('map');
						//element.style = 'height:300px;';
						myMap = L.map(element);

						// Add OSM tile leayer to the Leaflet map.
						L.tileLayer('https://{s}.tile.osm.org/{z}/{x}/{y}.png', {
						    attribution: '&copy; <a href="https://osm.org/copyright">OpenStreetMap</a> contributors'
						}).addTo(myMap);
					}
					jQuery(document).ready(function () { 
						var polilinedata = JSON.parse($("input[name=poliline_data]").val());
						var progressIndicatorElement = jQuery.progressIndicator({
				              'position' : 'html',
				              'blockInfo' : {
				               'enabled' : true
				              }
				        });
						progressIndicatorElement.progressIndicator({'mode' : 'hide'});
						var result2 = polilinedata;
						
						initOpenMap();
						if(result2.length != 0){
							
							var target = L.latLng(result2[0].lat,result2[0].lng);
							// Set map's center to target with zoom 14.
							myMap.setView(target, 20);

							var flightPlanCoordinates = [];
							jQuery.each(result2, function(index, item) {
								flightPlanCoordinates.push([item.label,item.lat,item.lng]);
								
							});
							var marker , i;
							for (i = 0; i < flightPlanCoordinates.length; i++) {
		                    	marker = new L.marker([flightPlanCoordinates[i][1], flightPlanCoordinates[i][2]])
							    .bindPopup(flightPlanCoordinates[i][0])
							    .addTo(myMap);
							    marker.on('mouseover', function(event){
								  marker.openPopup();
								});
								markers[i] = marker;
		                    }
						}else{
							initOpenMap();
						}

						//listviewcontent tr click event
						jQuery(".clickDrawPoliline").on("click",".drawPoliline",function(e){
					
							e.preventDefault(); 
							e.stopPropagation();
							
							L.tileLayer('https://{s}.tile.osm.org/{z}/{x}/{y}.png', {
						    attribution: '&copy; <a href="https://osm.org/copyright">OpenStreetMap</a> contributors'
							}).addTo(myMap);

							var polilinedata = JSON.parse($("input[name=poliline_data]").val());
							var element = jQuery(e.currentTarget);
							var recordid = element.closest('tr').attr('data-id');
							var flightPlanCoordinates = [];
							jQuery.each(polilinedata, function(index, item) {
								if(recordid == item.recordId){
									flightPlanCoordinates.push(item);
								}
							});
							var target = L.latLng(flightPlanCoordinates[0].lat,flightPlanCoordinates[0].lng);

							// Set map's center to target with zoom 14.
							myMap.setView(target, 20);

							var marker = new L.marker([flightPlanCoordinates[0].lat, flightPlanCoordinates[0].lng])
							    .bindPopup(flightPlanCoordinates[0].label)
							    .addTo(myMap);
							marker.openPopup(); 

							var marker = new L.marker([flightPlanCoordinates[1].lat, flightPlanCoordinates[1].lng])
							    .bindPopup(flightPlanCoordinates[1].label)
							    .addTo(myMap);
							marker.openPopup(); 

							var pointList = flightPlanCoordinates;

							var firstpolyline = new L.Polyline(pointList, {
							    color: 'red',
							    weight: 3,
							    opacity: 0.5,
							    smoothFactor: 1
							});
							firstpolyline.addTo(myMap);
						});
					});
				</script>
	{/literal}
{/if}

{* START YOUR IMPLEMENTATION FROM BELOW. Use {debug} for information *}
{include file="PicklistColorMap.tpl"|vtemplate_path:$MODULE}
<div class="col-sm-12 col-xs-12">
<div class="col-sm-6 col-xs-6" id="listdiv">
	{if $MODULE neq 'EmailTemplates' && $SEARCH_MODE_RESULTS neq true}
		{assign var=LEFTPANELHIDE value=$CURRENT_USER_MODEL->get('leftpanelhide')}
		<div class="essentials-toggle" title="{vtranslate('LBL_LEFT_PANEL_SHOW_HIDE', 'Vtiger')}">
			<span class="essentials-toggle-marker fa {if $LEFTPANELHIDE eq '1'}fa-chevron-right{else}fa-chevron-left{/if} cursorPointer"></span>
		</div>
	{/if}
	<input type="hidden" name="view" id="view" value="{$VIEW}" />
	<input type="hidden" name="cvid" value="{$VIEWID}" />
	<input type="hidden" name="pageStartRange" id="pageStartRange" value="{$PAGING_MODEL->getRecordStartRange()}" />
	<input type="hidden" name="pageEndRange" id="pageEndRange" value="{$PAGING_MODEL->getRecordEndRange()}" />
	<input type="hidden" name="previousPageExist" id="previousPageExist" value="{$PAGING_MODEL->isPrevPageExists()}" />
	<input type="hidden" name="nextPageExist" id="nextPageExist" value="{$PAGING_MODEL->isNextPageExists()}" />
	<input type="hidden" name="alphabetSearchKey" id="alphabetSearchKey" value= "{$MODULE_MODEL->getAlphabetSearchField()}" />
	<input type="hidden" name="Operator" id="Operator" value="{$OPERATOR}" />
	<input type="hidden" name="totalCount" id="totalCount" value="{$LISTVIEW_COUNT}" />
	<input type='hidden' name="pageNumber" value="{$PAGE_NUMBER}" id='pageNumber'>
	<input type='hidden' name="pageLimit" value="{$PAGING_MODEL->getPageLimit()}" id='pageLimit'>
	<input type="hidden" name="noOfEntries" value="{$LISTVIEW_ENTRIES_COUNT}" id="noOfEntries">
	<input type="hidden" name="currentSearchParams" value="{Vtiger_Util_Helper::toSafeHTML(Zend_JSON::encode($SEARCH_DETAILS))}" id="currentSearchParams" />
	<input type="hidden" name="currentTagParams" value="{Vtiger_Util_Helper::toSafeHTML(Zend_JSON::encode($TAG_DETAILS))}" id="currentTagParams" />
	<input type="hidden" name="noFilterCache" value="{$NO_SEARCH_PARAMS_CACHE}" id="noFilterCache" >
	<input type="hidden" name="orderBy" value="{$ORDER_BY}" id="orderBy">
	<input type="hidden" name="sortOrder" value="{$SORT_ORDER}" id="sortOrder">
	<input type="hidden" name="list_headers" value='{$LIST_HEADER_FIELDS}'/>
	<input type="hidden" name="tag" value="{$CURRENT_TAG}" />
	<input type="hidden" name="folder_id" value="{$FOLDER_ID}" />
	<input type="hidden" name="folder_value" value="{$FOLDER_VALUE}" />
	<input type="hidden" name="viewType" value="{$VIEWTYPE}" />
	<input type="hidden" name="app" id="appName" value="{$SELECTED_MENU_CATEGORY}">
	<input type="hidden" id="isExcelEditSupported" value="{if $MODULE_MODEL->isExcelEditAllowed()}yes{else}no{/if}" />
	{if !empty($PICKIST_DEPENDENCY_DATASOURCE)}
		<input type="hidden" name="picklistDependency" value='{Vtiger_Util_Helper::toSafeHTML($PICKIST_DEPENDENCY_DATASOURCE)}' />
	{/if}
	{if !$SEARCH_MODE_RESULTS}
		{include file="ListViewActions.tpl"|vtemplate_path:$MODULE}
	{/if}

	<input type="hidden" name="poliline_data" value='{$MAPPOLILINE}'/>

	<div id="table-content" class="table-container">
		<form name='list' id='listedit' action='' onsubmit="return false;">
			<table id="listview-table" class="table {if $LISTVIEW_ENTRIES_COUNT eq '0'}listview-table-norecords {/if} listview-table ">
				<thead>
					<tr class="listViewContentHeader">
						<th>
							{if !$SEARCH_MODE_RESULTS}
					<div class="table-actions">
						<div class="dropdown" style="float:left;">
							<span class="input dropdown-toggle" data-toggle="dropdown" title="{vtranslate('LBL_CLICK_HERE_TO_SELECT_ALL_RECORDS',$MODULE)}">
								<input class="listViewEntriesMainCheckBox" type="checkbox">
							</span>
						</div>
						{if $MODULE_MODEL->isFilterColumnEnabled()}
							<div id="listColumnFilterContainer">
								<div class="listColumnFilter {if $CURRENT_CV_MODEL and !($CURRENT_CV_MODEL->isCvEditable())}disabled{/if}"  
									 {if $CURRENT_CV_MODEL->isCvEditable()}
										 title="{vtranslate('LBL_CLICK_HERE_TO_MANAGE_LIST_COLUMNS',$MODULE)}"
									 {else}
										 {if $CURRENT_CV_MODEL->get('viewname') eq 'All' and !$CURRENT_USER_MODEL->isAdminUser()} 
											 title="{vtranslate('LBL_SHARED_LIST_NON_ADMIN_MESSAGE',$MODULE)}"
										 {elseif !$CURRENT_CV_MODEL->isMine()}
											 {assign var=CURRENT_CV_USER_ID value=$CURRENT_CV_MODEL->get('userid')}
											 {if !Vtiger_Functions::isUserExist($CURRENT_CV_USER_ID)}
												 {assign var=CURRENT_CV_USER_ID value=Users::getActiveAdminId()}
											 {/if}
											 title="{vtranslate('LBL_SHARED_LIST_OWNER_MESSAGE',$MODULE, getUserFullName($CURRENT_CV_USER_ID))}"
										 {/if}
									 {/if}
									 {if $MODULE eq 'Documents'}style="width: 10%;"{/if}
									 data-toggle="tooltip" data-placement="bottom" data-container="body">
									<i class="fa fa-th-large"></i>
								</div>
							</div>
						{/if}
					</div>
				{elseif $SEARCH_MODE_RESULTS}
					{vtranslate('LBL_ACTIONS',$MODULE)}
				{/if}
				</th>
				{foreach item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
					{if $SEARCH_MODE_RESULTS || ($LISTVIEW_HEADER->getFieldDataType() eq 'multipicklist')}
						{assign var=NO_SORTING value=1}
					{else}
						{assign var=NO_SORTING value=0}
					{/if}
					<th {if $COLUMN_NAME eq $LISTVIEW_HEADER->get('name')} nowrap="nowrap" {/if}>
						<a href="#" class="{if $NO_SORTING}noSorting{else}listViewContentHeaderValues{/if}" {if !$NO_SORTING}data-nextsortorderval="{if $COLUMN_NAME eq $LISTVIEW_HEADER->get('name')}{$NEXT_SORT_ORDER}{else}ASC{/if}" data-columnname="{$LISTVIEW_HEADER->get('name')}"{/if} data-field-id='{$LISTVIEW_HEADER->getId()}'>
							{if !$NO_SORTING}
								{if $COLUMN_NAME eq $LISTVIEW_HEADER->get('name')}
									<i class="fa fa-sort {$FASORT_IMAGE}"></i>
								{else}
									<i class="fa fa-sort customsort"></i>
								{/if}
							{/if}
							&nbsp;{vtranslate($LISTVIEW_HEADER->get('label'), $LISTVIEW_HEADER->getModuleName())}&nbsp;
						</a>
						{if $COLUMN_NAME eq $LISTVIEW_HEADER->get('name')}
							<a href="#" class="removeSorting"><i class="fa fa-remove"></i></a>
							{/if}
					</th>
				{/foreach}
				</tr>

				{if $MODULE_MODEL->isQuickSearchEnabled() && !$SEARCH_MODE_RESULTS}
					<tr class="searchRow">
						<th class="inline-search-btn">
					<div class="table-actions">
						<button class="btn btn-success btn-sm" data-trigger="listSearch">{vtranslate("LBL_SEARCH",$MODULE)}</button>
					</div>
					</th>
					{foreach item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
						<th>
							{assign var=FIELD_UI_TYPE_MODEL value=$LISTVIEW_HEADER->getUITypeModel()}
							{include file=vtemplate_path($FIELD_UI_TYPE_MODEL->getListSearchTemplateName(),$MODULE) FIELD_MODEL= $LISTVIEW_HEADER SEARCH_INFO=$SEARCH_DETAILS[$LISTVIEW_HEADER->getName()] USER_MODEL=$CURRENT_USER_MODEL}
							<input type="hidden" class="operatorValue" value="{$SEARCH_DETAILS[$LISTVIEW_HEADER->getName()]['comparator']}">
						</th>
					{/foreach}
					</tr>
				{/if}
				</thead>
				<tbody class="overflow-y">
					{foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=listview}
						{assign var=DATA_ID value=$LISTVIEW_ENTRY->getId()}
						{assign var=DATA_URL value=$LISTVIEW_ENTRY->getDetailViewUrl()}
						{if $SEARCH_MODE_RESULTS && $LISTVIEW_ENTRY->getModuleName() == "ModComments"}
							{assign var=RELATED_TO value=$LISTVIEW_ENTRY->get('related_to_model')}
							{assign var=DATA_ID value=$RELATED_TO->getId()}
							{assign var=DATA_URL value=$RELATED_TO->getDetailViewUrl()}
						{/if}
						<tr class="listViewEntries clickDrawPoliline" data-id='{$DATA_ID}' data-recordUrl='{$DATA_URL}&app={$SELECTED_MENU_CATEGORY}' id="{$MODULE}_listView_row_{$smarty.foreach.listview.index+1}" {if $MODULE eq 'Calendar'}data-recurring-enabled='{$LISTVIEW_ENTRY->isRecurringEnabled()}'{/if}>
							<td class = "listViewRecordActions">
								{include file="ListViewRecordActions.tpl"|vtemplate_path:$MODULE}
							</td>
							{if ($LISTVIEW_ENTRY->get('document_source') eq 'Google Drive' && $IS_GOOGLE_DRIVE_ENABLED) || ($LISTVIEW_ENTRY->get('document_source') eq 'Dropbox' && $IS_DROPBOX_ENABLED)}
						<input type="hidden" name="document_source_type" value="{$LISTVIEW_ENTRY->get('document_source')}">
					{/if}
					{foreach item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
						{assign var=LISTVIEW_HEADERNAME value=$LISTVIEW_HEADER->get('name')}
						{assign var=LISTVIEW_ENTRY_RAWVALUE value=$LISTVIEW_ENTRY->getRaw($LISTVIEW_HEADER->get('column'))}
						{if $LISTVIEW_HEADER->getFieldDataType() eq 'currency' || $LISTVIEW_HEADER->getFieldDataType() eq 'text'}
							{assign var=LISTVIEW_ENTRY_RAWVALUE value=$LISTVIEW_ENTRY->getTitle($LISTVIEW_HEADER)}
						{/if}
						{assign var=LISTVIEW_ENTRY_VALUE value=$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME)}
						<td class="listViewEntryValue drawPoliline" data-name="{$LISTVIEW_HEADER->get('name')}" title="{$LISTVIEW_ENTRY->getTitle($LISTVIEW_HEADER)}" data-rawvalue="{$LISTVIEW_ENTRY_RAWVALUE}" data-field-type="{$LISTVIEW_HEADER->getFieldDataType()}">
							<span class="fieldValue">
								<span class="value">
									{if ($LISTVIEW_HEADER->isNameField() eq true or $LISTVIEW_HEADER->get('uitype') eq '4') and $MODULE_MODEL->isListViewNameFieldNavigationEnabled() eq true }
										
										<a href="{$LISTVIEW_ENTRY->getDetailViewUrl()}&app={$SELECTED_MENU_CATEGORY}">{$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME)}</a>
										{if $MODULE eq 'Products' &&$LISTVIEW_ENTRY->isBundle()}
											&nbsp;-&nbsp;<i class="mute">{vtranslate('LBL_PRODUCT_BUNDLE', $MODULE)}</i>
										{/if}
									{else if $MODULE_MODEL->getName() eq 'Documents' && $LISTVIEW_HEADERNAME eq 'document_source'}
										{$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME)}
									{else}
										{if $LISTVIEW_HEADER->get('uitype') eq '72'}
											{assign var=CURRENCY_SYMBOL_PLACEMENT value={$CURRENT_USER_MODEL->get('currency_symbol_placement')}}
											{if $CURRENCY_SYMBOL_PLACEMENT eq '1.0$'}
												{$LISTVIEW_ENTRY_VALUE}{$LISTVIEW_ENTRY->get('currencySymbol')}
											{else}
												{$LISTVIEW_ENTRY->get('currencySymbol')}{$LISTVIEW_ENTRY_VALUE}
											{/if}
										{else if $LISTVIEW_HEADER->get('uitype') eq '71'}
											{assign var=CURRENCY_SYMBOL value=$LISTVIEW_ENTRY->get('userCurrencySymbol')}
											{if $LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME) neq NULL}
												{CurrencyField::appendCurrencySymbol($LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME), $CURRENCY_SYMBOL)}
											{/if}
										{else if $LISTVIEW_HEADER->getFieldDataType() eq 'picklist'}
											{if $LISTVIEW_ENTRY->get('activitytype') eq 'Task'}
												{assign var=PICKLIST_FIELD_ID value={$LISTVIEW_HEADER->getId()}}
											{else}
												{if $LISTVIEW_HEADER->getName() eq 'taskstatus'}
													{assign var="EVENT_STATUS_FIELD_MODEL" value=Vtiger_Field_Model::getInstance('eventstatus', Vtiger_Module_Model::getInstance('Events'))}
													{if $EVENT_STATUS_FIELD_MODEL}
														{assign var=PICKLIST_FIELD_ID value={$EVENT_STATUS_FIELD_MODEL->getId()}}
													{else} 
														{assign var=PICKLIST_FIELD_ID value={$LISTVIEW_HEADER->getId()}}
													{/if}
												{else}
													{assign var=PICKLIST_FIELD_ID value={$LISTVIEW_HEADER->getId()}}
												{/if}
											{/if}
											<span {if !empty($LISTVIEW_ENTRY_VALUE)} class="picklist-color picklist-{$PICKLIST_FIELD_ID}-{Vtiger_Util_Helper::convertSpaceToHyphen($LISTVIEW_ENTRY_RAWVALUE)}" {/if}> {$LISTVIEW_ENTRY_VALUE} </span>
										{else if $LISTVIEW_HEADER->getFieldDataType() eq 'multipicklist'}
											{assign var=MULTI_RAW_PICKLIST_VALUES value=explode('|##|',$LISTVIEW_ENTRY->getRaw($LISTVIEW_HEADERNAME))}
											{assign var=MULTI_PICKLIST_VALUES value=explode(',',$LISTVIEW_ENTRY_VALUE)}
											{assign var=ALL_MULTI_PICKLIST_VALUES value=array_flip($LISTVIEW_HEADER->getPicklistValues())}
											{foreach item=MULTI_PICKLIST_VALUE key=MULTI_PICKLIST_INDEX from=$MULTI_PICKLIST_VALUES}
												<span {if !empty($LISTVIEW_ENTRY_VALUE)} class="picklist-color picklist-{$LISTVIEW_HEADER->getId()}-{Vtiger_Util_Helper::convertSpaceToHyphen(trim($ALL_MULTI_PICKLIST_VALUES[trim($MULTI_PICKLIST_VALUE)]))}"{/if} > 
													{if trim($MULTI_PICKLIST_VALUES[$MULTI_PICKLIST_INDEX]) eq vtranslate('LBL_NOT_ACCESSIBLE', $MODULE)} 
														<font color="red"> 
														{trim($MULTI_PICKLIST_VALUES[$MULTI_PICKLIST_INDEX])} 
														</font> 
													{else} 
														{trim($MULTI_PICKLIST_VALUES[$MULTI_PICKLIST_INDEX])} 
													{/if}
													{if !empty($MULTI_PICKLIST_VALUES[$MULTI_PICKLIST_INDEX + 1])},{/if}
												</span>
											{/foreach}
										{else}
											{$LISTVIEW_ENTRY_VALUE}
										{/if}
									{/if}
								</span>
							</span>
							{if $LISTVIEW_HEADER->isEditable() eq 'true' && $LISTVIEW_HEADER->isAjaxEditable() eq 'true'}
								<span class="hide edit">
								</span>
							{/if}
						</td>
					{/foreach}
					</tr>
				{/foreach}
				{if $LISTVIEW_ENTRIES_COUNT eq '0'}
					<tr class="emptyRecordsDiv">
						{assign var=COLSPAN_WIDTH value={count($LISTVIEW_HEADERS)}+1}
						<td colspan="{$COLSPAN_WIDTH}">
							<div class="emptyRecordsContent">
								{assign var=SINGLE_MODULE value="SINGLE_$MODULE"}
								{vtranslate('LBL_NO')} {vtranslate($MODULE, $MODULE)} {vtranslate('LBL_FOUND')}.
								{if $IS_CREATE_PERMITTED}
									<a style="color:blue" href="{$MODULE_MODEL->getCreateRecordUrl()}"> {vtranslate('LBL_CREATE')}</a>
									{if Users_Privileges_Model::isPermitted($MODULE, 'Import') && $LIST_VIEW_MODEL->isImportEnabled()}
										{vtranslate('LBL_OR', $MODULE)}
										<a style="color:blue" href="#" onclick="return Vtiger_Import_Js.triggerImportAction()">{vtranslate('LBL_IMPORT', $MODULE)}</a>
										{vtranslate($MODULE, $MODULE)}
									{else}
										{vtranslate($SINGLE_MODULE, $MODULE)}
									{/if}
								{/if}
							</div>
						</td>
					</tr>
				{/if}
				</tbody>
			</table>
		</form>
	</div>
	<div id="scroller_wrapper" class="bottom-fixed-scroll">
		<div id="scroller" class="scroller-div"></div>
	</div>
	<div class="ctlefthideessentials-toggle" title="Listing Panel Show/Hide">
		<span class="essentials-toggle-marker fa cursorPointer fa-chevron-left"></span>
	</div>
</div>
{* code added by sapna*}
<div class="col-sm-6 col-xs-6" id="mapdiv">
	<div class="ctrighthideessentials-toggle" title="Map Panel Show/Hide">
		<span class="essentials-toggle-marker fa cursorPointer fa-chevron-right"></span>
	</div>
	<div id='map' style="width:100%;min-height: 500px; margin: 0; padding: 0;margin-top: 30px;top:10px;">
</div>
{* code end*}
</div>
