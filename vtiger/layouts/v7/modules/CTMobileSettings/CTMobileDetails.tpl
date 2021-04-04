{*<!-- 7.0
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
<style>
h4{
font-weight:bold;
font-size:16px;}

.upgrade-button {
background-color: #287DF2;
border: none;
color: white;
padding: 15px 32px;
text-align: center;
text-decoration: none;
display: inline-block;
font-size: 16px;
margin: 4px 2px;
cursor: pointer;
}

.upgrade{
	width: 95px;
    padding: 2px;
    font-size: 16px;
	cursor:pointer;
	background-color:#178FFF;
	border: 1px solid #178FFF;
	color:#fff;
	border-radius: 4px;
}

.marquee > .overlay{
	z-index: 1111 !important;
}

#checklistTable td {
	text-align: center;
	padding: 2px;
}

#waitmsg{
	text-align: center;
	display: none;
}

#WhatsNew{
	background:#4ad504;    display: inline-block;
    text-transform: uppercase;
    height: 19px;
    line-height: 19px;
    font-size: 9px;
    font-weight: 700;
    color: #fff;
    border-radius: 10px;
    padding: 0 8px;
}
</style>
{/literal}

<link href="layouts/v7/modules/CTMobileSettings/style.css" rel="stylesheet">
<div class="container-fluid">
		<div class="row-fluid top_header">
			<div class="col-lg-6">
				<div class="logo dashboard_head_pad">{vtranslate("MODULE_LBL",$MODULE)}</div>
			</div>
			<div class="col-lg-6">
				<div class="row dashboard_head_pad">
					  <div class="col-lg-4">
					  </div>
					  <div class="col-lg-2 dashboard_head_pad">
					  	{if strtolower($LICENSE_DATA['Plan']) eq 'free'}
					  	<a href="{CTMobileSettings_Module_Model::$CTMOBILE_MYACCOUNT_URL}" title="{vtranslate("Upgrade to premium version to get more feature",$MODULE)}" id="help_btn" data-url="{CTMobileSettings_Module_Model::$CTMOBILE_MYACCOUNT_URL}"><img src="layouts/v7/modules/CTMobileSettings/images/upgrade-icon.png" alt="Icon" style="height:40px;"></a>
			            {/if}
			          </div>
			          <div class="col-lg-2 dashboard_head_pad pull-right">
			            <a href="#" title="{vtranslate("Help",$MODULE)}" id="help_btn" data-url="{CTMobileSettings_Module_Model::$CTMOBILE_HELP_URL}"><img src="layouts/v7/modules/CTMobileSettings/images/question-icon.png" alt="Icon" ></a>
			          </div>
					  <div class="col-lg-2 dashboard_head_pad pull-right">
						<a href="#" title="{vtranslate("Apple Store",$MODULE)}" id="ios_btn" data-url="{CTMobileSettings_Module_Model::$CTMOBILE_APPLE_STORE_URL}"><img src="layouts/v7/modules/CTMobileSettings/images/apple-icon.png" alt="Icon"></a>
					  </div>
			          <div class="col-lg-2 dashboard_head_pad pull-right">
			             <a href="#" title="{vtranslate("Android Store",$MODULE)}" id="android_btn" data-url="{CTMobileSettings_Module_Model::$CTMOBILE_ANDROID_STORE_URL}"><img src="layouts/v7/modules/CTMobileSettings/images/android-icon.png" alt="Icon"></a>
			          </div>

			          {*code added by sapna start*} 
						{if $CT_REQUIREMENTS eq false}
							<div class="col-lg-2 dashboard_head_pad pull-right">
							 <a class="showErrorModal" href="#" title="{vtranslate("Error",$MODULE)}" id="android_btn"><img src="layouts/v7/modules/CTMobileSettings/images/error.png" alt="Icon" style="margin-top: -8px;"></a>
							</div>
						{/if}
				       {* code end *}
				</div>
			</div>
		</div>
