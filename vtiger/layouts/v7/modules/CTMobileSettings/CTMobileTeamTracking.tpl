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
<style>
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

.ctlefthideessentials-toggle,.ctleftshowessentials-toggle,.ctrighthideessentials-toggle,.ctrightshowessentials-toggle{
 	background-color: white;
    font-weight: bold;
    padding: 5px 2px !important;
    /*position: absolute;*/
    top: 175px !important;
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
    top: 7px !important;
    right: 1px;
}
div#example1_wrapper {
    position: relative;
}
.ctlefthideessentials-toggle{
	position: absolute;
    top: 7px !important;
    left :auto !important;
    right: 1px !important;;
}
div#map {
    position: relative !important;
}
.ctrighthideessentials-toggle{
	position: absolute;
    top: 7px !important;
    left: 1px !important;
}
.col-lg-1.col-md-1.col-sm-1.collapseArrow {
    padding-left: 0;
    padding-right: 0;
    display: inline-block;
    float: left;
}

.listtimeline table { border-collapse: collapse;
border-collapse: separate;
border-spacing: 0 1em;
}
#tbltimeline tr{ /*outline: thin solid black;*/
	background-color: #f5f5f5;
box-shadow: 8px 7px 4px gray;
}
#tbltimeline td{
	padding: 2px 20px;
	box-sizing:border-box;
}

#tbltimeline tr {
    outline: none; 
    background-color: transparent;
    box-shadow: none; 
    margin-left: 4em;
    min-height: 50px;
    border-left: 1px dashed #3d8bcf;
    padding: 0 0 0px 25px;
    position: relative;
    display: inline-block;
    float: left;
    width:90%;
}

#tbltimeline tr::before {
    position: absolute;
    left: -10px;
    top: 20px;
    content: " ";
    border: 8px solid #333;
    border-radius: 500%;
    background: #3d8bcf;
    height: 20px;
    width: 20px;
    transition: all 500ms ease-in-out;
}
#tbltimeline tr:hover::before {
    border-color: #fbfbfb;
    transition: all 1000ms ease-in-out;
}
form#EditView .col-lg-1.col-md-1.col-sm-1 button.btn.btn-success.btn-sm {
    width: 100%;
    padding: 5px 10px;
}
form#EditView .col-lg-4.col-md-4.col-sm-4 .btn{
	background: transparent !important;
	padding: 0;
    border: 1px solid transparent;
    border-radius:0;
    outline:0;
    margin: 0 10px !important;
}
form#EditView .col-lg-4.col-md-4.col-sm-4 .btn img{
	width:36px !important;
}
form#EditView .col-lg-4.col-md-4.col-sm-4 {
    float: right;
    text-align: right;
}
.main_div .btn-success:hover, .main_div .btn-success:active, .main_div .btn-success.active, .main_div .open>.dropdown-toggle.btn-success{
	box-shadow: none;
}
.main_div .btn:focus, .btn:active:focus,.main_div .btn.active:focus, .btn.focus,.main_div .btn:active.focus, .main_div .btn.active.focus{
	outline:0;
}

#tbltimeline td:nth-child(1), #tbltimeline td:nth-child(4) { 
    width: 25%; 
}

#tbltimeline td:nth-child(2) { 
    width: 10%; 
}

#tbltimeline td:nth-child(3) { 
	width: 40%; 
}

table tr {
    cursor: pointer;
}

</style>
{/literal}
	
	<script type="text/javascript" src="layouts/v7/modules/CTMobileSettings/resources/moment.min.js"></script>
	<script type="text/javascript" src="layouts/v7/modules/CTMobileSettings/resources/daterangepicker.min.js"></script>
	
	<link rel="stylesheet" type="text/css" href="layouts/v7/modules/CTMobileSettings/daterangepicker.css" />

	<!-- Datatable CSS -->
	<!-- <link href='//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css' rel='stylesheet' type='text/css'> -->

	<!-- jQuery Library -->
	<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> -->

	<!-- Datatable JS -->
	<!-- <script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script> -->

	<link rel="stylesheet" type="text/css" href="layouts/v7/modules/CTMobileSettings/dataTables.bootstrap.min.css" />
	<script type="text/javascript" src="layouts/v7/modules/CTMobileSettings/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="layouts/v7/modules/CTMobileSettings/dataTables.bootstrap.min.js"></script>

