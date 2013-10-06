<h1>Pages</h1>
<a href="/<?php echo $_['base_url']; ?>admin/module/pages/new/" class="button"><i class="icon-plus"></i>&ensp;New page</a>
<table class="list">
  <tr>
	<th style="width:120px;"></th>
	<th>Title</th>
	<th>Link</th>
	<th>Content</th>
  </tr>
 <?php foreach ($_['pages'] as $item): ?>
  <tr>
	<td class="no_wrap centered">
	 <div class="dropdown">
	  <a href="/<?php echo $_['base_url']; ?>admin/module/pages/<?php echo $item['id']; ?>/" class="dropdown-select small-button"><i class="icon-pencil"></i>&ensp;Edit</a><a href="#" class="dropdown-toggle small-button"><i class="icon-caret-down"></i></a>
      <ul class="dropdown-menu" role="menu">
	   <li><a href="/<?php echo $_['base_url'] . $item['link']; ?>"><i class="icon-fixed-width icon-eye-open"></i>&ensp;View</a></li>
	   <li class="popbox">
	    <a href="#" class="open"><i class="icon-fixed-width icon-trash"></i>&ensp;Delete</a>
	    <div class="box">
	     <div class="arrow"></div>
	     <div class="arrow-border"></div>

	     <p class="no_wrap">Do you really want to remove this page?</p>
	     <p class="centered"><a href="/<?php echo $_['base_url']; ?>admin/module/pages/remove/<?php echo $item['id']; ?>/" class="small-alert-button">Remove</a><a href="#" class="close small-button">Cancel</a></p>
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