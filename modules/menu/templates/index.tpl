{* level 0 *}
{assign var=i value=1}
<ul id="nav">
 {while true}
  {if $i > $menu|@count}{break}{/if}
  <li{if $menu.{$i}.selected} class="selected"{/if}>
   <a href="{$base_url}{$menu.{$i}.link}">{$menu.{$i}.name}</a>

   {* level 1 *}
   {assign var=i value=$i+1}
   {if $i <= $menu|@count && $menu.{$i}.level > $menu.{$i-1}.level}
    <ul>
     {while true}
      {if $i > $menu|@count || $menu.{$i}.level < 1}{break}{/if}
      <li{if $menu.{$i}.selected} class="selected"{/if}>
       <a href="{$base_url}{$menu.{$i}.link}">{$menu.{$i}.name}</a>

       {* level 2 *}
       {assign var=i value=$i+1}
       {if $i <= $menu|@count && $menu.{$i}.level > $menu.{$i-1}.level}
        <ul>
         {while true}
          {if $i > $menu|@count || $menu.{$i}.level < 2}{break}{/if}

          <li{if $menu.{$i}.selected} class="selected"{/if}>
           <a href="{$base_url}{$menu.{$i}.link}">{$menu.{$i}.name}</a>
          </li>
          {assign var=i value=$i+1}
         {/while}
        </ul>
       {/if}

      </li>
     {/while}
    </ul>
   {/if}

  </li>
 {/while}
</ul>