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
#result ul{ 
   list-style: none;
   width: 110%;
   padding-right: 0%;
   position: relative;
   padding-left: 0px;
   border: 1px solid #d7d1d1 !important;
   margin-top: 2px;
   margin-left: -11px;
   z-index: 1; 
   background:#fff; 
}
#result ul li{ 
  
   padding: 4px;
   margin-bottom: 1px;
}
#result ul li:hover{ 
   cursor: pointer; 
}
</style>
{/literal}

<script type="text/javascript" src="layouts/v7/modules/CTMobileSettings/resources/moment.min.js"></script>
<script type="text/javascript" src="layouts/v7/modules/CTMobileSettings/resources/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="layouts/v7/modules/CTMobileSettings/daterangepicker.css" />

<link rel="stylesheet" type="text/css" href="layouts/v7/modules/CTMobileSettings/dataTables.bootstrap.min.css" />
<script type="text/javascript" src="layouts/v7/modules/CTMobileSettings/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="layouts/v7/modules/CTMobileSettings/dataTables.bootstrap.min.js"></script>

<script src="https://unpkg.com/@google/markerclustererplus@4.0.1/dist/markerclustererplus.min.js"></script>
{if $API_KEY neq ''}
       <script async defer
      src="https://maps.googleapis.com/maps/api/js?key={$API_KEY}&callback=initMap">
      </script>
      {literal}
          <script type='text/javascript'>

          var flightPath;
          var map;
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
           jQuery('#mapRoutes').on('change',function(){
            var progressIndicatorElement = jQuery.progressIndicator({
                'position' : 'html',
                'blockInfo' : {
                    'enabled' : true
                }
            });
            var params = {};
            var routeid = jQuery('#mapRoutes').val();
            params['module'] = 'CTMobileSettings';
            params['action'] = 'getListRoute';
            params['mode'] = 'getRoutePoint';
            params['routeid'] = routeid;
            AppConnector.request(params).then(
            function(data) {
              progressIndicatorElement.progressIndicator({'mode' : 'hide'});
              var result2 = data.result.marker;
              var flightPlanCoordinates2 = [];
              jQuery.each(result2, function(index, item) {
                flightPlanCoordinates2.push({lat:parseFloat(item.lat),lng:parseFloat(item.lng),info:item.label});
                
              });
              initMap();
              var locations = flightPlanCoordinates2;
              var infoWin = new google.maps.InfoWindow();
              // Add some markers to the map.
              // Note: The code uses the JavaScript Array.prototype.map() method to
              // create an array of markers based on a given "locations" array.
              // The map() method here has nothing to do with the Google Maps API.
              var markers = locations.map(function(location, i) {
                var marker = new google.maps.Marker({
                  position: location,
                  map: map
                });
                google.maps.event.addListener(marker, 'click', function(evt) {
                  infoWin.setContent(location.info);
                  infoWin.open(map, marker);
                })
                map.setCenter(marker.getPosition());
                return marker;
              });

              // Add a marker clusterer to manage the markers.
             /* markerCluster = new MarkerClusterer(map, markers, {
                imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m',
              });*/

              var mcOptions = {
                imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m',
                infoOnClick: true, 
                infoOnClickZoom: 7
              };
        
              markerCluster = new MarkerClusterer(map, markers, mcOptions);

              google.maps.event.addListener(markerCluster, "clusterclick", multiChoice);
          });
        });

      });


      function multiChoice(cluster) {
             /*var cluster = mc.clusters_;*/
             var zoom = markerCluster.getMap().getZoom();
             var maxZoom = 15;
             // if more than 1 point shares the same lat/long
             // the size of the cluster array will be 1 AND
             // the number of markers in the cluster will be > 1
             // REMEMBER: maxZoom was already reached and we can't zoom in anymore
             if (zoom >= maxZoom == 1 && cluster.markers_.length > 1)
             {
                  var markers = cluster.markers_;
                  var a = 360.0 / markers.length;
                  for (var i=0; i < markers.length; i++)
                  {
                      var pos = markers[i].getPosition();
                      var newLat = pos.lat() + -.00004 * Math.cos((+a*i) / 180 * Math.PI);  // x
                      var newLng = pos.lng() + -.00004 * Math.sin((+a*i) / 180 * Math.PI);  // Y
                      var finalLatLng = new google.maps.LatLng(newLat,newLng);
                      markers[i].setPosition(finalLatLng);
                      markers[i].setMap(markerCluster.getMap());
                  }
                  cluster.clusterIcon_.hide();
                  return ;
             }

             return true;
        }
     
      </script>
       {/literal}
   {else}
     <script src="https://api-maps.yandex.ru/2.1/?lang=en_US&amp;" type="text/javascript"></script>
    {literal}
        <script type='text/javascript'>
              ymaps.ready(init);
              var myMap = '';
              function init() {
              // Creating the map.
                myMap = new ymaps.Map("map", {
                    center: [0,-180],
                    zoom: 2,
                    controls: ['zoomControl','typeSelector']
                  }
                );
              }
          jQuery(document).ready(function () { 
            jQuery('#mapRoutes').on('change',function(){
              var progressIndicatorElement = jQuery.progressIndicator({
                  'position' : 'html',
                  'blockInfo' : {
                      'enabled' : true
                  }
              });
              var params = {};
              var routeid = jQuery('#mapRoutes').val();
              params['module'] = 'CTMobileSettings';
              params['action'] = 'getListRoute';
              params['mode'] = 'getRoutePoint';
              params['routeid'] = routeid;
              AppConnector.request(params).then(
              function(data) {
                progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                var result2 = data.result.marker;
                var flightPlanCoordinates2 = [];
                jQuery.each(result2, function(index, item) {
                    flightPlanCoordinates2.push([item.label,parseFloat(item.lat),parseFloat(item.lng)]);
                });
                myMap.geoObjects.removeAll();
                myMap.setCenter([result2[0].lat,result2[0].lng]);
                myMap.setZoom(12);
                for (i = 0; i < flightPlanCoordinates2.length; i++) {
                  myMap.geoObjects.add(new ymaps.Placemark([flightPlanCoordinates2[i][1],flightPlanCoordinates2[i][2]], {
                    balloonContent: flightPlanCoordinates2[i][0]
                  }, {
                    preset: 'islands#icon',
                    iconColor: '#0095b6'
                  }));
                }
              });
            });
            });
        </script>
     {/literal}
  {/if}

