{extends file="parent:frontend/detail/buy.tpl"}
{block name="frontend_detail_buy"}
<div class="partner-container">
{$afPartnerName.name}
<img class="afpartner" src="{$afPartnerName.image|mediaUrl}" style="max-widtH:150px; max-height:150px;"/>
</div>
{$smarty.block.parent}
{/block}