</div>
{if strtolower($LICENSE_DATA['Plan']) eq 'free'}
<div class="container-fluid">
		<div class="row-fluid" style="text-align:center;font-size:16px;background:#4d9ffb;color:#fff;">
			{vtranslate('Do you Love CRMTiger Apps ? Would Like to enjoy more features ?',$MODULE)}
			<a href="{CTMobileSettings_Module_Model::$CTMOBILE_MYACCOUNT_URL}" target="_blank">{vtranslate('Click here',$MODULE)}</a>
			{vtranslate('to upgrade to premium plan to enjoy more features',$MODULE)}
		</center>
		</div>
</div>
{/if}
<div class="marquee">
  {if $CT_REQUIREMENTS eq false}
  	
  <div id="myModal" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">{vtranslate('Verifying extensions updates...','CTMobileSettings')}</h4>
          </div>
	    	<table id="checklistTable" border="1" cellspacing="2" style="width:90%; margin:10px 0px 10px 10px;">
                 
                    <tr>
                      <td><b>{vtranslate('Features','CTMobileSettings')}</b></td>.
                      <td><b>{vtranslate('Status','CTMobileSettings')}</b></td>
                      <td><b>{vtranslate('Action','CTMobileSettings')}</b></td>
                    </tr>
					   {foreach from=$CT_REQUIREMENTS_DATA key=keys item=name}	
			                    <tr>
			                      <td>{vtranslate($CT_REQUIREMENTS_DATA[$keys]['requirements_module'],$CT_REQUIREMENTS_DATA[$keys]['requirements_module'])}</td>
						{if $CT_REQUIREMENTS_DATA[$keys]['requirements'] eq 1}	
						     <td><span style="color:green;">{vtranslate('Success','CTMobileSettings')}</span></td>			
						     <td>&nbsp;</td>	
						{else}
							
							<td><span style="color:red;" title="{$CT_REQUIREMENTS_DATA[$keys]['requirements_desc']}">{vtranslate('Fail !!!','CTMobileSettings')}</span></td>
							<td><a class="upgrade" href="javascript:updateModule('{$CT_REQUIREMENTS_DATA[$keys]['requirements_module']}')">{vtranslate('Install','CTMobileSettings')}</a></td>
						{/if}    
                    </tr>
				   {/foreach}
                 	
            </table>
            <div><strong>{vtranslate('Note : If you\'re experience any problem in installation of above extensions.','CTMobileSettings')}</strong> 
			<br/><strong><a href="https://kb.crmtiger.com/knowledge-base/error-code-solutions/" target="_blank" style="color: rgb(17, 85, 204);" onmouseover="this.style.color='#00008b'" onmouseout="this.style.color='#15c'">{vtranslate('Click here','CTMobileSettings')}</a> {vtranslate('to download and install manually one by one all updated extensions related to CRMTiger Mobile Apps','CTMobileSettings')}</strong>
			</div>
            <div id="waitmsg">
            	<p>{vtranslate('Please wait while we are updating extensions for you...','CTMobileSettings')}</p>
            	<p>{vtranslate('It will take around 1-2 minutes for extensions to update..','CTMobileSettings')}</p>
            </div>	
        </div>

    </div>
  </div>
  
  {/if}
