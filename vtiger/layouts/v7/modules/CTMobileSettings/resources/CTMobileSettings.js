 /*+*******************************************************************************
 * The content of this file is subject to the CRMTiger Pro license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is vTiger
 * The Modified Code of the Original Code owned by https://crmtiger.com/
 * Portions created by CRMTiger.com are Copyright(C) CRMTiger.com
 * All Rights Reserved.
  ***************************************************************************** */

jQuery(document).ready(function(){
	
	jQuery(".menuBar").children(".span9").css("width","60%");
    jQuery(".menuBar").children(".span3").css("width","40%");
	count = 0;
	var url = "index.php?module=CTMobileSettings&action=chkPermission&mode=GetRequirement";
	var params = {
		"url":url
	};
	app.request.post(params).then(
		  function(err, data){
			if(err === null) {
				count = data.count;
				var is_admin = data.is_admin;
				if(is_admin){
					if(count > 0){
						 var bgColor='FF0000';
						 var msg='Extension installation has not been completed.';
						 var btn='<button class="btn btn-danger" style="margin-right:5px;" onclick="location.href=\'index.php?module=CTMobileSettings&parent=Settings&view=Details&mode=step1\'">Complete Install</button>';
						 var VTPremiumIcon = ['<li class="dropdown">',
													  '<div style="margin-top: 13px;" class="">',
														'<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" style="padding: 10px;">',
														  '<img   src="layouts/v7/modules/CTMobileSettings/img/CRMTiger.png" >',
														'</a>',
														'<div class="dropdown-menu" role="menu">',
														  '<div class="row">',
															'<div class="col-lg-12" style="min-width: 350px; padding: 10px 30px;">'+msg+'</div>',
														  '</div>',
														  '<div class="clearfix">',
															'<hr style="margin: 10px 0 !important">',
															  '<div class="text-center">'+btn+'</div>',
															'</div>',
														  '</div>',
														'</div>',
													'</li>'].join('');

							var headerIcons = $('#navbar ul.nav.navbar-nav');
							if (headerIcons.length > 0){
								headerIcons.first().prepend(VTPremiumIcon);
							}
					}else{
						var url = "index.php?module=CTMobileSettings&action=chkPermission&mode=GetLicense";
						var params = {
							"url":url
						};
						app.request.post(params).then(
						  function(err, data){
							if(err === null) {
								if(data.result === 0){
									 var bgColor='FFFF00';
									 var msg='License Key Setup has not been completed.';
									 var btn='<button class="btn btn-warning" style="margin-right:5px;" onclick="location.href=\'index.php?module=CTMobileSettings&parent=Settings&view=LicenseDetail\'">Setup License Key</button>';
									 var VTPremiumIcon = ['<li class="dropdown">',
															  '<div style="margin-top: 13px;" class="">',
																'<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" style="padding: 10px;">',
																  '<img  src="layouts/v7/modules/CTMobileSettings/img/CRMTiger.png" >',
																'</a>',
																'<div class="dropdown-menu" role="menu">',
																  '<div class="row">',
																	'<div class="col-lg-12" style="min-width: 350px; padding: 10px 30px;">'+msg+'</div>',
																  '</div>',
																  '<div class="clearfix">',
																	'<hr style="margin: 10px 0 !important">',
																	  '<div class="text-center">'+btn+'</div>',
																	'</div>',
																  '</div>',
																'</div>',
															'</li>'].join('');

									var headerIcons = $('#navbar ul.nav.navbar-nav');
									if (headerIcons.length > 0){
										headerIcons.first().prepend(VTPremiumIcon);
									}
								}else{

									var css = '<style>';
									css+=' .circle {margin: auto;overflow: hidden;';
									css+=' span {display: block;font-size:240px;color: #fff;height: 500px;width: 500px;text-align: center;padding-top: 24%;}}';

									css+=' .circle {-webkit-animation:grow 4s 5;}';

									css+=' @-webkit-keyframes grow {0% {-webkit-transform: scale( 0 );-moz-transform: scale( 0 );-o-transform: scale( 0 );-ms-transform: scale( 0 );transform: scale( 0.5 );}';
									css+=' 50% {-webkit-transform: scale( 0.1 );-moz-transform: scale( 0.1 );-o-transform: scale( 0.1 );-ms-transform: scale( 0.1 );transform: scale( 1.0 );}';
  									css+=' 100% {-webkit-transform: scale( 0 );-moz-transform: scale( 0 );-o-transform: scale( 0 );-ms-transform: scale( 0 );transform: scale( 0.5 );}}  </style>';


									var bgColor='008000';
									 var msg='License Key Setup and Extension Installation has been completed.';
									 var btn='<button class="btn btn-success" style="margin-right:5px;" onclick="location.href=\'index.php?module=CTMobileSettings&parent=Settings&view=Details\'">'+app.vtranslate('CRMTiger Apps Dashboard')+'</button>';
									 var VTPremiumIcon = ['<li class="dropdown">'+css,
															  '<div style="margin-top: 13px;" class="">',
																'<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" style="padding: 10px;">',
																  '<img class="circle" src="layouts/v7/modules/CTMobileSettings/img/CRMTiger.png" >',
																'</a>',
																'<div class="dropdown-menu" role="menu">',
																  '<div class="row">',
																	'<div class="col-lg-12" style="min-width: 350px; padding: 10px 30px;">'+msg+'</div>',
																  '</div>',
																  '<div class="clearfix">',
																	'<hr style="margin: 10px 0 !important">',
																	  '<div class="text-center">'+btn+'</div>',
																	'</div>',
																  '</div>',
																'</div>',
															'</li>'].join('');

									var headerIcons = $('#navbar ul.nav.navbar-nav');
									if (headerIcons.length > 0){
										headerIcons.first().prepend(VTPremiumIcon);
									}
									
								}
							}
						});
					}	
				}
			}
	});
	
});



		
