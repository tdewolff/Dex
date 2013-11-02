<h2>Templates</h2>
<ul class="table">
  <li>
	<div style="width:120px;">&nbsp;</div>
	<div style="width:120px;">Name</div>
	<div style="width:120px;">Author</div>
	<div style="width:540px;">Description</div>
  </li>
 <?php foreach ($_['templates'] as $item): ?>
  <li>
	<div style="width:120px;">&nbsp;</div>
	<div style="width:120px;"><?php echo $item['title']; ?></div>
	<div style="width:120px;"><?php echo $item['author']; ?></div>
	<div style="width:540px;"><?php echo $item['description']; ?></div>
  </li>
 <?php endforeach; ?>
</table>