</div>
<div class="container-fluid dashboard_container_box">
     <div class="row-fluid" style="display: inline-block;width: 100%;">
        <div class="col-md-3 fr_bx">
           <div class="dashboard_icon_box">
             <div class="dash_box">
				<div class="col-md-12">
                  <center><h4 class="mn_ttl">{vtranslate("Active Users",$MODULE)}</h4></center> 
                </div> 
				<div style="display: inline-block;width: 100%;"><hr></div>
                <div class="col-md-6 center-block">
                    <div class="midd-img-one">
                      <img src="layouts/v7/modules/CTMobileSettings/images/active-users.png" alt="Icon"> 
                    </div>
                </div>
                <div class="col-md-6">
                     <h4 class="text-right count_dwn"><b>{$ACTIVE_USER}</b></h4>
                </div>
             </div>
           </div>
        </div>
        <div class="col-md-3 fr_bx">
           <div class="dashboard_icon_box">
             <div class="dash_box">
				<div class="col-md-12">
                  <center><h4 class="mn_ttl">{vtranslate("Mobile Users",$MODULE)}</h4></center>
                </div>   
				<div style="display: inline-block;width: 100%;"><hr></div>				
                <div class="col-md-6 center-block ">
                    <div class="midd-img-one">
                      <img src="layouts/v7/modules/CTMobileSettings/images/mobile-users.png" alt="Icon">
                    </div>
                </div>
                <div class="col-md-6">
                     <h4 class="text-right count_dwn"><b>{$MOBILE_USER}</b></h4>
                </div>
             </div>
           </div>
        </div>
        <div class="col-md-3 fr_bx">
           <div class="dashboard_icon_box">
             <div class="dash_box">
				<div class="col-md-12">
                  <center><h4 class="mn_ttl">{vtranslate("Checked-In Meetings",$MODULE)}</h4></center>
                </div>
				<div style="display: inline-block;width: 100%;"><hr></div>
                <div class="col-md-6 center-block ">
                    <div class="midd-img-one">
                      <img src="layouts/v7/modules/CTMobileSettings/images/cheked-in.png" alt="Icon"> 
                    </div>
                </div>
                <div class="col-md-6">
                     <h4 class="text-right count_dwn"><b>{$MEETING_RECORDS}</b></h4>
                </div>
             </div>
           </div>
        </div>
        <div class="col-md-3 fr_bx">
           <div class="dashboard_icon_box">
             <div class="dash_box">
				<div class="col-md-12">
                  <center><h4 class="mn_ttl">{vtranslate("Checked-Out Meetings",$MODULE)}</h4></center>
                </div> 
				<div style="display: inline-block;width: 100%;"><hr></div>				
                <div class="col-md-6 center-block ">
                    <div class="midd-img-one">
                      <img src="layouts/v7/modules/CTMobileSettings/images/cheked-out.png" alt="Icon"> 
                    </div>
                </div>
                <div class="col-md-6">
                     <h4 class="text-right count_dwn"><b>{$CHECKOUT_RECORDS}</b></h4>
                </div>
             </div>
           </div>
        </div>
     </div>



     <div class="row-fluid">
		<div class="col-md-12">
			<section id="pinBoot">
				{if $CURRENT_USER->get('is_admin') eq 'on'}
				<article class="white-panel">
					<div class="middle-second-boc">
						<h3><span class="glyphicon glyphicon-briefcase"></span> {vtranslate("LBL_ACCOUNT_SUMMARY",$MODULE)}
						<a href="https://kb.crmtiger.com/article-categories/mobileapps/" target="_blank"><span class="glyphicon glyphicon-question-sign pull-right" style="font-size:25px"></span></a></h3> 

						<div class="box_count">
						   <p>{vtranslate("LBL_ORDER",$MODULE)} : <b>{$LICENSE_DATA['ORDER_ID']}</b></p>
						   
						   {if $LICENSE_DATA['Plan'] neq 'Premium ( Yearly )'}
						   		<p>{vtranslate("LBL_MY_PLAN",$MODULE)} : <b>{$LICENSE_DATA['Plan']}</b>
						   		<button class="upgrade" onclick="window.open('{CTMobileSettings_Module_Model::$CTMOBILE_MYACCOUNT_URL}','_blank');" data-url="{CTMobileSettings_Module_Model::$CTMOBILE_MYACCOUNT_URL}">{vtranslate("upgrade",$MODULE)}</button></p>
						   {else}
						   		<p>{vtranslate("LBL_MY_PLAN",$MODULE)} : <b>{$LICENSE_DATA['Plan']}</b>
						   {/if}
						   {if strtolower($LICENSE_DATA['Plan']) neq 'free'}
							  <p>{vtranslate("Next renewal date",$MODULE)} : <b>{Vtiger_Date_UIType::getDisplayValue($LICENSE_DATA['NextPaymentDate'])}</b></p>
						   {/if}

						   <p class="ctbtn" title="{vtranslate("LBL_LICENSE_CONFIGURATION",$MODULE)}" data-url="{CTMobileSettings_Module_Model::$CTMOBILE_LICENSE_DETAILVIEW_URL}"><a href="{CTMobileSettings_Module_Model::$CTMOBILE_LICENSE_DETAILVIEW_URL}"><b>{vtranslate("LBL_LICENSE_CONFIGURATION",$MODULE)}</b><img src="layouts/v7/modules/CTMobileSettings/images/right-arrow.png" alt="Icon" class="dash_arrow"></a></p>
						   
						   {if strtolower($LICENSE_DATA['Plan']) eq 'free'}
						   <p class="ctbtn" title="{vtranslate("BTN_CTMOBILE_ACCESS_USER",$MODULE)}" data-url="{CTMobileSettings_Module_Model::$CTMOBILE_ACCESSUSER_URL}"><a href="{CTMobileSettings_Module_Model::$CTMOBILE_ACCESSUSER_URL}"><b>{vtranslate("BTN_CTMOBILE_ACCESS_USER",$MODULE)}</b><img src="layouts/v7/modules/CTMobileSettings/images/right-arrow.png" alt="Icon" class="dash_arrow"></a></p>
						   {/if}

						   <p class="ctbtn" title="{vtranslate("Setup Language for Mobile",$MODULE)}" data-url="{CTMobileSettings_Module_Model::$CTMOBILE_LANGUAGE_URL}"><a href="{CTMobileSettings_Module_Model::$CTMOBILE_LANGUAGE_URL}"><b>{vtranslate("Setup Language for Mobile",$MODULE)}</b><img src="layouts/v7/modules/CTMobileSettings/images/right-arrow.png" alt="Icon" class="dash_arrow"></a></p>

						   <p class="ctbtn" title="{vtranslate("LBL_CLOSE_ACCOUNT",$MODULE)}" id="unInstallCTMobile"><a href="#"><b>{vtranslate("LBL_CLOSE_ACCOUNT",$MODULE)}</b><img src="layouts/v7/modules/CTMobileSettings/images/right-arrow.png" alt="Icon" class="dash_arrow "></a></p>
						</div>
					</div>
				</article>
				{/if}


				{if $CURRENT_USER->get('is_admin') eq 'on'}
				<article class="white-panel">
				  {if strtolower($LICENSE_DATA['Plan']) eq 'free'}
					 <button class="blur_box_btn" data-url="{CTMobileSettings_Module_Model::$CTMOBILE_MYACCOUNT_URL}">{vtranslate("Premium",$MODULE)}</button>
					  <div class="overlay"></div>
					{/if}
				  <div class="col-md-12 col-xs-12 backgeound_comm_class">
				   <div class="middle-second-boc">
						<h3><span class="glyphicon glyphicon-cog"></span> {vtranslate("General Settings",$MODULE)}
						<a href="https://kb.crmtiger.com/article-categories/mobileapps/" target="_blank"><span class="glyphicon glyphicon-question-sign pull-right" style="font-size:25px"></span></a></h3>
						  <div class="box_count">

						  	<p class="ctbtn" title="{vtranslate('Feature access management','CTMobileSettings')}" data-url="{CTMobileSettings_Module_Model::$CTMOBILE_USER_SETTINGS_URL}" style="font-size:16px;margin-bottom:8px !important;"><a href="{CTMobileSettings_Module_Model::$CTMOBILE_USER_SETTINGS_URL}"><b>{vtranslate('Feature access management','CTMobileSettings')}</b><img src="layouts/v7/modules/CTMobileSettings/images/right-arrow.png" alt="Icon" class="dash_arrow "></a></p>

						  	<p>{vtranslate("Allow access to various CRMtiger Apps feature",$MODULE)}</p>

							<ul>
							<li>{vtranslate("Access to CRMTiger Mobile App",$MODULE)}</li>
							<li>{vtranslate("Module Management",$MODULE)} <img src="layouts/v7/modules/CTMobileSettings/icon/new.png"></li>
							<li>{vtranslate("Premium Feature management",$MODULE)} <img src="layouts/v7/modules/CTMobileSettings/icon/new.png"></li>
							<li>{vtranslate("Location tracking",$MODULE)}</li>
							<li>{vtranslate("Time tracking",$MODULE)}</li>
							<li>{vtranslate("Route planning",$MODULE)}</li>
							<li>{vtranslate("Call Logging",$MODULE)} <img src="layouts/v7/modules/CTMobileSettings/icon/new.png"/></li>
							</ul>

							<p class="ctbtn" title="{vtranslate("Fields access management",$MODULE)}" data-url="{CTMobileSettings_Module_Model::$CTMOBILE_FIELD_SETTINGS_URL}" style="font-size:16px;margin-bottom:8px !important;"><a href="{CTMobileSettings_Module_Model::$CTMOBILE_FIELD_SETTINGS_URL}"><b>{vtranslate("Fields access management",$MODULE)}</b><img src="layouts/v7/modules/CTMobileSettings/images/right-arrow.png" alt="Icon" class="dash_arrow "></a></p>

							<p>{vtranslate("Add/Setup fields for premium feature",$MODULE)}</p>

							<ul>
							<li>{vtranslate("Setup fields for vCard",$MODULE)}</li>
							<li>{vtranslate("Setup fields for BarCode",$MODULE)}</li>
							<li>{vtranslate("Setup fields for Signature / Pictures or Documents",$MODULE)}</li>
							</ul>

						  </div>

						  
					</div>
				  </div>
				</article>
				{/if}

				
		

				{if $CURRENT_USER->get('is_admin') eq 'on'}
				<article class="white-panel">
				   <div class="middle-second-boc ">
						<h3><span class="glyphicon glyphicon-saved"></span> {vtranslate("LBL_APP_UPDATES",$MODULE)} 
						<a href="https://kb.crmtiger.com/article-categories/mobileapps/" target="_blank"><span class="glyphicon glyphicon-question-sign pull-right" style="font-size:25px"></span></a></h3>
						
						<div class="box_count">
						   <p>{vtranslate("Your Version",$MODULE)} : <b>{$VERSION}</b></p>
						   <p>{vtranslate("LBL_LATEST_VERSION",$MODULE)} : <b>{$ext_ver}</b></p>
						   
						   {if $VERSION neq $ext_ver}
						   <p title="{vtranslate("LBL_CLICK_UPDATE",$MODULE)}" id="updatectmobile" data-url="{CTMobileSettings_Module_Model::$CTMOBILE_UPGRADEVIEW_URL}"><a href="#"><b>{vtranslate("LBL_CLICK_UPDATE",$MODULE)}</b><img src="layouts/v7/modules/CTMobileSettings/images/right-arrow.png" alt="Icon" class="dash_arrow"></a></p>
						   {else}
						   <p><b><label class="text text-success">{vtranslate("LBL_UPDATED_VERSION",$MODULE)}</label></b></p>
						   {/if}

						   <p class="ctbtn"><a target="_blank" href="{CTMobileSettings_Module_Model::$CTMOBILE_RELEASE_NOTE_URL}" title="{vtranslate("View Release Note",$MODULE)}"><b>{vtranslate("View Release Note",$MODULE)}</b><img src="layouts/v7/modules/CTMobileSettings/images/right-arrow.png" alt="Icon" class="dash_arrow"></a></p>

						   <a href="#" id="WhatsNew">What's New</a>
						</div>  
					</div>
				</article>
				{/if}
				
				{if $CURRENT_USER->get('is_admin') eq 'on'}
				<article class="white-panel">
					{if strtolower($LICENSE_DATA['Plan']) eq 'free'}
					 <button class="blur_box_btn" data-url="{CTMobileSettings_Module_Model::$CTMOBILE_MYACCOUNT_URL}">{vtranslate("Premium",$MODULE)}</button>
					  <div class="overlay"></div>
					{/if}
				   <div class="col-md-12 col-xs-12 backgeound_comm_class">
				   <div class="middle-second-boc ">
						<h3><span class="glyphicon glyphicon-map-marker"></span> {vtranslate("LBL_MAP_CONFIGURATION",$MODULE)} 
						<a href="https://kb.crmtiger.com/article-categories/mobileapps/" target="_blank"><span class="glyphicon glyphicon-question-sign pull-right" style="font-size:25px"></span></a></h3>
						
						<div class="box_count">
						   <p>{vtranslate("LBL_CTMOBILE_LIMITED_OFFER",$MODULE)}</p>

						   <p class="ctbtn"><a href="{CTMobileSettings_Module_Model::$CTMOBILE_GEOLOCATION_SETUP_URL}"><b>{vtranslate("GEO Location Settings",$MODULE)}</b><img src="layouts/v7/modules/CTMobileSettings/images/right-arrow.png" alt="Icon" class="dash_arrow"></a></p>
				
						   {vtranslate("CRMTiger provides the following Map related features",$MODULE)}
						   <ul>
						   <li>{vtranslate("Nearby Contacts view in Mobile app",$MODULE)}</li>
						   <li>{vtranslate("Live Tracking of Team(users) who enable their GPS",$MODULE)}</li>
						   <li>{vtranslate("Calculate Distance between two Location",$MODULE)}</li>
						   </ul>
						   
						</div>  
					  </div>
					 </div>
				</article>
				{/if}


				
				<article class="white-panel">
				  {if strtolower($LICENSE_DATA['Plan']) eq 'free'}
					 <button class="blur_box_btn" data-url="{CTMobileSettings_Module_Model::$CTMOBILE_MYACCOUNT_URL}">{vtranslate("Premium",$MODULE)}</button>
					  <div class="overlay"></div>
					{/if}
				  <div class="col-md-12 col-xs-12 backgeound_comm_class">
				   <div class="middle-second-boc">
						<h3><span class="fa fa-bar-chart"></span> {vtranslate("Reports & Analytics",$MODULE)}
						<a href="https://kb.crmtiger.com/article-categories/mobileapps/" target="_blank"><span class="glyphicon glyphicon-question-sign pull-right" style="font-size:25px"></span></a></h3>
						  <div class="box_count">

						  	<h4>{vtranslate("Team Activities Report",$MODULE)}</h4>
						  	
						  	
							<p class="ctbtn" title="{vtranslate("User activity on Map",$MODULE)}"  data-url="{CTMobileSettings_Module_Model::$CTMOBILE_TEAMTRACKING_URL}"><a href="{CTMobileSettings_Module_Model::$CTMOBILE_TEAMTRACKING_URL}"><b>{vtranslate("User activity on Map",$MODULE)}</b><img src="layouts/v7/modules/CTMobileSettings/images/right-arrow.png" alt="Icon" class="dash_arrow "></a></p>
							

							<p class="ctbtn" title="{vtranslate("Time Tracking Report",$MODULE)}"  data-url="{$TIME_TRACKING_LOG_URL}"><a href="{$TIME_TRACKING_LOG_URL}"><b>{vtranslate("Time Tracking Report",$MODULE)}</b><img src="layouts/v7/modules/CTMobileSettings/images/right-arrow.png" alt="Icon" class="dash_arrow "></a></p>

							<h4>{vtranslate("Meeting & Attendance Report",$MODULE)}</h4>

							<p class="ctbtn" title="{vtranslate("Meeting & Attendance (GEO Location)",$MODULE)}"  data-url="{$CTATTENDANCE_URL}"><a href="{$CTATTENDANCE_URL}"><b>{vtranslate("Meeting & Attendance (GEO Location)",$MODULE)}</b><img src="layouts/v7/modules/CTMobileSettings/images/right-arrow.png" alt="Icon" class="dash_arrow "></a></p>

							<h4>{vtranslate("LBL_ROUTE_PLANNING_REPORT",$MODULE)}</h4>

							<p class="ctbtn" title="{vtranslate("Route Activities",$MODULE)}" data-url="{CTMobileSettings_Module_Model::$CTMOBILE_ROUTE_ANALYTICS_URL}"><a href="{CTMobileSettings_Module_Model::$CTMOBILE_ROUTE_ANALYTICS_URL}"><b>{vtranslate("Route Activities",$MODULE)}</b><img src="layouts/v7/modules/CTMobileSettings/images/right-arrow.png" alt="Icon" class="dash_arrow "></a></p>

						  </div>

						  
					</div>
				  </div>
				</article>


				<article class="white-panel">
					{if strtolower($LICENSE_DATA['Plan']) eq 'free'}
					 <button class="blur_box_btn" data-url="{CTMobileSettings_Module_Model::$CTMOBILE_MYACCOUNT_URL}">{vtranslate("Premium",$MODULE)}</button>
					  <div class="overlay"></div>
					{/if}
					<div class="col-md-12 col-xs-12 backgeound_comm_class premium_box">
						<div class="middle-second-boc ">
							<h3><span class="glyphicon glyphicon-bell"></span> {vtranslate("Notification Settings",$MODULE)} 
							<a href="https://kb.crmtiger.com/article-categories/mobileapps/" target="_blank"><span class="glyphicon glyphicon-question-sign pull-right" style="font-size:25px"></span></a></h3>
							
							<div class="box_count">
							  {if $CURRENT_USER->get('is_admin') eq 'on'}
							   <p class="ctbtn" title="{vtranslate("Setup Important(Default) Notification",$MODULE)}" data-url="{CTMobileSettings_Module_Model::$NOTIFICATIONS_SETTINGS_URL}"><a href="{CTMobileSettings_Module_Model::$NOTIFICATIONS_SETTINGS_URL}"><b>{vtranslate("Setup Important(Default) Notification",$MODULE)}</b><img src="layouts/v7/modules/CTMobileSettings/images/right-arrow.png" alt="Icon" class="dash_arrow "></a> <img src="layouts/v7/modules/CTMobileSettings/icon/new.png"> </p>
							  {/if}

							  


							  {if $CURRENT_USER->get('is_admin') eq 'on'}
							   <p class="ctbtn" title="{vtranslate("Setup Notification Action from Workflow",$MODULE)}" data-url="{CTMobileSettings_Module_Model::$CTMOBILE_WORKFLOW_URL}"><a href="{CTMobileSettings_Module_Model::$CTMOBILE_WORKFLOW_URL}"><b>{vtranslate("Setup Notification Action from Workflow",$MODULE)}</b><img src="layouts/v7/modules/CTMobileSettings/images/right-arrow.png" alt="Icon" class="dash_arrow "></a></p>

							   <p><a class="text text-info" href="https://youtu.be/D34Uv-gIGNE" target="_blank"><span>{vtranslate("Click here to see how to setup ?",$MODULE)}</span></a></p>
							  {/if}
							   <p class="ctbtn" title="{vtranslate("Send Push Notification to Users",$MODULE)}"  data-url="{CTMobileSettings_Module_Model::$CTMOBILE_CTPUSHNOTIFICATION_URL}"><a href="{CTMobileSettings_Module_Model::$CTMOBILE_CTPUSHNOTIFICATION_URL}"><b>{vtranslate("Send Push Notification to Users",$MODULE)}</b><img src="layouts/v7/modules/CTMobileSettings/images/right-arrow.png" alt="Icon" class="dash_arrow"></a></p>
							   <br/>
							   <p class="ctbtn" title="{vtranslate("Notification Logs",$MODULE)}" data-url="{$CTPUSHNOTIFICATION_URL}"><a href="{$CTPUSHNOTIFICATION_URL}"><b>{vtranslate("Notification Logs",$MODULE)}</b><img src="layouts/v7/modules/CTMobileSettings/images/right-arrow.png" alt="Icon" class="dash_arrow "></a></p>

							   
							</div>
						</div>
					</div>
				</article>


			</section>
		</div>
    </div>