<div class="container-fluid">
    <div class="widget_header row-fluid">
        <button type="button" class="btn btn-info pull-right" style="background:#287DF2 !important;" onclick='window.location.href="{CTMobileSettings_Module_Model::$CTMOBILE_DETAILVIEW_URL}"'>{vtranslate('Go To CRMTiger Settings',$MODULE)}</button>
        <h3>{vtranslate('CRMTiger Mobile Apps - Route Analytics', 'CTMobileSettings')}</h3>
    </div>
    <hr>
    <!-- <h5 style="margin-left:20px;">{vtranslate('Add/Remove users from list to access of route planning','CTMobileSettings')}</h5> -->
    <div class="clearfix"></div>
    
    <div class="tab-content massEditContent">
            <div class="summaryWidgetContainer" id="route_analytics_settings">
              <ul class="nav nav-tabs massEditTabs">
                 <li class="active">
                      <a href="#listview" data-toggle="tab" >
                          <strong>
                              {vtranslate('List', 'CTMobileSettings')}
                          </strong>
                      </a>
                  </li>
                  <li >
                      <a href="#mapview" data-toggle="tab">
                          <strong>
                              {vtranslate('Map', 'CTMobileSettings')}
                          </strong>
                      </a>
                  </li>   
              </ul>
              <div class="tab-content massEditContent">
                  <div class="tab-pane" id="mapview">
                      <br/>
                      <div class="container-fluid">
                            <select class="select2" id="mapRoutes" name="mapRoutes" data-placeholder="{vtranslate('Select Routes', $MODULE)}" style="width:30%;">
                                    <option value="">{vtranslate('Select Routes', $MODULE)}</option>
                              
                            </select>
                            
                            <input type="text" class="inputElement" id="mapdaterange" name="mapdaterange" value="" style="width:30%;display: inline!important;margin-left:1%;"/>

                            <select class="select2" id="mapUsers" name="mapUsers" data-placeholder="{vtranslate('Select Users And Groups', $MODULE)}" style="width:30%;margin-left:1%;">
                                <option value="all" data-field-name="{vtranslate('LBL_ALL',$MODULE)} {vtranslate('LBL_USERS',$MODULE)}">{vtranslate('LBL_ALL',$MODULE)} {vtranslate('LBL_USERS',$MODULE)}</option>
                                <optgroup label="{vtranslate('LBL_USERS')}">
                                {foreach key=FIELD_NAME item=FIELD_MODEL from=$USER_MODEL}
                                    <option value="{$FIELD_MODEL['userid']}" data-field-name="{$FIELD_MODEL['username']}"
                                            >{$FIELD_MODEL['username']}
                                    </option>
                                {/foreach}
                                </optgroup>
                                <optgroup label="{vtranslate('LBL_GROUPS')}">
                                {foreach key=FIELD_NAME item=FIELD_MODEL from=$GROUPS_MODEL}
                                    <option value="{$FIELD_MODEL['userid']}" data-field-name="{$FIELD_MODEL['username']}"
                                            >{$FIELD_MODEL['username']}
                                    </option>
                                {/foreach}
                                </optgroup>
                            </select>


                      </div>
                      <div id='map' style="width:100%;min-height: 54em; margin: 0; padding: 0;margin-top: 14px;">
                      </div>
                     
                  </div>
                  
                  <div class="tab-pane active" id="listview" style="">
                       <br/>
                       <div class="container-fluid" id="EditConfigEditor">
                            <form class="form-horizontal recordEditView" id="EditView" name="edit" method="post" action="index.php" enctype="multipart/form-data">
                                <input type="hidden" name="module" value="CTMobileSettings" />
                                <input type="hidden" name="action" value="getListRoute" />
                                <input type="hidden" name="mode" value="ExportData" />

                                <div class="search-links-container col-md-3 col-lg-3 hidden-sm">
                                <div class="search-link hidden-xs" style="margin-top:0px; height:30px;">
                                  <span class="fa fa-search" aria-hidden="true"></span>
                                  <input type="hidden" id="searchtextvalue" name="searchtextvalue"/>
                                  <input type="text" class="keyword-input" id="searchbox" placeholder="{vtranslate('LBL_TYPE_SEARCH')} {vtranslate('LBL_RECORD')}"  style="height:24px;color:black;"/>
                                       <div id="result"><ul></ul></div>
                                
                                </div>
                                </div>

                                <select class="select2" id="listUsers" name="listUsers" data-placeholder="{vtranslate('Select Users And Groups', $MODULE)}" style="width:17%">
                                    <option value="all" data-field-name="{vtranslate('LBL_ALL',$MODULE)} {vtranslate('LBL_USERS',$MODULE)}">{vtranslate('LBL_ALL',$MODULE)} {vtranslate('LBL_USERS',$MODULE)}</option>
                                    <optgroup label="{vtranslate('LBL_USERS')}">
                                    {foreach key=FIELD_NAME item=FIELD_MODEL from=$USER_MODEL}
                                        <option value="{$FIELD_MODEL['userid']}" data-field-name="{$FIELD_MODEL['username']}"
                                                >{$FIELD_MODEL['username']}
                                        </option>
                                    {/foreach}
                                    </optgroup>
                                    <optgroup label="{vtranslate('LBL_GROUPS')}">
                                    {foreach key=FIELD_NAME item=FIELD_MODEL from=$GROUPS_MODEL}
                                        <option value="{$FIELD_MODEL['userid']}" data-field-name="{$FIELD_MODEL['username']}"
                                                >{$FIELD_MODEL['username']}
                                        </option>
                                    {/foreach}
                                    </optgroup>
                                </select>

                                <input type="text" class="inputElement" id="listdaterange" name="listdaterange" value="" style="width:17%;display: inline!important;margin-left:1%;"/>
                                <button type='submit' class='btn btn-success saveButton' name="type" value="csv" style="width:15%;display: inline!important;margin-left:1%;">{vtranslate('EXPORT AS CSV', $MODULE)}</button>
                                <button type='submit' class='btn btn-success saveButton' name="type" value="excel" style="width:16%;display: inline!important;margin-left:1%;">{vtranslate('EXPORT AS EXCEL', $MODULE)}</button>
                            </form>
                       </div>
                       <br/>
                       <div id="listRoute">
                            <table id="example1" class='table table-bordered table-striped'>
                                <thead>
                                    <tr><th></th><th> {vtranslate('Route','CTMobileSettings')} </th><th> {vtranslate('Date of Route','CTMobileSettings')} </th><th> {vtranslate('Assigned To','CTMobileSettings')} </th><th> {vtranslate('Record Type','CTMobileSettings')} </th><th> {vtranslate('Name','CTMobileSettings')} </th><th> {vtranslate('Route Status','CTMobileSettings')} </th><th> {vtranslate('Notes','CTMobileSettings')} </th><th> {vtranslate('Check-in Time','CTMobileSettings')} </th><th> {vtranslate('Check-out Time','CTMobileSettings')} </th><th> {vtranslate('Check-in Location','CTMobileSettings')} </th><th> {vtranslate('Check-out Location','CTMobileSettings')} </th><th>{vtranslate('LBL_ACTION')}</th></tr>
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


