<h2>Pages</h2>
<a href="/<?php echo $_['base_url']; ?>admin/module/pages/new/" class="button" style="margin-left:20px;"><i class="icon-plus"></i>&ensp;New page</a>
<ul class="table">
  <li>
	<div style="width:120px;">&nbsp;</div>
	<div style="width:120px;">Title</div>
	<div style="width:200px;">Link</div>
	<div style="width:460px;">Content</div>
  </li>
 <?php foreach ($_['pages'] as $item): ?>
  <li id="<?php echo $item['module_pages_id']; ?>">
	<div style="width:120px; overflow:visible;">
	 <div class="dropdown">
	  <a href="/<?php echo $_['base_url']; ?>admin/module/pages/<?php echo $item['module_pages_id']; ?>/" class="dropdown-select list-button"><i class="icon-pencil"></i>&ensp;Edit</a><a href="#" class="dropdown-toggle list-button"><i class="icon-caret-down"></i></a>
      <ul class="dropdown-menu" role="menu">
	   <li><a href="/<?php echo $_['base_url'] . $item['url']; ?>"><i class="icon-fixed-width icon-eye-open"></i>&ensp;View</a></li>
	   <li>
	    <a href="#" class="halt"><i class="icon-fixed-width icon-trash"></i>&ensp;Delete</a>
	    <a href="#" class="sure" onclick="ajax(this, 'DELETE', {module_pages_id: <?php echo $item['module_pages_id']; ?>}, function() {
	    	hideRow(<?php echo $item['module_pages_id']; ?>);
	    });"><i class="icon-fixed-width icon-trash"></i>&ensp;Really?</a>
	   </li>
	  </ul>
	 </div>
	</div>
	<div style="width:120px;"><?php echo $item['title']; ?></div>
	<div style="width:200px;">/<?php echo $_['base_url'] . $item['url']; ?></div>
	<div style="width:460px;"><?php echo $item['content']; ?></div>
  </li>
 <?php endforeach; ?>
</table>