<?php

Hooks::attach('site', 0, function() {
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

Hooks::attach('error', 0, function() {
    ob_clean();

	global $error_loop;
	if ($error_loop)
		echo 'Error fired while displaying an error message, exiting loop';
	$error_loop = true;

	Core::addTitle('Error');
	Core::assign('error', Error::getLast());

    Hooks::emit('site-header');
    echo '<section class="page-wrapper">';

    echo '<header>';
    Hooks::emit('header');
    echo '</header>';

    echo '<nav class="navigation" role="navigation">';
    Hooks::emit('navigation');
    echo '</nav>';

    echo '<article class="main" role="main">';
    Core::render('error.tpl');
    echo '</article>';

    echo '<footer>';
    Hooks::emit('footer');
    echo '</footer>';

    echo '</section>';
    Hooks::emit('site-footer');
});

Hooks::attach('admin-error', 0, function() {
    ob_clean();

	global $error_loop;
	if ($error_loop)
		echo 'Error fired while displaying an error message, exiting loop';
	$error_loop = true;

	Core::addTitle('Error');
	Core::assign('error', Error::getLast());

    Hooks::emit('admin-header');
    Core::render('admin/error.tpl');
    Hooks::emit('admin-footer');
});

////////////////////////////////////////////////////////////////

Hooks::attach('site-header', 0, function() {
	site_header();
	Core::render('header.tpl');
});

Hooks::attach('site-footer', 0, function() {
	site_footer();
	Core::render('footer.tpl');
});

Hooks::attach('admin-header', 0, function() {
	site_header();
	Core::render('admin/header.tpl');
});

Hooks::attach('admin-footer', 0, function() {
	site_footer();
	Core::render('admin/footer.tpl');
});

////////////////////////////////////////////////////////////////

Hooks::attach('main', 0, function() {
    global $db, $base_url;

    $link_id = Core::getLinkId();
    $template_name = Core::getTemplateName();

    $table = $db->query("SELECT * FROM content WHERE link_id = '" . $db->escape($link_id) . "';");
    while ($row = $table->fetch())
    	Core::assign($row['name'], $row['content']);

	Core::renderTemplate($template_name);

	if (Session::loggedIn())
		echo '<a href="/' . $base_url . 'admin/pages/' . $link_id . '/">Edit page</a>';
});

////////////////////////////////////////////////////////////////

function site_header() {
	$titles = Core::getTitles();
	$externalStyles = Core::getExternalStyles();
	$styles = Core::getStyles();
	$externalScripts = Core::getExternalScripts('header');
	$scripts = Core::getScripts('header');

	if (count($titles))
		Core::assign('header_title', implode(' - ', array_reverse($titles)));
	Core::assign('header_external_styles', $externalStyles);
	if (count($styles))
		Core::assign('header_style', Resource::cacheFiles($styles, 'css'));
	Core::assign('header_external_scripts', $externalScripts);
	if (count($scripts))
		Core::assign('header_script', Resource::cacheFiles($scripts, 'js'));
}

function site_footer() {
	$externalScripts = Core::getExternalScripts('footer');
	$scripts = Core::getScripts('footer');

	Core::assign('footer_external_scripts', $externalScripts);
	if (count($scripts))
		Core::assign('footer_script', Resource::cacheFiles($scripts, 'js'));
}

////////////////////////////////////////////////////////////////

?>