{literal}
	<script type="text/javascript">
		jQuery(document).ready(function () {
			jQuery('.listtimeline').hide();
			jQuery("#listmapUserRoute").on("click",".ctlefthideessentials-toggle",function(){
				if(jQuery('#mapuserroute').css('display') == 'none') {
					jQuery('#listuserroute').show();
					jQuery('#mapuserroute').show();
					jQuery('.ctrightshowessentials-toggle').removeClass('ctrightshowessentials-toggle').addClass('ctrighthideessentials-toggle');
					jQuery('.ctrighthideessentials-toggle').find('span').removeClass('fa-chevron-left').addClass('fa-chevron-right');
					jQuery('#mapuserroute').removeClass('col-lg-12 col-md-12 col-sm-12').addClass('col-lg-6 col-md-6 col-sm-6');
					jQuery('#listuserroute').removeClass('col-lg-12 col-md-12 col-sm-12').addClass('col-lg-6 col-md-6 col-sm-6');
				}else{
					jQuery(this).removeClass('ctlefthideessentials-toggle').addClass('ctleftshowessentials-toggle');
					jQuery(this).find('span').removeClass('fa-chevron-left').addClass('fa-chevron-right');
					jQuery('#listuserroute').hide();
					jQuery('#mapuserroute').removeClass('col-lg-6 col-md-6 col-sm-6').addClass('col-lg-12 col-md-12 col-sm-12');
				}
			});


			jQuery("#listmapUserRoute").on("click",".ctleftshowessentials-toggle", function(){
				jQuery(this).removeClass('ctleftshowessentials-toggle').addClass('ctlefthideessentials-toggle');
				jQuery(this).find('span').removeClass('fa-chevron-right').addClass('fa-chevron-left');
				jQuery('#listuserroute').show();
				jQuery('#mapuserroute').removeClass('col-lg-12 col-md-12 col-sm-12').addClass('col-lg-6 col-md-6 col-sm-6');
			});

			jQuery("#listmapUserRoute").on("click",".ctrighthideessentials-toggle",function(){
				if(jQuery('#listuserroute').css('display') == 'none') {
					jQuery('#listuserroute').show();
					jQuery('#mapuserroute').show();
					jQuery('.ctleftshowessentials-toggle').removeClass('ctleftshowessentials-toggle').addClass('ctlefthideessentials-toggle');
					jQuery('.ctlefthideessentials-toggle').find('span').removeClass('fa-chevron-right').addClass('fa-chevron-left');
					jQuery('#mapuserroute').removeClass('col-lg-12 col-md-12 col-sm-12').addClass('col-lg-6 col-md-6 col-sm-6');
					jQuery('#listuserroute').removeClass('col-lg-12 col-md-12 col-sm-12').addClass('col-lg-6 col-md-6 col-sm-6');
				}else{
					jQuery(this).removeClass('ctrighthideessentials-toggle').addClass('ctrightshowessentials-toggle');
					jQuery(this).find('span').removeClass('fa-chevron-right').addClass('fa-chevron-left');
					jQuery('#mapuserroute').hide();
					jQuery('#listuserroute').removeClass('col-lg-6 col-md-6 col-sm-6').addClass('col-lg-12 col-md-12 col-sm-12');
				}
			});

			jQuery("#listmapUserRoute").on("click",".ctrightshowessentials-toggle", function(){
				jQuery(this).removeClass('ctrightshowessentials-toggle').addClass('ctrighthideessentials-toggle');
				jQuery(this).find('span').removeClass('fa-chevron-left').addClass('fa-chevron-right');
				jQuery('#mapuserroute').show();
				jQuery('#listuserroute').removeClass('col-lg-12 col-md-12 col-sm-12').addClass('col-lg-6 col-md-6 col-sm-6');
			});

			jQuery('#modnavigator').remove();

			jQuery('.settingsPageDiv').removeClass('settingsPageDiv content-area clearfix');

			jQuery('#searchbox').focusout(function(){
				setTimeout(function(){ 
					jQuery('#result ul').empty();
				}, 200);
			});
			
	    });
	</script>

