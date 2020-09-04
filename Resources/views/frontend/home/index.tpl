{extends file="parent:frontend/home/index.tpl"}

{block name="frontend_index_header_navigation"}
{$smarty.block.parent}
<div class="partner-container">
    {$afPartnerName.name}
    <img src="{$afPartnerName.image|mediaUrl}"style="max-width:150px;max-height:150px;"/>
</div>
{/block}
