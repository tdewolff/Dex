<h1>Media</h1>
<table class="grid">
 <?php foreach ($_['media'] as $i => $item): ?>
  <?php if ($i % 5 == 0) { echo '<tr>'; } ?>
	<td class="no_wrap centered vertical_top">
	 <div style="width:150px; overflow:hidden;"><strong><?php echo $item['title']; ?></strong><br />(<?php echo $item['width'] . '&times;' . $item['height']; ?>)</div>
	 <a href="/<?php echo $_['base_url'] . $item['url']; ?>" data-fancybox-group="gallery" class="fancybox">
      <img src="/<?php echo $_['base_url'] . $item['url']; ?>?w=150" alt="<?php echo $item['title']; ?>"  class="media_image" <?php if ($item['width'] > 150) { echo 'style="width:150px;"'; } ?> />
     </a>
	</td>
  <?php if ($i % 5 == 4) { echo '</tr>'; } ?>
 <?php endforeach; ?>
</table>