<h2>Themes</h2>
<table class="grid">
 <?php foreach ($_['themes'] as $i => $item): ?>
  <?php if ($i % 3 == 0) { echo '<tr>'; } ?>
	<td class="no_wrap centered vertical_top <?php if ($item['name'] == $_['current_theme']) { echo 'theme_current'; } ?>">
	 <div class="theme_caption"><strong><?php echo $item['title']; ?></strong><br />(<?php echo $item['author']; ?>)</div>
	 <img src="/<?php echo $_['base_url']; ?>themes/<?php echo $item['name']; ?>/preview.png" alt="<?php echo $item['name']; ?>" class="theme_image" width="256" height="256" /><br />

     <?php if ($item['name'] != $_['current_theme']): ?>
	  <a href="/<?php echo $_['base_url']; ?>admin/themes/use/<?php echo $item['name']; ?>/" class="small-button"><i class="icon-check"></i>&ensp;Use</a>
	 <?php endif; ?>

	 <span class="popbox">
	  <a href="#" class="open small-alert-button"><i class="icon-trash"></i></a>
	  <div class="box">
	   <div class="arrow"></div>
	   <div class="arrow-border"></div>

	   <p class="centered no_wrap">Do you really want to destroy this theme?<br />This removes all files of this theme!</p>
	   <p class="centered"><a href="/<?php echo $_['base_url']; ?>admin/themes/destroy/<?php echo $item['name']; ?>/" class="small-alert-button">Remove</a><a href="#" class="close small-button">Cancel</a></p>
	  </div>
	 </span>
	</td>
  <?php if ($i % 3 == 2) { echo '</table>tr>'; } ?>
 <?php endforeach; ?>
</table>