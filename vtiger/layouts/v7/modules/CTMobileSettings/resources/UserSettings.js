 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
jQuery.Class("CTMobileSettings_UserSettings_Js",{
    editInstance:false,
    getInstance: function(){
        if(CTMobileSettings_UserSettings_Js.editInstance == false){
            var instance = new CTMobileSettings_Settings_Js();
            CTMobileSettings_UserSettings_Js.editInstance = instance;
            return instance;
        }
        return CTMobileSettings_UserSettings_Js.editInstance;
    }
},{ 
    registerSaveSettings:function() {
        jQuery(".btnSaveCalllogUser").on("click", function(e) {
            var progressIndicatorElement = jQuery.progressIndicator({
              'position' : 'html',
              'blockInfo' : {
               'enabled' : true
              }
             });
            form = jQuery(this).closest('form');
            var saveUrl = form.serializeFormData();
            AppConnector.request(saveUrl).then(
                function(data) {
                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                    var params = {
                       title : app.vtranslate("Calllog Users Save Successfully"),
                       text: app.vtranslate("Calllog Users Save Successfully"),
                       animation: 'show',
                       type: 'info'
                    };
                    Vtiger_Helper_Js.showPnotify(params);
                }
            );
        });

        jQuery(".btnSaveDistanceUnit").on("click", function(e) {
            var distanceUnit = jQuery('#distanceUnit').val();
            var route_users = jQuery('#route_users').val();
            app.helper.showProgress();
            var params= {
                "module" : "CTMobileSettings",
                "action" :"SaveRouteGeneralSettings",
                "distanceUnit" : distanceUnit,
                "route_users"  : route_users
            };
            AppConnector.request(params).then(function(data){
                app.helper.hideProgress();
                var params = {
                   title : app.vtranslate('Route General Settings Save Successfully'),
                   text: app.vtranslate("Route General Settings Save Successfully"),
                   animation: 'show',
                   type: 'info'
                };
                Vtiger_Helper_Js.showPnotify(params);
            });
        });

        jQuery(".btnSaveStatus").on("click", function(e) {
            app.helper.showProgress();
            form = jQuery('#route_status_settings').find('#RouteStatus-Settings');
            var saveUrl = form.serializeFormData();

            AppConnector.request(saveUrl).then(function(data){
                app.helper.hideProgress();
                var params = {
                   title : app.vtranslate('Route Status Settings Save Successfully'),
                   text: app.vtranslate("Route Status Settings Save Successfully"),
                   animation: 'show',
                   type: 'info'
                };
                Vtiger_Helper_Js.showPnotify(params);
            });
        });

        jQuery(".btnSaveLiveUser").on("click", function(e) {
            var progressIndicatorElement = jQuery.progressIndicator({
              'position' : 'html',
              'blockInfo' : {
               'enabled' : true
              }
             });
            form = jQuery('#live_user_settings').find('#LiveUser-Settings');
            var saveUrl = form.serializeFormData();
            AppConnector.request(saveUrl).then(
                function(data) {
                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                    var params = {
                       title : app.vtranslate("LiveTracking Users Save Successfully"),
                       text: app.vtranslate("LiveTracking Users Save Successfully"),
                       animation: 'show',
                       type: 'info'
                    };
                    Vtiger_Helper_Js.showPnotify(params);
                }
            );
        });

        jQuery(".btnSaveAccessUser").on("click", function(e) {
            var progressIndicatorElement = jQuery.progressIndicator({
              'position' : 'html',
              'blockInfo' : {
               'enabled' : true
              }
             });
            form = jQuery('#access_user_settings').find('#AccessUser-Settings');
            var saveUrl = form.serializeFormData();
            AppConnector.request(saveUrl).then(
                function(data) {
                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                    var params = {
                       title : app.vtranslate('CTMobile Access Users Save Successfully'),
                       text: app.vtranslate("CTMobile Access Users Save Successfully"),
                       animation: 'show',
                       type: 'info'
                    };
                    Vtiger_Helper_Js.showPnotify(params);
                }
            );
        });

        jQuery(".btnSaveModuleManagement").on("click", function(e) {
            form = jQuery('#modules_management_form');
            var modules_management_module = jQuery('#modules_management_module').val();
            if(modules_management_module == null){
                var params = {
                   title : app.vtranslate('Select atleast one Module'),
                   text: app.vtranslate("Select atleast one Module"),
                   animation: 'show',
                   type: 'info'
                };
                Vtiger_Helper_Js.showPnotify(params); 
            }else{
              var progressIndicatorElement = jQuery.progressIndicator({
                'position' : 'html',
                'blockInfo' : {
                 'enabled' : true
                }
               });
              var saveUrl = form.serializeFormData();
              AppConnector.request(saveUrl).then(
                  function(data) {
                      progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                      var params = {
                         title : app.vtranslate('Mobile Apps Modules Saved Successfully'),
                         text: app.vtranslate("Mobile Apps Modules Saved Successfully"),
                         animation: 'show',
                         type: 'info'
                      };
                      Vtiger_Helper_Js.showPnotify(params);
                  }
              );
            }
        });

        jQuery(".btnSavePremiumFeatureSettings").on("click", function(e) {
            var progressIndicatorElement = jQuery.progressIndicator({
              'position' : 'html',
              'blockInfo' : {
               'enabled' : true
              }
             });
            form = jQuery('#premium_feature_settings_form');
            var saveUrl = form.serializeFormData();
            AppConnector.request(saveUrl).then(
                function(data) {
                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                    var params = {
                       title : app.vtranslate('Premium Features Save Successfully'),
                       text: app.vtranslate("Premium Features Save Successfully"),
                       animation: 'show',
                       type: 'info'
                    };
                    Vtiger_Helper_Js.showPnotify(params);
                }
            );
        });

        jQuery(".btnSaveModule").on("click", function(e) {
            var moduleList = jQuery('#timetracking_moduleFields').val();
            app.helper.showProgress();
            var params= {
                "module" : "CTMobileSettings",
                "action" :"SaveTimeTrackerSettings",
                "moduleList" : moduleList,
            };
            AppConnector.request(params).then(function(data){
                app.helper.hideProgress();
                var params = {
                   title : app.vtranslate('CTMobile Time Tracking Modules Save Successfully'),
                   text: app.vtranslate("CTMobile Time Tracking Modules Save Successfully"),
                   animation: 'show',
                   type: 'info'
                };
                Vtiger_Helper_Js.showPnotify(params);
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

    registerEventsforShowHide : function(){
        jQuery(".user_settings").bootstrapSwitch();
        jQuery(".premium_feature_button").bootstrapSwitch();

        jQuery('.premium_feature_button').on('switchChange.bootstrapSwitch',function(e){
            var currentElement = jQuery(e.currentTarget);
            if (currentElement.bootstrapSwitch('state')) {
              currentElement.val('1');
            }else{
              currentElement.val('0');
            }
        });

        jQuery('.user_settings').each(function(){
            var id =  jQuery(this).attr('id');
            var div = id+'_div';
            if(div == 'location_tracking_div'){
              if (this.checked) {
                jQuery('.'+div).show();
              }else{
                jQuery('.'+div).hide();
              }
            }else{
              if (this.checked) {
                jQuery('#'+div).show();
              }else{
                jQuery('#'+div).hide();
              }
            }
        });

        jQuery('.user_settings').on('switchChange.bootstrapSwitch',function(e){
            var currentElement = jQuery(e.currentTarget);
            var id =  currentElement.attr('id');
            var div = id+'_div';
            if(div == 'location_tracking_div'){
              if (currentElement.bootstrapSwitch('state')) {
                jQuery('.'+div).show();
                value = '1';
              }else{
                jQuery('.'+div).hide();
                value = '0';
              }
            }else{
              if (currentElement.bootstrapSwitch('state')) {
                jQuery('#'+div).show();
                value = '1';
              }else{
                jQuery('#'+div).hide();
                value = '0';
              }
            }

            var params= {
                "module" : "CTMobileSettings",
                "action" :"SaveAjaxUserSettings",
                "fieldname" : id,
                "fieldvalue"  : value
            };
            AppConnector.request(params).then(function(data){
                
            });
        });
    },
    /**
     * Function which will handle the registrations for the elements
     */
    registerEvents : function() {
        this.registerSaveSettings(); 
        this.registerAppTriggerEvent();  
        this.registerEventsforShowHide();
    }
});


jQuery(document).ready(function () {
    var instance = new CTMobileSettings_UserSettings_Js();
    instance.registerEvents();

});
