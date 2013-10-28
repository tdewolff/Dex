<h2>Themes</h2>
<table class="grid">
 <?php foreach ($_['themes'] as $i => $theme): ?>
  <?php if ($i % 3 == 0) { echo '<tr>'; } ?>
	<td class="no_wrap centered vertical_top <?php if ($theme['name'] == $_['current_theme']) { echo 'theme_current'; } ?>">
	 <h4 class="theme_caption" <?php if ($theme['name'] == $_['current_theme']) { echo 'style="color:white;"'; } ?>><?php echo $theme['title']; ?></h4>
     <div class="theme_caption">(<?php echo $theme['author']; ?>)</div>
	 <img src="/<?php echo $_['base_url']; ?>res/theme/<?php echo $theme['name']; ?>/preview.png" alt="<?php echo $theme['name']; ?>" class="theme_image" width="256" height="256" /><br />
	 <a href="#" <?php if ($theme['name'] == $_['current_theme']) { echo 'style="display:none;"'; } ?> class="small-button" onclick="ajax('PUT', {theme_name: '<?php echo $theme['name']; ?>'}, function() { console.log('yup'); $('.grid td').addClass('theme_current'); $(this).closest('td').removeClass('theme_current'); $('.grid a').fadeIn(); $(this).fadeOut(); }, function(e) {console.log(JSON.stringify(e));});"><i class="icon-check"></i>&ensp;Use</a>
	</td>
  <?php if ($i % 3 == 2) { echo '</table>tr>'; } ?>
 <?php endforeach; ?>
</table>