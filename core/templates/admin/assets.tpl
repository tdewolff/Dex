<h2>Assets</h2>
<table class="grid">
 <?php foreach ($_['assets'] as $i => $item): ?>
  <?php if ($i % 4 == 0) { echo '<tr>'; } ?>
	<td class="no_wrap centered vertical_top">
	 <div class="assets_caption"><strong><?php echo $item['title']; ?></strong></div>
     <div class="assets_caption">(<?php echo $item['width'] . '&times;' . $item['height']; ?>)</div>
	 <a href="/<?php echo $_['base_url'] . $item['url']; ?>" data-fancybox-group="gallery" class="fancybox">
      <img src="/<?php echo $_['base_url'] . $item['url']; ?>?w=200"
           alt=""
           title="<?php echo $item['title']; ?>"
           class="assets_image"
           <?php echo Common::imageSizeAttributes($item['url'], 200); ?> />
     </a>
	</td>
  <?php if ($i % 4 == 3) { echo '</tr>'; } ?>
 <?php endforeach; ?>
</table>