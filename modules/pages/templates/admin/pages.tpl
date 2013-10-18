<h2>Pages</h2>
<a href="/<?php echo $_['base_url']; ?>admin/module/pages/new/" class="button" style="margin-left:20px;"><i class="icon-plus"></i>&ensp;New page</a>
<table class="list">
  <tr>
	<th></th>
	<th>Title</th>
	<th>Link</th>
	<th width="99%">Content</th>
  </tr>
 <?php foreach ($_['pages'] as $item): ?>
  <tr id="<?php echo $item['id']; ?>">
	<td class="no_wrap">
	 <div class="dropdown">
	  <a href="/<?php echo $_['base_url']; ?>admin/module/pages/<?php echo $item['id']; ?>/" class="dropdown-select list-button"><i class="icon-pencil"></i>&ensp;Edit</a><a href="#" class="dropdown-toggle list-button"><i class="icon-caret-down"></i></a>
      <ul class="dropdown-menu" role="menu">
	   <li><a href="/<?php echo $_['base_url'] . $item['link']; ?>"><i class="icon-fixed-width icon-eye-open"></i>&ensp;View</a></li>
	   <li class="popbox">
	    <a href="#" class="open"><i class="icon-fixed-width icon-trash"></i>&ensp;Delete</a>
	    <div class="box">
	     <div class="arrow"></div>
	     <div class="arrow-border"></div>

	     <p class="no_wrap">Do you really want to remove this page?</p>
	     <p class="centered"><a href="#" class="small-alert-button" onclick="ajaxAction('/<?php echo $_['base_url']; ?>admin/module/pages/remove/<?php echo $item['id']; ?>/', function() { hideTableRow(<?php echo $item['id']; ?>); });">Remove</a><a href="#" class="close small-button">Cancel</a></p>
	    </div>
	   </li>
	  </ul>
	 </div>
	</td>
	<td class="no_wrap"><?php echo $item['title']; ?></td>
	<td><?php echo $_['domain_url'] . $_['base_url'] . $item['link']; ?></td>
	<td><?php echo (strlen($item['content']) > 50 ? substr($item['content'], 0, 50) . '...' : $item['content']); ?></td>
  </tr>
 <?php endforeach; ?>
</table>