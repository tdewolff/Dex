<h2>Themes</h2>
<table class="grid">
 <?php foreach ($_['themes'] as $i => $theme): ?>
  <?php if ($i % 3 == 0) { echo '<tr>'; } ?>
	<td class="no_wrap centered vertical_top <?php if ($theme['name'] == $_['current_theme']) { echo 'theme_current'; } ?>">
	 <h4 class="theme_caption"><?php echo $theme['title']; ?></h4>
     <div class="theme_caption">(<?php echo $theme['author']; ?>)</div>
	 <img src="/<?php echo $_['base_url']; ?>res/theme/<?php echo $theme['name']; ?>/preview.png" alt="<?php echo $theme['name']; ?>" class="theme_image" width="256" height="256" /><br />
	 <a href="#" class="small-button" onclick="ajax(this, 'PUT', {theme_name: '<?php echo $theme['name']; ?>'}, function(element) {
        $('.grid td').removeClass('theme_current');
        $(element).closest('td').addClass('theme_current');
     });"><i class="icon-check"></i>&ensp;Use</a>
	</td>
  <?php if ($i % 3 == 2) { echo '</table>tr>'; } ?>
 <?php endforeach; ?>
</table>