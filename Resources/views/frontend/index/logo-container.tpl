{extends file="parent:frontend/index/logo-container.tpl"}

{block name="frontend_index_logo"}
{$smarty.block.parent}
<div class="partner-container"style="display:flex;flex-direction:column;">
    <img class="afpartnerimage" src="{$afPartnerName.image|mediaUrl}"style="max-width:150px;max-height:150px;"/>
    <p class="afpartnername">{$afPartnerName.name}</p>
</div>
{/block}
