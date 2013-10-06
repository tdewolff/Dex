<h1>Menu</h1>
<?php $_['menu']->renderForm(); ?>
<table class="list">
  <tr>
	<th style="width:120px;"></th>
	<th>Menu name</th>
	<th>Page title</th>
	<th>Link</th>
  </tr>
 <?php foreach ($_['menu_items'] as $id => $item): ?>
  <tr>
	<td class="no_wrap centered">
	 <span class="popbox">
	  <a href="#" class="open small-alert-button"><i class="icon-trash"></i></a>
	  <div class="box">
	   <div class="arrow"></div>
	   <div class="arrow-border"></div>

	   <p class="no_wrap">Do you really want to remove this menu item?</p>
	   <p class="centered"><a href="/<?php echo $_['base_url']; ?>admin/module/menu/remove/<?php echo $id; ?>/" class="small-alert-button">Remove</a><a href="#" class="close small-button">Cancel</a></p>
	  </div>
	 </span>
	</td>
	<td class="no_wrap"><?php for ($i = 0; $i < $item['level']; $i++) { echo '&#8211;'; } echo $item['name']; ?></td>
	<td class="no_wrap"><?php echo $item['title']; ?></td>
	<td><a href="/<?php echo $_['base_url'] . $item['link']; ?>"><?php echo $_['domain_url'] . $_['base_url'] . $item['link']; ?></a></td>
  </tr>
 <?php endforeach; ?>
</table>