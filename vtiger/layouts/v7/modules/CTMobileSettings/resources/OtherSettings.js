jQuery.Class('CTMobileSettings_OtherSettings_Js', {}, {
	
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
    
	UninstallCTMobile : function(){
		jQuery('#unInstallCTMobile').live('click',function(){
			var params = {};
			params['module'] = app.getModuleName();
			params['parent'] = app.getParentModuleName();
			params['action'] = 'Uninstall';
			var message = app.vtranslate('MSG_CTMOBILE_POPUP1')+'<br/>'+app.vtranslate('MSG_CTMOBILE_POPUP1_2');
			var message1 = app.vtranslate('MSG_CTMOBILE_POPUP2');
			app.helper.showConfirmationBox({'message' : message}).then(function(data) {
				app.helper.showConfirmationBox({'message' : message1}).then(function(data) {
					app.request.post({data: params}).then(function(err, response) {
						if(response){
							var params = {
								title : app.vtranslate('MSG_CTMOBILE_UNISTALL'),
								text: app.vtranslate('MSG_CTMOBILE_UNISTALL'),
								animation: 'show',
								type: 'success'
							};
							Vtiger_Helper_Js.showPnotify(params);
							window.location.href = response;
						}	
					});
				});
			});
		});
	},
	DeactivateCTMobileLicense : function(){
		jQuery('#deactivateLicense').live('click',function(){
			var params = {};
			params['module'] = app.getModuleName();
			params['parent'] = app.getParentModuleName();
			params['action'] = 'Deactivate';
			var message1 = app.vtranslate('MSG_CTMOBILE_DEACTIVATE_POPUP');
			app.helper.showConfirmationBox({'message' : message1}).then(function(data) {
				app.request.post({data: params}).then(function(err, response) {
					if(response){
						var params = {
							title : response.message,
							text: response.message,
							animation: 'show',
							type: 'success'
						};
						Vtiger_Helper_Js.showPnotify(params);
						location.reload();
					}	
				});
			});
		});
	},
    registerEvents: function () {
		var thisInstance = this;
		this.registerAppTriggerEvent();
		this.UninstallCTMobile();
		this.DeactivateCTMobileLicense();
	}
});
jQuery(document).ready(function () {
	var Instance =  new CTMobileSettings_OtherSettings_Js();
	Instance.registerEvents();
	var view =app.view();
	if(view == 'TeamTracking'){
	 var date = new Date();
	   var month = date.getMonth()+1;
	   var day = date.getDate();
	   var year = date.getFullYear();
	   var newdate = year + '/' + month + '/' + day;
	    if(day > 7){
		   day = day -7;
		   if(day < 10 ){
			   day = '0' + day;
		   }
		   if(month < 10 ){
			   month = '0' + month;
		   }
		   var lastDate = year + '/' + month + '/' + day;
	   }else{
		   var month31 = [1,3,5,7,8,10,12];
		   if(month > 1){
				day = 7 - day;
				month = month - 1;
				if(jQuery.inArray(month, month31) !== -1){
					day = 31 - day;
				}else{
					day = 30 - day;
				}
				year = year - 1;
				if(day < 10 ){
				   day = '0' + day;
				}
				if(month < 10 ){
				   month = '0' + month;
				}
				var lastDate = year + '/' + month + '/' + day;
		   }else{
				day = day -7;
				month = month - 1;
				if(jQuery.inArray(month, month31) !== -1){
					day = 31 - day;
				}else{
					day = 30 - day;
				}
				if(day < 10 ){
				   day = '0' + day;
				}
				if(month < 10 ){
				   month = '0' + month;
				}
				var lastDate = year + '/' + month + '/' + day;
		   }
	   }
	   var Today = newdate;
	   var tempdate = date;
	   tempdate.setDate(tempdate.getDate() - 1);
	   var Yesterday = tempdate.getFullYear()+'/'+(tempdate.getMonth()+1)+'/'+tempdate.getDate();

	   var tempdate1 = date;
	   tempdate1.setDate(tempdate1.getDate() - 6);
	   var last7days = tempdate1.getFullYear()+'/'+(tempdate1.getMonth()+1)+'/'+tempdate1.getDate();

	   var tempdate2 = date;
	   tempdate2.setDate(tempdate2.getDate() - 29);
	   var last30days = tempdate2.getFullYear()+'/'+(tempdate2.getMonth()+1)+'/'+tempdate2.getDate();

	   var date = new Date();
	   var firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
	   var lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);
	   var firstday = firstDay.getFullYear()+'/'+(firstDay.getMonth()+1)+'/'+firstDay.getDate();
	   var lastday = lastDay.getFullYear()+'/'+(lastDay.getMonth()+1)+'/'+lastDay.getDate();

	   var date = new Date();
	   var firstDay = new Date(date.getFullYear(), date.getMonth()-1, 1);
       var lastDay = new Date(date.getFullYear(), date.getMonth(), 0);
       var monthStartDay = firstDay.getFullYear()+'/'+(firstDay.getMonth()+1)+'/'+firstDay.getDate();
       var monthEndDay = lastDay.getFullYear()+'/'+(lastDay.getMonth()+1)+'/'+lastDay.getDate();

       var date = new Date();
	   var firstDay = new Date(date.getFullYear(), 0, 1);
       var lastDay = new Date(date.getFullYear(), 12, 0);
       var yearStartDay = firstDay.getFullYear()+'/'+(firstDay.getMonth()+1)+'/'+firstDay.getDate();
       var yearEndDay = lastDay.getFullYear()+'/'+(lastDay.getMonth()+1)+'/'+lastDay.getDate();

       var date = new Date();
	   var firstDay = new Date(date.getFullYear()-1, 0, 1);
       var lastDay = new Date(date.getFullYear()-1, 12, 0);
       var lastyearStartDay = firstDay.getFullYear()+'/'+(firstDay.getMonth()+1)+'/'+firstDay.getDate();
       var lastyearEndDay = lastDay.getFullYear()+'/'+(lastDay.getMonth()+1)+'/'+lastDay.getDate();

	   var dateValue = lastDate + ' - ' + newdate;
	   jQuery('input[name="daterange"]').val(dateValue);
	   jQuery('input[name="daterange"]').daterangepicker({    locale: {
		  format: 'YYYY/MM/DD'
		},
	 	ranges: {
           'Today': [Today,Today],
           'Yesterday': [Yesterday, Yesterday],
           'Last 7 Days': [last7days, Today],
           'Last 30 Days': [last30days, Today],
           'This Month': [firstday, lastday],
           'This Year': [yearStartDay, yearEndDay],
           'Last Year': [lastyearStartDay, lastyearEndDay]
        }
    });

	}
	if(view = 'LicenseDetail'){
		var url = "index.php?module=CTMobileSettings&action=chkPermission&mode=GetLicense";
		var params = {
			"url":url
		};
		app.request.post(params).then(
		  function(err, data){
			if(err === null) {
				if(data.result != 0){
					var android_store_url = 'https://play.google.com/store/apps/details?id=com.crmtiger.vtigercrm&hl=en';
					var ios_store_url = 'https://apps.apple.com/in/app/crmtiger-vtiger-mobile/id1274011679';
					var detailView_url = 'index.php?module=CTMobileSettings&parent=Settings&view=Details';
					var darkcolor =  '#00008b';
					var message_html = "You've successfully enter your license information - Enjoy CRMTiger Mobile Apps by Downloading Mobile Apps from Store.<br/><a style='color:#15c !important;'  onMouseOver="+'this.style.color="#00008b"'+" onMouseOut="+'this.style.color="#15c"'+" href='"+android_store_url+"' target='_blank'>Click here</a> to download Apps for Android users <br/><a style='color:#15c !important;' onMouseOver="+'this.style.color="#00008b"'+" onMouseOut="+'this.style.color="#15c"'+" href='"+ios_store_url+"' target='_blank'>Click here </a> to download Apps for iOS users<br/><a style='color:#15c !important;' onMouseOver="+'this.style.color="#00008b"'+" onMouseOut="+'this.style.color="#15c"'+" href='"+detailView_url+"' >Click here</a> view CRMTiger Mobile Apps management Dashboard - an Intuitive Dashboard for CRM Admin to manage various activities of CRMTiger Mobile Apps";
					jQuery('#successMessage').html(message_html);
				}
			}
		});
	}
	
     jQuery('.editButton').on('click',function(){
		var url = jQuery(this).attr('data-url');
		location.href = url;
	 });

	 jQuery('#ctopenstreetmap').on('click',function(){
		var url = jQuery(this).attr('data-url');
		location.href = url;
	 });
	 
	 jQuery('#ctgooglemap').on('click',function(){
		var url = jQuery(this).attr('data-url');
		location.href = url;
	 });


	 jQuery('#license_btn').on('click',function(){
			var url = jQuery(this).attr('data-url');
			location.href = url;
	 });

	 
	 jQuery('#updatectmobile').live('click',function(){
			var url = jQuery(this).attr('data-url');
			location.href = url;
	 });
	 jQuery('#help_btn').on('click',function(){
			var url = jQuery(this).attr('data-url');
			window.open(url, '_blank');
	 });
	 jQuery('#ios_btn').on('click',function(){
			var url = jQuery(this).attr('data-url');
			window.open(url, '_blank');
	 });
	 jQuery('#android_btn').on('click',function(){
			var url = jQuery(this).attr('data-url');
			window.open(url, '_blank');
	 });

	 jQuery('.blur_box_btn').on('click',function(){
			var url = jQuery(this).attr('data-url');
			window.open(url, '_blank');
	 });

	 jQuery('#WhatsNew').on('click',function(){
	 	var popupInstance = Vtiger_Popup_Js.getInstance();
	 		params['module'] = "CTMobileSettings";
            params['view'] = "WhatsNew";
            popupInstance.showPopup(params);
	 });

	 //open modal popup on error icon click
	$(function () {
        $("a[class='showErrorModal']").click(function () {
            $("#myModal").modal("show");
            return false;
        });
    });
	 //jQuery('#myModal').modal('show'); 
	 jQuery('#updateModal').modal('show');
	 
	jQuery('#save_license_settings').on('click',function(){
		License_Key = jQuery('input[name="License_Key"]').val();
		if(License_Key == ''){

			var params = {
				title : app.vtranslate('Please Enter License Key'),
				text: app.vtranslate('Please Enter License Key'),
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
			params['parent'] = app.getParentModuleName();
			params['action'] = 'SaveLicense';
			params['license_key'] = License_Key;
			
			AppConnector.request(params).then(function(data) {
					progressIndicatorElement.progressIndicator({'mode' : 'hide'});
					var msg=data.result['msg'];
					var code =data.result['code'];
					console.log(data);
					console.log(code);
					if(code === 100 || code === 101){
						var params = {
							title : app.vtranslate(msg),
							text: msg,
							animation: 'show',
							type: 'error'
						};
						Vtiger_Helper_Js.showPnotify(params);	
					}else if(code === 103){
						another_domain = data.result['domain'];
						license_key = data.result['license_key'];

						var params = {};
						params['module'] = app.getModuleName();
						params['parent'] = app.getParentModuleName();
						params['action'] = 'Deactivate';
						params['domain'] = another_domain;
						params['license_key'] = license_key;
						var message1 = app.vtranslate('Your License key is already registered with "'+another_domain+'", Are you Sure Want to Deactivate from there and Activate to current Instance ?');
						app.helper.showConfirmationBox({'message' : message1}).then(function(data) {
							var progressIndicatorElement2 = jQuery.progressIndicator({
								'position' : 'html',
								'blockInfo' : {
									'enabled' : true
								}
							});
							app.request.post({data: params}).then(function(err, response) {
								if(response){
									var params2 = {};
									params2['module'] = app.getModuleName();
									params2['parent'] = app.getParentModuleName();
									params2['action'] = 'SaveLicense';
									params2['license_key'] = license_key;
									AppConnector.request(params2).then(function(data2) {
										progressIndicatorElement2.progressIndicator({'mode' : 'hide'});
										var msg2=data2.result['msg'];
										var code2 =data2.result['code'];
										if(code2 === 100 || code2 === 101 || code2 === 103){
											var pparams = {
												title : app.vtranslate('Something went wrong'),
												text: app.vtranslate('Something went wrong'),
												animation: 'show',
												type: 'error'
											};
											Vtiger_Helper_Js.showPnotify(pparams);
										}else{
											var params = {
												title : app.vtranslate(msg2),
												text: msg2,
												animation: 'show',
												type: 'info'
											};
											Vtiger_Helper_Js.showMessage(params);
											var redirectUrl = 'index.php?module=CTMobileSettings&parent=Settings&view=CTMobileAccessUser';
											window.location.href = redirectUrl;
										}
									});
								}	
							});
						});	
					}else{
						var params = {
							title : app.vtranslate(msg),
							text: msg,
							animation: 'show',
							type: 'info'
						};
						Vtiger_Helper_Js.showMessage(params);
						var redirectUrl = 'index.php?module=CTMobileSettings&parent=Settings&view=CTMobileAccessUser';
						window.location.href = redirectUrl;
					}
			});
		}
	 
		
	});

});


