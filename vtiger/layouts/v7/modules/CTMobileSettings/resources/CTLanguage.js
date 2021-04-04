 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
jQuery.Class("CTMobileSettings_Language_Js",{
    editInstance:false,
    getInstance: function(){
        if(CTMobileSettings_Language_Js.editInstance == false){
            var instance = new CTMobileSettings_Settings_Js();
            CTMobileSettings_Language_Js.editInstance = instance;
            return instance;
        }
        return CTMobileSettings_Language_Js.editInstance;
    }
},{
    updatedBlockSequence : {},    
    registerSelectModuleChange:function() {
        jQuery("#ct_language_settings").on("change","#ct_language", function(e) {
            var progressIndicatorElement = jQuery.progressIndicator({
              'position' : 'html',
              'blockInfo' : {
               'enabled' : true
              }
             });
            var ct_language=jQuery(this).val();
            var ct_section=jQuery('[name="ct_section"]').val();
            var selectedFields=jQuery("#ct_language_settings").find("#selectedFields");
            if(ct_language !='' && ct_section !='') {
                var params= {
                    "type": "POST",
                    "module" : "CTMobileSettings",
                    "view" :"CTLanguageAjax",
                    "ctlanguage" : ct_language,
                    "ctlanguage_section":ct_section,
                };
                AppConnector.request(params).then(
                    function(data) {
                        progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                        selectedFields.html(data.result);
                        app.changeSelectElementView(selectedFields);
                    }
                )
            }else {
                progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                selectedFields.html('');
            }
        });

        jQuery("#ct_language_settings").on("change","#ct_section", function(e) {
            var progressIndicatorElement = jQuery.progressIndicator({
              'position' : 'html',
              'blockInfo' : {
               'enabled' : true
              }
             });
            var ct_section=jQuery(this).val();
            var ct_language=jQuery('[name="ct_language"]').val();
            var selectedFields=jQuery("#ct_language_settings").find("#selectedFields");
            if(ct_language !='' && ct_section !='') {
                var params= {
                    "type": "POST",
                    "module" : "CTMobileSettings",
                    "view" :"CTLanguageAjax",
                    "ctlanguage" : ct_language,
                    "ctlanguage_section":ct_section,
                };
                AppConnector.request(params).then(
                    function(data) {
                        progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                        selectedFields.html(data.result);
                        app.changeSelectElementView(selectedFields);
                    }
                )
            }else {
                progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                selectedFields.html('');
            }
        });
    },

    registerSaveSettings:function() {
        jQuery("#ct_language_settings").on("click",".btnSaveSettings", function(e) {
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
                       title : "Language Fields Save Successfully",
                       text: 'Language Field Save Successfully',
                       animation: 'show',
                       type: 'info'
                    };
                    Vtiger_Helper_Js.showPnotify(params);
                }
            );
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
        this.registerSelectModuleChange();
        this.registerSaveSettings(); 
        this.registerAppTriggerEvent();     
    }
});


jQuery(document).ready(function () {
    var instance = new CTMobileSettings_Language_Js();
    instance.registerEvents();
});
