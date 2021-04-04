{*<!--
 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */
-->*}

{literal}
<style type="text/css">
.nav-tabs > li.active > a, .nav-tabs > li.active > a:hover, .nav-tabs > li.active > a:focus {
    color: #333;
    border-bottom: 3px solid #555;
}
.ctblocks{
  margin-bottom: 10px;
}


div#ctmobile_user_settings {
    background: #f4f5f6;
    border: 1px solid rgb(44 59 73 / 0.15);
    padding: 20px 20px;
}


.widget_header.row-fluid button.btn.btn-info.pull-right {
    background: #2c3b49 !important;
    border-radius: 4px;
    padding: 9px 10px;
}

.widget_header.row-fluid {
    border: 1px solid rgb(44 59 73 / 0.15);
    padding: 15px 15px;
}
.widget_header.row-fluid h3 {
    color: #333333;
    font-size: 20px;
    font-weight: 600;
    margin: 0 0 10px;
}
.widget_header.row-fluid button.btn.btn-info.pull-right {
    background: #2c3b49 !important;
    border-radius: 4px;
    padding: 9px 10px;
}

#ctmobile_user_settings {
    background: #f4f5f6;
    border: 1px solid rgb(44 59 73 / 0.15);
    padding: 15px 15px;
}

#ctmobile_user_settings h4 {
    color: #333;
    font-size: 16px;
    line-height: 22px;
    font-weight: 600;
    margin: 0;
}
#ctmobile_user_settings h5 {
    color: #333;
    font-size: 14px;
    font-weight: 600;
    margin:0 0 10px;
}

table{
	margin-bottom: 0px !important;
}

</style>
{/literal}

<div class="container-fluid">
    <div class="widget_header row-fluid">
    	<button type="button" class="btn btn-info pull-right" style="background:#287DF2 !important;" onclick='window.location.href="{CTMobileSettings_Module_Model::$CTMOBILE_DETAILVIEW_URL}"'>{vtranslate('Go To CRMTiger Settings',$MODULE)}</button>
        <h3>{vtranslate("CRMtiger Mobile Apps - Feature management",$MODULE)}</h3>
    </div>
