jQuery.Class('CTMobileSettings_GeocodeReport_Js', {}, {
	
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
		
		registerSynclatlongEvent : function(){
			jQuery('#synclatlong').on('click',function(){
				var params = {};
				params['module'] = app.getModuleName();
				params['parent'] = app.getParentModuleName();
				params['action'] = 'addLatlong';
				app.helper.showProgress();
				AppConnector.request(params).then(
				function(data) {
					console.log(data);
					app.helper.hideProgress();
				});
			});
		},
		/**
		* Function to register events
		*/
	registerEvents: function () {
		var thisInstance = this;
		this.registerAppTriggerEvent();
		this.registerSynclatlongEvent();
		
	}
	
})

jQuery(document).ready(function () {
	var CTMobileSettingsInstance = new CTMobileSettings_GeocodeReport_Js();
    CTMobileSettingsInstance.registerEvents();
});
