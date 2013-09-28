<h1>Links</h1>
<a href="{$base_url}admin/links/new/" class="button"><i class="icon-plus"></i>&ensp;New link</a>
<table class="list">
  <tr>
	<th style="width:120px;"></th>
	<th>Link</th>
	<th>Title</th>
	<th>Modules</th>
  </tr>
 {foreach from=$links item=link}
  <tr>
	<td class="no_wrap centered">
	 <div class="dropdown">
	  <a href="{$base_url}admin/links/{$link.id}/" class="dropdown-select small-button"><i class="icon-pencil"></i>&ensp;Edit</a>
      <a href="#" class="dropdown-toggle small-button"><i class="icon-caret-down"></i></a>
      <ul class="dropdown-menu" role="menu">
	   <li><a href="{$base_url}{$page.link}"><i class="icon-fixed-width icon-eye-open"></i>&ensp;View</a></li>
	   <li class="popbox">
	    <a href="#" class="open"><i class="icon-fixed-width icon-trash"></i>&ensp;Delete</a>
	    <div class="box">
	     <div class="arrow"></div>
	     <div class="arrow-border"></div>

	     <p class="no_wrap">Do you really want to remove this link?</p>
	     <p class="centered"><a href="{$base_url}admin/links/remove/{$link.id}/" class="small-alert-button">Remove</a><a href="#" class="close small-button">Cancel</a></p>
	    </div>
	   </li>
	  </ul>
	 </div>
	</td>
	<td><a href="{$base_url}{$link.link}">{$domain_url}{$base_url}{$link.link}</a></td>
	<td class="no_wrap">{$link.title}</td>
	<td class="no_wrap">{$link.module_names}</td>
  </tr>
 {/foreach}
</table>