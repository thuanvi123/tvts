/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("CTRoutePlanning_Edit_Js",{

},{

	realtedtoCTRouteElement : false,

	getrealtedtoCTRouteElement : function(form) {
		if(typeof form == "undefined") {
			form = this.getForm();
		}
		this.realtedtoCTRouteElement =  jQuery('#ctroute_realtedto_display', form);
		return this.realtedtoCTRouteElement;
	},

	openPopUp : function(e){
		var thisInstance = this;
		var parentElem = thisInstance.getParentElement(jQuery(e.target));

		var params = this.getPopUpParams(parentElem);
		params.view = 'Popup';
		params.multi_select = true;
		var isMultiple = false;
		if(params.multi_select) {
				isMultiple = true;
		}

		var sourceFieldElement = jQuery('input[class="sourceField"]',parentElem);

		var prePopupOpenEvent = jQuery.Event(Vtiger_Edit_Js.preReferencePopUpOpenEvent);
		sourceFieldElement.trigger(prePopupOpenEvent);

		if(prePopupOpenEvent.isDefaultPrevented()) {
				return ;
		}
		var popupInstance = Vtiger_Popup_Js.getInstance();
		popupInstance.showPopup(params,function(data){
			var responseData = JSON.parse(data);
			var dataList = new Array();
			for(var id in responseData){
					var data = {
							'name' : responseData[id].name,
							'id' : id
					}
					dataList.push(data);
					if(!isMultiple) {
							thisInstance.setReferenceFieldValue(parentElem, data);
					}
			}

			if(isMultiple) {
				sourceFieldElement.trigger(Vtiger_Edit_Js.refrenceMultiSelectionEvent,{'data':dataList});
			}
			sourceFieldElement.trigger(Vtiger_Edit_Js.postReferenceSelectionEvent,{'data':responseData});
		});
	},

	registerRelatedContactSpecificEvents : function(form) {
		var thisInstance = this;
		if(typeof form == "undefined") {
			form = this.getForm();
		}
		form.find('[name="ctroute_realtedto"]').on(Vtiger_Edit_Js.preReferencePopUpOpenEvent,function(e){
			
			parentIdElement = form.find('[name="ctroute_realtedto"]');
			
			var container = parentIdElement.closest('td');
			var popupReferenceModule = jQuery('input[name="popupReferenceModule"]',container).val();

		})
		//If module is not events then we dont have to register events
		this.getrealtedtoCTRouteElement(form).select2({
			 minimumInputLength: 3,
			 ajax : {
				'url' : 'index.php?module=Contacts&action=BasicAjax&search_module=Contacts',
				'dataType' : 'json',
				'data' : function(term,page){
					 var data = {};
					 data['search_value'] = term;
					 return data;
				},
				'results' : function(data){
					data.results = data.result;
					for(var index in data.results ) {

						var resultData = data.result[index];
						resultData.text = resultData.label;
					}
					return data
				},
				 transport : function(params){
					return jQuery.ajax(params);
				 }
			 },
			 multiple : true,
			 //To Make the menu come up in the case of quick create
			 dropdownCss : {'z-index' : '10001'}
		});

		//To add multiple selected contact from popup
		form.find('[name="ctroute_realtedto"]').on(Vtiger_Edit_Js.refrenceMultiSelectionEvent,function(e,result){
			thisInstance.addNewCTRouteRelatedList(result,form);
		});

		this.fillRelatedCTRoute(form);
	},
	/**
	 * Function to get reference search params
	 */
	getReferenceSearchParams : function(element){
		var tdElement = jQuery(element).closest('td');
		var params = {};
		var previousTd = tdElement.prev();
		var multiModuleElement = jQuery('select.referenceModulesList', previousTd);

		var referenceModuleElement;
		if(multiModuleElement.length) {
			referenceModuleElement = multiModuleElement;
		} else {
			referenceModuleElement = jQuery('input[name="popupReferenceModule"]',tdElement).length ?
										jQuery('input[name="popupReferenceModule"]',tdElement) : jQuery('input.popupReferenceModule',tdElement);
		}
		var searchModule =  referenceModuleElement.val();
		params.search_module = searchModule;
		return params;
	},


	initializeCTRouteList : function(form) {
		var realtedtoCTRouteElement = this.getrealtedtoCTRouteElement(form);
		if(this.isEvents(form) && realtedtoCTRouteElement.length) {
			jQuery('<input type="hidden" name="ctrouteidlist" /> ').appendTo(form).val(realtedtoCTRouteElement.val().split(',').join(';'));
			form.find('[name="ctroute_realtedto"]').attr('name','');
		}
	},


	registerRecordPreSaveEvent : function(form) {
		var thisInstance = this;
		if(typeof form === "undefined") {
			form = this.getForm();
		}
		var InitialFormData = form.serialize();
		app.event.one(Vtiger_Edit_Js.recordPresaveEvent,function(e) {
			thisInstance.initializeCTRouteList(form);
		});
	},


	/**
	 * Function which will fill the already saved contacts on load
	 */
	fillRelatedCTRoute : function(form) {
		if(typeof form == "undefined") {
			form = this.getForm();
		}
		var relatedContactValue = form.find('[name="relatedCTRouteInfo"]').data('value');
		for(var contactId in relatedContactValue) {
			var info = relatedContactValue[contactId];
			info.text = info.name;
			relatedContactValue[contactId] = info;
		}
		this.getrealtedtoCTRouteElement(form).select2('data',relatedContactValue);
	},


	addNewCTRouteRelatedList : function(newContactInfo, form){
		if(form.length <= 0) {
			form = this.getForm();
		}
		 var resultentData = new Array();

			var element =  jQuery('#ctroute_realtedto_display', form);
			var selectContainer = jQuery(element.data('select2').container, form);
			var choices = selectContainer.find('.select2-search-choice');
			choices.each(function(index,element){
				resultentData.push(jQuery(element).data('select2-data'));
			});
			var select2FormatedResult = newContactInfo.data;
			for(var i=0 ; i < select2FormatedResult.length; i++) {
			  var recordResult = select2FormatedResult[i];
			  recordResult.text = recordResult.name;
			  resultentData.push( recordResult );
			}
			element.select2('data',resultentData);
			
			form.find('[name="relatedCTRouteInfo"]').data('value', resultentData);
			var realtedtoCTRouteElement = this.getrealtedtoCTRouteElement(form);
			if(realtedtoCTRouteElement.length > 0) {
				jQuery('<input type="hidden" name="ctrouteidlist" /> ').appendTo(form).val(realtedtoCTRouteElement.val().split(',').join(';'));
				form.find('[name="ctroute_realtedto"]').attr('name','');
			}
			
	},

	referenceCreateHandler : function(container) {

		var thisInstance = this;
		var form = thisInstance.getForm();
		var mode = jQuery(form).find('[name="module"]').val();
		if(container.find('.sourceField').attr('name') != 'ctroute_realtedto'){ 
			this._super(container); 
			return; 
		}
		 var postQuickCreateSave  = function(data) {
			var params = {};
			params.name = data._recordLabel;
			params.id = data._recordId;
			thisInstance.addNewCTRouteRelatedList({'data':[params]}, container);
		}

		var referenceModuleName = this.getReferencedModuleName(container);
		var quickCreateNode = jQuery('#quickCreateModules').find('[data-name="'+ referenceModuleName +'"]');
		if(quickCreateNode.length <= 0) {
			return app.helper.showErrorNotification({message:app.vtranslate('JS_NO_CREATE_OR_NOT_QUICK_CREATE_ENABLED')});

		}
		quickCreateNode.trigger('click',{'callbackFunction':postQuickCreateSave});
	},


	registerBasicEvents : function(container) {
		this._super(container);
		this.registerRelatedContactSpecificEvents(container);
	}
});
