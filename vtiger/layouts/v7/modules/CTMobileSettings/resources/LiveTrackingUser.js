 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
jQuery.Class("CTMobileSettings_LiveTrackingUser_Js",{
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
    
    registerSaveSettings:function() {
        jQuery(".btnSaveLiveUser").on("click", function(e) {
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
                       title : app.vtranslate("LiveTracking Users Save Successfully"),
                       text: app.vtranslate("LiveTracking Users Save Successfully"),
                       animation: 'show',
                       type: 'info'
                    };
                    Vtiger_Helper_Js.showPnotify(params);
                    var url = data.result.Detail_Url;
                    location.href = url;
                }
            );
        });
    },

    /**
     * Function which will handle the registrations for the elements
     */
    registerEvents : function() {
        this.registerSaveSettings();     
    }
});


jQuery(document).ready(function () {
    var instance = new CTMobileSettings_LiveTrackingUser_Js();
    instance.registerEvents();

});
