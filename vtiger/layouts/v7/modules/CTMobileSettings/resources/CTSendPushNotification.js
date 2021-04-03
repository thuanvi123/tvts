 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
jQuery.Class("CTMobileSettings_CTSendPushNotification_Js",{
    editInstance:false,
    getInstance: function(){
        if(CTMobileSettings_CTSendPushNotification_Js.editInstance == false){
            var instance = new CTMobileSettings_Settings_Js();
            CTMobileSettings_CTSendPushNotification_Js.editInstance = instance;
            return instance;
        }
        return CTMobileSettings_CTSendPushNotification_Js.editInstance;
    }
},{
    
    registerSaveSettings:function() {
        jQuery(".btnSendNotification").on("click", function(e) {
            var type =  jQuery('[name="type"]').val();
            var users = jQuery('[name="Users[]"]').val();
            var title = jQuery('[name="title"]').val();
            var message = jQuery('[name="message"]').val();
            if(users == '' || users == null){
              var params = {
                title : app.vtranslate('Please select users or groups'),
                text: app.vtranslate('Please select users or groups'),
                animation: 'show',
                type: 'error'
              };
              Vtiger_Helper_Js.showPnotify(params);
            }
            if(title == ''){
              var params = {
                title : app.vtranslate('Please enter notification title'),
                text: app.vtranslate('Please enter notification title'),
                animation: 'show',
                type: 'error'
              };
              Vtiger_Helper_Js.showPnotify(params);
            }
            if(message == ''){
              var params = {
                title : app.vtranslate('Please enter notification message'),
                text: app.vtranslate('Please enter notification message'),
                animation: 'show',
                type: 'error'
              };
              Vtiger_Helper_Js.showPnotify(params);
            }
            if(type == 'link'){
              var notification_url = jQuery('[name="notification_url"]').val();
              if(notification_url == ''){
                  var params = {
                    title : app.vtranslate('Please enter URL'),
                    text: app.vtranslate('Please enter URL'),
                    animation: 'show',
                    type: 'error'
                  };
                  Vtiger_Helper_Js.showPnotify(params);
                  notification_url = false;
              }else if(!notification_url.match(/^http([s]?):\/\/.*/)){
                 var params = {
                    title : app.vtranslate('URL should contain http or https'),
                    text: app.vtranslate('URL should contain http or https'),
                    animation: 'show',
                    type: 'error'
                  };
                  Vtiger_Helper_Js.showPnotify(params);
                  notification_url = false;
              }
            }else{
              var notification_url = true;
            }
            if(users && title && message && notification_url){
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
                         title : app.vtranslate("Push notification send successfully"),
                         text: app.vtranslate("Push notification send successfully"),
                         animation: 'show',
                         type: 'info'
                      };
                      Vtiger_Helper_Js.showPnotify(params);
                      //var url = data.result.Detail_Url;
                      //location.href = url;
                      location.reload();

                  }
              );
            }
        });
    },

    registerTypeChange : function(){
        jQuery('#notification_url').closest('tr').hide();
        jQuery('#type').on('change',function(){
            var type =  jQuery(this).val();
            if(type == 'link'){
              jQuery('#notification_url').closest('tr').show();
            }else{
              jQuery('#notification_url').closest('tr').hide();
            }
        });
    },

    /**
     * Function which will handle the registrations for the elements
     */
    registerEvents : function() {
        this.registerSaveSettings(); 
        this.registerTypeChange();    
    }
});


jQuery(document).ready(function () {
    var instance = new CTMobileSettings_CTSendPushNotification_Js();
    instance.registerEvents();

});
