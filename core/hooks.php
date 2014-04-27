<?php

Hooks::attach('site', 0, function () {
	Hooks::emit('site-header');
	echo '<section class="page-wrapper">';

	echo '<header>';
	Hooks::emit('header');
	echo '</header>';

	echo '<nav class="navigation" role="navigation">';
	Hooks::emit('navigation');
	echo '</nav>';

	echo '<article class="main" role="main">';
	Hooks::emit('main');
	echo '</article>';

	echo '<footer>';
	Hooks::emit('footer');
	echo '</footer>';

	echo '</section>';
	Hooks::emit('site-footer');
});

$error_loop = false;

Hooks::attach('site-error', 0, function () {
	ob_clean();

	global $error_loop;
	if ($error_loop)
		echo 'Error fired while displaying an error message, exiting loop';
	$error_loop = true;

	Core::addTitle('Error');
	Core::set('error', Error::getErrors());

	Hooks::emit('site-header');
	echo '<section class="page-wrapper">';

	echo '<header>';
	Hooks::emit('header');
	echo '</header>';

	echo '<nav class="navigation" role="navigation">';
	Hooks::emit('navigation');
	echo '</nav>';

	echo '<article class="main error" role="main">';
	Core::render('admin/error.tpl');
	echo '</article>';

	echo '<footer>';
	Hooks::emit('footer');
	echo '</footer>';

	echo '</section>';
	Hooks::emit('site-footer');
});

Hooks::attach('admin-error', 0, function () {
	ob_clean();

	global $error_loop;
	if ($error_loop)
		echo 'Error fired while displaying an error message, exiting loop';
	$error_loop = true;

	Core::addTitle('Error');
	Core::set('error', Error::getErrors());

	Hooks::emit('admin-header');
	Core::render('admin/error.tpl');
	Hooks::emit('admin-footer');
});

////////////////////////////////////////////////////////////////

Hooks::attach('site-header', 0, function () {
	site_header();
	Core::render('header.tpl');
});

Hooks::attach('site-footer', 0, function () {
	site_footer();
	Core::render('footer.tpl');
});

Hooks::attach('admin-header', 0, function () {
	site_header();
	Core::render('admin/header.tpl');
});

Hooks::attach('admin-footer', 0, function () {
	site_footer();
	Core::render('admin/footer.tpl');
});

////////////////////////////////////////////////////////////////

function site_header() {
	global $settings;

	$titles = Core::getTitles();
	$externalStyles = Core::getExternalStyles();
	$styles = Core::getStyles();
	$externalScripts = Core::getExternalScripts('header');
	$scripts = Core::getScripts('header');

	Core::set('header_description', Common::tryOrEmpty($settings, 'description'));
	if (strlen($settings['keywords']))
		Core::set('header_keywords', implode(',', json_decode($settings['keywords'])));

	if (count($titles))
		Core::set('header_title', implode(' - ', array_reverse($titles)));
	Core::set('header_external_styles', $externalStyles);
	if (count($styles))
		Core::set('header_style', Resource::cacheFiles($styles, 'css'));
	Core::set('header_external_scripts', $externalScripts);
	if (count($scripts))
		Core::set('header_script', Resource::cacheFiles($scripts, 'js'));
}

function site_footer() {
	$externalScripts = Core::getExternalScripts('footer');
	$scripts = Core::getScripts('footer');

	Core::set('footer_external_scripts', $externalScripts);
	if (count($scripts))
		Core::set('footer_script', Resource::cacheFiles($scripts, 'js'));
}

////////////////////////////////////////////////////////////////
