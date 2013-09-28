<h1>Users</h1>
<a href="{$base_url}admin/users/new/" class="button"><i class="icon-plus"></i>&ensp;New user</a>
<table class="list">
  <tr>
	<th style="width:120px;"></th>
	<th>Username</th>
	<th>Userlevel</th>
  </tr>
 {foreach from=$users item=user}
  <tr>
	<td class="no_wrap centered">
	 <div class="dropdown">
	  <a href="{$base_url}admin/users/{$user.id}/" class="dropdown-select small-button"><i class="icon-pencil"></i>&ensp;Edit</a><a href="#" class="dropdown-toggle small-button"><i class="icon-caret-down"></i></a>
      <ul class="dropdown-menu" role="menu">
	   <li class="popbox">
	    <a href="#" class="open"><i class="icon-fixed-width icon-trash"></i>&ensp;Delete</a>
	    <div class="box">
	     <div class="arrow"></div>
	     <div class="arrow-border"></div>

	     <p class="no_wrap">Do you really want to remove this user?</p>
	     <p class="centered"><a href="{$base_url}admin/users/remove/{$user.id}/" class="small-alert-button">Remove</a><a href="#" class="close small-button">Cancel</a></p>
	    </div>
	   </li>
	  </ul>
	 </div>
	</td>
	<td class="no_wrap">{$user.username}</td>
	<td class="no_wrap">{$user.userlevel}</td>
  </tr>
 {/foreach}
</table>