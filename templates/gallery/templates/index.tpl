<?php

echo '<ul class="gallery">';
foreach ($_['images'] as $image)
{
	echo '<li><figure><a href="/' . $_['base_url'] . 'res/' . $image['url'] . '" title="' . $image['title'] . '" rel="gallery" class="fancybox"><img src="/' . $_['base_url'] . 'res/' . $image['url'] . '/w=' . $_['max_width'] . '/t=' . $image['mtime'] . '/" ' . $image['attr'] . '></a><figcaption>' . $image['title'] . '</figcation></figure></li>';
}
echo '</ul>';