 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
jQuery.Class("CTMobileSettings_NotificationSettings_Js",{
    editInstance:false,
    getInstance: function(){
        if(CTMobileSettings_NotificationSettings.editInstance == false){
            var instance = new CTMobileSettings_Settings_Js();
            CTMobileSettings_NotificationSettings_Js.editInstance = instance;
            return instance;
        }
        return CTMobileSettings_NotificationSettings_Js.editInstance;
    }
},{
    updatedBlockSequence : {},    
    registerCheckBoxChange:function() {
        form = jQuery("#notification_settings").find('#notification_settings_form');
        jQuery("input[type='checkbox']").bootstrapSwitch();

        jQuery('#allow_notification').on('switchChange.bootstrapSwitch',function(e){
            var currentElement = jQuery(e.currentTarget);
            if (currentElement.bootstrapSwitch('state')) {
              form.find('[type="checkbox"]').bootstrapSwitch('state', true);
            }else{
              form.find('[type="checkbox"]').bootstrapSwitch('state', false);
            }
        });

        jQuery(form).on('switchChange.bootstrapSwitch', "input[type='checkbox']", function (e) {
            var currentElement = jQuery(e.currentTarget);
            if(currentElement.bootstrapSwitch('state')){
                currentElement.attr('value','1');
            } else {
                currentElement.attr('value','0');
            }
        });

        jQuery(form).on('switchChange.bootstrapSwitch', "input[name='event_reminder']", function (e) {
            var currentElement = jQuery(e.currentTarget);
            if(currentElement.bootstrapSwitch('state')){
                jQuery('#reminder-controls').css('visibility','visible');
            }else{
                jQuery('#reminder-controls').css('visibility','collapse');
            }
        });

        jQuery(form).on('switchChange.bootstrapSwitch', "input[name='task_reminder']", function (e) {
            var currentElement = jQuery(e.currentTarget);
            if(currentElement.bootstrapSwitch('state')){
                jQuery('#task-reminder-controls').css('visibility','visible');
            }else{
                jQuery('#task-reminder-controls').css('visibility','collapse');
            }
        });

    },
    
    registerSaveSettings:function() {

        jQuery("#notification_settings").on("click",".btnSaveSettings", function(e) {

            form = jQuery("#notification_settings").find('#notification_settings_form');
            var saveUrl = form.serializeFormData();
            var record_assigned = saveUrl.record_assigned;
            var record_assigned_module = jQuery('#record_assigned_module').val();
            var record_assigned_title = jQuery('[name="record_assigned_title"]').val();
            var record_assigned_message = jQuery('[name="record_assigned_message"]').val();
            
            var comment_assigned = saveUrl.comment_assigned;
            var comment_assigned_module = jQuery('#comment_assigned_module').val();
            var comment_assigned_title = jQuery('[name="comment_assigned_title"]').val();
            var comment_assigned_message = jQuery('[name="comment_assigned_message"]').val();

            var follow_record = saveUrl.follow_record;
            var follow_record_module = jQuery('#follow_record_module').val();
            var follow_record_title = jQuery('[name="follow_record_title"]').val();
            var follow_record_message = jQuery('[name="follow_record_message"]').val();

            var event_invitation = saveUrl.event_invitation;
            var event_invitation_title = jQuery('[name="event_invitation_title"]').val();
            var event_invitation_message = jQuery('[name="event_invitation_message"]').val();

            var event_reminder = saveUrl.event_reminder;
            var event_reminder_title = jQuery('[name="event_reminder_title"]').val();
            var event_reminder_message = jQuery('[name="event_reminder_message"]').val();

            var comment_mentioned = saveUrl.comment_mentioned;
            var comment_mentioned_title = jQuery('[name="comment_mentioned_title"]').val();
            var comment_mentioned_message = jQuery('[name="comment_mentioned_message"]').val();

            var task_reminder = saveUrl.task_reminder;
            var task_reminder_title = jQuery('[name="task_reminder_title"]').val();
            var task_reminder_message = jQuery('[name="task_reminder_message"]').val();


            if(event_invitation == '1' && (event_invitation_title == '' || event_invitation_message == '')){
                app.helper.showErrorNotification({message:"Enter Notification Title and Message"}); 
                return false; 
            }else if(event_reminder == '1' && (event_reminder_title == '' || event_reminder_message == '')){
                app.helper.showErrorNotification({message:"Enter Notification Title and Message"}); 
                return false; 
            }else if(comment_mentioned == '1' && (comment_mentioned_title == '' || comment_mentioned_message == '')){
                app.helper.showErrorNotification({message:"Enter Notification Title and Message"}); 
                return false; 
            }else if(task_reminder == '1' && (task_reminder_title == '' || task_reminder_message == '')){
                app.helper.showErrorNotification({message:"Enter Notification Title and Message"}); 
                return false; 
            }else if(record_assigned == '1' && record_assigned_module == null){
                app.helper.showErrorNotification({message:"Select atleast one Module for Record Assigned"}); 
                return false; 
            }else if(record_assigned == '1' && (record_assigned_title == '' || record_assigned_message == '')){
                app.helper.showErrorNotification({message:"Enter Notification Title and Message"}); 
                return false; 
            }else if(comment_assigned == '1' && comment_assigned_module == null){
                app.helper.showErrorNotification({message:"Select atleast one module for Comment on Assigned Record"}); 
                return false; 
            }else if(comment_assigned == '1' && (comment_assigned_title == '' || comment_assigned_message == '')){
                app.helper.showErrorNotification({message:"Enter Notification Title and Message"}); 
                return false; 
            }else if(follow_record == '1' && follow_record_module == null){
                app.helper.showErrorNotification({message:"Select atleast one module for Follow Record"}); 
                return false; 
            }else if(follow_record == '1' && (follow_record_title == '' || follow_record_message == '')){
                app.helper.showErrorNotification({message:"Enter Notification Title and Message"}); 
                return false; 
            }else{
                var progressIndicatorElement = jQuery.progressIndicator({
                  'position' : 'html',
                  'blockInfo' : {
                   'enabled' : true
                  }
                });
                AppConnector.request(saveUrl).then(
                    function(data) {
                        progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                        var params = {
                           title : "Notification Settings Save Successfully",
                           text: 'Notification Settings Save Successfully',
                           animation: 'show',
                           type: 'info'
                        };
                        Vtiger_Helper_Js.showPnotify(params);
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

    registerEventsforShowHide : function(){
        
        form = jQuery("#notification_settings").find('#notification_settings_form');
        if(form.find('[name="comment_assigned"]').bootstrapSwitch('state') == true){
            var div = 'comment_assigned_div';
            jQuery('.'+div).show();
        }else{
            var div = 'comment_assigned_div';
            jQuery('.'+div).hide();
        }

        if(form.find('[name="event_invitation"]').bootstrapSwitch('state') == true){
            var div = 'event_invitation_div';
            jQuery('.'+div).show();
        }else{
            var div = 'event_invitation_div';
            jQuery('.'+div).hide();
        }

        if(form.find('[name="event_reminder"]').bootstrapSwitch('state') == true){
            var div = 'event_reminder_div';
            jQuery('.'+div).show();
        }else{
            var div = 'event_reminder_div';
            jQuery('.'+div).hide();
        }

        if(form.find('[name="comment_mentioned"]').bootstrapSwitch('state') == true){
            var div = 'comment_mentioned_div';
            jQuery('.'+div).show();
        }else{
            var div = 'comment_mentioned_div';
            jQuery('.'+div).hide();
        }

        if(form.find('[name="task_reminder"]').bootstrapSwitch('state') == true){
            var div = 'task_reminder_div';
            jQuery('.'+div).show();
        }else{
            var div = 'task_reminder_div';
            jQuery('.'+div).hide();
        }

        if(form.find('[name="record_assigned"]').bootstrapSwitch('state') == true){
            var div = 'record_assigned_div';
            jQuery('.'+div).show();
        }else{
            var div = 'record_assigned_div';
            jQuery('.'+div).hide();
        }

        if(form.find('[name="follow_record"]').bootstrapSwitch('state') == true){
            var div = 'follow_record_div';
            jQuery('.'+div).show();
        }else{
            var div = 'follow_record_div';
            jQuery('.'+div).hide();
        }

        jQuery(form).on('switchChange.bootstrapSwitch', "input[name='comment_assigned']", function (e) {
            var currentElement = jQuery(e.currentTarget);
            var div = 'comment_assigned_div';
            if(currentElement.bootstrapSwitch('state')){
              jQuery('.'+div).show();
            }else{
              jQuery('.'+div).hide();
            }
        });

        jQuery(form).on('switchChange.bootstrapSwitch', "input[name='event_invitation']", function (e) {
            var currentElement = jQuery(e.currentTarget);
            var div = 'event_invitation_div';
            if(currentElement.bootstrapSwitch('state')){
              jQuery('.'+div).show();
            }else{
              jQuery('.'+div).hide();
            }
        });

        jQuery(form).on('switchChange.bootstrapSwitch', "input[name='event_reminder']", function (e) {
            var currentElement = jQuery(e.currentTarget);
            var div = 'event_reminder_div';
            if(currentElement.bootstrapSwitch('state')){
              jQuery('.'+div).show();
            }else{
              jQuery('.'+div).hide();
            }
        });

        jQuery(form).on('switchChange.bootstrapSwitch', "input[name='comment_mentioned']", function (e) {
            var currentElement = jQuery(e.currentTarget);
            var div = 'comment_mentioned_div';
            if(currentElement.bootstrapSwitch('state')){
              jQuery('.'+div).show();
            }else{
              jQuery('.'+div).hide();
            }
        });
        jQuery(form).on('switchChange.bootstrapSwitch', "input[name='record_assigned']", function (e) {
            var currentElement = jQuery(e.currentTarget);
            var div = 'record_assigned_div';
            if(currentElement.bootstrapSwitch('state')){
              jQuery('.'+div).show();
            }else{
              jQuery('.'+div).hide();
            }
        });

        jQuery(form).on('switchChange.bootstrapSwitch', "input[name='task_reminder']", function (e) {
            var currentElement = jQuery(e.currentTarget);
            var div = 'task_reminder_div';
            if(currentElement.bootstrapSwitch('state')){
              jQuery('.'+div).show();
            }else{
              jQuery('.'+div).hide();
            }
        });

        jQuery(form).on('switchChange.bootstrapSwitch', "input[name='follow_record']", function (e) {
            var currentElement = jQuery(e.currentTarget);
            var div = 'follow_record_div';
            if(currentElement.bootstrapSwitch('state')){
              jQuery('.'+div).show();
            }else{
              jQuery('.'+div).hide();
            }
        });

        jQuery(form).on('change','#events-invitation-fieldnames',function(){
            var text = jQuery(this).val();
            insertAtCaret('event_invitation_message',text);
        });

        jQuery(form).on('change','#events-reminder-fieldnames',function(){
            var text = jQuery(this).val();
            insertAtCaret('event_reminder_message',text);
        });

        jQuery(form).on('change','#task-reminder-fieldnames',function(){
            var text = jQuery(this).val();
            insertAtCaret('task_reminder_message',text);
        });

        jQuery(form).on('change','#comment-mentioned-fieldnames',function(){
            var text = jQuery(this).val();
            insertAtCaret('comment_mentioned_message',text);
        });
    },

    /**
     * Function which will handle the registrations for the elements
     */
    registerEvents : function() {
        this.registerCheckBoxChange();
        this.registerSaveSettings(); 
        this.registerAppTriggerEvent();
        this.registerEventsforShowHide(); 
    }
});


jQuery(document).ready(function () {
    var instance = new CTMobileSettings_NotificationSettings_Js();
    instance.registerEvents();
});



function insertAtCaret(areaId, text) {
  var txtarea = document.getElementById(areaId);
  if (!txtarea) {
    return;
  }

  var scrollPos = txtarea.scrollTop;
  var strPos = 0;
  var br = ((txtarea.selectionStart || txtarea.selectionStart == '0') ?
    "ff" : (document.selection ? "ie" : false));
  if (br == "ie") {
    txtarea.focus();
    var range = document.selection.createRange();
    range.moveStart('character', -txtarea.value.length);
    strPos = range.text.length;
  } else if (br == "ff") {
    strPos = txtarea.selectionStart;
  }

  var front = (txtarea.value).substring(0, strPos);
  var back = (txtarea.value).substring(strPos, txtarea.value.length);
  txtarea.value = front + text + back;
  strPos = strPos + text.length;
  if (br == "ie") {
    txtarea.focus();
    var ieRange = document.selection.createRange();
    ieRange.moveStart('character', -txtarea.value.length);
    ieRange.moveStart('character', strPos);
    ieRange.moveEnd('character', 0);
    ieRange.select();
  } else if (br == "ff") {
    txtarea.selectionStart = strPos;
    txtarea.selectionEnd = strPos;
    txtarea.focus();
  }

  txtarea.scrollTop = scrollPos;
}