function updateModule(moduleName){
	var postData = {
	"module": 'CTMobileSettings',
	"action": 'updateModuleAjax',
	"updateModName": moduleName
	};
	jQuery('#waitmsg').show();
	var progressIndicatorElement = jQuery.progressIndicator({
		'position' : 'html',
		'blockInfo' : {
			'enabled' : true
		}
	});
	app.request.post({'data': postData}).then(function (response) {
			progressIndicatorElement.progressIndicator({'mode' : 'hide'});
			location.reload(true);	
		},
		function(error,err){
			progressIndicatorElement.progressIndicator({'mode' : 'hide'});
			var errorhtml = '<p>Oooops.. Its taking longer time than expected..<p>';
			errorhtml+='<p><a href="https://kb.crmtiger.com/knowledge-base/error-code-solutions/" target="_blank">Click here</a> , if you still wants to manually update extension</p>';
			jQuery('#waitmsg').html(errorhtml);
			jQuery('#waitmsg').show();
		}
	);

	setTimeout(function() {
		progressIndicatorElement.progressIndicator({'mode' : 'hide'});
			var errorhtml = '<p>Oooops.. Its taking longer time than expected..<p>';
			errorhtml+='<p><a href="https://kb.crmtiger.com/knowledge-base/error-code-solutions/" target="_blank">Click here</a> , if you still wants to manually update extension</p>';
			jQuery('#waitmsg').html(errorhtml);
			jQuery('#waitmsg').show();
    }, 1000*120);
}
