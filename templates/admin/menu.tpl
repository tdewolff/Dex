<h2>Menu</h2>
<?php $_['menu']->renderForm(); ?>
<table class="list">
  <tr>
	<th style="width:120px;"></th>
	<th>Menu name</th>
	<th>Page title</th>
	<th>Link</th>
  </tr>
 <?php foreach ($_['menu_items'] as $id => $item): ?>
  <tr id="<?php echo $id; ?>">
	<td class="no_wrap centered">
	 <div class="dropdown">
	  <a href="/<?php echo $_['base_url']; ?>admin/module/menu/<?php echo $id; ?>/" class="dropdown-select list-button"><i class="icon-pencil"></i>&ensp;Edit</a><a href="#" class="dropdown-toggle list-button"><i class="icon-caret-down"></i></a>
      <ul class="dropdown-menu" role="menu">
	   <li><a href="/<?php echo $_['base_url']; ?>admin/module/menu/moveup/<?php echo $id; ?>"><i class="icon-fixed-width icon-arrow-up"></i>&ensp;Move up</a></li>
	   <li><a href="/<?php echo $_['base_url']; ?>admin/module/menu/movedown/<?php echo $id; ?>"><i class="icon-fixed-width icon-arrow-down"></i>&ensp;Move down</a></li>
	   <li class="popbox">
	    <a href="#" class="open"><i class="icon-fixed-width icon-trash"></i>&ensp;Delete</a>
	    <div class="box">
	     <div class="arrow"></div>
	     <div class="arrow-border"></div>

	     <p class="no_wrap">Do you really want to remove this menu item?</p>
	     <p class="centered"><a href="#" class="small-alert-button" onclick="ajaxAction('/<?php echo $_['base_url']; ?>admin/module/menu/remove/<?php echo $id; ?>/', function() { hideTableRow(<?php echo $id; ?>); });">Remove</a><a href="#" class="close small-button">Cancel</a></p>
	    </div>
	   </li>
	  </ul>
	 </div>
	</td>
	<td class="no_wrap"><?php for ($i = 0; $i < $item['level']; $i++) { echo '&#8211;'; } echo $item['name']; ?></td>
	<td class="no_wrap"><?php echo $item['title']; ?></td>
	<td><a href="/<?php echo $_['base_url'] . $item['link']; ?>"><?php echo $_['domain_url'] . $_['base_url'] . $item['link']; ?></a></td>
  </tr>
 <?php endforeach; ?>
</table>