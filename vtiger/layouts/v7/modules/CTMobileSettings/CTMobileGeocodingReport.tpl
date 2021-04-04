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
<script src="layouts/v7/modules/CTMobileSettings/resources/highcharts.js"></script>
<script src="layouts/v7/modules/CTMobileSettings/resources/exporting.js"></script>
<script src="layouts/v7/modules/CTMobileSettings/resources/export-data.js"></script>
<div class="row">
	<div style="float:right;margin-right:5%;">
		<button type="button" class="btn btn-info pull-right" style="background:#287DF2 !important;" onclick='window.location.href="{CTMobileSettings_Module_Model::$CTMOBILE_DETAILVIEW_URL}"'>{vtranslate('Go To CRMTiger Settings',$MODULE)}</button>
	</div>
</div>
<hr>
<div id="Contacts" style="min-width:45%; height: 400px; max-width: 45%; margin: 0 auto;float:left;"></div>
<div id="Leads" style="min-width:45%; height: 400px; max-width: 45%; margin: 0 auto;float:right;"></div>
<div id="Accounts" style="min-width:45%; height: 400px; max-width: 45%; margin: 0 auto;float:left;"></div>
<div id="Calendar" style="min-width:45%; height: 400px; max-width: 45%; margin: 0 auto;float:right;"></div>

<input type="hidden" name="Contacts_total" value="{$GEOCODING_REPORT['Contacts']['total']}"/>
<input type="hidden" name="Contacts_geocoded" value="{$GEOCODING_REPORT['Contacts']['geocoded']}"/>
<input type="hidden" name="Contacts_nongeocoded" value="{$GEOCODING_REPORT['Contacts']['nongeocoded']}"/>
<input type="hidden" name="Contacts_pending" value="{$GEOCODING_REPORT['Contacts']['pending']}"/>
<input type="hidden" name="Contacts_nonAddress" value="{$GEOCODING_REPORT['Contacts']['nonAddress']}"/>

<input type="hidden" name="Leads_total" value="{$GEOCODING_REPORT['Leads']['total']}"/>
<input type="hidden" name="Leads_geocoded" value="{$GEOCODING_REPORT['Leads']['geocoded']}"/>
<input type="hidden" name="Leads_nongeocoded" value="{$GEOCODING_REPORT['Leads']['nongeocoded']}"/>
<input type="hidden" name="Leads_pending" value="{$GEOCODING_REPORT['Leads']['pending']}"/>
<input type="hidden" name="Leads_nonAddress" value="{$GEOCODING_REPORT['Leads']['nonAddress']}"/>

<input type="hidden" name="Accounts_total" value="{$GEOCODING_REPORT['Accounts']['total']}"/>
<input type="hidden" name="Accounts_geocoded" value="{$GEOCODING_REPORT['Accounts']['geocoded']}"/>
<input type="hidden" name="Accounts_nongeocoded" value="{$GEOCODING_REPORT['Accounts']['nongeocoded']}"/>
<input type="hidden" name="Accounts_pending" value="{$GEOCODING_REPORT['Accounts']['pending']}"/>
<input type="hidden" name="Accounts_nonAddress" value="{$GEOCODING_REPORT['Accounts']['nonAddress']}"/>

<input type="hidden" name="Calendar_total" value="{$GEOCODING_REPORT['Calendar']['total']}"/>
<input type="hidden" name="Calendar_geocoded" value="{$GEOCODING_REPORT['Calendar']['geocoded']}"/>
<input type="hidden" name="Calendar_nongeocoded" value="{$GEOCODING_REPORT['Calendar']['nongeocoded']}"/>
<input type="hidden" name="Calendar_pending" value="{$GEOCODING_REPORT['Calendar']['pending']}"/>
<input type="hidden" name="Calendar_nonAddress" value="{$GEOCODING_REPORT['Calendar']['nonAddress']}"/>
{literal}
<script type='text/javascript'>
var Contacts_total = jQuery('input[name="Contacts_total"]').val();
var Contacts_geocoded = jQuery('input[name="Contacts_geocoded"]').val();
var Contacts_nongeocoded = jQuery('input[name="Contacts_nongeocoded"]').val();
var Contacts_pending = jQuery('input[name="Contacts_pending"]').val();
var Contacts_nonAddress = jQuery('input[name="Contacts_nonAddress"]').val();
var Contacts_geocoded_percentage = (Contacts_geocoded/Contacts_total)*100;
var Contacts_nongeocoded_percentage = (Contacts_nongeocoded/Contacts_total)*100;
var Contacts_pending_percentage = (Contacts_pending/Contacts_total)*100;
var Contacts_nonAddress_percentage = (Contacts_nonAddress/Contacts_total)*100;

