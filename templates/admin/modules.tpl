<h1>Modules</h1>
<table class="list">
  <tr>
	<th></th>
	<th>Name</th>
	<th>Author</th>
	<th>Description</th>
  </tr>
 {foreach from=$modules item=module}
  <tr>
	<td class="no_wrap centered">
	 <div class="dropdown">
	  <a href="{$base_url}admin/module/{$module.name}/" class="dropdown-select small-button"><i class="icon-arrow-right"></i>&ensp;Go to</a><a href="#" class="dropdown-toggle small-button"><i class="icon-caret-down"></i></a>
      <ul class="dropdown-menu" role="menu">
	   {if $module.enabled == 0}
	    <li><a href="{$base_url}admin/modules/enable/{$module.name}/"><i class="icon-fixed-width icon-ok"></i>&ensp;Enable</a></li>
	   {else}
	    <li><a href="{$base_url}admin/modules/disable/{$module.name}/"><i class="icon-fixed-width icon-ban-circle"></i>&ensp;Disable</a></li>
	   {/if}
	   <li class="popbox">
	    <a href="#" class="open"><i class="icon-fixed-width icon-trash"></i>&ensp;Delete</a>
	    <div class="box">
	     <div class="arrow"></div>
	     <div class="arrow-border"></div>

	     <p class="no_wrap">Do you really want to clean this module?<br />This removes all database entries of this plugin and reinstall it!</p>
	     <p class="centered"><a href="{$base_url}admin/modules/destroy/{$module.name}/" class="small-alert-button">Remove</a><a href="#" class="close small-button">Cancel</a></p>
	    </div>
	   </li>
	  </ul>
	 </div>
	</td>
	<td class="no_wrap {if $module.enabled == 0}disabled{/if}">{$module.title} {if $module.enabled == 0}(disabled){/if}</td>
	<td class="no_wrap {if $module.enabled == 0}disabled{/if}">{$module.author}</td>
	<td {if $module.enabled == 0}class="disabled"{/if}>
	 <span class="popbox">
	  {if $module.description|strlen > 80}<a href="#" class="open">{/if}{$module.description|truncate:80}{if $module.description|strlen > 80}</a>{/if}
	  <div class="box">
	   <div class="arrow"></div>
	   <div class="arrow-border"></div>

	   <p class="pre_wrap">{$module.description}</p>
	  </div>
	 </span>
	</td>
  </tr>
 {/foreach}
</table>