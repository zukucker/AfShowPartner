{extends file="parent:frontend/index/logo-container.tpl"}

{block name="frontend_index_logo"}
  {$smarty.block.parent}
  <div>
    <div>
      {$AfPartnerName.name}
    </div>
    <div>
      <img src="{$AfPartnerName.image|mediaurl}"/>
    </div>
  </div>

  <style>
      .logo--shop.block{
        display: flex !important;
        flex-direction: column !important;
        justify-content: center !important;
        align-items: center !important;
        width: 100% !important;
      }
  </style>
{/block}