<div id="ctmobile_user_settings">
	<table style="margin-top:25px;" class="table table-borderless">
	    <tr>
	      <td style="width:85%;"><h4>{vtranslate('Allow users to access mobile apps','CTMobileSettings')} <a href="https://kb.crmtiger.com/article-categories/mobileapps/" target="_blank"><img src="layouts/v7/modules/CTMobileSettings/images/question-icon.png" alt="Icon" style="width:15px;"></a></h4></td>
	      <td style="width:30%">
	        <label class="switch">
	        <input type="checkbox" class="user_settings" name="access_user" id="access_user" {if $SELECTED_USER_SETTINGS['access_user'] eq '1'} checked {/if} value="1" data-on-color="success">
	        <div class="slider"></div>
	        </label>
	      </td>
	    </tr>
	</table>

	<div id="access_user_div">
	<div class="container-fluid ctblocks">
	      <div class="clearfix"></div>
	      <div class="summaryWidgetContainer" id="access_user_settings">
			   <form action="index.php" method="post" id="AccessUser-Settings" class="form-horizontal">
					<input type="hidden" name="module" value="CTMobileSettings">
					<input type="hidden" name="action" value="SaveAjaxMAccessUser">
					<table class="table table-bordered blockContainer showInlineTable equalSplit" style="width: 500px;">
						<tr>
							<td colspan="2" class="fieldValue medium">
								<h5>{vtranslate('Select users to allow access of CRMTiger Mobile Apps','CTMobileSettings')}</h5>
								<select class="select2" multiple="true" id="moduleFields" name="fields[]" data-placeholder="Select fields" style="width: 800px">
								<optgroup label="">
									<option value="selectAll" {if in_array('selectAll',$ACCESS_SELECTED_FIELDS)} selected {/if}>{vtranslate('LBL_ALL_USERS','CTMobileSettings')}</option>
								</optgroup>
								<optgroup label="{vtranslate('LBL_USERS')}">
									{foreach key=FIELD_NAME item=FIELD_MODEL from=$ACCESS_USER_MODEL}
										<option value="{$FIELD_MODEL['userid']}" data-field-name="{$FIELD_MODEL['username']}"
												 {if in_array($FIELD_MODEL['userid'], $ACCESS_SELECTED_FIELDS)}
	                                               selected
	                                             {/if}
												>{$FIELD_MODEL['username']}
										</option>
									{/foreach}
									</optgroup>
									<optgroup label="{vtranslate('LBL_GROUPS')}">
									{foreach key=FIELD_NAME item=FIELD_MODEL from=$ACCESS_GROUPS_MODEL}
										<option value="{$FIELD_MODEL['userid']}" data-field-name="{$FIELD_MODEL['username']}"
										{if in_array($FIELD_MODEL['userid'], $ACCESS_SELECTED_FIELDS)}
	                                               selected
	                                             {/if}
												>{$FIELD_MODEL['username']}
										</option>
									{/foreach}
									</optgroup>
								</select>
							</td>
						</tr>
					</table>
					<div class="row-fluid">
						<button class="btn btn-success btnSaveAccessUser" type="button">{vtranslate('LBL_SAVE', 'CTMobileSettings')}</button>
						<a class="cancelLink" type="reset" onclick="javascript:window.history.back();">{vtranslate('Cancel','CTMobileSettings')}</a>
					</div>
				</form>
	      </div>
	</div>

	<table style="margin-top:25px;" class="table table-borderless">
	    <tr>
	      <td style="width:85%;"><h4>{vtranslate('Modules Management','CTMobileSettings')} <a href=https://kb.crmtiger.com/article-categories/mobileapps/" target="_blank"><img src="layouts/v7/modules/CTMobileSettings/images/question-icon.png" alt="Icon" style="width:15px;"></a></h4></td>
	      <td style="width:30%">
	      </td>
	    </tr>
	</table>

	<div class="container-fluid ctblocks" id="modules_management_div">
	      <div class="clearfix"></div>
	      <div class="summaryWidgetContainer" id="access_user_settings">
			   <form action="index.php" method="post" id="modules_management_form" class="form-horizontal">
					<input type="hidden" name="module" value="CTMobileSettings">
					<input type="hidden" name="action" value="SaveAjaxModuleManagement">

					<div class="row-fluid">
			            <div class="select-search" style="margin-top:15px;">
			            	<h5>{vtranslate('Select modules to allow in CRMTiger Mobile Apps','CTMobileSettings')}</h5>
			                <select class="select2" multiple="true" id="modules_management_module" name="modules_management_module[]" data-placeholder="Select Modules" style="width: 800px">
			                	<option value="selectAll" {if in_array('selectAll',$MODULE_MANAGEMENT_MODULES)} selected {/if}>{vtranslate('LBL_ALL','CTMobileSettings')} {vtranslate('LBL_MODULE','CTMobileSettings')}</option>
			                    {foreach item=MODULE_MODEL key=TAB_ID from=$TIMETRACKING_ALL_MODULE}
			                            <option value="{$MODULE_MODEL->getName()}" {if in_array($MODULE_MODEL->getName(), $MODULE_MANAGEMENT_MODULES)} selected {/if}>{vtranslate($MODULE_MODEL->getName(),$MODULE_MODEL->getName())}</option>
			                    {/foreach}
			                </select>
			            </div>
			        </div>
			        <br/>
					<div class="row-fluid">
						<button class="btn btn-success btnSaveModuleManagement" type="button">{vtranslate('LBL_SAVE', 'CTMobileSettings')}</button>
						<a class="cancelLink" type="reset" onclick="javascript:window.history.back();">{vtranslate('Cancel','CTMobileSettings')}</a>
					</div>
				</form>
	      </div>
	</div>

	<table style="margin-top:25px;" class="table table-borderless">
	    <tr>
	      <td style="width:85%;"><h4>{vtranslate('Premium features management','CTMobileSettings')} <a href="https://kb.crmtiger.com/article-categories/mobileapps/" target="_blank"><img src="layouts/v7/modules/CTMobileSettings/images/question-icon.png" alt="Icon" style="width:15px;"></a></h4></td>
	      <td style="width:30%">
	      </td>
	    </tr>
	</table>

	<div class="container-fluid" id="premium_feature_div">
		  <h6>{vtranslate('Manage CRMTiger Mobile Apps premium features to enable/disable for all the users.','CTMobileSettings')}</h6>
	      <div class="summaryWidgetContainer" id="premium_feature_settings">
	          <form action="index.php" method="post" id="premium_feature_settings_form" class="form-horizontal"/>
	          <input type="hidden" name="module" value="CTMobileSettings"/>
	          <input type="hidden" name="action" value="SavePremiumFeatureSettings"/>
	           <table class="table table-borderless">
	                <tbody>
	                  <tr>
	                    <td colspan="5"><h5><b>{vtranslate('Asset / Card Scanner','CTMobileSettings')}</b></h5></td>
	                  </tr>
	                  <tr>
	                    <td></td>
	                    <td style="width:30%;">{vtranslate('QR Card Scanner','CTMobileSettings')}</td>
	                    <td style="width:10%;">
	                      <label class="switch">
	                      <input type="checkbox" class="premium_feature_button" name="qr_code_scanner" value="1" {if $SELECTED_FEATURE['qr_code_scanner'] eq '1'}checked{/if} data-on-color="success">
	                      <div class="slider"></div>
	                      </label>
	                    </td>
	                    <td style="width:30%;">{vtranslate('Business Card Scanner','CTMobileSettings')}</td>
	                    <td>
	                      <label class="switch">
	                      <input type="checkbox" class="premium_feature_button" name="business_card_scanner" value="1" {if $SELECTED_FEATURE['business_card_scanner'] eq '1'}checked{/if} data-on-color="success">
	                      <div class="slider"></div>
	                      </label>
	                    </td>
	                  </tr>
	                  <tr>
	                    <td></td>
	                    <td>{vtranslate('Asset Tracking','CTMobileSettings')}</td>
	                    <td>
	                      <label class="switch">
	                      <input type="checkbox" class="premium_feature_button" name="asset_tracking" value="1" {if $SELECTED_FEATURE['asset_tracking'] eq '1'}checked{/if} data-on-color="success">
	                      <div class="slider"></div>
	                      </label>
	                    </td>
	                  </tr>
	                  <tr id="geo_tracking">
		                  <td colspan="3"><h4>{vtranslate('GEO tracking & Configuration','CTMobileSettings')} <a href="https://kb.crmtiger.com/article-categories/mobileapps/" target="_blank"><img src="layouts/v7/modules/CTMobileSettings/images/question-icon.png" alt="Icon" style="width:15px;"></a></h4></td>
					      <td colspan="2">
					        <label class="switch" style="float: right;margin-right: 35px;">
					        <input type="checkbox" class="user_settings" name="location_tracking" id="location_tracking" {if $SELECTED_USER_SETTINGS['location_tracking'] eq '1'} checked {/if} value="1" data-on-color="success">
					        <div class="slider"></div>
					        </label>
					      </td>
				      </tr> 
				      <tr class="location_tracking_div">
	                  	<td colspan="5">
	                  		<span class="KB_New_Editor_Highlights" style="margin: 10px 0px; position: relative; display: inline-block; padding: 10px 10px 10px 40px; background-color: rgb(252, 237, 158); border-right: 5px solid rgb(242, 219, 104)">
							    <img data-image="contentStyle" style="position: absolute; left: 12px; top: 14px; width: 15px; max-width: 100%" data-type="non-resize" src="layouts/v7/modules/CTMobileSettings/icon/file.png">
							    <div><b>Note</b> : {vtranslate('Location tracking feature track location of the users when they are accessing check-in, check-out, time tracking,Route Planner','CTMobileSettings')} 
							    {vtranslate('and add/update records for any module from CRMTiger Mobile Apps.','CTMobileSettings')} 
							    </div>
							</span>
	                  	</td>
	                  </tr> 
	                  <tr class="location_tracking_div">
	                    <td></td>
	                    <td style="width:30%;">{vtranslate('Meeting Check-In/Check-Out','CTMobileSettings')}</td>
	                    <td style="width:10%;">
	                      <label class="switch">
	                      <input type="checkbox" class="premium_feature_button" name="meeting_checkin" value="1" {if $SELECTED_FEATURE['meeting_checkin'] eq '1'}checked{/if} data-on-color="success">
	                      <div class="slider"></div>
	                      </label>
	                    </td>
	                    <td style="width:30%;">{vtranslate('Attendance(Shift management)','CTMobileSettings')}</td>
	                    <td>
	                      <label class="switch">
	                      <input type="checkbox" class="premium_feature_button" name="attendance_checkin" value="1" {if $SELECTED_FEATURE['attendance_checkin'] eq '1'}checked{/if} data-on-color="success">
	                      <div class="slider"></div>
	                      </label>
	                    </td>
	                  </tr>
	                  <tr class="location_tracking_div">
	                    <td></td>
	                    <td>{vtranslate('Nearby Customer','CTMobileSettings')}</td>
	                    <td>
	                      <label class="switch">
	                      <input type="checkbox" class="premium_feature_button" name="nearby_customer" value="1" {if $SELECTED_FEATURE['nearby_customer'] eq '1'}checked{/if} data-on-color="success">
	                      <div class="slider"></div>
	                      </label>
	                    </td>
	                    <td>{vtranslate('Record View on Map','CTMobileSettings')}</td>
	                    <td>
	                      <label class="switch">
	                      <input type="checkbox" class="premium_feature_button" name="record_map_view" value="1" {if $SELECTED_FEATURE['record_map_view'] eq '1'}checked{/if} data-on-color="success">
	                      <div class="slider"></div>
	                      </label>
	                    </td>
	                  </tr>
	                  <tr class="location_tracking_div">
	                    <td></td>
	                    <td>{vtranslate('Address Auto Finder','CTMobileSettings')}</td>
	                    <td>
	                      <label class="switch">
	                      <input type="checkbox" class="premium_feature_button" name="address_autofinder" value="1" {if $SELECTED_FEATURE['address_autofinder'] eq '1'}checked{/if} data-on-color="success">
	                      <div class="slider"></div>
	                      </label>
	                    </td>
	                  </tr>
	                  <tr class="location_tracking_div">
	                  	<td colspan="5">
	                  		<div class="container-fluid ctblocks" id="location_tracking_div">
						       <h5 style="margin-left:20px;">{vtranslate('Add/Remove users from list to track location of users','CTMobileSettings')}</h5>
						      <div class="clearfix"></div>
						      <div class="summaryWidgetContainer" id="live_user_settings">
									<table class="table table-bordered blockContainer showInlineTable equalSplit" style="width: 500px;">
										<tr>
											<td colspan="2" class="fieldValue medium">
												<select class="select2" multiple="true" id="moduleFields" name="livetracking_users[]" data-placeholder="Select Users" style="width: 800px">
													{foreach key=FIELD_NAME item=FIELD_MODEL from=$LIVE_USER_MODEL}
														<option value="{$FIELD_MODEL['userid']}" data-field-name="{$FIELD_MODEL['username']}"
																 {if in_array($FIELD_MODEL['userid'], $LIVE_SELECTED_FIELDS)}
					                                               selected
					                                             {/if}
																>{$FIELD_MODEL['username']}
														</option>
													{/foreach}
												</select>
											</td>
										</tr>
									</table>
									<br />
									<div class="row-fluid">
										<a href="{CTMobileSettings_Module_Model::$CTMOBILE_TEAMTRACKING_URL}" class="btn btn-primary fa fa-bar-chart pull-right" title="{vtranslate('User activity on Map','CTMobileSettings')}" aria-hidden="true" style="margin-right: 70px;"></a>
									</div>
						      </div>
						</div>
	                  	</td>
	                  </tr>      
	                  <tr>
	                  	<td colspan="5">
	                  		<span class="KB_New_Editor_Highlights" style="margin: 10px 0px; position: relative; display: inline-block; padding: 10px 10px 10px 40px; background-color: rgb(252, 237, 158); border-right: 5px solid rgb(242, 219, 104)">
							    <img data-image="contentStyle" style="position: absolute; left: 12px; top: 14px; width: 15px; max-width: 100%" data-type="non-resize" src="layouts/v7/modules/CTMobileSettings/icon/file.png">
							    <div><b>Note</b> : {vtranslate('Nearby Customer,Record View on Map function only work if  Record has been GEO coded using GEO Settings','CTMobileSettings')} 
							     <a href="{CTMobileSettings_Module_Model::$CTMOBILE_GEOLOCATION_SETUP_URL}" style="color:#15c;">{vtranslate('Click Here','CTMobileSettings')}</a>
							    </div>
							</span>
	                  	</td>
	                  </tr>
	                  <tr>
	                    <td colspan="5"><h5><b>{vtranslate('Communication','CTMobileSettings')}</b></h5></td>
	                  </tr>
	                  <tr>
	                    <td></td>
	                    <td>{vtranslate('Call from CRMTiger Apps','CTMobileSettings')}</td>
	                    <td>
	                      <label class="switch">
	                      <input type="checkbox" class="premium_feature_button" name="call_from_app" value="1" {if $SELECTED_FEATURE['call_from_app'] eq '1'}checked{/if} data-on-color="success">
	                      <div class="slider"></div>
	                      </label>
	                    </td>
	                    <td>{vtranslate('Email from CRMTiger Apps','CTMobileSettings')}</td>
	                    <td>
	                      <label class="switch">
	                      <input type="checkbox" class="premium_feature_button" name="email_from_app" value="1" {if $SELECTED_FEATURE['email_from_app'] eq '1'}checked{/if} data-on-color="success">
	                      <div class="slider"></div>
	                      </label>
	                    </td>
	                  </tr>
	                  <tr>
	                    <td></td>
	                    <td>{vtranslate('SMS  from CRMTiger Apps','CTMobileSettings')}</td>
	                    <td>
	                      <label class="switch">
	                      <input type="checkbox" class="premium_feature_button" name="sms_from_app" value="1" {if $SELECTED_FEATURE['sms_from_app'] eq '1'}checked{/if} data-on-color="success">
	                      <div class="slider"></div>
	                      </label>
	                    </td>
	                    <td>{vtranslate('WhatsApp from CRMTiger Apps','CTMobileSettings')}</td>
	                    <td>
	                      <label class="switch">
	                      <input type="checkbox" class="premium_feature_button" name="whatsapp_from_app" value="1" {if $SELECTED_FEATURE['whatsapp_from_app'] eq '1'}checked{/if} data-on-color="success">
	                      <div class="slider"></div>
	                      </label>
	                    </td>
	                  </tr>
	                </tbody>
	           </table>
	            <div class="row-fluid">
	                <button class="btn btn-success btnSavePremiumFeatureSettings" type="button">{vtranslate('LBL_SAVE', 'CTMobileSettings')}</button>
	                <a class="cancelLink" type="reset" onclick="javascript:window.history.back();">{vtranslate('Cancel','CTMobileSettings')}</a>
	            </div>
	        </form>
	    </div>
	</div>


	<table style="margin-top:25px;" class="table table-borderless">
	    <tr>
	      <td style="width:85%"><h4>{vtranslate('Route Planner Configuration','CTMobileSettings')} <a href="https://kb.crmtiger.com/article-categories/mobileapps/" target="_blank"><img src="layouts/v7/modules/CTMobileSettings/images/question-icon.png" alt="Icon" style="width:15px;"></a></h4></td>
	      <td style="width:30%">
	        <label class="switch">
	        <input type="checkbox" class="user_settings" name="route_planner" id="route_planner" {if $SELECTED_USER_SETTINGS['route_planner'] eq '1'} checked {/if} value="1" data-on-color="success">
	        <div class="slider"></div>
	        </label>
	      </td>
	    </tr>
	</table>
	<div class="container-fluid ctblocks" id="route_planner_div">
	    <div class="clearfix"></div>
	    <div class="tab-content massEditContent">
	              <ul class="nav nav-tabs massEditTabs">
	                 <li class="active">
	                      <a href="#routegeneral" data-toggle="tab" >
	                          <strong>
	                              {vtranslate('Route General', 'CTMobileSettings')}
	                          </strong>
	                      </a>
	                  </li>
	                  <li >
	                      <a href="#routestatus" data-toggle="tab">
	                          <strong>
	                              {vtranslate('Route Status', 'CTMobileSettings')}
	                          </strong>
	                      </a>
	                  </li>   
	              </ul>

	        <div class="tab-content massEditContent">
	            <div class="tab-pane active" id="routegeneral">
	            	<br/>
            		<span class="KB_New_Editor_Highlights" style="margin: 10px 0px; position: relative; display: inline-block; padding: 10px 10px 10px 40px; background-color: rgb(252, 237, 158); border-right: 5px solid rgb(242, 219, 104)">
					    <img data-image="contentStyle" style="position: absolute; left: 12px; top: 14px; width: 15px; max-width: 100%" data-type="non-resize" src="layouts/v7/modules/CTMobileSettings/icon/file.png">
					    <div><b>Note</b> : {vtranslate('Route Planner require integration with Google API(Preferable for accurate result), Although it works with Openstreet(free) Map Settings but No-Guaranteed of accurate result.','CTMobileSettings')} 
					     <a href="#geo_tracking" style="color:#15c;">{vtranslate('Click Here','CTMobileSettings')}</a>
					     {vtranslate('to setup MAP API before work with Route Planner','CTMobileSettings')}
					    </div>
					</span>
	            	<h5 style="margin-left:20px;">{vtranslate('Add/Remove users from list to access of route planning','CTMobileSettings')}</h5>
	            	<div class="summaryWidgetContainer" id="route_distance_unit_settings">
				        <div class="row-fluid">
				            
				            <div class="select-search" style="margin-top:15px;">
				                <select class="select2" multiple="true" id="route_users" name="route_users[]" data-placeholder="Select Users" style="width: 800px">
				                    {foreach key=FIELD_NAME item=FIELD_MODEL from=$USER_MODEL}
				                        <option value="{$FIELD_MODEL['userid']}" data-field-name="{$FIELD_MODEL['username']}"
				                                 {if in_array($FIELD_MODEL['userid'], $ROUTE_USERS)}
				                                   selected
				                                 {/if}
				                                >{$FIELD_MODEL['username']}
				                        </option>
				                    {/foreach}
				                </select>
				            </div>
				        </div>
				        <div class="row-fluid">
				            <div style="margin-top:15px;">
				                {vtranslate('(Setting of distance will calculate distance from one location to another in either Miles or Kilometers)',$MODULE)}
				            </div>
				            <div class="select-search" style="margin-top:15px;">
				                <select class="select2" id="distanceUnit" name="distanceUnit" data-placeholder="{vtranslate('Select Distance Unit',$MODULE)}" style="width: 300px">
				                   <option value="Kilometers" {if $DISTANCE_UNIT eq 'Kilometers'} selected {/if}>{vtranslate('Kilometers',$MODULE)}</option>
				                   <option value="Miles" {if $DISTANCE_UNIT eq 'Miles'} selected {/if}>{vtranslate('Miles',$MODULE)}</option>
				                </select>
				            </div>
				        </div>
				        <br/>
				        <div class="row-fluid">
				            <button class="btn btn-success btnSaveDistanceUnit" type="button">{vtranslate('LBL_SAVE', 'CTMobileSettings')}</button>
				            <a class="cancelLink" type="reset" onclick="javascript:window.history.back();">{vtranslate('Cancel','CTMobileSettings')}</a>
				            <a href="{CTMobileSettings_Module_Model::$CTMOBILE_ROUTE_ANALYTICS_URL}" class="btn btn-primary fa fa-bar-chart pull-right" title="{vtranslate('Route Planning Report','CTMobileSettings')}" aria-hidden="true" style="margin-right: 70px;"></a>
				        </div>  
				    </div>
	            </div>

	           	<div class="tab-pane" id="routestatus" style="">
	           		<br/>
	           		<h5 style="margin-left:20px;">{vtranslate('Set text of Route status as per your business requirement. This will be display in Route planner screen in Mobile Apps','CTMobileSettings')}</h5>
	           		<h5 style="margin-left:20px;">{vtranslate('For Example: "Complete" status here consider as "Route" has been completed, You can set text value of that to either "Close" or "Finish".','CTMobileSettings')}</h5>
	           		<div class="summaryWidgetContainer" id="route_status_settings">
				        <form action="index.php" method="post" id="RouteStatus-Settings" class="form-horizontal">
				        <input type="hidden" name="module" value="CTMobileSettings">
				        <input type="hidden" name="action" value="SaveRouteGeneralSettings">
				        <input type="hidden" name="mode" value="SaveStatus">
				            <div class="row-fluid">
				                <table class="table table-bordered blockContainer showInlineTable equalSplit">
				                    {foreach from=$ROUTE_STATUS key=key item=STATUS}
				                        <tr>
				                            <td class="fieldLabel alignMiddle">
				                                {$STATUS['routestatusname']}
				                            </td>
				                            <td class="fieldValue">
				                                <input class="inputElement" type="text" name="status_{$STATUS['routestatusid']}" value="{$STATUS['routestatuslabel']}">
				                            </td>
				                        </tr>
				                    {/foreach}
				                </table>
				            </div>
				            <br/>
				            <div class="row-fluid">
				                <button class="btn btn-success btnSaveStatus" type="button">{vtranslate('LBL_SAVE', 'CTMobileSettings')}</button>
				                <a class="cancelLink" type="reset" onclick="javascript:window.history.back();">{vtranslate('Cancel','CTMobileSettings')}</a>
				                <a href="{CTMobileSettings_Module_Model::$CTMOBILE_ROUTE_ANALYTICS_URL}" class="btn btn-primary fa fa-bar-chart pull-right" title="{vtranslate('Route Planning Report','CTMobileSettings')}" aria-hidden="true" style="margin-right: 70px;"></a>
				            </div> 
				        </form>
				    </div> 
	           	</div>
	        </div>
	    
		</div>
	</div>

	<table style="margin-top:25px;" class="table table-borderless">
	    <tr>
	      <td style="width:85%"><h4>{vtranslate('Time Tracker Module Configuration','CTMobileSettings')} <a href="https://kb.crmtiger.com/article-categories/mobileapps/" target="_blank"><img src="layouts/v7/modules/CTMobileSettings/images/question-icon.png" alt="Icon" style="width:15px;"></a></h4></td>
	      <td style="width:30%">
	        <label class="switch">
	        <input type="checkbox" class="user_settings" name="time_tracker" id="time_tracker" {if $SELECTED_USER_SETTINGS['time_tracker'] eq '1'} checked {/if} value="1" data-on-color="success">
	        <div class="slider"></div>
	        </label>
	      </td>
	    </tr>
	</table>
	<div class="container-fluid ctblocks" id="time_tracker_div">
		<h5 style="margin-left:20px;">{vtranslate('Add modules to track time against record of module','CTMobileSettings')}</h5>
		<span class="KB_New_Editor_Highlights" style="margin: 10px 0px; position: relative; display: inline-block; padding: 10px 10px 10px 40px; background-color: rgb(252, 237, 158); border-right: 5px solid rgb(242, 219, 104)">
		    <img data-image="contentStyle" style="position: absolute; left: 12px; top: 14px; width: 15px; max-width: 100%" data-type="non-resize" src="layouts/v7/modules/CTMobileSettings/icon/file.png">
		    <div><b>Note</b> : {vtranslate('Time tracking function only Track Location if GEO Location Tracking is enable for the user from GEO Location tracking section ','CTMobileSettings')} 
		     <a href="#geo_tracking" style="color:#15c;">{vtranslate('Click Here','CTMobileSettings')}</a>
		    </div>
		</span>
	    <div class="clearfix"></div>
	    <div class="summaryWidgetContainer" id="timetracker_module_settings">

	        <div class="row-fluid">
	            <div class="select-search" style="margin-top:15px;">
	                <select class="select2" multiple="true" id="timetracking_moduleFields" name="module[]" data-placeholder="Select Modules" style="width: 800px">
	                    {foreach item=MODULE_MODEL key=TAB_ID from=$TIMETRACKING_ALL_MODULE}
	                            <option value="{$MODULE_MODEL->getName()}" {if in_array($MODULE_MODEL->getName(), $TIMETRACKEMODULES)} selected {/if}>{vtranslate($MODULE_MODEL->getName(),$MODULE_MODEL->getName())}</option>
	                    {/foreach}
	                </select>
	            </div>
	        </div>
	        <br/>
	        <div class="row-fluid">
	            <button class="btn btn-success btnSaveModule" type="button">{vtranslate('LBL_SAVE', 'CTMobileSettings')}</button>
	            <a class="cancelLink" type="reset" onclick="javascript:window.history.back();">{vtranslate('Cancel','CTMobileSettings')}</a>
	            <a href="index.php?module=CTTimeTracker&view=List" class="btn btn-primary fa fa-bar-chart pull-right" title="{vtranslate('Time Tracking Report','CTMobileSettings')}" aria-hidden="true" style="margin-right: 70px;"></a>
	        </div>  
	    </div>  
	</div>

	<table style="margin-top:25px;" class="table table-borderless">
	    <tr>
	      <td style="width:85%"><h4>{vtranslate('Call Logging Configuration','CTMobileSettings')} <a href="https://kb.crmtiger.com/article-categories/mobileapps/" target="_blank"><img src="layouts/v7/modules/CTMobileSettings/images/question-icon.png" alt="Icon" style="width:15px;"></a></h4></td>
	      <td style="width:30%">
	        <label class="switch">
	        <input type="checkbox" class="user_settings" name="call_logging" id="call_logging" {if $SELECTED_USER_SETTINGS['call_logging'] eq '1'} checked {/if} value="1" data-on-color="success">
	        <div class="slider"></div>
	        </label>
	      </td>
	    </tr>
	</table>
	<div class="container-fluid ctblocks" id="call_logging_div">
		   
	       <h6 style="margin-left:20px;">{vtranslate('This feature allow to Log regular call from users phone. (This feature only support selected mobile device as per the list','CTMobileSettings')} <a href="https://kb.crmtiger.com/knowledge-base/crmtiger-premium-features/call-logging" target="_blank" style="color: #15c !important;">{vtranslate('Click here for more information','CTMobileSettings')}</a>) {vtranslate('Auto create activities(events) on finished of call with Date & Time of call','CTMobileSettings')}</h6>
	       <form action="index.php" method="post" id="CalllogUser-Settings" class="form-horizontal">
	       <input type="checkbox" id="autoActivityCreate" name="autoActivityCreate" value="yes" style="float: left;margin-left: 20px;margin-right: 5px;" {if $AUTOCREATEACTIVITY neq 0}checked{/if}>
		   	<h6 style="margin-left:20px;"><b>{vtranslate('Auto create activities(events) on finished of call with Date & Time of call','CTMobileSettings')}</b></h6><br/>

	       <h6 style="margin-left:20px;">{vtranslate('Add/Remove users from list to allow call recording/logging feature','CTMobileSettings')}</h6>
	      <div class="clearfix"></div>
	      <div class="summaryWidgetContainer" id="calllog_user_settings">
			  
					<input type="hidden" name="module" value="CTMobileSettings">
					<input type="hidden" name="action" value="SaveAjaxCalllogUser">
					<table class="table table-bordered blockContainer showInlineTable equalSplit" style="width: 500px;">
						<tr>
							<td colspan="2" class="fieldValue medium">
								<select class="select2" multiple="true" id="moduleFields" name="fields[]" data-placeholder="Select Users" style="width: 800px">
									{foreach key=FIELD_NAME item=FIELD_MODEL from=$LIVE_USER_MODEL}
										<option value="{$FIELD_MODEL['userid']}" data-field-name="{$FIELD_MODEL['username']}"
												 {if in_array($FIELD_MODEL['userid'], $SELECTED_CALLLOG_USERS)}
	                                               selected
	                                             {/if}
												>{$FIELD_MODEL['username']}
										</option>
									{/foreach}
								</select>
							</td>
						</tr>
					</table>
					<div class="row-fluid">
						<button class="btn btn-success btnSaveCalllogUser" type="button">{vtranslate('LBL_SAVE', 'CTMobileSettings')}</button>
						<a class="cancelLink" type="reset" onclick="javascript:window.history.back();">{vtranslate('Cancel', $MODULE)}</a>
					</div>
				</form>
	</div>

	</div>

</div>
</div>

