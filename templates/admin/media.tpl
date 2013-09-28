<h1>Media</h1>
<table class="grid">
 {foreach from=$media item=media_item key=i}
  {if $i % 5 == 0}<tr>{/if}
	<td class="no_wrap centered vertical_top">
	 <div style="width:150px; overflow:hidden;"><strong>{$media_item.title}</strong><br />({$media_item.width}&times;{$media_item.height})</div>
	 <a href="{$base_url}{$media_item.url}" data-fancybox-group="gallery" class="fancybox">
      <img src="{$base_url}{$media_item.url}?w=150" alt="{$media_item.title}"  class="media_image" {if $media_item.width > 150}style="width:150px;"{/if} />
     </a>
	</td>
  {if $i % 5 == 4}</tr>{/if}
 {/foreach}
</table>