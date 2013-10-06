<?php

function main_header() {
	$titles = Dexterous::getTitles();
	$styles = Dexterous::getStyles();
	$scripts = Dexterous::getScripts('header');

	if (count($titles))
		Dexterous::assign('header_title', implode(' - ', array_reverse($titles)));
	if (count($styles))
		Dexterous::assign('header_style', Common::minifyCss($styles));
	if (count($scripts))
		Dexterous::assign('header_script', Common::minifyJs($scripts));
}

function main_footer() {
	$scripts = Dexterous::getScripts('footer');
	if (count($scripts))
		Dexterous::assign('footer_script', Common::minifyJs($scripts));
}

Hooks::attach('error', 0, function() {
	global $uri;

	Dexterous::addTitle('Error');
	if ($uri[0] == 'admin')
	{
		Hooks::emit('admin_header');
		Dexterous::render('admin/error.tpl');
		Hooks::emit('admin_footer');
	}
	else
	{
		Hooks::emit('header');
		Dexterous::render('error.tpl');
		Hooks::emit('footer');
	}
	exit;
});

Hooks::attach('header', 0, function() {
	main_header();
	Dexterous::render('header.tpl');
});

Hooks::attach('footer', 0, function() {
	main_footer();
	Dexterous::render('footer.tpl');
});

Hooks::attach('admin_header', 0, function() {
	main_header();
	Dexterous::render('admin/header.tpl');
});

Hooks::attach('admin_footer', 0, function() {
	main_footer();
	Dexterous::render('admin/footer.tpl');
});

?>