{/literal}

	{if $API_KEY neq ''}
		<script async defer
		src="https://maps.googleapis.com/maps/api/js?key={$API_KEY}&callback=initMap">
		</script>
		{literal}
		<script type='text/javascript'>
			var viewtype='list';
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

				setTimeout(function(){ 
					jQuery('.timeLineViewButton').trigger('click');
				}, 500);
				
				jQuery('.listViewButton').on('click',function(){
		        	var params = {};
		        	jQuery('.listtableview').show();
					jQuery('.listtimeline').hide();
					viewtype='list';	

					var mainmodule = app.getModuleName();
					var parentmodule  = app.getParentModuleName();
					var action = 'getUserLatLong';
					var user_id = jQuery('#routeUser').val();
					var daterange = jQuery('input[name="daterange"]').val();
					var mode = 'ServerSideAjax';
					//var checkmodule  = jQuery('#sourceModule').val();
					var searchtext = jQuery("#searchbox").val();
					getUserDataWithAPIServerSide(mainmodule,parentmodule,action,user_id,daterange,mode,searchtext);	

				});

				jQuery('#example1 tbody').on('click', 'tr', function () {
					var index = jQuery(this).index();
					for (var i = 0; i < markers.length; i++) {
						google.maps.event.trigger(markers[i], 'click');
					}
					google.maps.event.trigger(markers[index], 'mouseover');
				});
				
				/*jQuery('#routeUser').on('change',function(){
					
					var mainmodule = app.getModuleName();
					var parentmodule  = app.getParentModuleName();
					var action = 'getUserLatLong';
					var user_id = jQuery(this).val();
					var daterange = jQuery('input[name="daterange"]').val();
					//var checkmodule  = jQuery('#sourceModule').val();
					var searchtext = jQuery("#searchbox").val();
					if(searchtext == ""){
						jQuery("#hiddensearchval").val('');
					}
					var searchid = jQuery("#hiddensearchval").val();

					if(viewtype == 'list'){
						var mode = 'ServerSideAjax';
						getUserDataWithAPIServerSide(mainmodule,parentmodule,action,user_id,daterange,mode,searchid);	
					}
					else if(viewtype == 'timeline'){
						var mode = 'timeline';
						getUserDataWithAPITimeline(mainmodule,parentmodule,action,user_id,daterange,mode,searchid);		
					}
				});

				jQuery('#daterange').on('change',function(){
					
					var mainmodule = app.getModuleName();
					var parentmodule = app.getParentModuleName();
					var action = 'getUserLatLong';
					var user_id = jQuery('#routeUser').val();
					var daterange = jQuery(this).val();
					
					var searchtext = jQuery("#searchbox").val();
					if(searchtext == ""){
						jQuery("#hiddensearchval").val('');
					}
					var searchid = jQuery("#hiddensearchval").val();
					if(viewtype == 'list'){
						var mode = 'ServerSideAjax';
						getUserDataWithAPIServerSide(mainmodule,parentmodule,action,user_id,daterange,mode,searchid);	
					}
					else if(viewtype == 'timeline'){
						var mode = 'timeline';
						getUserDataWithAPITimeline(mainmodule,parentmodule,action,user_id,daterange,mode,searchid);		
					}
				});*/

				$("#searchbox").keyup(function() {
					      
				       var searchvalue = $('#searchbox').val();
				       if (searchvalue == "") {
					    	$("#result ul").empty();
				       }
				       else {
						var daterange = jQuery('input[name="daterange"]').val();
					  	var postData = {
						"module": app.getModuleName(),
						"parent": app.getParentModuleName(),
						"action": 'getUserLatLong',
						"mode":'getLabelRecord',
						"user_id":jQuery('#routeUser').val(),
						"daterange": daterange,
						"checkmodule":'',
						"searchvalue":searchvalue
						};
					
						app.request.post({'data': postData}).then(function (err, response) {
					
						    var len = response.length;
						    $("#result ul").empty();
						    for( var i = 0; i<len; i++){
								$("#result ul").append("<li value='"+response[i]['id']+"'>"+response[i]['text']+"</li>");
						    }
						    // binding click event to li
						    $("#result li").bind("click",function(){
								setText(this);
						    });
						},
						function(error,err){

						});
				       }
				});	



				jQuery('.timeLineViewButton').on('click',function(){
		        	var params = {};
		        	jQuery('.listtableview').hide();
					jQuery('.listtimeline').show();
					viewtype='timeline';
		            var modulenm = app.getModuleName();
					var parent  = app.getParentModuleName();
					var action = 'getUserLatLong';
					var user_id = jQuery('#routeUser').val();
					var daterange = jQuery('input[name="daterange"]').val();
					//var checkmodule = jQuery('#sourceModule').val();
					var mode = 'timeline';
					var searchtext = jQuery("#searchbox").val();
					if(searchtext == ""){
						jQuery("#hiddensearchval").val('');
					}
					//var searchid = jQuery("#hiddensearchval").val();

					getUserDataWithAPITimeline(modulenm,parent,action,user_id,daterange,mode,searchtext);	
					
	        	});

		        jQuery('#tbltimeline tbody').on('click', 'tr', function () {
					var index = jQuery(this).index();
					for (var i = 0; i < markers.length; i++) {
						google.maps.event.trigger(markers[i], 'click');
					}
					google.maps.event.trigger(markers[index], 'mouseover');
				});

				jQuery('#searchbtn').on('click',function(){
					var mainmodule = app.getModuleName();
				    var parentmodule = app.getParentModuleName();
				    var action = 'getUserLatLong';
				    var user_id = jQuery('#routeUser').val();
				    var daterange = jQuery('input[name="daterange"]').val();
				    var searchtext = jQuery("#searchbox").val();

				    if(viewtype == 'list'){
				    	var mode = 'ServerSideAjax';
				    	getUserDataWithAPIServerSide(mainmodule,parentmodule,action,user_id,daterange,mode,searchtext);	
				    }else if(viewtype == 'timeline'){
						var mode = 'timeline';
						getUserDataWithAPITimeline(mainmodule,parentmodule,action,user_id,daterange,mode,searchtext);		
					}
				});
		 	});

			function setText(element){
				var recordid = $(element).val();
			    var value = $(element).text();
			    $("#searchbox").val(value);
			    $("#result ul").empty();
			    
			    //used when view change list to timeline or vice versa
			    $('#hiddensearchval').val(recordid);

			}

			function getUserDataWithAPIServerSide(mainmodule,parentmodule,action,userid,daterange,mode,searchvalue){

				var url = "index.php?module="+mainmodule+"&parent="+parentmodule+"&action="+action+"&mode="+mode+"&user_id="+userid+"&daterange="+daterange+"&searchvalue="+searchvalue;

				if ( $.fn.dataTable.isDataTable( '#example1' ) ) {
				   table.destroy();
				}
				
				table = jQuery('#example1').DataTable({
		          'paging'      : true,
		          'lengthChange': false,
		          'searching'   : false,
		          'ordering'    : false,
		          'info'        : true,
		          'autoWidth'   : false,
		          'pageLength' : 20,
		          'processing': true,
			      'serverSide': true,
			      'serverMethod': 'post',
			      'ajax': {
			          'url':url
			      },
			      "columns": [
		            { "data": "username" },
		            { "data": "record_label" },
		            { "data": "datetime" },
		            { "data": "action" },
		            { "data": "view_details" }
		          ],
			      "drawCallback": function( settings ) {

				        var data = settings.json.data;
						initMap();
						markers = [];
				        jQuery.each(data, function( index, value ) {
							map.setCenter({lat:value.lat, lng:value.lng});
							map.setZoom(20);
							var infowindow = new google.maps.InfoWindow();
							var marker, i;
							i = index;
							
							  marker = new google.maps.Marker({
								position: new google.maps.LatLng(value.lat, value.lng),
								map: map,
							  });
							
							  google.maps.event.addListener(marker, 'mouseover', (function(marker, i) {
								return function() {
								  infowindow.setContent(value.label);
								  infowindow.open(map, marker);
								}
							  })(marker, i));

							  google.maps.event.addListener(marker, 'click', (function(marker, i) {
								return function() {
								  infowindow.close();
								}
							  })(marker, i));
							  markers[i] = marker;	
						});
				   }
		        });
			}	

			function getUserDataWithAPITimeline(mainmodule,parentmodule,action,userid,daterange,mode,searchvalue){
				
				var url = "index.php?module="+mainmodule+"&parent="+parentmodule+"&action="+action+"&mode="+mode+"&user_id="+userid+"&daterange="+daterange+"&searchvalue="+searchvalue;

				if ( $.fn.dataTable.isDataTable( '#tbltimeline' ) ) {
				   table.destroy();
				}

				table = jQuery('#tbltimeline').DataTable({
					'paging'      : true,
					'lengthChange': false,
					'searching'   : false,
					'ordering'    : false,
					'info'        : true,
					'autoWidth'   : false,
					'pageLength' : 10,
					'processing': true,
					'serverSide': true,
					'serverMethod': 'post',
					'ajax': {
					  'url':url
					},
					"columns": [
					{ "data": "activitytime" },
					{ "data": "moduleimg" },
					{ "data": "action" },
					{ "data": "modifiedby" },
					],
					"drawCallback": function( settings ) {
						var data = settings.json.data;
						
						initMap();
						markers = [];
				        jQuery.each(data, function( index, value ) {
							map.setCenter({lat:value.lat, lng:value.lng});
							map.setZoom(20);
							var infowindow = new google.maps.InfoWindow();
							var marker, i;
							i = index;
							
							marker = new google.maps.Marker({
								position: new google.maps.LatLng(value.lat, value.lng),
								map: map,
							});

							google.maps.event.addListener(marker, 'mouseover', (function(marker, i) {
								return function() {
							  		infowindow.setContent(value.label);
							  		infowindow.open(map, marker);
								}
							})(marker, i));

							google.maps.event.addListener(marker, 'click', (function(marker, i){
									return function() {
							  		infowindow.close();
								}
							})(marker, i));
							markers[i] = marker;	
						});
					}
			    });
			}	

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
					var viewtype='list';
					function initOpenMap() {
					 	var element = document.getElementById('map');
						//element.style = 'height:300px;';
						myMap = L.map(element);

						// Add OSM tile leayer to the Leaflet map.
						var mylayer = L.tileLayer('https://{s}.tile.osm.org/{z}/{x}/{y}.png', {
						    attribution: '&copy; <a href="https://osm.org/copyright">OpenStreetMap</a> contributors'
						}).addTo(myMap);
					}

					jQuery(document).ready(function () { 
						initOpenMap();
						setTimeout(function(){ 
							jQuery('.timeLineViewButton').trigger('click');
						}, 500);
				
						jQuery('.listViewButton').on('click',function(){
							var params = {};
				        	jQuery('.listtableview').show();
							jQuery('.listtimeline').hide();
							viewtype='list';	

							var mainmodule = app.getModuleName();
							var parentmodule  = app.getParentModuleName();
							var action = 'getUserLatLong';
							var user_id = jQuery('#routeUser').val();
							var daterange = jQuery('input[name="daterange"]').val();
							var mode = 'ServerSideAjax';
							var searchtext = jQuery("#searchbox").val();

							getUserDataServerSide(mainmodule,parentmodule,action,user_id,daterange,mode,searchtext);	

						});

						jQuery('#example1 tbody').on('click', 'tr', function () {
							var index = jQuery(this).index();
							// var info = table.page.info();
							// index = index + info.start;
							for (var i = 0; i < markers.length; i++) {
								markers[index].openPopup();
							}
							
						});
/*
						jQuery('#routeUser').on('change',function(){
							
							var mainmodule = app.getModuleName();
							var parentmodule = app.getParentModuleName();
							var action = 'getUserLatLong';
							var user_id = jQuery(this).val();
							var daterange = jQuery('input[name="daterange"]').val();
							var searchtext = jQuery("#searchbox").val();
							if(searchtext == ""){
								jQuery("#hiddensearchval").val('');
							}
							var searchid = jQuery("#hiddensearchval").val();

							if(viewtype == 'list'){
								var mode = 'ServerSideAjax';
								getUserDataServerSide(mainmodule,parentmodule,action,user_id,daterange,mode,searchid);	
							}
							else if(viewtype == 'timeline'){
								var mode = 'timeline';
								getUserDataTimeline(mainmodule,parentmodule,action,user_id,daterange,mode,searchid);		
							}
						});

						jQuery('#daterange').on('change',function(){
								
							var mainmodule = app.getModuleName();
							var parentmodule = app.getParentModuleName();
							var action = 'getUserLatLong';
							var user_id = jQuery('#routeUser').val();
							var daterange = jQuery(this).val();
							var searchtext = jQuery("#searchbox").val();
							if(searchtext == ""){
								jQuery("#hiddensearchval").val('');
							}
							var searchid = jQuery("#hiddensearchval").val();

							if(viewtype == 'list'){
								var mode = 'ServerSideAjax';
								getUserDataServerSide(mainmodule,parentmodule,action,user_id,daterange,mode,searchid);	
							}
							else if(viewtype == 'timeline'){
								var mode = 'timeline';
								getUserDataTimeline(mainmodule,parentmodule,action,user_id,daterange,mode,searchid);		
							}
						});*/

						$("#searchbox").keyup(function() {
					      
					    	var searchvalue = $('#searchbox').val();
					       	if (searchvalue == "") {
						   		$("#display").html("");
					       	}
					       	else {
								var daterange = jQuery('input[name="daterange"]').val();
							  	var postData = {
									"module": app.getModuleName(),
									"parent": app.getParentModuleName(),
									"action": 'getUserLatLong',
									"mode":'getLabelRecord',
									"user_id":jQuery('#routeUser').val(),
									"daterange": daterange,
									"checkmodule":'',
									"searchvalue":searchvalue
								};
						
								app.request.post({'data': postData}).then(function (err, response) {
						
							    	var len = response.length;
							    	$("#result ul").empty();
							    	for( var i = 0; i<len; i++){
										$("#result ul").append("<li value='"+response[i]['id']+"'>"+response[i]['text']+"</li>");
							    	}
								    // binding click event to li
								    $("#result li").bind("click",function(){
										setText(this);
								    });
								},
								function(error,err){

								});
					        }
					   	});	

						jQuery('#searchbtn').on('click',function(){
							var mainmodule = app.getModuleName();
						    var parentmodule = app.getParentModuleName();
						    var action = 'getUserLatLong';
						    var user_id = jQuery('#routeUser').val();
						    var daterange = jQuery('input[name="daterange"]').val();
						    var searchtext = jQuery("#searchbox").val();

						    if(viewtype == 'list'){
						    	var mode = 'ServerSideAjax';
						    	getUserDataServerSide(mainmodule,parentmodule,action,user_id,daterange,mode,searchtext);	
						    }else if(viewtype == 'timeline'){
								var mode = 'timeline';
								getUserDataTimeline(mainmodule,parentmodule,action,user_id,daterange,mode,searchtext);		
							}
						});
					   	
						
						jQuery('.timeLineViewButton').on('click',function(){
				        	var params = {};
				        	jQuery('.listtableview').hide();
							jQuery('.listtimeline').show();
							viewtype ='timeline';
				            var modulenm = app.getModuleName();
							var parent  = app.getParentModuleName();
							var action = 'getUserLatLong';
							var user_id = jQuery('#routeUser').val();
							var daterange = jQuery('input[name="daterange"]').val();
							//var checkmodule = jQuery('#sourceModule').val();
							var mode = 'timeline';
							var searchtext = jQuery("#searchbox").val();
							if(searchtext == ""){
								jQuery("#hiddensearchval").val('');
							}
							var searchid = jQuery("#hiddensearchval").val();
							getUserDataTimeline(modulenm,parent,action,user_id,daterange,mode,searchtext);	
							
			        	});

				        jQuery('#tbltimeline tbody').on('click', 'tr', function () {
							var index = jQuery(this).index();
							for (var i = 0; i < markers.length; i++) {
								markers[index].openPopup();
							}
						}); 
					});
					function setText(element){
					    var recordid = $(element).val();	
					    var value = $(element).text();
					    $("#searchbox").val(value);
					    $("#result ul").empty();

					    //used when go in timeline view
					    $('#hiddensearchval').val(recordid);

					}
					function getUserDataServerSide(modulename,parentmodule,action,userid,daterange,mode,searchvalue){
						
						var url = "index.php?module="+modulename+"&parent="+parentmodule+"&action="+action+"&mode="+mode+"&user_id="+userid+"&daterange="+daterange+"&searchvalue="+searchvalue;

						if ( $.fn.dataTable.isDataTable( '#example1' ) ) {
						   table.destroy();
						}

						table = jQuery('#example1').DataTable({
							'paging'      : true,
							'lengthChange': false,
							'searching'   : false,
							'ordering'    : false,
							'info'        : true,
							'autoWidth'   : false,
							'pageLength' : 20,
							'processing': true,
							'serverSide': true,
							'serverMethod': 'post',
							'ajax': {
							  'url':url
							},
							"columns": [
								{ "data": "username" },
								{ "data": "record_label" },
								{ "data": "datetime" },
								{ "data": "action" },
								{ "data": "view_details" }
							],
							"drawCallback": function( settings ) {
								
								var result2 = settings.json.data;
								if(result2.length != 0){
									for (var i = 0; i < markers.length; i++) {
										myMap.removeLayer(markers[i]);
									}
									
									markers = [];
									var target = L.latLng(result2[0].lat,result2[0].lng);

									// Set map's center to target with zoom 14.
									myMap.setView(target, 20);

									jQuery.each(result2, function(index, item) {
										i =index;
										marker = new L.marker([item.lat, item.lng])
									    .bindPopup(item.label)
									    .addTo(myMap);
									    marker.on('mouseover', function(event){
										  marker.openPopup();
										});
										markers[i] = marker;
									});
								}
							}
		        		});	
					}

					function getUserDataTimeline(modulename,parentmodule,action,userid,daterange,mode,searchvalue){

						var url = "index.php?module="+modulename+"&parent="+parentmodule+"&action="+action+"&mode="+mode+"&user_id="+userid+"&daterange="+daterange+"&searchvalue="+searchvalue;

						if ( $.fn.dataTable.isDataTable( '#tbltimeline' ) ) {
						   table1.destroy();
						}

						table1 = jQuery('#tbltimeline').DataTable({
							'paging'      : true,
							'lengthChange': false,
							'searching'   : false,
							'ordering'    : false,
							'info'        : true,
							'autoWidth'   : false,
							'pageLength' : 10,
							'processing': true,
							'serverSide': true,
							'serverMethod': 'post',
							'ajax': {
							  'url':url
							},
							"columns": [
							{ "data": "activitytime" },
							{ "data": "moduleimg" },
							{ "data": "action" },
							{ "data": "modifiedby" },
							],
							"drawCallback": function( settings ) {
								var result2 = settings.json.data;
								if(result2.length != 0){

									for (var i = 0; i < markers.length; i++) {
										myMap.removeLayer(markers[i]);
									}
									
									markers = [];
									var target = L.latLng(result2[0].lat,result2[0].lng);

									// Set map's center to target with zoom 14.
									myMap.setView(target, 20);

									//var tabledata = [];
									jQuery.each(result2, function(index, item) {
										i =index;
										marker = new L.marker([item.lat, item.lng])
									    .bindPopup(item.label)
									    .addTo(myMap);
									    marker.on('mouseover', function(event){
										  marker.openPopup();
										});
										markers[i] = marker;
										
									});
								}
							}
					    });
					}

				</script>
	   {/literal}
   {/if}
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="layouts/v7/modules/CTMobileSettings/CustomStyle.css" rel="stylesheet" type="text/css">
<button type="button" class="btn btn-info pull-right" style="background:#287DF2 !important;" onclick='window.location.href="{CTMobileSettings_Module_Model::$CTMOBILE_DETAILVIEW_URL}"'>{vtranslate('Go To CRMTiger Settings',$MODULE)}</button>
<label style="font:24px solid black;margin-left:20px;margin-top:10px;">{vtranslate("CRMTiger Mobile Apps - Team Activities",$MODULE)}</label>
<hr>
<div class="">
  <div class="container-fluid">
    <div class="row-fluid">
    
	<div class="main_div">
		<div class="box2">
			<form class="form-horizontal recordEditView" id="EditView" name="edit" method="post" action="index.php" enctype="multipart/form-data">
				<div class="row">
				<input type="hidden" name="module" value="CTMobileSettings" />
                <input type="hidden" name="action" value="getUserLatLong" />
                <input type="hidden" name="mode" value="ExportData" />
				<!-- code by sapna -->
					<div class="search-links-container col-md-3 col-lg-3 hidden-sm">
					<div class="search-link hidden-xs" style="margin-top:0px; height:30px;">
						<span class="fa fa-search" aria-hidden="true"></span>
						<input type="hidden" id="searchtextvalue" name="searchtextvalue"/>
						<input type="text" class="keyword-input" name="searchbox" id="searchbox" placeholder="{vtranslate('LBL_TYPE_SEARCH')} {vtranslate('LBL_RECORD')}" 	style="height:24px;color:black;"/>
		     				 <div id="result"><ul></ul></div>
					
					</div>
					</div>
					<!-- code by sapna end-->
					<div class=" col-lg-2 col-md-2 col-sm-2">
					<select data-fieldname="routeUser" id="routeUser" data-fieldtype="picklist" class="inputElement select2" style="margin-left:1%;">
					<option value="">{vtranslate('All user',$MODULE)}</option>
					{foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$ROUTE_USER}
					<option value="{$PICKLIST_VALUE['id']}">{$PICKLIST_VALUE['name']}</option>
					{/foreach}
					</select>
					</div>

					<div class=" col-lg-2 col-md-2 col-sm-2">
					<input type="text" class="inputElement" id="daterange" name="daterange" value="" style="display: inline!important;margin-left:1%;"/>
					</div>

					<div class=" col-lg-1 col-md-1 col-sm-1">
						<button type="button" class="btn btn-success btn-sm" id="searchbtn">Search</button>
					</div>

					<div class=" col-lg-4 col-md-4 col-sm-4">
					<button type='button' class='btn btn-success timeLineViewButton' name="view" value="csv" style="display: inline!important;margin-left:1%;" title="{vtranslate('TimeLine View', $MODULE)}"><img src="layouts/v7/modules/CTMobileSettings/icon/timeline-icon.png"/></button>

					<button type='button' class='btn btn-success listViewButton' name="view" value="csv" style="display: inline!important;margin-left:1%;" title="{vtranslate('List View', $MODULE)}"><img src="layouts/v7/modules/CTMobileSettings/icon/map-view.png"/></button>

					<button type='submit' class='btn btn-success saveButton' name="type" value="csv" style="display: inline!important;margin-left:1%;" title="{vtranslate('EXPORT AS CSV', $MODULE)}"><img src="layouts/v7/modules/CTMobileSettings/icon/export-csv.png"/></button>
		            <button type='submit' class='btn btn-success saveButton' name="type" value="excel" style="display: inline!important;margin-left:1%;" title="{vtranslate('EXPORT AS EXCEL', $MODULE)}"><img src="layouts/v7/modules/CTMobileSettings/icon/export-excel.png"/></button>
		            </div>
				</div>
			</form>
			<input type="hidden" name="hiddensearchval" id="hiddensearchval" value="" />
		  	<div class="row" id="listmapUserRoute">
		  		<div class="col-lg-6 col-md-6 col-sm-6" id="listuserroute">
		  			<div class="listtimeline">
		  				<table id="tbltimeline" style="width: 100%;">
		  					<thead style="display: none;">
		                        <tr>
		                        	<th style="width:25%;"></th>
		                        	<th style="width:25%;"></th>
		                        	<th style="width:30%;"></th>
		                        	<th style="width:10%;"></th>
		                        </tr>
		                    </thead>
		                    <tbody>
		                    </tbody>
		  				</table>
		  			</div>
		  			<div class="listtableview">
		                <table id="example1" class='table table-bordered table-striped'>
		                    <thead>
		                        <tr><th style="width:25%;"> {vtranslate('User Name','CTMobileSettings')} </th><th style="width:25%;"> {vtranslate('Customer','CTMobileSettings')} </th><th style="width:30%;"> {vtranslate('Date Time','CTMobileSettings')} </th><th style="width:10%;"> {vtranslate('Action','CTMobileSettings')} </th><th style="width:10%;"></th></tr>
		                    </thead>
		                    <tbody>
		                    </tbody>
		                </table>
		            </div>
	                <div class="ctlefthideessentials-toggle" title="Listing Panel Show/Hide">
						<span class="essentials-toggle-ma
						rker fa cursorPointer fa-chevron-left"></span>
					</div>
	            </div>
	            <div class="col-lg-6 col-md-6 col-sm-6" id="mapuserroute">	

					<div class="ctrighthideessentials-toggle" title="Map Panel Show/Hide">
						<span class="essentials-toggle-marker fa cursorPointer fa-chevron-right"></span>
					</div>   		                   	
					<div id='map' style="width:100%;min-height: 730px; margin: 0; padding: 0; margin-top: 10px;"></div>
				</div>
            </div>  			
		</div>
	</div>
	</div>
	</div>
	</div>
	
