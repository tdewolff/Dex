<h2>Modules</h2>
<table class="list">
  <tr>
	<th></th>
	<th>Name</th>
	<th>Author</th>
	<th width="99%">Description</th>
  </tr>
 <?php foreach ($_['modules'] as $module): ?>
  <tr>
	<td class="no_wrap">
	 <div class="dropdown">
	  <a href="/<?php echo $_['base_url']; ?>admin/module/<?php echo $module['module_name']; ?>/" class="dropdown-select list-button"><i class="icon-arrow-right"></i>&ensp;Go to</a><a href="#" class="dropdown-toggle list-button"><i class="icon-caret-down"></i></a>
      <ul class="dropdown-menu" role="menu">
	   <?php if ($module['enabled'] == 0): ?>
	    <li><a href="/<?php echo $_['base_url']; ?>admin/modules/enable/<?php echo $module['module_name']; ?>/"><i class="icon-fixed-width icon-ok"></i>&ensp;Enable</a></li>
	   <?php else: ?>
	    <li><a href="/<?php echo $_['base_url']; ?>admin/modules/disable/<?php echo $module['module_name']; ?>/"><i class="icon-fixed-width icon-ban-circle"></i>&ensp;Disable</a></li>
	   <?php endif; ?>
	  </ul>
	 </div>
	</td>
	<td class="no_wrap <?php if ($module['enabled'] == 0) { echo 'disabled'; } ?>"><?php echo $module['title']; if ($module['enabled'] == 0) { echo ' (disabled)'; } ?></td>
	<td class="no_wrap <?php if ($module['enabled'] == 0) { echo 'disabled'; } ?>"><?php echo $module['author']; ?></td>
	<td <?php if ($module['enabled'] == 0) { echo 'class="disabled"'; } ?>>
	 <span class="popbox">
	  <?php if (strlen($module['description']) > 80): ?>
	   <a href="#" class="open"><?php echo substr($module['description'], 0, 80) . '...'; ?></a>
	  <?php else: ?>
	  	<?php echo $module['description']; ?>
	  <?php endif; ?>
	  <div class="box">
	   <div class="arrow"></div>
	   <div class="arrow-border"></div>

	   <p class="pre_wrap"><?php echo $module['description']; ?></p>
	  </div>
	 </span>
	</td>
  </tr>
 <?php endforeach; ?>
</table>