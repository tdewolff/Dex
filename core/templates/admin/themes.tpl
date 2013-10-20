<h2>Themes</h2>
<table class="grid">
 <?php foreach ($_['themes'] as $i => $theme): ?>
  <?php if ($i % 3 == 0) { echo '<tr>'; } ?>
	<td class="no_wrap centered vertical_top <?php if ($theme['name'] == $_['current_theme']) { echo 'theme_current'; } ?>">
	 <h4 class="theme_caption" <?php if ($theme['name'] == $_['current_theme']) { echo 'style="color:white;"'; } ?>><?php echo $theme['title']; ?></h4>
     <div class="theme_caption">(<?php echo $theme['author']; ?>)</div>
	 <img src="/<?php echo $_['base_url']; ?>res/theme/<?php echo $theme['name']; ?>/preview.png" alt="<?php echo $theme['name']; ?>" class="theme_image" width="256" height="256" /><br />

     <?php if ($theme['name'] != $_['current_theme']): ?>
	  <a href="/<?php echo $_['base_url']; ?>admin/themes/use/<?php echo $theme['name']; ?>/" class="small-button"><i class="icon-check"></i>&ensp;Use</a>
	 <?php endif; ?>
	</td>
  <?php if ($i % 3 == 2) { echo '</table>tr>'; } ?>
 <?php endforeach; ?>
</table>