 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
jQuery.Class("CTMobileSettings_RouteAnalytics_Js",{
    editInstance:false,
    getInstance: function(){
        if(CTMobileSettings_RouteAnalytics_Js.editInstance == false){
            var instance = new CTMobileSettings_Settings_Js();
            CTMobileSettings_RouteAnalytics_Js.editInstance = instance;
            return instance;
        }
        return CTMobileSettings_RouteAnalytics_Js.editInstance;
    }
},{ 
    registerDateRangeSettings:function() {

            table = jQuery('#example1').DataTable({
              'paging'      : true,
              'lengthChange': true,
              'searching'   : false,
              'ordering'    : true,
              'info'        : true,
              'autoWidth'   : false
            });
           var params = {};
           params['module'] = 'CTMobileSettings';
           params['action'] = 'getListRoute';
           params['mode'] = 'gettimezone';
           AppConnector.request(params).then(
            function(data) {
              if(data){
                var Today = data.result.today; 
                var Yesterday = data.result.yesterday;
                var last7days = data.result.last7days;
                var last30days = data.result.last30days;
                var monthStartDay = data.result.monthStartDay;
                var monthEndDay = data.result.monthEndDay;
                var yearStartDay = data.result.yearStartDay;
                var yearEndDay = data.result.yearEndDay;
                var lastyearStartDay = data.result.lastyearStartDay;
                var lastyearEndDay = data.result.lastyearEndDay;

                 var dateValue = last7days + ' - ' + Today;
                 jQuery('input[name="listdaterange"]').val(dateValue);
                 jQuery('input[name="listdaterange"]').daterangepicker({    locale: {
                        format: 'YYYY/MM/DD'
                      },
                      ranges: {
                         'Today': [Today,Today],
                         'Yesterday': [Yesterday, Yesterday],
                         'Last 7 Days': [last7days, Today],
                         'Last 30 Days': [last30days, Today],
                         'This Month': [monthStartDay, monthEndDay],
                         'This Year': [yearStartDay, yearEndDay],
                         'Last Year': [lastyearStartDay, lastyearEndDay]
                      }
                  });

                 var dateValue = last7days + ' - ' + Today;
                 jQuery('input[name="mapdaterange"]').val(dateValue);
                 jQuery('input[name="mapdaterange"]').daterangepicker({    locale: {
                        format: 'YYYY/MM/DD'
                      },
                      ranges: {
                         'Today': [Today,Today],
                         'Yesterday': [Yesterday, Yesterday],
                         'Last 7 Days': [last7days, Today],
                         'Last 30 Days': [last30days, Today],
                         'This Month': [monthStartDay, monthEndDay],
                         'This Year': [yearStartDay, yearEndDay],
                         'Last Year': [lastyearStartDay, lastyearEndDay]
                      }
                  });

                var listdaterange = jQuery('#listdaterange').val();
                var listUsers = jQuery('#listUsers').val();
                var params = {};
                params['module'] = 'CTMobileSettings';
                params['action'] = 'getListRoute';
                params['mode'] = 'getlist';
                params['listdaterange'] = listdaterange;
                params['listUsers'] = listUsers;
                var progressIndicatorElement = jQuery.progressIndicator({
                    'position' : 'html',
                    'blockInfo' : {
                        'enabled' : true
                    }
                });
                AppConnector.request(params).then(
                function(data) {
                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                    if(data){
                        var tabledata = JSON.parse(data.result);
                        table.rows().remove();
                        table.rows.add(
                           tabledata
                        ).draw(); 
                    }
                });

                var mapdaterange = jQuery('#mapdaterange').val();
                var mapUsers = jQuery('#mapUsers').val();
                var params = {};
                params['module'] = 'CTMobileSettings';
                params['action'] = 'getListRoute';
                params['mode'] = 'listRoute';
                params['mapdaterange'] = mapdaterange;
                params['mapUsers'] = mapUsers;
                var progressIndicatorElement = jQuery.progressIndicator({
                    'position' : 'html',
                    'blockInfo' : {
                        'enabled' : true
                    }
                });
                AppConnector.request(params).then(
                function(data) {
                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                    if(data){
                        $("#mapRoutes").empty().append(data.result);
                    } 
                });

                 
              }
            });

            $("#searchbox").keyup(function() {               
               var searchvalue = $('#searchbox').val();
               if (searchvalue == "") {
                    $("#result ul").empty();
               } else {
                    var listdaterange = jQuery('#listdaterange').val();
                    var listUsers = jQuery('#listUsers').val();
                    var postData = {
                    "module": app.getModuleName(),
                    "parent": app.getParentModuleName(),
                    "action": 'getListRoute',
                    "mode": "listRecord",
                    "listdaterange": listdaterange,
                    "listUsers": listUsers,
                    "searchtext":searchvalue
                    };
                
                   AppConnector.request(postData).then(
                        function(data) {
                
                        var len = data.result.length;
                        $("#result ul").empty();
                        for( var i = 0; i<len; i++){
                            $("#result ul").append("<li value='"+data.result[i]['id']+"'>"+data.result[i]['text']+"</li>");
                        }
                        // binding click event to li
                        $("#result li").bind("click",function(){
                            setText(this);
                        });
                    },function(error){
                         $("#result ul").empty();s
                    });
                }
            });

    },

    registerListRouteEvent : function(){

        jQuery('#listUsers,#listdaterange').on('change',function(){
            var listdaterange = jQuery('#listdaterange').val();
            var listUsers = jQuery('#listUsers').val();
            var params = {};
            params['module'] = 'CTMobileSettings';
            params['action'] = 'getListRoute';
            params['mode'] = 'getlist';
            params['listdaterange'] = listdaterange;
            params['listUsers'] = listUsers;
            var progressIndicatorElement = jQuery.progressIndicator({
                'position' : 'html',
                'blockInfo' : {
                    'enabled' : true
                }
            });
            AppConnector.request(params).then(
            function(data) {
                progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                if(data){
                    var tabledata = JSON.parse(data.result);
                    table.rows().remove();
                    table.rows.add(
                       tabledata
                    ).draw();
                } 
            });
        });

        jQuery('#mapUsers,#mapdaterange').on('change',function(){
            var mapdaterange = jQuery('#mapdaterange').val();
            var mapUsers = jQuery('#mapUsers').val();
            var params = {};
            params['module'] = 'CTMobileSettings';
            params['action'] = 'getListRoute';
            params['mode'] = 'listRoute';
            params['mapdaterange'] = mapdaterange;
            params['mapUsers'] = mapUsers;
            var progressIndicatorElement = jQuery.progressIndicator({
                'position' : 'html',
                'blockInfo' : {
                    'enabled' : true
                }
            });
            AppConnector.request(params).then(
            function(data) {
                progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                if(data){
                    $("#mapRoutes").empty().append(data.result);
                } 
            });
        });
    },

    registerAppTriggerEvent : function() {
        jQuery('.app-menu').removeClass('hide');
        var toggleAppMenu = function(type) {
            var appMenu = jQuery('.app-menu');
            var appNav = jQuery('.app-nav');
            appMenu.appendTo('#page');
            appMenu.css({
                'top' : appNav.offset().top + appNav.height(),
                'left' : 0
            });
            if(typeof type === 'undefined') {
                type = appMenu.is(':hidden') ? 'show' : 'hide';
            }
            if(type == 'show') {
                appMenu.show(200, function() {});
            } else {
                appMenu.hide(200, function() {});
            }
        };

        jQuery('.app-trigger, .app-icon, .app-navigator').on('click',function(e){
            e.stopPropagation();
            toggleAppMenu();
        });

        jQuery('html').on('click', function() {
            toggleAppMenu('hide');
        });

        jQuery(document).keyup(function (e) {
            if (e.keyCode == 27) {
                if(!jQuery('.app-menu').is(':hidden')) {
                    toggleAppMenu('hide');
                }
            }
        });

        jQuery('.app-modules-dropdown-container').hover(function(e) {
            var dropdownContainer = jQuery(e.currentTarget);
            jQuery('.dropdown').removeClass('open');
            if(dropdownContainer.length) {
                if(dropdownContainer.hasClass('dropdown-compact')) {
                    dropdownContainer.find('.app-modules-dropdown').css('top', dropdownContainer.position().top - 8);
                } else {
                    dropdownContainer.find('.app-modules-dropdown').css('top', '');
                }
                dropdownContainer.addClass('open').find('.app-item').addClass('active-app-item');
            }
        }, function(e) {
            var dropdownContainer = jQuery(e.currentTarget);
            dropdownContainer.find('.app-item').removeClass('active-app-item');
            setTimeout(function() {
                if(dropdownContainer.find('.app-modules-dropdown').length && !dropdownContainer.find('.app-modules-dropdown').is(':hover') && !dropdownContainer.is(':hover')) {
                    dropdownContainer.removeClass('open');
                }
            }, 500);

        });

        jQuery('.app-item').on('click', function() {
            var url = jQuery(this).data('defaultUrl');
            if(url) {
                window.location.href = url;
            }
        });

        jQuery(window).resize(function() {
            jQuery(".app-modules-dropdown").mCustomScrollbar("destroy");
            app.helper.showVerticalScroll(jQuery(".app-modules-dropdown").not('.dropdown-modules-compact'), {
                setHeight: $(window).height(),
                autoExpandScrollbar: true
            });
            jQuery('.dropdown-modules-compact').each(function() {
                var element = jQuery(this);
                var heightPer = parseFloat(element.data('height'));
                app.helper.showVerticalScroll(element, {
                    setHeight: $(window).height()*heightPer - 3,
                    autoExpandScrollbar: true,
                    scrollbarPosition: 'outside'
                });
            });
        });
        app.helper.showVerticalScroll(jQuery(".app-modules-dropdown").not('.dropdown-modules-compact'), {
            setHeight: $(window).height(),
            autoExpandScrollbar: true,
            scrollbarPosition: 'outside'
        });
        jQuery('.dropdown-modules-compact').each(function() {
            var element = jQuery(this);
            var heightPer = parseFloat(element.data('height'));
            app.helper.showVerticalScroll(element, {
                setHeight: $(window).height()*heightPer - 3,
                autoExpandScrollbar: true,
                scrollbarPosition: 'outside'
            });
        });
    },
    /**
     * Function which will handle the registrations for the elements
     */
    registerEvents : function() {
        this.registerAppTriggerEvent();  
        this.registerDateRangeSettings();
        this.registerListRouteEvent();
    }
});