</div>










<style>
	
#pinBoot {
  position: relative;
  max-width: 100%;
  width: 100%;
}

.white-panel {
  position: absolute;
  background: white;
  border-radius:5px;
}

.white-panel h1 {
  font-size: 1em;
}
.white-panel h1 a {
  color: #A92733;
}
.white-panel:hover {
  box-shadow: 1px 1px 10px rgba(0, 0, 0, 0.5);
  margin-top: -5px;
  -webkit-transition: all 0.3s ease-in-out;
  -moz-transition: all 0.3s ease-in-out;
  -o-transition: all 0.3s ease-in-out;
  transition: all 0.3s ease-in-out;
}
</style>
<script>
	$(document).ready(function() {
$('#pinBoot').pinterest_grid({
no_columns: 3,
padding_x: 10,
padding_y: 10,
margin_bottom: 50,
single_column_breakpoint: 900
});
});

;(function ($, window, document, undefined) {
    var pluginName = 'pinterest_grid',
        defaults = {
            padding_x: 10,
            padding_y: 10,
            no_columns: 3,
            margin_bottom: 50,
            single_column_breakpoint: 700
        },
        columns,
        $article,
        article_width;

    function Plugin(element, options) {
        this.element = element;
        this.options = $.extend({}, defaults, options) ;
        this._defaults = defaults;
        this._name = pluginName;
        this.init();
    }

    Plugin.prototype.init = function () {
        var self = this,
        resize_finish;
        self.make_layout_change(self);
    };

    Plugin.prototype.calculate = function (single_column_mode) {
        var self = this,
            tallest = 0,
            row = 0,
            $container = $(this.element),
            container_width = $container.width();
            $article = $(this.element).children();

        if(single_column_mode === true) {
            article_width = $container.width() - self.options.padding_x;
        } else {
            article_width = ($container.width() - self.options.padding_x * self.options.no_columns) / self.options.no_columns;
        }

        $article.each(function() {
            $(this).css('width', article_width);
        });

        columns = self.options.no_columns;

        $article.each(function(index) {
            var current_column,
                left_out = 0,
                top = 0,
                $this = $(this),
                prevAll = $this.prevAll(),
                tallest = 0;

            if(single_column_mode === false) {
                current_column = (index % columns);
            } else {
                current_column = 0;
            }

            for(var t = 0; t < columns; t++) {
                $this.removeClass('c'+t);
            }

            if(index % columns === 0) {
                row++;
            }

            $this.addClass('c' + current_column);
            $this.addClass('r' + row);

            prevAll.each(function(index) {
                if($(this).hasClass('c' + current_column)) {
                    top += $(this).outerHeight() + self.options.padding_y;
                }
            });

            if(single_column_mode === true) {
                left_out = 0;
            } else {
                left_out = (index % columns) * (article_width + self.options.padding_x);
            }

            $this.css({
                'left': left_out,
                'top' : top
            });
        });

        this.tallest($container);
        $(window).resize();
    };

    Plugin.prototype.tallest = function (_container) {
        var column_heights = [],
            largest = 0;

        for(var z = 0; z < columns; z++) {
            var temp_height = 0;
            _container.find('.c'+z).each(function() {
                temp_height += $(this).outerHeight();
            });
            column_heights[z] = temp_height;
        }

        largest = Math.max.apply(Math, column_heights);
        _container.css('height', largest + (this.options.padding_y + this.options.margin_bottom));
    };

    Plugin.prototype.make_layout_change = function (_self) {
        if($(window).width() < _self.options.single_column_breakpoint) {
            _self.calculate(true);
        } else {
            _self.calculate(false);
        }
    };

    $.fn[pluginName] = function (options) {
        return this.each(function () {
            if (!$.data(this, 'plugin_' + pluginName)) {
                $.data(this, 'plugin_' + pluginName,
                new Plugin(this, options));
            }
        });
    }

})(jQuery, window, document);
</script>
