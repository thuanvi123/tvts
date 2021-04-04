 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
jQuery.Class("CTMobileSettings_Settings_Js",{
    editInstance:false,
    getInstance: function(){
        if(CTMobileSettings_Settings_Js.editInstance == false){
            var instance = new CTMobileSettings_Settings_Js();
            CTMobileSettings_Settings_Js.editInstance = instance;
            return instance;
        }
        return CTMobileSettings_Settings_Js.editInstance;
    }
},{
    updatedBlockSequence : {},    
    registerSelectModuleChange:function() {
        jQuery("#global_search_settings").on("change","#search_module", function(e) {
            var progressIndicatorElement = jQuery.progressIndicator({
              'position' : 'html',
              'blockInfo' : {
               'enabled' : true
              }
             });
            var searchModule=jQuery(this).val();
            var selectedFields=jQuery("#global_search_settings").find("#selectedFields");
            if(searchModule !='') {
                var params= {
                    "type": "POST",
                    "module" : "CTMobileSettings",
                    "view" :"SettingsAjax",
                    "search_module" : searchModule,
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
    },

        /*change event of select module for auto address fields by sapna*/
    registerAutoSelectModuleChange:function() {

        table = jQuery('#example3').DataTable({
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
        params['mode'] = 'getAutoAddress';
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

        jQuery("#global_search_settings").on("change","#autosearch_module", function(e) {
            
            var progressIndicatorElement = jQuery.progressIndicator({
              'position' : 'html',
              'blockInfo' : {
               'enabled' : true
              }
             });
            var searchModule=jQuery(this).val();
            var selectedFields=jQuery("#global_search_settings").find("#autoSelectedFields");
            if(searchModule !='') {
                var params= {
                    "type": "POST",
                    "module" : "CTMobileSettings",
                    "view" :"SettingsAjax",
                    "search_module" : searchModule,
                    "mode" : "autoAddressField",
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
    },
    /* change event code end */

    registerSelectFieldsEvent:function() {
        jQuery('#global_search_settings').on("change","#moduleFields", function(e) {
            jQuery('#global_search_settings').find('input[name="active"]').attr("checked","checked");
        })
    },
    registerSaveSettings:function() {
        jQuery("#global_search_settings").on("click",".btnSaveSettings", function(e) {
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
                       title : "Field Save Successfully",
                       text: 'Field Save Successfully',
                       animation: 'show',
                       type: 'info'
                    };
                    Vtiger_Helper_Js.showPnotify(params);
                }
            );
        });
    },

    registerSortableEvent : function() {
        var thisInstance = this;
        var contents = jQuery('#arrangeModules');
        var table = contents.find('.searchModule');
        contents.sortable({
            'containment' : contents,
            'items' : table,
            'revert' : true,
            'tolerance':'pointer',
            'cursor' : 'move',
            'update' : function(e, ui) {
                thisInstance.updateBlockSequence();
            }
        });
    },
    /**
     * Function which will update module sequence
     */
    updateBlockSequence : function() {
        var thisInstance = this;
        var progressIndicatorElement = jQuery.progressIndicator({
            'position' : 'html',
            'blockInfo' : {
                'enabled' : true
            }
        });

        var sequence = JSON.stringify(thisInstance.updateBlocksListByOrder());
        progressIndicatorElement.progressIndicator({'mode' : 'hide'});
        var params = {};
        params['module'] = 'CTMobileSettings';
        params['action'] = 'ActionAjax';
        params['mode'] = 'updateSequenceNumber';
        params['sequence'] = sequence;

        AppConnector.request(params).then(
            function(data) {
                progressIndicatorElement.progressIndicator({'mode' : 'hide'});
            },
            function(error) {
                progressIndicatorElement.progressIndicator({'mode' : 'hide'});
            }
        );
    },
    /**
     * Function which will arrange the sequence number of modules
     */
    updateBlocksListByOrder : function() {
        var thisInstance = this;
        var contents = jQuery('#arrangeModules')
        contents.find('.searchModule').each(function(index,domElement){
            var blockTable = jQuery(domElement);

            var blockId = blockTable.data('module');
            var actualBlockSequence = blockTable.data('sequence');
            var expectedBlockSequence = (index+1);

            if(expectedBlockSequence != actualBlockSequence) {
                blockTable.data('sequence', expectedBlockSequence);
            }
            thisInstance.updatedBlockSequence[blockId] = expectedBlockSequence;
        });
        return thisInstance.updatedBlockSequence;
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

    registerEventsForFilterChanged : function(){
        // jQuery('#module_filter').on("change", function(e) {
     jQuery('#global_search_settings').on("change","#module_filter", function(e) {
            var filterId = jQuery(this).val();
            if(filterId){
                var module =  jQuery('#search_module').val();
                var progressIndicatorElement = jQuery.progressIndicator({
                    'position' : 'html',
                    'blockInfo' : {
                        'enabled' : true
                    }
                });
                var params = {};
                params['module'] = 'CTMobileSettings';
                params['action'] = 'FilterAjax';
                params['search_module'] = module;
                params['filterId'] = filterId;

                AppConnector.request(params).then(
                function(data) {
                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                    jQuery('#totalRecords').find('span').html(data.result.totalRecords);
                    jQuery('#addressRecords').find('span').html('<a id="addressPopup" href="#">'+data.result.AddressRecords+'</a>');
                    jQuery('#nonAddressRecords').find('span').html('<a id="nonAddressPopup" href="#">'+data.result.nonAddressRecords+'</a>');
                });
            }
         });

        jQuery('#global_search_settings').on("click","#btnsyncNow", function(e) {
            var module =  jQuery('#search_module').val();
            var filterId =  jQuery('#module_filter').val();
            var progressIndicatorElement = jQuery.progressIndicator({
                    'position' : 'html',
                    'blockInfo' : {
                        'enabled' : true
                    }
                });
                var params = {};
                params['module'] = 'CTMobileSettings';
                params['action'] = 'SyncLatLongAjax';
                params['search_module'] = module;
                params['filterId'] = filterId;

                AppConnector.request(params).then(
                function(data) {
                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                    var params = {
                       title : app.vtranslate("Sync GEO Location successfully"),
                       text: app.vtranslate('Sync GEO Location successfully'),
                       animation: 'show',
                       type: 'info'
                    };
                    Vtiger_Helper_Js.showPnotify(params);
                });

        });
    },

    registerEventsForNonAddressPopup : function(){
        var popupInstance = Vtiger_Popup_Js.getInstance();
        var params = {};
         jQuery('#global_search_settings').on('click','#nonAddressPopup',function(){
            var module =  jQuery('#search_module').val();
            var filterId =  jQuery('#module_filter').val();

            params['module'] = "CTMobileSettings";
            params['view'] = "NonAddressPopup";
            params['cvid'] = filterId;
            params['src_module'] = module;
            params['page'] = 1;
            
            popupInstance.showPopup(params);
        });

        jQuery('.CTNonAddressEntries').live('click',function(){
            var id = jQuery(this).attr('data-id');
            var module =  jQuery('#search_module').val();
            var url = 'index.php?module='+module+'&view=Detail&record='+id;
            window.open(url,'_blank');
        });
    },

    registerEventsForAddressPopup : function(){
        var popupInstance = Vtiger_Popup_Js.getInstance();
        var params = {};
         jQuery('#global_search_settings').on('click','#addressPopup',function(){
            var module =  jQuery('#search_module').val();
            var filterId =  jQuery('#module_filter').val();

            params['module'] = "CTMobileSettings";
            params['view'] = "AddressPopup";
            params['cvid'] = filterId;
            params['src_module'] = module;
            params['page'] = 1;
            
            popupInstance.showPopup(params);
        });
    },

    /**
     * Function which will handle the registrations for the elements
     */
    registerEvents : function() {
        this.registerSelectModuleChange();
        this.registerAutoSelectModuleChange();
        this.registerSaveSettings();
        this.registerSelectFieldsEvent();
        this.registerSortableEvent();   
        this.registerAppTriggerEvent(); 
        this.registerEventsForFilterChanged();
        this.registerEventsForNonAddressPopup(); 
        this.registerEventsForAddressPopup();
    }
});


jQuery(document).ready(function () {
    var instance = new CTMobileSettings_Settings_Js();
    instance.registerEvents();
    
    jQuery('#search_module').val(jQuery('#search_module option:eq(1)').val()).trigger('change');
    jQuery('select#search_module').val(jQuery('#search_module option:eq(1)').val()).select2();

    jQuery('#geocodingReport').on('click',function(){
		var url = jQuery(this).attr('data-url');
		location.href = url;
	});

    jQuery('#ctapivalidate').on('click',function(){
        var api_Key = jQuery('input[name="api_Key"]').val();
        if(api_Key == ''){
            var params = {
                title : app.vtranslate('Please Enter Google Api Key'),
                text: app.vtranslate('Please Enter Google Api Key'),
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

            var params = {};
            params['module'] = app.getModuleName();
            params['action'] = 'ValidateApi';
            params['api_Key'] = api_Key;
            
            AppConnector.request(params).then(
                function(data) {
                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                    var msg=data.result['msg'];
                    var code =data.result['code'];
                    if(code == 100){
                        var params = {
                            title : app.vtranslate(msg),
                            text: msg,
                            animation: 'show',
                            type: 'error'
                        };
                        Vtiger_Helper_Js.showPnotify(params);   
                    }else if(code == 101){
                        var params = {
                            title : app.vtranslate(msg),
                            text: msg,
                            animation: 'show',
                            type: 'error'
                        };
                        Vtiger_Helper_Js.showPnotify(params);   
                    }else{
                        var params = {
                                title : app.vtranslate(msg),
                                text: msg,
                                animation: 'show',
                                type: 'info'
                            };
                        Vtiger_Helper_Js.showMessage(params);
                    }
                });
        }
    });
	 
    jQuery('#save_api_Key').on('click',function(){
			var api_Key = jQuery('input[name="api_Key"]').val();
			if(api_Key == ''){

				var params = {
					title : app.vtranslate('Please Enter Google Api Key'),
					text: app.vtranslate('Please Enter Google Api Key'),
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

				var params = {};
				params['module'] = app.getModuleName();
				params['action'] = 'SaveApi';
				params['api_Key'] = api_Key;
				
				AppConnector.request(params).then(
					function(data) {
						progressIndicatorElement.progressIndicator({'mode' : 'hide'});
						var msg=data.result['msg'];
						var code =data.result['code'];
						if(code == 100){
							var params = {
								title : app.vtranslate(msg),
								text: msg,
								animation: 'show',
								type: 'error'
							};
							Vtiger_Helper_Js.showPnotify(params);	
						}else if(code == 101){
							var params = {
								title : app.vtranslate(msg),
								text: msg,
								animation: 'show',
								type: 'error'
							};
							Vtiger_Helper_Js.showPnotify(params);	
						}else{

							var params = {
									title : app.vtranslate(msg),
									text: msg,
									animation: 'show',
									type: 'info'
								};
							Vtiger_Helper_Js.showMessage(params);
							location.reload();
						}
			   });
			}
 
    
	});

    jQuery('#remove_api_Key').on('click',function(){
        var params = {};
        params['module'] = app.getModuleName();
        params['action'] = 'RemoveApi';
        var progressIndicatorElement = jQuery.progressIndicator({
            'position' : 'html',
            'blockInfo' : {
                'enabled' : true
            }
        });
        AppConnector.request(params).then(
        function(data) {
            progressIndicatorElement.progressIndicator({'mode' : 'hide'});
            var msg=data.result['msg'];
            var nparams = {
                    title : msg,
                    text: msg,
                    animation: 'show',
                    type: 'info'
                };
            Vtiger_Helper_Js.showMessage(nparams);
            location.reload();
        });
    });

});


function deleteAutoAddressField(id) {
    var message = app.vtranslate('Are you sure want to delete this record ?');
    app.helper.showConfirmationBox({'message' : message}).then(function(data) {
        var params = {};
        params['module'] = 'CTMobileSettings';
        params['action'] = 'getListRoute';
        params['mode'] = 'deleteAutoSearch';
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
                
                var params = {};
                params['module'] = 'CTMobileSettings';
                params['action'] = 'getListRoute';
                params['mode'] = 'getAutoAddress';
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

function editAutoSearch(module) {
    jQuery('#autosearch_module').val(module).trigger("change");
    jQuery('#autosearch_module').select2();
}
