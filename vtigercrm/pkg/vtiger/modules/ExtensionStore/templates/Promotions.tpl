
{strip}
    <div class="banner-container">
        <div class="row-fluid"></div>
        <div class="banner">
            <ul class="bxslider">
                {foreach $PROMOTIONS as $PROMOTION}
                    <li>
                        {assign var=SUMMARY value=$PROMOTION->get('summary')}
                        {assign var=EXTENSION_NAME value=$PROMOTION->get('label')}
                        {if is_numeric($SUMMARY)}
                            {assign var=LOCATION_URL value=$PROMOTION->getLocationUrl($SUMMARY, $EXTENSION_NAME)}
                        {else}
                            {assign var=LOCATION_URL value={$SUMMARY}}
                        {/if}
                        <img src="">
{*                        <a onclick="window.open('{$LOCATION_URL}')"><img src="{if $PROMOTION->get('bannerURL')}{$PROMOTION->get('bannerURL')}{/if}" title="{$PROMOTION->get('label')}" /></a>*}
                    </li>
                {/foreach}
            </ul>
        </div>
    </div>
{/strip}