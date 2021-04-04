<?php /* Smarty version Smarty-3.1.7, created on 2021-03-27 16:29:45
         compiled from "/home/hmvtkebv/public_html/vtigercrm/vtiger/includes/runtime/../../layouts/v7/modules/CTMobileSettings/CTMobileDetails.tpl" */ ?>
<?php /*%%SmartyHeaderCode:960723366605f5d79a5b114-47906844%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f2069aa1e9f8c2ac1010381bc7b46b5b3d7acc52' => 
    array (
      0 => '/home/hmvtkebv/public_html/vtigercrm/vtiger/includes/runtime/../../layouts/v7/modules/CTMobileSettings/CTMobileDetails.tpl',
      1 => 1616862048,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '960723366605f5d79a5b114-47906844',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'MODULE' => 0,
    'LICENSE_DATA' => 0,
    'CT_REQUIREMENTS' => 0,
    'CT_REQUIREMENTS_DATA' => 0,
    'keys' => 0,
    'ACTIVE_USER' => 0,
    'MOBILE_USER' => 0,
    'MEETING_RECORDS' => 0,
    'CHECKOUT_RECORDS' => 0,
    'CURRENT_USER' => 0,
    'VERSION' => 0,
    'ext_ver' => 0,
    'TIME_TRACKING_LOG_URL' => 0,
    'CTATTENDANCE_URL' => 0,
    'CTPUSHNOTIFICATION_URL' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_605f5d79b2822',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_605f5d79b2822')) {function content_605f5d79b2822($_smarty_tpl) {?>

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


<link href="layouts/v7/modules/CTMobileSettings/style.css" rel="stylesheet">
<div class="container-fluid">
		<div class="row-fluid top_header">
			<div class="col-lg-6">
				<div class="logo dashboard_head_pad"><?php echo vtranslate("MODULE_LBL",$_smarty_tpl->tpl_vars['MODULE']->value);?>
</div>
			</div>
			<div class="col-lg-6">
				<div class="row dashboard_head_pad">
					  <div class="col-lg-4">
					  </div>
					  <div class="col-lg-2 dashboard_head_pad">
					  	<?php if (strtolower($_smarty_tpl->tpl_vars['LICENSE_DATA']->value['Plan'])=='free'){?>
					  	<a href="<?php echo CTMobileSettings_Module_Model::$CTMOBILE_MYACCOUNT_URL;?>
" title="<?php echo vtranslate("Upgrade to premium version to get more feature",$_smarty_tpl->tpl_vars['MODULE']->value);?>
" id="help_btn" data-url="<?php echo CTMobileSettings_Module_Model::$CTMOBILE_MYACCOUNT_URL;?>
"><img src="layouts/v7/modules/CTMobileSettings/images/upgrade-icon.png" alt="Icon" style="height:40px;"></a>
			            <?php }?>
			          </div>
			          <div class="col-lg-2 dashboard_head_pad pull-right">
			            <a href="#" title="<?php echo vtranslate("Help",$_smarty_tpl->tpl_vars['MODULE']->value);?>
" id="help_btn" data-url="<?php echo CTMobileSettings_Module_Model::$CTMOBILE_HELP_URL;?>
"><img src="layouts/v7/modules/CTMobileSettings/images/question-icon.png" alt="Icon" ></a>
			          </div>
					  <div class="col-lg-2 dashboard_head_pad pull-right">
						<a href="#" title="<?php echo vtranslate("Apple Store",$_smarty_tpl->tpl_vars['MODULE']->value);?>
" id="ios_btn" data-url="<?php echo CTMobileSettings_Module_Model::$CTMOBILE_APPLE_STORE_URL;?>
"><img src="layouts/v7/modules/CTMobileSettings/images/apple-icon.png" alt="Icon"></a>
					  </div>
			          <div class="col-lg-2 dashboard_head_pad pull-right">
			             <a href="#" title="<?php echo vtranslate("Android Store",$_smarty_tpl->tpl_vars['MODULE']->value);?>
" id="android_btn" data-url="<?php echo CTMobileSettings_Module_Model::$CTMOBILE_ANDROID_STORE_URL;?>
"><img src="layouts/v7/modules/CTMobileSettings/images/android-icon.png" alt="Icon"></a>
			          </div>

			           
						<?php if ($_smarty_tpl->tpl_vars['CT_REQUIREMENTS']->value==false){?>
							<div class="col-lg-2 dashboard_head_pad pull-right">
							 <a class="showErrorModal" href="#" title="<?php echo vtranslate("Error",$_smarty_tpl->tpl_vars['MODULE']->value);?>
" id="android_btn"><img src="layouts/v7/modules/CTMobileSettings/images/error.png" alt="Icon" style="margin-top: -8px;"></a>
							</div>
						<?php }?>
				       
				</div>
			</div>
		</div>
</div>
<?php if (strtolower($_smarty_tpl->tpl_vars['LICENSE_DATA']->value['Plan'])=='free'){?>
<div class="container-fluid">
		<div class="row-fluid" style="text-align:center;font-size:16px;background:#4d9ffb;color:#fff;">
			<?php echo vtranslate('Do you Love CRMTiger Apps ? Would Like to enjoy more features ?',$_smarty_tpl->tpl_vars['MODULE']->value);?>

			<a href="<?php echo CTMobileSettings_Module_Model::$CTMOBILE_MYACCOUNT_URL;?>
" target="_blank"><?php echo vtranslate('Click here',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</a>
			<?php echo vtranslate('to upgrade to premium plan to enjoy more features',$_smarty_tpl->tpl_vars['MODULE']->value);?>

		</center>
		</div>
</div>
<?php }?>
<div class="marquee">
  <?php if ($_smarty_tpl->tpl_vars['CT_REQUIREMENTS']->value==false){?>
  	
  <div id="myModal" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title"><?php echo vtranslate('Verifying extensions updates...','CTMobileSettings');?>
</h4>
          </div>
	    	<table id="checklistTable" border="1" cellspacing="2" style="width:90%; margin:10px 0px 10px 10px;">
                 
                    <tr>
                      <td><b><?php echo vtranslate('Features','CTMobileSettings');?>
</b></td>.
                      <td><b><?php echo vtranslate('Status','CTMobileSettings');?>
</b></td>
                      <td><b><?php echo vtranslate('Action','CTMobileSettings');?>
</b></td>
                    </tr>
					   <?php  $_smarty_tpl->tpl_vars['name'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['name']->_loop = false;
 $_smarty_tpl->tpl_vars['keys'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['CT_REQUIREMENTS_DATA']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['name']->key => $_smarty_tpl->tpl_vars['name']->value){
$_smarty_tpl->tpl_vars['name']->_loop = true;
 $_smarty_tpl->tpl_vars['keys']->value = $_smarty_tpl->tpl_vars['name']->key;
?>	
			                    <tr>
			                      <td><?php echo vtranslate($_smarty_tpl->tpl_vars['CT_REQUIREMENTS_DATA']->value[$_smarty_tpl->tpl_vars['keys']->value]['requirements_module'],$_smarty_tpl->tpl_vars['CT_REQUIREMENTS_DATA']->value[$_smarty_tpl->tpl_vars['keys']->value]['requirements_module']);?>
</td>
						<?php if ($_smarty_tpl->tpl_vars['CT_REQUIREMENTS_DATA']->value[$_smarty_tpl->tpl_vars['keys']->value]['requirements']==1){?>	
						     <td><span style="color:green;"><?php echo vtranslate('Success','CTMobileSettings');?>
</span></td>			
						     <td>&nbsp;</td>	
						<?php }else{ ?>
							
							<td><span style="color:red;" title="<?php echo $_smarty_tpl->tpl_vars['CT_REQUIREMENTS_DATA']->value[$_smarty_tpl->tpl_vars['keys']->value]['requirements_desc'];?>
"><?php echo vtranslate('Fail !!!','CTMobileSettings');?>
</span></td>
							<td><a class="upgrade" href="javascript:updateModule('<?php echo $_smarty_tpl->tpl_vars['CT_REQUIREMENTS_DATA']->value[$_smarty_tpl->tpl_vars['keys']->value]['requirements_module'];?>
')"><?php echo vtranslate('Install','CTMobileSettings');?>
</a></td>
						<?php }?>    
                    </tr>
				   <?php } ?>
                 	
            </table>
            <div><strong><?php echo vtranslate('Note : If you\'re experience any problem in installation of above extensions.','CTMobileSettings');?>
</strong> 
			<br/><strong><a href="https://kb.crmtiger.com/knowledge-base/error-code-solutions/" target="_blank" style="color: rgb(17, 85, 204);" onmouseover="this.style.color='#00008b'" onmouseout="this.style.color='#15c'"><?php echo vtranslate('Click here','CTMobileSettings');?>
</a> <?php echo vtranslate('to download and install manually one by one all updated extensions related to CRMTiger Mobile Apps','CTMobileSettings');?>
</strong>
			</div>
            <div id="waitmsg">
            	<p><?php echo vtranslate('Please wait while we are updating extensions for you...','CTMobileSettings');?>
</p>
            	<p><?php echo vtranslate('It will take around 1-2 minutes for extensions to update..','CTMobileSettings');?>
</p>
            </div>	
        </div>

    </div>
  </div>
  
  <?php }?>
</div>
<div class="container-fluid dashboard_container_box">
     <div class="row-fluid" style="display: inline-block;width: 100%;">
        <div class="col-md-3 fr_bx">
           <div class="dashboard_icon_box">
             <div class="dash_box">
				<div class="col-md-12">
                  <center><h4 class="mn_ttl"><?php echo vtranslate("Active Users",$_smarty_tpl->tpl_vars['MODULE']->value);?>
</h4></center> 
                </div> 
				<div style="display: inline-block;width: 100%;"><hr></div>
                <div class="col-md-6 center-block">
                    <div class="midd-img-one">
                      <img src="layouts/v7/modules/CTMobileSettings/images/active-users.png" alt="Icon"> 
                    </div>
                </div>
                <div class="col-md-6">
                     <h4 class="text-right count_dwn"><b><?php echo $_smarty_tpl->tpl_vars['ACTIVE_USER']->value;?>
</b></h4>
                </div>
             </div>
           </div>
        </div>
        <div class="col-md-3 fr_bx">
           <div class="dashboard_icon_box">
             <div class="dash_box">
				<div class="col-md-12">
                  <center><h4 class="mn_ttl"><?php echo vtranslate("Mobile Users",$_smarty_tpl->tpl_vars['MODULE']->value);?>
</h4></center>
                </div>   
				<div style="display: inline-block;width: 100%;"><hr></div>				
                <div class="col-md-6 center-block ">
                    <div class="midd-img-one">
                      <img src="layouts/v7/modules/CTMobileSettings/images/mobile-users.png" alt="Icon">
                    </div>
                </div>
                <div class="col-md-6">
                     <h4 class="text-right count_dwn"><b><?php echo $_smarty_tpl->tpl_vars['MOBILE_USER']->value;?>
</b></h4>
                </div>
             </div>
           </div>
        </div>
        <div class="col-md-3 fr_bx">
           <div class="dashboard_icon_box">
             <div class="dash_box">
				<div class="col-md-12">
                  <center><h4 class="mn_ttl"><?php echo vtranslate("Checked-In Meetings",$_smarty_tpl->tpl_vars['MODULE']->value);?>
</h4></center>
                </div>
				<div style="display: inline-block;width: 100%;"><hr></div>
                <div class="col-md-6 center-block ">
                    <div class="midd-img-one">
                      <img src="layouts/v7/modules/CTMobileSettings/images/cheked-in.png" alt="Icon"> 
                    </div>
                </div>
                <div class="col-md-6">
                     <h4 class="text-right count_dwn"><b><?php echo $_smarty_tpl->tpl_vars['MEETING_RECORDS']->value;?>
</b></h4>
                </div>
             </div>
           </div>
        </div>
        <div class="col-md-3 fr_bx">
           <div class="dashboard_icon_box">
             <div class="dash_box">
				<div class="col-md-12">
                  <center><h4 class="mn_ttl"><?php echo vtranslate("Checked-Out Meetings",$_smarty_tpl->tpl_vars['MODULE']->value);?>
</h4></center>
                </div> 
				<div style="display: inline-block;width: 100%;"><hr></div>				
                <div class="col-md-6 center-block ">
                    <div class="midd-img-one">
                      <img src="layouts/v7/modules/CTMobileSettings/images/cheked-out.png" alt="Icon"> 
                    </div>
                </div>
                <div class="col-md-6">
                     <h4 class="text-right count_dwn"><b><?php echo $_smarty_tpl->tpl_vars['CHECKOUT_RECORDS']->value;?>
</b></h4>
                </div>
             </div>
           </div>
        </div>
     </div>



     <div class="row-fluid">
		<div class="col-md-12">
			<section id="pinBoot">
				<?php if ($_smarty_tpl->tpl_vars['CURRENT_USER']->value->get('is_admin')=='on'){?>
				<article class="white-panel">
					<div class="middle-second-boc">
						<h3><span class="glyphicon glyphicon-briefcase"></span> <?php echo vtranslate("LBL_ACCOUNT_SUMMARY",$_smarty_tpl->tpl_vars['MODULE']->value);?>

						<a href="https://kb.crmtiger.com/article-categories/mobileapps/" target="_blank"><span class="glyphicon glyphicon-question-sign pull-right" style="font-size:25px"></span></a></h3> 

						<div class="box_count">
						   <p><?php echo vtranslate("LBL_ORDER",$_smarty_tpl->tpl_vars['MODULE']->value);?>
 : <b><?php echo $_smarty_tpl->tpl_vars['LICENSE_DATA']->value['ORDER_ID'];?>
</b></p>
						   
						   <?php if ($_smarty_tpl->tpl_vars['LICENSE_DATA']->value['Plan']!='Premium ( Yearly )'){?>
						   		<p><?php echo vtranslate("LBL_MY_PLAN",$_smarty_tpl->tpl_vars['MODULE']->value);?>
 : <b><?php echo $_smarty_tpl->tpl_vars['LICENSE_DATA']->value['Plan'];?>
</b>
						   		<button class="upgrade" onclick="window.open('<?php echo CTMobileSettings_Module_Model::$CTMOBILE_MYACCOUNT_URL;?>
','_blank');" data-url="<?php echo CTMobileSettings_Module_Model::$CTMOBILE_MYACCOUNT_URL;?>
"><?php echo vtranslate("upgrade",$_smarty_tpl->tpl_vars['MODULE']->value);?>
</button></p>
						   <?php }else{ ?>
						   		<p><?php echo vtranslate("LBL_MY_PLAN",$_smarty_tpl->tpl_vars['MODULE']->value);?>
 : <b><?php echo $_smarty_tpl->tpl_vars['LICENSE_DATA']->value['Plan'];?>
</b>
						   <?php }?>
						   <?php if (strtolower($_smarty_tpl->tpl_vars['LICENSE_DATA']->value['Plan'])!='free'){?>
							  <p><?php echo vtranslate("Next renewal date",$_smarty_tpl->tpl_vars['MODULE']->value);?>
 : <b><?php echo Vtiger_Date_UIType::getDisplayValue($_smarty_tpl->tpl_vars['LICENSE_DATA']->value['NextPaymentDate']);?>
</b></p>
						   <?php }?>

						   <p class="ctbtn" title="<?php echo vtranslate("LBL_LICENSE_CONFIGURATION",$_smarty_tpl->tpl_vars['MODULE']->value);?>
" data-url="<?php echo CTMobileSettings_Module_Model::$CTMOBILE_LICENSE_DETAILVIEW_URL;?>
"><a href="<?php echo CTMobileSettings_Module_Model::$CTMOBILE_LICENSE_DETAILVIEW_URL;?>
"><b><?php echo vtranslate("LBL_LICENSE_CONFIGURATION",$_smarty_tpl->tpl_vars['MODULE']->value);?>
</b><img src="layouts/v7/modules/CTMobileSettings/images/right-arrow.png" alt="Icon" class="dash_arrow"></a></p>
						   
						   <?php if (strtolower($_smarty_tpl->tpl_vars['LICENSE_DATA']->value['Plan'])=='free'){?>
						   <p class="ctbtn" title="<?php echo vtranslate("BTN_CTMOBILE_ACCESS_USER",$_smarty_tpl->tpl_vars['MODULE']->value);?>
" data-url="<?php echo CTMobileSettings_Module_Model::$CTMOBILE_ACCESSUSER_URL;?>
"><a href="<?php echo CTMobileSettings_Module_Model::$CTMOBILE_ACCESSUSER_URL;?>
"><b><?php echo vtranslate("BTN_CTMOBILE_ACCESS_USER",$_smarty_tpl->tpl_vars['MODULE']->value);?>
</b><img src="layouts/v7/modules/CTMobileSettings/images/right-arrow.png" alt="Icon" class="dash_arrow"></a></p>
						   <?php }?>

						   <p class="ctbtn" title="<?php echo vtranslate("Setup Language for Mobile",$_smarty_tpl->tpl_vars['MODULE']->value);?>
" data-url="<?php echo CTMobileSettings_Module_Model::$CTMOBILE_LANGUAGE_URL;?>
"><a href="<?php echo CTMobileSettings_Module_Model::$CTMOBILE_LANGUAGE_URL;?>
"><b><?php echo vtranslate("Setup Language for Mobile",$_smarty_tpl->tpl_vars['MODULE']->value);?>
</b><img src="layouts/v7/modules/CTMobileSettings/images/right-arrow.png" alt="Icon" class="dash_arrow"></a></p>

						   <p class="ctbtn" title="<?php echo vtranslate("LBL_CLOSE_ACCOUNT",$_smarty_tpl->tpl_vars['MODULE']->value);?>
" id="unInstallCTMobile"><a href="#"><b><?php echo vtranslate("LBL_CLOSE_ACCOUNT",$_smarty_tpl->tpl_vars['MODULE']->value);?>
</b><img src="layouts/v7/modules/CTMobileSettings/images/right-arrow.png" alt="Icon" class="dash_arrow "></a></p>
						</div>
					</div>
				</article>
				<?php }?>


				<?php if ($_smarty_tpl->tpl_vars['CURRENT_USER']->value->get('is_admin')=='on'){?>
				<article class="white-panel">
				  <?php if (strtolower($_smarty_tpl->tpl_vars['LICENSE_DATA']->value['Plan'])=='free'){?>
					 <button class="blur_box_btn" data-url="<?php echo CTMobileSettings_Module_Model::$CTMOBILE_MYACCOUNT_URL;?>
"><?php echo vtranslate("Premium",$_smarty_tpl->tpl_vars['MODULE']->value);?>
</button>
					  <div class="overlay"></div>
					<?php }?>
				  <div class="col-md-12 col-xs-12 backgeound_comm_class">
				   <div class="middle-second-boc">
						<h3><span class="glyphicon glyphicon-cog"></span> <?php echo vtranslate("General Settings",$_smarty_tpl->tpl_vars['MODULE']->value);?>

						<a href="https://kb.crmtiger.com/article-categories/mobileapps/" target="_blank"><span class="glyphicon glyphicon-question-sign pull-right" style="font-size:25px"></span></a></h3>
						  <div class="box_count">

						  	<p class="ctbtn" title="<?php echo vtranslate('Feature access management','CTMobileSettings');?>
" data-url="<?php echo CTMobileSettings_Module_Model::$CTMOBILE_USER_SETTINGS_URL;?>
" style="font-size:16px;margin-bottom:8px !important;"><a href="<?php echo CTMobileSettings_Module_Model::$CTMOBILE_USER_SETTINGS_URL;?>
"><b><?php echo vtranslate('Feature access management','CTMobileSettings');?>
</b><img src="layouts/v7/modules/CTMobileSettings/images/right-arrow.png" alt="Icon" class="dash_arrow "></a></p>

						  	<p><?php echo vtranslate("Allow access to various CRMtiger Apps feature",$_smarty_tpl->tpl_vars['MODULE']->value);?>
</p>

							<ul>
							<li><?php echo vtranslate("Access to CRMTiger Mobile App",$_smarty_tpl->tpl_vars['MODULE']->value);?>
</li>
							<li><?php echo vtranslate("Module Management",$_smarty_tpl->tpl_vars['MODULE']->value);?>
 <img src="layouts/v7/modules/CTMobileSettings/icon/new.png"></li>
							<li><?php echo vtranslate("Premium Feature management",$_smarty_tpl->tpl_vars['MODULE']->value);?>
 <img src="layouts/v7/modules/CTMobileSettings/icon/new.png"></li>
							<li><?php echo vtranslate("Location tracking",$_smarty_tpl->tpl_vars['MODULE']->value);?>
</li>
							<li><?php echo vtranslate("Time tracking",$_smarty_tpl->tpl_vars['MODULE']->value);?>
</li>
							<li><?php echo vtranslate("Route planning",$_smarty_tpl->tpl_vars['MODULE']->value);?>
</li>
							<li><?php echo vtranslate("Call Logging",$_smarty_tpl->tpl_vars['MODULE']->value);?>
 <img src="layouts/v7/modules/CTMobileSettings/icon/new.png"/></li>
							</ul>

							<p class="ctbtn" title="<?php echo vtranslate("Fields access management",$_smarty_tpl->tpl_vars['MODULE']->value);?>
" data-url="<?php echo CTMobileSettings_Module_Model::$CTMOBILE_FIELD_SETTINGS_URL;?>
" style="font-size:16px;margin-bottom:8px !important;"><a href="<?php echo CTMobileSettings_Module_Model::$CTMOBILE_FIELD_SETTINGS_URL;?>
"><b><?php echo vtranslate("Fields access management",$_smarty_tpl->tpl_vars['MODULE']->value);?>
</b><img src="layouts/v7/modules/CTMobileSettings/images/right-arrow.png" alt="Icon" class="dash_arrow "></a></p>

							<p><?php echo vtranslate("Add/Setup fields for premium feature",$_smarty_tpl->tpl_vars['MODULE']->value);?>
</p>

							<ul>
							<li><?php echo vtranslate("Setup fields for vCard",$_smarty_tpl->tpl_vars['MODULE']->value);?>
</li>
							<li><?php echo vtranslate("Setup fields for BarCode",$_smarty_tpl->tpl_vars['MODULE']->value);?>
</li>
							<li><?php echo vtranslate("Setup fields for Signature / Pictures or Documents",$_smarty_tpl->tpl_vars['MODULE']->value);?>
</li>
							</ul>

						  </div>

						  
					</div>
				  </div>
				</article>
				<?php }?>

				
		

				<?php if ($_smarty_tpl->tpl_vars['CURRENT_USER']->value->get('is_admin')=='on'){?>
				<article class="white-panel">
				   <div class="middle-second-boc ">
						<h3><span class="glyphicon glyphicon-saved"></span> <?php echo vtranslate("LBL_APP_UPDATES",$_smarty_tpl->tpl_vars['MODULE']->value);?>
 
						<a href="https://kb.crmtiger.com/article-categories/mobileapps/" target="_blank"><span class="glyphicon glyphicon-question-sign pull-right" style="font-size:25px"></span></a></h3>
						
						<div class="box_count">
						   <p><?php echo vtranslate("Your Version",$_smarty_tpl->tpl_vars['MODULE']->value);?>
 : <b><?php echo $_smarty_tpl->tpl_vars['VERSION']->value;?>
</b></p>
						   <p><?php echo vtranslate("LBL_LATEST_VERSION",$_smarty_tpl->tpl_vars['MODULE']->value);?>
 : <b><?php echo $_smarty_tpl->tpl_vars['ext_ver']->value;?>
</b></p>
						   
						   <?php if ($_smarty_tpl->tpl_vars['VERSION']->value!=$_smarty_tpl->tpl_vars['ext_ver']->value){?>
						   <p title="<?php echo vtranslate("LBL_CLICK_UPDATE",$_smarty_tpl->tpl_vars['MODULE']->value);?>
" id="updatectmobile" data-url="<?php echo CTMobileSettings_Module_Model::$CTMOBILE_UPGRADEVIEW_URL;?>
"><a href="#"><b><?php echo vtranslate("LBL_CLICK_UPDATE",$_smarty_tpl->tpl_vars['MODULE']->value);?>
</b><img src="layouts/v7/modules/CTMobileSettings/images/right-arrow.png" alt="Icon" class="dash_arrow"></a></p>
						   <?php }else{ ?>
						   <p><b><label class="text text-success"><?php echo vtranslate("LBL_UPDATED_VERSION",$_smarty_tpl->tpl_vars['MODULE']->value);?>
</label></b></p>
						   <?php }?>

						   <p class="ctbtn"><a target="_blank" href="<?php echo CTMobileSettings_Module_Model::$CTMOBILE_RELEASE_NOTE_URL;?>
" title="<?php echo vtranslate("View Release Note",$_smarty_tpl->tpl_vars['MODULE']->value);?>
"><b><?php echo vtranslate("View Release Note",$_smarty_tpl->tpl_vars['MODULE']->value);?>
</b><img src="layouts/v7/modules/CTMobileSettings/images/right-arrow.png" alt="Icon" class="dash_arrow"></a></p>

						   <a href="#" id="WhatsNew">What's New</a>
						</div>  
					</div>
				</article>
				<?php }?>
				
				<?php if ($_smarty_tpl->tpl_vars['CURRENT_USER']->value->get('is_admin')=='on'){?>
				<article class="white-panel">
					<?php if (strtolower($_smarty_tpl->tpl_vars['LICENSE_DATA']->value['Plan'])=='free'){?>
					 <button class="blur_box_btn" data-url="<?php echo CTMobileSettings_Module_Model::$CTMOBILE_MYACCOUNT_URL;?>
"><?php echo vtranslate("Premium",$_smarty_tpl->tpl_vars['MODULE']->value);?>
</button>
					  <div class="overlay"></div>
					<?php }?>
				   <div class="col-md-12 col-xs-12 backgeound_comm_class">
				   <div class="middle-second-boc ">
						<h3><span class="glyphicon glyphicon-map-marker"></span> <?php echo vtranslate("LBL_MAP_CONFIGURATION",$_smarty_tpl->tpl_vars['MODULE']->value);?>
 
						<a href="https://kb.crmtiger.com/article-categories/mobileapps/" target="_blank"><span class="glyphicon glyphicon-question-sign pull-right" style="font-size:25px"></span></a></h3>
						
						<div class="box_count">
						   <p><?php echo vtranslate("LBL_CTMOBILE_LIMITED_OFFER",$_smarty_tpl->tpl_vars['MODULE']->value);?>
</p>

						   <p class="ctbtn"><a href="<?php echo CTMobileSettings_Module_Model::$CTMOBILE_GEOLOCATION_SETUP_URL;?>
"><b><?php echo vtranslate("GEO Location Settings",$_smarty_tpl->tpl_vars['MODULE']->value);?>
</b><img src="layouts/v7/modules/CTMobileSettings/images/right-arrow.png" alt="Icon" class="dash_arrow"></a></p>
				
						   <?php echo vtranslate("CRMTiger provides the following Map related features",$_smarty_tpl->tpl_vars['MODULE']->value);?>

						   <ul>
						   <li><?php echo vtranslate("Nearby Contacts view in Mobile app",$_smarty_tpl->tpl_vars['MODULE']->value);?>
</li>
						   <li><?php echo vtranslate("Live Tracking of Team(users) who enable their GPS",$_smarty_tpl->tpl_vars['MODULE']->value);?>
</li>
						   <li><?php echo vtranslate("Calculate Distance between two Location",$_smarty_tpl->tpl_vars['MODULE']->value);?>
</li>
						   </ul>
						   
						</div>  
					  </div>
					 </div>
				</article>
				<?php }?>


				
				<article class="white-panel">
				  <?php if (strtolower($_smarty_tpl->tpl_vars['LICENSE_DATA']->value['Plan'])=='free'){?>
					 <button class="blur_box_btn" data-url="<?php echo CTMobileSettings_Module_Model::$CTMOBILE_MYACCOUNT_URL;?>
"><?php echo vtranslate("Premium",$_smarty_tpl->tpl_vars['MODULE']->value);?>
</button>
					  <div class="overlay"></div>
					<?php }?>
				  <div class="col-md-12 col-xs-12 backgeound_comm_class">
				   <div class="middle-second-boc">
						<h3><span class="fa fa-bar-chart"></span> <?php echo vtranslate("Reports & Analytics",$_smarty_tpl->tpl_vars['MODULE']->value);?>

						<a href="https://kb.crmtiger.com/article-categories/mobileapps/" target="_blank"><span class="glyphicon glyphicon-question-sign pull-right" style="font-size:25px"></span></a></h3>
						  <div class="box_count">

						  	<h4><?php echo vtranslate("Team Activities Report",$_smarty_tpl->tpl_vars['MODULE']->value);?>
</h4>
						  	
						  	
							<p class="ctbtn" title="<?php echo vtranslate("User activity on Map",$_smarty_tpl->tpl_vars['MODULE']->value);?>
"  data-url="<?php echo CTMobileSettings_Module_Model::$CTMOBILE_TEAMTRACKING_URL;?>
"><a href="<?php echo CTMobileSettings_Module_Model::$CTMOBILE_TEAMTRACKING_URL;?>
"><b><?php echo vtranslate("User activity on Map",$_smarty_tpl->tpl_vars['MODULE']->value);?>
</b><img src="layouts/v7/modules/CTMobileSettings/images/right-arrow.png" alt="Icon" class="dash_arrow "></a></p>
							

							<p class="ctbtn" title="<?php echo vtranslate("Time Tracking Report",$_smarty_tpl->tpl_vars['MODULE']->value);?>
"  data-url="<?php echo $_smarty_tpl->tpl_vars['TIME_TRACKING_LOG_URL']->value;?>
"><a href="<?php echo $_smarty_tpl->tpl_vars['TIME_TRACKING_LOG_URL']->value;?>
"><b><?php echo vtranslate("Time Tracking Report",$_smarty_tpl->tpl_vars['MODULE']->value);?>
</b><img src="layouts/v7/modules/CTMobileSettings/images/right-arrow.png" alt="Icon" class="dash_arrow "></a></p>

							<h4><?php echo vtranslate("Meeting & Attendance Report",$_smarty_tpl->tpl_vars['MODULE']->value);?>
</h4>

							<p class="ctbtn" title="<?php echo vtranslate("Meeting & Attendance (GEO Location)",$_smarty_tpl->tpl_vars['MODULE']->value);?>
"  data-url="<?php echo $_smarty_tpl->tpl_vars['CTATTENDANCE_URL']->value;?>
"><a href="<?php echo $_smarty_tpl->tpl_vars['CTATTENDANCE_URL']->value;?>
"><b><?php echo vtranslate("Meeting & Attendance (GEO Location)",$_smarty_tpl->tpl_vars['MODULE']->value);?>
</b><img src="layouts/v7/modules/CTMobileSettings/images/right-arrow.png" alt="Icon" class="dash_arrow "></a></p>

							<h4><?php echo vtranslate("LBL_ROUTE_PLANNING_REPORT",$_smarty_tpl->tpl_vars['MODULE']->value);?>
</h4>

							<p class="ctbtn" title="<?php echo vtranslate("Route Activities",$_smarty_tpl->tpl_vars['MODULE']->value);?>
" data-url="<?php echo CTMobileSettings_Module_Model::$CTMOBILE_ROUTE_ANALYTICS_URL;?>
"><a href="<?php echo CTMobileSettings_Module_Model::$CTMOBILE_ROUTE_ANALYTICS_URL;?>
"><b><?php echo vtranslate("Route Activities",$_smarty_tpl->tpl_vars['MODULE']->value);?>
</b><img src="layouts/v7/modules/CTMobileSettings/images/right-arrow.png" alt="Icon" class="dash_arrow "></a></p>

						  </div>

						  
					</div>
				  </div>
				</article>


				<article class="white-panel">
					<?php if (strtolower($_smarty_tpl->tpl_vars['LICENSE_DATA']->value['Plan'])=='free'){?>
					 <button class="blur_box_btn" data-url="<?php echo CTMobileSettings_Module_Model::$CTMOBILE_MYACCOUNT_URL;?>
"><?php echo vtranslate("Premium",$_smarty_tpl->tpl_vars['MODULE']->value);?>
</button>
					  <div class="overlay"></div>
					<?php }?>
					<div class="col-md-12 col-xs-12 backgeound_comm_class premium_box">
						<div class="middle-second-boc ">
							<h3><span class="glyphicon glyphicon-bell"></span> <?php echo vtranslate("Notification Settings",$_smarty_tpl->tpl_vars['MODULE']->value);?>
 
							<a href="https://kb.crmtiger.com/article-categories/mobileapps/" target="_blank"><span class="glyphicon glyphicon-question-sign pull-right" style="font-size:25px"></span></a></h3>
							
							<div class="box_count">
							  <?php if ($_smarty_tpl->tpl_vars['CURRENT_USER']->value->get('is_admin')=='on'){?>
							   <p class="ctbtn" title="<?php echo vtranslate("Setup Important(Default) Notification",$_smarty_tpl->tpl_vars['MODULE']->value);?>
" data-url="<?php echo CTMobileSettings_Module_Model::$NOTIFICATIONS_SETTINGS_URL;?>
"><a href="<?php echo CTMobileSettings_Module_Model::$NOTIFICATIONS_SETTINGS_URL;?>
"><b><?php echo vtranslate("Setup Important(Default) Notification",$_smarty_tpl->tpl_vars['MODULE']->value);?>
</b><img src="layouts/v7/modules/CTMobileSettings/images/right-arrow.png" alt="Icon" class="dash_arrow "></a> <img src="layouts/v7/modules/CTMobileSettings/icon/new.png"> </p>
							  <?php }?>

							  


							  <?php if ($_smarty_tpl->tpl_vars['CURRENT_USER']->value->get('is_admin')=='on'){?>
							   <p class="ctbtn" title="<?php echo vtranslate("Setup Notification Action from Workflow",$_smarty_tpl->tpl_vars['MODULE']->value);?>
" data-url="<?php echo CTMobileSettings_Module_Model::$CTMOBILE_WORKFLOW_URL;?>
"><a href="<?php echo CTMobileSettings_Module_Model::$CTMOBILE_WORKFLOW_URL;?>
"><b><?php echo vtranslate("Setup Notification Action from Workflow",$_smarty_tpl->tpl_vars['MODULE']->value);?>
</b><img src="layouts/v7/modules/CTMobileSettings/images/right-arrow.png" alt="Icon" class="dash_arrow "></a></p>

							   <p><a class="text text-info" href="https://youtu.be/D34Uv-gIGNE" target="_blank"><span><?php echo vtranslate("Click here to see how to setup ?",$_smarty_tpl->tpl_vars['MODULE']->value);?>
</span></a></p>
							  <?php }?>
							   <p class="ctbtn" title="<?php echo vtranslate("Send Push Notification to Users",$_smarty_tpl->tpl_vars['MODULE']->value);?>
"  data-url="<?php echo CTMobileSettings_Module_Model::$CTMOBILE_CTPUSHNOTIFICATION_URL;?>
"><a href="<?php echo CTMobileSettings_Module_Model::$CTMOBILE_CTPUSHNOTIFICATION_URL;?>
"><b><?php echo vtranslate("Send Push Notification to Users",$_smarty_tpl->tpl_vars['MODULE']->value);?>
</b><img src="layouts/v7/modules/CTMobileSettings/images/right-arrow.png" alt="Icon" class="dash_arrow"></a></p>
							   <br/>
							   <p class="ctbtn" title="<?php echo vtranslate("Notification Logs",$_smarty_tpl->tpl_vars['MODULE']->value);?>
" data-url="<?php echo $_smarty_tpl->tpl_vars['CTPUSHNOTIFICATION_URL']->value;?>
"><a href="<?php echo $_smarty_tpl->tpl_vars['CTPUSHNOTIFICATION_URL']->value;?>
"><b><?php echo vtranslate("Notification Logs",$_smarty_tpl->tpl_vars['MODULE']->value);?>
</b><img src="layouts/v7/modules/CTMobileSettings/images/right-arrow.png" alt="Icon" class="dash_arrow "></a></p>

							   
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
<?php }} ?>