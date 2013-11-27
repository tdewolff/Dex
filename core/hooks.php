<?php

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
		Core::assign('header_style', Resource::concatenateFiles($styles, 'css'));
	Core::assign('header_external_scripts', $externalScripts);
	if (count($scripts))
		Core::assign('header_script', Resource::concatenateFiles($scripts, 'js'));
}

function site_footer() {
	$externalScripts = Core::getExternalScripts('footer');
	$scripts = Core::getScripts('footer');

	Core::assign('footer_external_scripts', $externalScripts);
	if (count($scripts))
		Core::assign('footer_script', Resource::concatenateFiles($scripts, 'js'));
}

////////////////////////////////////////////////////////////////

?>
