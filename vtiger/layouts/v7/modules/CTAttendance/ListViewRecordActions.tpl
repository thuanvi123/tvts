{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}
{strip}
<!--LIST VIEW RECORD ACTIONS-->

<div class="table-actions">
    {if !$SEARCH_MODE_RESULTS}
    <span class="input" >
        <input type="checkbox" value="{$LISTVIEW_ENTRY->getId()}" class="listViewEntriesCheckBox"/>
    </span>
    {/if}

    <span>
    	{if $LISTVIEW_ENTRY->getCheckInLocation() neq ',' AND $LISTVIEW_ENTRY->getCheckInLocation() neq ''}
        	<a onclick='window.open("https://www.google.com/maps/search/?api=1&query={$LISTVIEW_ENTRY->getCheckInLocation()}");return false;'><i class="fa fa-map-marker" style="color:green;" title="{vtranslate('Click to see Check-in Location on Map',$MODULE)}" ></i></a>
        {else}
            <a style="cursor: alias;" onclick='return false;'><i class="fa fa-map-marker" title="{vtranslate('No Check-in Location',$MODULE)}" ></i></a>
        {/if}
    </span>


    <span>
    	{if $LISTVIEW_ENTRY->getCheckOutLocation() neq ',' AND $LISTVIEW_ENTRY->getCheckOutLocation() neq ''}
        	<a onclick='window.open("https://www.google.com/maps/search/?api=1&query={$LISTVIEW_ENTRY->getCheckOutLocation()}");return false;'><i class="fa fa-map-marker" {if $LISTVIEW_ENTRY->getCheckOutLocation() neq ',' AND $LISTVIEW_ENTRY->getCheckOutLocation() neq ''} style="color:red;"{/if} title="{vtranslate('Click to see Check-out Location on Map',$MODULE)}"></i></a>
        {else}
          <a style="cursor: alias;" onclick='return false;'><i class="fa fa-map-marker" title="{vtranslate('No Check-out Location',$MODULE)}" ></i></a>
        {/if}
    </span>

    <span>
        {if ($LISTVIEW_ENTRY->getCheckOutLocation() neq ',' AND $LISTVIEW_ENTRY->getCheckOutLocation() neq '') OR ($LISTVIEW_ENTRY->getCheckInLocation() neq ',' AND $LISTVIEW_ENTRY->getCheckInLocation() neq '') }
            <a class="map-marker"><i class="fa fa-map" title="{vtranslate('View Location on right panel map',$MODULE)}" style="color:#0078A8;"></i></a>
        {else}
            <a style="cursor: alias;" onclick='return false;'><i class="fa fa-map" title="{vtranslate('View Location on right panel map',$MODULE)}" ></i></a>
        {/if}
    </span>
    
    {if $LISTVIEW_ENTRY->get('starred') eq 'Yes'}
        {assign var=STARRED value=true}
    {else}
        {assign var=STARRED value=false}
    {/if}
    {if $QUICK_PREVIEW_ENABLED eq 'true'}
		<span>
			<a class="quickView fa fa-eye icon action" data-app="{$SELECTED_MENU_CATEGORY}" title="{vtranslate('LBL_QUICK_VIEW', $MODULE)}"></a>
		</span>
    {/if}
	{if $MODULE_MODEL->isStarredEnabled()}
		<span>
			<a class="markStar fa icon action {if $STARRED} fa-star active {else} fa-star-o{/if}" title="{if $STARRED} {vtranslate('LBL_STARRED', $MODULE)} {else} {vtranslate('LBL_NOT_STARRED', $MODULE)}{/if}"></a>
		</span>
	{/if}
    <span class="more dropdown action">
        <span href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
            <i class="fa fa-ellipsis-v icon"></i></span>
        <ul class="dropdown-menu">
            <li><a data-id="{$LISTVIEW_ENTRY->getId()}" href="{$LISTVIEW_ENTRY->getFullDetailViewUrl()}&app={$SELECTED_MENU_CATEGORY}">{vtranslate('LBL_DETAILS', $MODULE)}</a></li>
			{if $RECORD_ACTIONS}
				{if $RECORD_ACTIONS['edit']}
					<li><a data-id="{$LISTVIEW_ENTRY->getId()}" href="javascript:void(0);" data-url="{$LISTVIEW_ENTRY->getEditViewUrl()}&app={$SELECTED_MENU_CATEGORY}" name="editlink">{vtranslate('LBL_EDIT', $MODULE)}</a></li>
				{/if}
				{if $RECORD_ACTIONS['delete']}
					<li><a data-id="{$LISTVIEW_ENTRY->getId()}" href="javascript:void(0);" class="deleteRecordButton">{vtranslate('LBL_DELETE', $MODULE)}</a></li>
				{/if}
			{/if}
        </ul>
    </span>

    <div class="btn-group inline-save hide">
        <button class="button btn-success btn-small save" type="button" name="save"><i class="fa fa-check"></i></button>
        <button class="button btn-danger btn-small cancel" type="button" name="Cancel"><i class="fa fa-close"></i></button>
    </div>
</div>
{/strip}
