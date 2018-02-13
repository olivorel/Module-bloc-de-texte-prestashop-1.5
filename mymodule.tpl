<!-- Block mymodule -->
<div id="mymodule_block_bottom" class="block_module">
   <div class="block_bottom">
     <h2>{l s='%1$s' sprintf=$my_module_title mod='mymodule'}</h2>
    <p>
      {if !isset($my_module_name) || !$my_module_name}
             {l s='%1$s' sprintf=$my_module_name mod='mymodule'}
      {/if}
      <!--Les traductions dans PrestaShop 1.5", les variables doivent être marquées à l'aide de marqueurs sprintf(), tels que %s ou %1$s.-->
      {l s='%1$s' sprintf=$my_module_name mod='mymodule'}   
    </p>   
  </div>
</div>
<!-- /Block mymodule -->
