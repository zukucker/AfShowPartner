{extends file="parent:frontend/index/logo-container.tpl"}

{block name="frontend_index_logo"}
{$smarty.block.parent}
<script>
let partner = sessionStorage.getItem('partner')
if(sessionStorage.getItem('partner')){
    console.error(partner);
}
</script>

<div class="partner-container"style="display:flex;flex-direction:column;">
    <img class="afpartnerimage" src="{$afPartnerName.image|mediaUrl}"style="max-width:150px;max-height:150px;"/>
    <p class="afpartnername">{$afPartnerName.name}</p>
</div>
{/block}
