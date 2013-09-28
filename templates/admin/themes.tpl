<h1>Themes</h1>
<table class="grid">
 {foreach from=$themes item=theme key=i}
  {if $i % 3 == 0}<tr>{/if}
	<td class="no_wrap centered vertical_top {if $theme.name == $current_theme}theme_current{/if}">
	 <div class="theme_caption"><strong>{$theme.title}</strong><br />({$theme.author})</div>
	 <img src="{$base_url}themes/{$theme.name}/preview.png" alt="{$theme.name}" class="theme_image" /><br />

	 {if $theme.name != $current_theme}
	  <a href="{$base_url}admin/themes/use/{$theme.name}/" class="small-button"><i class="icon-check"></i>&ensp;Use</a>
	 {/if}

	 <span class="popbox">
	  <a href="#" class="open small-alert-button"><i class="icon-trash"></i></a>
	  <div class="box">
	   <div class="arrow"></div>
	   <div class="arrow-border"></div>

	   <p class="centered no_wrap">Do you really want to destroy this theme?<br />This removes all files of this theme!</p>
	   <p class="centered"><a href="{$base_url}admin/themes/destroy/{$theme.name}/" class="small-alert-button">Remove</a><a href="#" class="close small-button">Cancel</a></p>
	  </div>
	 </span>
	</td>
  {if $i % 3 == 2}</tr>{/if}
 {/foreach}
</table>