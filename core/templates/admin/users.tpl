<h2>Users</h2>
<a href="/<?php echo $_['base_url']; ?>admin/users/new/" class="button" style="margin-left:20px;"><i class="icon-plus"></i>&ensp;New user</a>
<ul class="table">
  <li>
	<div style="width:120px;">&nbsp;</div>
	<div style="width:200px;">Username</div>
	<div style="width:580px;">Permission level</div>
  </li>
 <?php foreach ($_['users'] as $item): ?>
  <li id="<?php echo $item['account_id']; ?>">
	<div style="width:120px; overflow:visible;">
	 <div class="dropdown">
	  <a href="/<?php echo $_['base_url']; ?>admin/users/<?php echo $item['account_id']; ?>/" class="dropdown-select list-button"><i class="icon-pencil"></i>&ensp;Edit</a><a href="#" class="dropdown-toggle list-button"><i class="icon-caret-down"></i></a>
      <ul class="dropdown-menu" role="menu">
	   <li>
	    <a href="#" class="halt"><i class="icon-fixed-width icon-trash"></i>&ensp;<?php echo ($item['account_id'] != Session::getAccountId() ? 'Delete' : '<del>Delete</del>'); ?></a>
	    <?php if ($item['account_id'] != Session::getAccountId()) { ?>
	    <a href="#" class="sure" onclick="ajax(this, 'POST', {account_id: <?php echo $item['account_id']; ?>}, function() {
	    	hideRow(<?php echo $item['account_id']; ?>);
	    });"><i class="icon-fixed-width icon-trash"></i>&ensp;Really?</a>
	    <?php } ?>
	   </li>
	  </ul>
	 </div>
	</div>
	<div style="width:200px;"><?php echo $item['username']; ?></div>
	<div style="width:580px;"><?php echo $item['permission']; ?></div>
  </li>
 <?php endforeach; ?>
</table>