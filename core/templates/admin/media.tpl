<h2>Media</h2>
<table class="grid">
 <?php foreach ($_['media'] as $i => $item): ?>
  <?php if ($i % 4 == 0) { echo '<tr>'; } ?>
	<td class="no_wrap centered vertical_top">
	 <div class="media_caption"><strong><?php echo $item['title']; ?></strong></div>
     <div class="media_caption">(<?php echo $item['width'] . '&times;' . $item['height']; ?>)</div>
	 <a href="/<?php echo $_['base_url'] . $item['url']; ?>" data-fancybox-group="gallery" class="fancybox">
      <img src="/<?php echo $_['base_url'] . $item['url']; ?>?w=200" alt="<?php echo $item['title']; ?>" class="media_image" <?php if ($item['width'] > 200) { echo 'width="200" height="' . floor(200.0 / $item['width'] * $item['height']) . '"'; } else { echo 'width="' . $item['width'] . '" height="' . $item['height'] . '"'; } ?> />
     </a>
	</td>
  <?php if ($i % 4 == 3) { echo '</tr>'; } ?>
 <?php endforeach; ?>
</table>