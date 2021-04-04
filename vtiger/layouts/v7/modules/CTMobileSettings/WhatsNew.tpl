{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}
{* modules/Vtiger/views/Popup.php *}
<style type="text/css">
    .page-applications-list-item-wrapper{
        display: block;
        margin-bottom:20px;
    }
    .platform-applications-list-item{
        position: relative;
        display: block;
        overflow: hidden;
        box-sizing: border-box;
        padding: 24px 28px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgb(76 76 76 / 45%);
        background: #fff;
        transition: all 0.35s ease;
        height: 100%;
    }
    .applications-list-item-header-info-title{
        color: #111;
        margin: 0;
        line-height: 1;
    }
    .applications-list-item-header-info-caption{
        line-height: 20px;
        margin-top: 8px;
        color: rgba(17,17,17,0.7);
    }
    .applications-list-item-triggers-item-badge{
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

    .newheading{
      padding: 10px 0 10px 0px;
      margin-left:30px;
    }

    .dasharrow{
          position: relative;
        /* float: right; */
        height: 14px;
        margin-left: 10px;
    }
</style>
{strip}
<div class="modal-dialog modal-lg">
    <div class="modal-content">
            <div class="modal-header">
                <div class="clearfix">
                    <div class="pull-right " >
                        <button type="button" class="close" aria-label="Close" data-dismiss="modal">
                            <span aria-hidden="true" class='fa fa-close'></span>
                        </button>
                    </div>
                    <h4 class="pull-left">
                        {vtranslate('What\'s New in Release 1.3.13','CTMobileSettings')}
                    </h4>
                </div>
            </div>
        <div class="modal-body">
            <div id="popupPageContainer" class="contentsDiv col-sm-12">
            
                <div id="popupContents" class="">
                  <div class="row">
                    <div class="newheading">
                     <a href="https://kb.crmtiger.com/knowledge-base/quick-record-search/" target="_blank"><b>{vtranslate('Quick Record Search','CTMobileSettings')}</b></a> : {vtranslate('Option to multiple field level Search','CTMobileSettings')}
                     <a href="https://kb.crmtiger.com/knowledge-base/quick-record-search/" target="_blank"><img src="layouts/v7/modules/CTMobileSettings/images/right-arrow.png" alt="Icon" class="dasharrow"></a>
                    </div>

                    <div class="newheading">
                     <a href="https://kb.crmtiger.com/knowledge-base/search-by-custom-filter/" target="_blank"><b>{vtranslate('Search by Custom Filter','CTMobileSettings')}</b></a> : {vtranslate('Create custom filter on the from Apps','CTMobileSettings')}
                     <a href="https://kb.crmtiger.com/knowledge-base/search-by-custom-filter/" target="_blank"><img src="layouts/v7/modules/CTMobileSettings/images/right-arrow.png" alt="Icon" class="dasharrow"></a>
                    </div>

                    <div class="newheading">
                     <a href="https://kb.crmtiger.com/knowledge-base/menu-management/" target="_blank"><b>{vtranslate('Menu Management','CTMobileSettings')}</b></a> : {vtranslate('Hide/show menu item from Mobile Apps','CTMobileSettings')}
                     <a href="https://kb.crmtiger.com/knowledge-base/menu-management/" target="_blank"><img src="layouts/v7/modules/CTMobileSettings/images/right-arrow.png" alt="Icon" class="dasharrow"></a>
                    </div>

                    <div class="newheading">
                     <a href="https://kb.crmtiger.com/knowledge-base/address-auto-finder/" target="_blank"><b>{vtranslate('Address Auto Finder','CTMobileSettings')}</b></a> : {vtranslate('Auto suggest address','CTMobileSettings')}
                     <a href="https://kb.crmtiger.com/knowledge-base/address-auto-finder/" target="_blank"><img src="layouts/v7/modules/CTMobileSettings/images/right-arrow.png" alt="Icon" class="dasharrow"></a>
                    </div>

                    <div class="newheading">
                     <a href="https://kb.crmtiger.com/knowledge-base/comment-mention-users/" target="_blank"><b>{vtranslate('Comment & Mention Users','CTMobileSettings')}</b></a> : {vtranslate('Mention Users in comments to get notification','CTMobileSettings')}
                     <a href="https://kb.crmtiger.com/knowledge-base/comment-mention-users/" target="_blank"><img src="layouts/v7/modules/CTMobileSettings/images/right-arrow.png" alt="Icon" class="dasharrow"></a>
                    </div>

                    <div class="newheading">
                     <a href="https://kb.crmtiger.com/knowledge-base/call-logging-recording/" target="_blank"><b>{vtranslate('Call logging & Recording','CTMobileSettings')}</b></a> : {vtranslate('Log and Record Call (Only for Specific Android Users)','CTMobileSettings')}
                     <a href="https://kb.crmtiger.com/knowledge-base/call-logging-recording/" target="_blank"><img src="layouts/v7/modules/CTMobileSettings/images/right-arrow.png" alt="Icon" class="dasharrow"></a>
                    </div>

                    <div class="newheading">
                     <a href="https://kb.crmtiger.com/knowledge-base/access-taskactivity-calendar/" target="_blank"><b>{vtranslate('Access Task/Activity Calendar','CTMobileSettings')}</b></a> : {vtranslate('Extensive Search on calendar','CTMobileSettings')}
                     <a href="https://kb.crmtiger.com/knowledge-base/access-taskactivity-calendar/" target="_blank"><img src="layouts/v7/modules/CTMobileSettings/images/right-arrow.png" alt="Icon" class="dasharrow"></a>
                    </div>

                  </div>
                </div>
               
            </div>
        </div>
    </div>
</div>
{/strip}