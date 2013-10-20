<h2>Menu</h2>
<?php print_r($_); /*$_['menu']->renderForm(); ?>
<ul class="list">
  <tr>
			<th style="width:40px;"></th>
			<th>Menu name</th>
			<th>Page title</th>
			<th>Link</th>
			<th style="width:60px;"></th>
  </tr>
 <?php foreach ($_['menu_items'] as $id => $item): ?>
  <tr id="<?php echo $id; ?>">
			<td style="width:40px;" class="no_wrap"><i class="icon-reorder"></i></td>
			<td class="no_wrap"><?php for ($i = 0; $i < $item['level']; $i++) { echo '&#8211;'; } echo $item['name']; ?></td>
			<td class="no_wrap"><?php echo $item['title']; ?></td>
			<td><a href="/<?php echo $_['base_url'] . $item['link']; ?>"><?php echo $_['domain_url'] . $_['base_url'] . $item['link']; ?></a></td>
			<td style="width:60px;" class="no_wrap popbox">
		  <a href="#" class="list-button open"><i class="icon-fixed-width icon-trash"></i></a>
		  <div class="box">
		   <div class="arrow"></div>
		   <div class="arrow-border"></div>

		   <p class="no_wrap">Do you really want to remove this menu item?</p>
		   <p class="centered"><a href="#" class="small-alert-button" onclick="ajaxAction('/<?php echo $_['base_url']; ?>admin/module/menu/remove/<?php echo $id; ?>/', function() { hideTableRow(<?php echo $id; ?>); });">Remove</a><a href="#" class="close small-button">Cancel</a></p>
		  </div>
			</td>
  </tr>
 <?php endforeach; ?>
</ul>

<?php*/
function listRecursion($_, $parent_id)
{
    echo '<ul ' . ($parent_id == 0 ? 'id="nav"' : '') . '>';
    foreach ($_['menu'][$parent_id] as $id => $item)
    {
        echo '<li>';
        echo '<a href="/' . $_['base_url'] . $item['url'] . '">' . $item['name'] . '</a>';

        if (isset($_['menu'][$id]))
            listRecursion($_, $id);

        echo '</li>';
    }
    echo '</ul>';
}

listRecursion($_, 0);
?>