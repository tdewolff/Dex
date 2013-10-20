<h2>Users</h2>
<a href="/<?php echo $_['base_url']; ?>admin/users/new/" class="button" style="margin-left:20px;"><i class="icon-plus"></i>&ensp;New user</a>
<table class="list">
  <tr>
	<th style="width:120px;"></th>
	<th>Username</th>
	<th>Permission level</th>
  </tr>
 <?php foreach ($_['users'] as $user): ?>
  <tr>
	<td class="no_wrap centered">
	 <div class="dropdown">
	  <a href="/<?php echo $_['base_url']; ?>admin/users/<?php echo $user['account_id']; ?>/" class="dropdown-select list-button"><i class="icon-pencil"></i>&ensp;Edit</a><a href="#" class="dropdown-toggle list-button"><i class="icon-caret-down"></i></a>
      <ul class="dropdown-menu" role="menu">
	   <li class="popbox">
	    <a href="#" class="open"><i class="icon-fixed-width icon-trash"></i>&ensp;Delete</a>
	    <div class="box">
	     <div class="arrow"></div>
	     <div class="arrow-border"></div>

	     <p class="no_wrap">Do you really want to remove this user?</p>
	     <p class="centered"><a href="/<?php echo $_['base_url']; ?>admin/users/remove/<?php echo $user['account_id']; ?>/" class="small-alert-button">Remove</a><a href="#" class="close small-button">Cancel</a></p>
	    </div>
	   </li>
	  </ul>
	 </div>
	</td>
	<td class="no_wrap"><?php echo $user['username']; ?></td>
	<td class="no_wrap"><?php echo $user['permission']; ?></td>
  </tr>
 <?php endforeach; ?>
</table>