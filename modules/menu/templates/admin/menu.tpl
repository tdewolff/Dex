<h1>Menu</h1>
{form data=$menu}
<table class="list">
  <tr>
	<th style="width:120px;"></th>
	<th>Menu name</th>
	<th>Page title</th>
	<th>Link</th>
  </tr>
 {foreach from=$menu_items key=id item=item}
  <tr>
	<td class="no_wrap centered">
	 <span class="popbox">
	  <a href="#" class="open small-alert-button"><i class="icon-trash"></i></a>
	  <div class="box">
	   <div class="arrow"></div>
	   <div class="arrow-border"></div>

	   <p class="no_wrap">Do you really want to remove this menu item?</p>
	   <p class="centered"><a href="{$base_url}admin/module/menu/remove/{$id}/" class="small-alert-button">Remove</a><a href="#" class="close small-button">Cancel</a></p>
	  </div>
	 </span>
	</td>
	<td class="no_wrap">{section name=i loop=$item.level}&#8211;{/section} {$item.name}</td>
	<td class="no_wrap">{$item.title}</td>
	<td><a href="{$base_url}{$item.link}">{$domain_url}{$base_url}{$item.link}</a></td>
  </tr>
 {/foreach}
</table>