jQuery(document).ready(function () {
    var instance = new CTMobileSettings_RouteAnalytics_Js();
    instance.registerEvents();

});


function setText(element){
    var recordid = $(element).val();
    var value = $(element).text();
    $("#searchbox").val(value);
    $("#result ul").empty();
    
    
    var listdaterange = jQuery('#listdaterange').val();
    var listUsers = jQuery('#listUsers').val();
    var params = {};
    params['module'] = 'CTMobileSettings';
    params['action'] = 'getListRoute';
    params['mode'] = 'getlist';
    params['listdaterange'] = listdaterange;
    params['listUsers'] = listUsers;
    params['recordid'] = recordid;
    var progressIndicatorElement = jQuery.progressIndicator({
        'position' : 'html',
        'blockInfo' : {
            'enabled' : true
        }
    });
    AppConnector.request(params).then(
    function(data) {
        progressIndicatorElement.progressIndicator({'mode' : 'hide'});
        if(data){
            var tabledata = JSON.parse(data.result);
            table.rows().remove();
            table.rows.add(
               tabledata
            ).draw(); 
        }
    });
    
} 



function deleteRoute(routeid) {
    var message = app.vtranslate('Are you sure want to delete this record ?');
    app.helper.showConfirmationBox({'message' : message}).then(function(data) {
        var params = {};
        params['module'] = 'CTMobileSettings';
        params['action'] = 'getListRoute';
        params['mode'] = 'deleteRoute';
        params['routeid'] = routeid;
        var progressIndicatorElement = jQuery.progressIndicator({
            'position' : 'html',
            'blockInfo' : {
                'enabled' : true
            }
        });
        AppConnector.request(params).then(
        function(data) {
            progressIndicatorElement.progressIndicator({'mode' : 'hide'});
            if(data){
                /*var tabledata = JSON.parse(data.result);
                table.rows().remove();
                table.rows.add(
                   tabledata
                ).draw();*/ 

                var listdaterange = jQuery('#listdaterange').val();
                var listUsers = jQuery('#listUsers').val();
                var params = {};
                params['module'] = 'CTMobileSettings';
                params['action'] = 'getListRoute';
                params['mode'] = 'getlist';
                params['listdaterange'] = listdaterange;
                params['listUsers'] = listUsers;
                var progressIndicatorElement2 = jQuery.progressIndicator({
                    'position' : 'html',
                    'blockInfo' : {
                        'enabled' : true
                    }
                });
                AppConnector.request(params).then(
                function(data) {
                    progressIndicatorElement2.progressIndicator({'mode' : 'hide'});
                    if(data){
                        var tabledata = JSON.parse(data.result);
                        table.rows().remove();
                        table.rows.add(
                           tabledata
                        ).draw(); 
                    }
                });
                var params = {
                   title : "Record deleted successfully",
                   text: 'Record deleted successfully',
                   animation: 'show',
                   type: 'info'
                };
                Vtiger_Helper_Js.showPnotify(params);
            }
        });
    });
}