var Leads_total = jQuery('input[name="Leads_total"]').val();
var Leads_geocoded = jQuery('input[name="Leads_geocoded"]').val();
var Leads_nongeocoded = jQuery('input[name="Leads_nongeocoded"]').val();
var Leads_pending = jQuery('input[name="Leads_pending"]').val();
var Leads_nonAddress = jQuery('input[name="Leads_nonAddress"]').val();
var Leads_geocoded_percentage = (Leads_geocoded/Leads_total)*100;
var Leads_nongeocoded_percentage = (Leads_nongeocoded/Leads_total)*100;
var Leads_pending_percentage = (Leads_pending/Leads_total)*100;
var Leads_nonAddress_percentage = (Leads_nonAddress/Leads_total)*100;

var Accounts_total = jQuery('input[name="Accounts_total"]').val();
var Accounts_geocoded = jQuery('input[name="Accounts_geocoded"]').val();
var Accounts_nongeocoded = jQuery('input[name="Accounts_nongeocoded"]').val();
var Accounts_pending = jQuery('input[name="Accounts_pending"]').val();
var Accounts_nonAddress = jQuery('input[name="Accounts_nonAddress"]').val();
var Accounts_geocoded_percentage = (Accounts_geocoded/Accounts_total)*100;
var Accounts_nongeocoded_percentage = (Accounts_nongeocoded/Accounts_total)*100;
var Accounts_pending_percentage = (Accounts_pending/Accounts_total)*100;
var Accounts_nonAddress_percentage = (Accounts_nonAddress/Accounts_total)*100;

var Calendar_total = jQuery('input[name="Calendar_total"]').val();
var Calendar_geocoded = jQuery('input[name="Calendar_geocoded"]').val();
var Calendar_nongeocoded = jQuery('input[name="Calendar_nongeocoded"]').val();
var Calendar_pending = jQuery('input[name="Calendar_pending"]').val();
var Calendar_nonAddress = jQuery('input[name="Calendar_nonAddress"]').val();
var Calendar_geocoded_percentage = (Calendar_geocoded/Calendar_total)*100;
var Calendar_nongeocoded_percentage = (Calendar_nongeocoded/Calendar_total)*100;
var Calendar_pending_percentage = (Calendar_pending/Calendar_total)*100;
var Calendar_nonAddress_percentage = (Calendar_nonAddress/Calendar_total)*100;

Highcharts.setOptions({
     colors: ['#008000', '#FF0000','#00FFFF','#808080']
    });
Highcharts.chart('Contacts', {
    chart: {
        plotBackgroundColor: null,
        plotBorderWidth: null,
        plotShadow: false,
        type: 'pie'
    },
    title: {
        text: 'Contacts <br/> <span> Total = '+Contacts_total+'</span>'
    },
    legend: {
		enabled: true,
		floating: true,
		verticalAlign: 'xbottom',
		align:'right',
		layout: 'vertical',
		
		labelFormatter : function() { 
			
			return '<span style=\"color:'+this.color+'\"> Hello '; 
		}

	},  
    tooltip: {
        pointFormat: '<b> {point.calculation} = {point.percentage:.1f}%</b>'
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: true,
                format: '{point.percentage:.1f} %',
                style: {
                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                }
            }
        }
    },
    series: [{
        name: '',
        colorByPoint: true,
        data: [{
            name: 'Geocoded',
            y: Contacts_geocoded_percentage,
            calculation : Contacts_geocoded + '/'+ Contacts_total,
        }, {
            name: 'Geocode Not Available',
            y: Contacts_nongeocoded_percentage,
		    calculation : Contacts_nongeocoded + '/'+ Contacts_total,
        }, {
            name: 'Pending Geocoding',
            y: Contacts_pending_percentage,
		    calculation : Contacts_pending + '/'+ Contacts_total,
        },{
            name: 'Records Without Address',
            y: Contacts_nonAddress_percentage,
		    calculation : Contacts_nonAddress + '/'+ Contacts_total,
        }]
    }]
});

