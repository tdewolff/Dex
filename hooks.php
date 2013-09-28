<?php

Hooks::attach('error', function() {
	global $uri;
	Dexterous::addTitle('Error');

	Hooks::emit('header');

	Dexterous::render(($uri[0] == 'admin' ? 'admin/' : '') . 'error.tpl');

	Hooks::emit('footer');
	exit;
});

Hooks::attach('header', function() {
	global $uri;

	$titles = Dexterous::getTitles();
	$styles = Dexterous::getStyles();
	$scripts = Dexterous::getScripts('header');

	if (count($titles))
		Dexterous::assign('header_title', implode(' - ', array_reverse($titles)));
	if (count($styles))
		Dexterous::assign('header_style', Common::minifyCss($styles));
	if (count($scripts))
		Dexterous::assign('header_script', Common::minifyJs($scripts));

	Dexterous::render(($uri[0] == 'admin' ? 'admin/' : '') . 'header.tpl');
});

Hooks::attach('index', function() {
	global $uri;

	$contents = Dexterous::getContents();

	if (isset($contents['navigation']))
		Dexterous::assign('navigation', implode($contents['navigation']));
	if (isset($contents['main']))
		Dexterous::assign('main', implode($contents['main']));

	Dexterous::render(($uri[0] == 'admin' ? 'admin/' : '') . 'index.tpl');
});

Hooks::attach('footer', function() {
	global $uri;

	$scripts = Dexterous::getScripts('footer');
	if (count($scripts))
		Dexterous::assign('footer_script', Common::minifyJs($scripts));

	Dexterous::render(($uri[0] == 'admin' ? 'admin/' : '') . 'footer.tpl');
});

?>
