 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
jQuery.Class("CTMobileSettings_FieldSettings_Js",{
    editInstance:false,
    getInstance: function(){
        if(CTMobileSettings_FieldSettings.editInstance == false){
            var instance = new CTMobileSettings_Settings_Js();
            CTMobileSettings_FieldSettings_Js.editInstance = instance;
            return instance;
        }
        return CTMobileSettings_FieldSettings_Js.editInstance;
    }
},{
    updatedBlockSequence : {},    
    registerSelectModuleChange:function() {
        jQuery("#vcard_fields_settings").on("change","#vcard_module", function(e) {
            var progressIndicatorElement = jQuery.progressIndicator({
              'position' : 'html',
              'blockInfo' : {
               'enabled' : true
              }
             });
            var searchModule=jQuery(this).val();
            var selectedFields=jQuery("#vcard_fields_settings").find("#vcardselectedFields");
            if(searchModule !='') {
                var params= {
                    "type": "POST",
                    "module" : "CTMobileSettings",
                    "view" :"VcardFieldsAjax",
                    "vcard_module" : searchModule,
                    "data" : {}
                };
                AppConnector.request(params).then(
                    function(data) {
                        progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                        selectedFields.html(data.result);
                        app.changeSelectElementView(selectedFields);
                        //register all select2 Elements
                        app.showSelect2ElementView(selectedFields.find('select.select2'));
                    }
                )
            }else {
                progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                selectedFields.html('');
            }
        });

        jQuery("#asset_fields_settings").on("change","#asset_module", function(e) {
            var progressIndicatorElement = jQuery.progressIndicator({
              'position' : 'html',
              'blockInfo' : {
               'enabled' : true
              }
             });
            var searchModule=jQuery(this).val();
            var selectedFields=jQuery("#asset_fields_settings").find("#AssetSelectedFields");
            if(searchModule !='') {
                var params= {
                    "type": "POST",
                    "module" : "CTMobileSettings",
                    "view" :"AssetFieldsAjax",
                    "asset_module" : searchModule,
                    "data" : {}
                };
                AppConnector.request(params).then(
                    function(data) {
                        progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                        selectedFields.html(data.result);
                        app.changeSelectElementView(selectedFields);
                        //register all select2 Elements
                        app.showSelect2ElementView(selectedFields.find('select.select2'));
                    }
                )
            }else {
                progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                selectedFields.html('');
            }
        });

        jQuery("#signature_fields_settings").on("change","#signature_module", function(e) {
            var progressIndicatorElement = jQuery.progressIndicator({
              'position' : 'html',
              'blockInfo' : {
               'enabled' : true
              }
             });
            var searchModule=jQuery(this).val();
            var selectedFields=jQuery("#signature_fields_settings").find("#SignatureSelectedFields");
            if(searchModule !='') {
                var params= {
                    "type": "POST",
                    "module" : "CTMobileSettings",
                    "view" :"SignatureFieldsAjax",
                    "signature_module" : searchModule,
                    "data" : {}
                };
                AppConnector.request(params).then(
                    function(data) {
                        progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                        selectedFields.html(data.result);
                        app.changeSelectElementView(selectedFields);
                        //register all select2 Elements
                        app.showSelect2ElementView(selectedFields.find('select.select2'));
                    }
                )
            }else {
                progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                selectedFields.html('');
            }
        });


        jQuery("#display_fields_settings").on("change","#display_field_module,#userid", function(e) {
            var progressIndicatorElement = jQuery.progressIndicator({
              'position' : 'html',
              'blockInfo' : {
               'enabled' : true
              }
             });
            var searchModule=jQuery('#display_field_module').val();
            var selectedFields=jQuery("#display_fields_settings").find("#displaySelectedFields");
            var userid=jQuery('#userid').val();
            if(searchModule !='') {
                var params= {
                    "type": "POST",
                    "module" : "CTMobileSettings",
                    "view" :"DisplayFieldsAjax",
                    "display_field_module" : searchModule,
                    "userid" : userid,
                    "data" : {}
                };
                AppConnector.request(params).then(
                    function(data) {
                        progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                        selectedFields.html(data.result);
                        app.changeSelectElementView(selectedFields);
                        //register all select2 Elements
                        app.showSelect2ElementView(selectedFields.find('select.select2'));
                    }
                )
            }else {
                progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                selectedFields.html('');
            }
        });

        jQuery("#signature_fields_settings").on("change","#moduleFields", function(e) {
            var progressIndicatorElement = jQuery.progressIndicator({
              'position' : 'html',
              'blockInfo' : {
               'enabled' : true
              }
             });
            var searchModule=jQuery("#signature_fields_settings").find("#signature_module").val();
            var fields = jQuery("#signature_fields_settings").find("#moduleFields").val();
            if(searchModule !='') {
                var params= {
                    "type": "POST",
                    "module" : "CTMobileSettings",
                    "view" :"SignatureFieldsAjax",
                    "mode" : "get_doc_type",
                    "signature_module" : searchModule,
                    "fields":fields,
                    "data" : {}
                };
                AppConnector.request(params).then(
                    function(data) {
                        progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                        $("#doc_type").empty().append(data.result);
                        var selectedFields=jQuery("#signature_fields_settings").find("#SignatureSelectedFields");
                        app.showSelect2ElementView(selectedFields.find('select.select2'));
                    }
                )
            }else {
                progressIndicatorElement.progressIndicator({'mode' : 'hide'});
            }
        });

        table = jQuery('#example1').DataTable({
          'paging'      : true,
          'lengthChange': true,
          'searching'   : false,
          'ordering'    : false,
          'info'        : true,
          'autoWidth'   : false
        });

        var params = {};
        params['module'] = 'CTMobileSettings';
        params['action'] = 'getListRoute';
        params['mode'] = 'getlistSignature';
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


        assetTable = jQuery('#example2').DataTable({
          'paging'      : true,
          'lengthChange': true,
          'searching'   : false,
          'ordering'    : false,
          'info'        : true,
          'autoWidth'   : false
        });

        var params = {};
        params['module'] = 'CTMobileSettings';
        params['action'] = 'getListRoute';
        params['mode'] = 'getlistAssetsTracking';
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
                assetTable.rows().remove();
                assetTable.rows.add(
                   tabledata
                ).draw(); 
            }
        });

        displayTable = jQuery('#example3').DataTable({
          'paging'      : true,
          'lengthChange': true,
          'searching'   : false,
          'ordering'    : false,
          'info'        : true,
          'autoWidth'   : false
        });

        var params = {};
        params['module'] = 'CTMobileSettings';
        params['action'] = 'getListRoute';
        params['mode'] = 'getlistDisplayFields';
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
                displayTable.rows().remove();
                displayTable.rows.add(
                   tabledata
                ).draw(); 
            }
        });
    },
    
    registerSaveSettings:function() {
        jQuery("#vcard_fields_settings").on("click",".btnSaveSettings", function(e) {
            var progressIndicatorElement = jQuery.progressIndicator({
              'position' : 'html',
              'blockInfo' : {
               'enabled' : true
              }
             });
            form = jQuery("#vcard_fields_settings").find('#vcard_Settings');
            var saveUrl = form.serializeFormData();
            AppConnector.request(saveUrl).then(
                function(data) {
                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                    var params = {
                       title : "Field Save Successfully",
                       text: 'Field Save Successfully',
                       animation: 'show',
                       type: 'info'
                    };
                    Vtiger_Helper_Js.showPnotify(params);
                }
            );
        });


        jQuery("#asset_fields_settings").on("click",".btnSaveSettings", function(e) {
            var progressIndicatorElement = jQuery.progressIndicator({
              'position' : 'html',
              'blockInfo' : {
               'enabled' : true
              }
             });
            form = jQuery("#asset_fields_settings").find('#asset_Settings');
            var saveUrl = form.serializeFormData();
            AppConnector.request(saveUrl).then(
                function(data) {
                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                    var params = {
                       title : "Field Save Successfully",
                       text: 'Field Save Successfully',
                       animation: 'show',
                       type: 'info'
                    };
                    Vtiger_Helper_Js.showPnotify(params);
                    location.reload();
                }
            );
        });


        jQuery("#barcode_fields_settings").on("click",".btnSaveSettings", function(e) {
            var progressIndicatorElement = jQuery.progressIndicator({
              'position' : 'html',
              'blockInfo' : {
               'enabled' : true
              }
             });
            form = jQuery("#barcode_fields_settings").find('#barcode_Settings');
            var saveUrl = form.serializeFormData();
            AppConnector.request(saveUrl).then(
                function(data) {
                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                    var params = {
                       title : "Field Save Successfully",
                       text: 'Field Save Successfully',
                       animation: 'show',
                       type: 'info'
                    };
                    Vtiger_Helper_Js.showPnotify(params);
                }
            );
        });

        jQuery("#display_fields_settings").on("click",".btnSaveSettings", function(e) {
            var progressIndicatorElement = jQuery.progressIndicator({
              'position' : 'html',
              'blockInfo' : {
               'enabled' : true
              }
             });
            form = jQuery("#display_fields_settings").find('#display_Settings');
            var saveUrl = form.serializeFormData();
            AppConnector.request(saveUrl).then(
                function(data) {
                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                    var params = {
                       title : "Field Save Successfully",
                       text: 'Field Save Successfully',
                       animation: 'show',
                       type: 'info'
                    };
                    Vtiger_Helper_Js.showPnotify(params);
                }
            );
        });

        jQuery("#signature_fields_settings").on("click",".btnSaveSettings", function(e) {
            var doc_type = jQuery("#signature_fields_settings").find('#doc_type').val();
            if(doc_type == ''){
                var params = {
                    title : app.vtranslate('Please Select Document Type'),
                    text: app.vtranslate('Please Select Document Type'),
                    animation: 'show',
                    type: 'error'
                };
                Vtiger_Helper_Js.showPnotify(params);
            }else{
                var progressIndicatorElement = jQuery.progressIndicator({
                  'position' : 'html',
                  'blockInfo' : {
                   'enabled' : true
                  }
                 });
                form = jQuery("#signature_fields_settings").find('#sign_Settings');
                var saveUrl = form.serializeFormData();
                AppConnector.request(saveUrl).then(
                    function(data) {
                        progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                        var params = {
                           title : "Field Save Successfully",
                           text: 'Field Save Successfully',
                           animation: 'show',
                           type: 'info'
                        };
                        Vtiger_Helper_Js.showPnotify(params);
                        location.reload();
                    }
                );
            }
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
    var instance = new CTMobileSettings_FieldSettings_Js();
    instance.registerEvents();

    /*var pageURL = jQuery(location).attr("href");
    var params = {};
    var query = pageURL.split('?');
    var vars = query[1].split('&');
    for (var i = 0; i < vars.length; i++) {
        var pair = vars[i].split('=');
        params[pair[0]] = decodeURIComponent(pair[1]);
    }
    
    mode = params.mode.toLowerCase();
    if(mode != ''){
        var blocks = mode+"_block";
        console.log(blocks);
        jQuery('#'+blocks).focus();
    }*/
    
});

function deleteDisplayField(id) {
    var message = app.vtranslate('Are you sure want to delete this record ?');
    app.helper.showConfirmationBox({'message' : message}).then(function(data) {
        var params = {};
        params['module'] = 'CTMobileSettings';
        params['action'] = 'getListRoute';
        params['mode'] = 'deleteDisplayField';
        params['id'] = id;
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

                var params = {};
                params['module'] = 'CTMobileSettings';
                params['action'] = 'getListRoute';
                params['mode'] = 'getlistDisplayFields';
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
                        displayTable.rows().remove();
                        displayTable.rows.add(
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

function deleteSignature(id) {
    var message = app.vtranslate('Are you sure want to delete this record ?');
    app.helper.showConfirmationBox({'message' : message}).then(function(data) {
        var params = {};
        params['module'] = 'CTMobileSettings';
        params['action'] = 'getListRoute';
        params['mode'] = 'deleteSignature';
        params['id'] = id;
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

                var params = {};
                params['module'] = 'CTMobileSettings';
                params['action'] = 'getListRoute';
                params['mode'] = 'getlistSignature';
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


function deleteAssetsTracking(id) {
    var message = app.vtranslate('Are you sure want to delete this record ?');
    app.helper.showConfirmationBox({'message' : message}).then(function(data) {
        var params = {};
        params['module'] = 'CTMobileSettings';
        params['action'] = 'getListRoute';
        params['mode'] = 'deleteAssetsTracking';
        params['id'] = id;
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

                var params = {};
                params['module'] = 'CTMobileSettings';
                params['action'] = 'getListRoute';
                params['mode'] = 'getlistAssetsTracking';
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
                        assetTable.rows().remove();
                        assetTable.rows.add(
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