jQuery('#Contacts').find('.highcharts-title').filter(function() {
	jQuery(this).find('tspan').filter(function(){
		if(jQuery(this).text() != 'Contacts'){
			jQuery(this).attr('x','100');
		}
	});	
});
Highcharts.chart('Leads', {
    chart: {
        plotBackgroundColor: null,
        plotBorderWidth: null,
        plotShadow: false,
        type: 'pie'
    },
    title: {
        text: 'Leads <br/> <span> Total = '+Leads_total+'</span>'
    },
    tooltip: {
       pointFormat: '<b> {point.calculation} = {point.percentage:.1f}%</b>'
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: true,
                format: '{point.percentage:.1f} %',
                style: {
                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                }
            }
        }
    },
    series: [{
        name: '',
        colorByPoint: true,
        data: [{
            name: 'Geocoded',
            y: Leads_geocoded_percentage,
            calculation : Leads_geocoded + '/'+ Leads_total,
        }, {
            name: 'Geocode Not Available',
            y: Leads_nongeocoded_percentage,
            calculation : Leads_nongeocoded + '/'+ Leads_total,
        }, {
            name: 'Pending Geocoding',
            y: Leads_pending_percentage,
            calculation : Leads_pending + '/'+ Leads_total,
        },{
            name: 'Records Without Address',
            y: Leads_nonAddress_percentage,
		    calculation : Leads_nonAddress + '/'+ Leads_total,
        }]
    }]
});
jQuery('#Leads').find('.highcharts-title').filter(function() {
	jQuery(this).find('tspan').filter(function(){
		if(jQuery(this).text() != 'Leads'){
			jQuery(this).attr('x','100');
		}
	});	
});
Highcharts.chart('Accounts', {
    chart: {
        plotBackgroundColor: null,
        plotBorderWidth: null,
        plotShadow: false,
        type: 'pie'
    },
    title: {
        text: 'Organizations <br/> <span> Total = '+Accounts_total+'</span>'
    },
    tooltip: {
       pointFormat: '<b> {point.calculation} = {point.percentage:.1f}%</b>'
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: true,
                format: '{point.percentage:.1f} %',
                style: {
                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                }
            }
        }
    },
    series: [{
        name: '',
        colorByPoint: true,
        data: [{
            name: 'Geocoded',
            y: Accounts_geocoded_percentage,
            calculation : Accounts_geocoded + '/'+ Accounts_total,
        }, {
            name: 'Geocode Not Available',
            y: Accounts_nongeocoded_percentage,
            calculation : Accounts_nongeocoded + '/'+ Accounts_total,
        }, {
            name: 'Pending Geocoding',
            y: Accounts_pending_percentage,
            calculation : Accounts_pending + '/'+ Accounts_total,
        },{
            name: 'Records Without Address',
            y: Accounts_nonAddress_percentage,
		    calculation : Accounts_nonAddress + '/'+ Accounts_total,
        }]
    }]
});
jQuery('#Accounts').find('.highcharts-title').filter(function() {
	jQuery(this).find('tspan').filter(function(){
		if(jQuery(this).text() != 'Organizations'){
			jQuery(this).attr('x','100');
		}
	});	
});
Highcharts.chart('Calendar', {
    chart: {
        plotBackgroundColor: null,
        plotBorderWidth: null,
        plotShadow: false,
        type: 'pie'
    },
    title: {
        text: 'Calendar <br/> <span> Total = '+Calendar_total+'</span>'
    },
    tooltip: {
       pointFormat: '<b> {point.calculation} = {point.percentage:.1f}%</b>'
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: true,
                format: '{point.percentage:.1f} %',
                style: {
                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                }
            }
        }
    },
    series: [{
        name: '',
        colorByPoint: true,
        data: [{
            name: 'Geocoded',
            y: Calendar_geocoded_percentage,
            calculation : Calendar_geocoded + '/'+ Calendar_total,
        }, {
            name: 'Geocode Not Available',
            y: Calendar_nongeocoded_percentage,
            calculation : Calendar_nongeocoded + '/'+ Calendar_total,
        }, {
            name: 'Pending Geocoding',
            y: Calendar_pending_percentage,
            calculation : Calendar_pending + '/'+ Calendar_total,
        },{
			name: 'Records Without Address',
            y: Calendar_nonAddress_percentage,
		    calculation : Calendar_nonAddress + '/'+ Calendar_total,
        }]
    }]
});
jQuery('#Calendar').find('.highcharts-title').filter(function() {
	jQuery(this).find('tspan').filter(function(){
		if(jQuery(this).text() != 'Calendar'){
			jQuery(this).attr('x','100');
		}
	});	
});
</script>
{/literal}
