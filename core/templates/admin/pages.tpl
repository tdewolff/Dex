<h2>Pages</h2>
<a href="/<?php echo $_['base_url']; ?>admin/pages/new/" class="button" style="margin-left:20px;"><i class="icon-plus"></i>&ensp;New page</a>
<ul class="table">
  <li>
	<div style="width:120px;">&nbsp;</div>
	<div style="width:120px;">Title</div>
	<div style="width:200px;">Link</div>
	<div style="width:120px;">Template</div>
	<div style="width:340px;">Content</div>
  </li>
 <?php foreach ($_['pages'] as $item): ?>
  <li id="<?php echo $item['link_id']; ?>">
	<div style="width:120px; overflow:visible;">
	 <div class="dropdown">
	  <a href="/<?php echo $_['base_url']; ?>admin/pages/<?php echo $item['link_id']; ?>/" class="dropdown-select list-button"><i class="icon-pencil"></i>&ensp;Edit</a><a href="#" class="dropdown-toggle list-button"><i class="icon-caret-down"></i></a>
      <ul class="dropdown-menu" role="menu">
	   <li>
	    <a href="#" class="halt"><i class="icon-fixed-width icon-trash"></i>&ensp;Delete</a>
	    <a href="#" class="sure" onclick="ajax(this, 'POST', {link_id: <?php echo $item['link_id']; ?>}, function() {
	    	hideRow(<?php echo $item['link_id']; ?>);
	    });"><i class="icon-fixed-width icon-trash"></i>&ensp;Really?</a>
	   </li>
	  </ul>
	 </div>
	</div>
	<div style="width:120px;"><?php echo $item['title']; ?></div>
	<div style="width:200px;"><a href="/<?php echo $_['base_url'] . $item['url']; ?>">/<?php echo $_['base_url'] . $item['url']; ?></a></div>
	<div style="width:120px;"><?php echo $item['template_name']; ?></div>
	<div style="width:340px;"><?php echo $item['content']; ?></div>
  </li>
 <?php endforeach; ?>
</table>