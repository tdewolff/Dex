<h2>Modules</h2>
<ul class="table">
  <li>
	<div style="width:120px;">&nbsp;</div>
	<div style="width:120px;">Name</div>
	<div style="width:120px;">Author</div>
	<div style="width:540px;">Description</div>
  </li>
 <?php foreach ($_['modules'] as $item): ?>
  <li id="<?php echo $item['module_name']; ?>">
	<div style="width:120px; overflow:visible;">
	 <div class="dropdown">
	  <a href="/<?php echo $_['base_url']; ?>admin/module/<?php echo $item['module_name']; ?>/" class="dropdown-select list-button">
	   <i class="icon-arrow-right"></i>&ensp;Go to</a><a href="#" class="dropdown-toggle list-button"><i class="icon-caret-down"></i>
	  </a>
      <ul class="dropdown-menu" role="menu">
       <li><a href="#" onclick="ajax(this, 'PUT', {module_name: '<?php echo $item['module_name']; ?>'}, function(element) {
	       	$('.dropdown-menu').fadeOut('fast');
	        $('#<?php echo $item['module_name']; ?> > div:not(:first-of-type)').toggleClass('disabled');
	        if ($('i', element).hasClass('icon-ok')) {
	       		$(element).html('<i class=&quot;icon-fixed-width icon-ban-circle&quot;></i>&ensp;Disable');
	       	} else {
	       		$(element).html('<i class=&quot;icon-fixed-width icon-ok&quot;></i>&ensp;Enable');
	       	}
       	});">
        <?php echo ($item['enabled'] == 0 ? '<i class="icon-fixed-width icon-ok"></i>&ensp;Enable'
                                          : '<i class="icon-fixed-width icon-ban-circle"></i>&ensp;Disable'); ?>
       </a></li>
	  </ul>
	 </div>
	</div>
	<div style="width:120px;" <?php if ($item['enabled'] == 0) { echo 'class="disabled"'; } ?>><?php echo $item['title']; ?></div>
	<div style="width:120px;" <?php if ($item['enabled'] == 0) { echo 'class="disabled"'; } ?>><?php echo $item['author']; ?></div>
	<div style="width:540px;" <?php if ($item['enabled'] == 0) { echo 'class="disabled"'; } ?>><?php echo $item['description']; ?></div>
  </li>
 <?php endforeach; ?>
</table>
