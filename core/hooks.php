<?php

function main_header() {
	$titles = Core::getTitles();
	$styles = Core::getStyles();
	$scripts = Core::getScripts('header');

	if (count($titles))
		Core::assign('header_title', implode(' - ', array_reverse($titles)));
	if (count($styles))
		Core::assign('header_style', Resource::concatenateFiles($styles, 'css'));
	if (count($scripts))
		Core::assign('header_script', Resource::concatenateFiles($scripts, 'js'));
}

function main_footer() {
	$scripts = Core::getScripts('footer');
	if (count($scripts))
		Core::assign('footer_script', Resource::concatenateFiles($scripts, 'js'));
}

Hooks::attach('error', 0, function() {
	global $url;

	Core::addTitle('Error');
	if ($url[0] == 'admin')
	{
		Hooks::emit('admin_header');
		Core::render('admin/error.tpl');
		Hooks::emit('admin_footer');
	}
	else
	{
		Hooks::emit('header');
		Core::render('error.tpl');
		Hooks::emit('footer');
	}
	exit;
});

Hooks::attach('header', 0, function() {
	main_header();
	Core::render('header.tpl');
});

Hooks::attach('footer', 0, function() {
	main_footer();
	Core::render('footer.tpl');
});

Hooks::attach('admin_header', 0, function() {
	main_header();
	Core::render('admin/header.tpl');
});

Hooks::attach('admin_footer', 0, function() {
	main_footer();
	Core::render('admin/footer.tpl');
});

Hooks::attach('main', 0, function() {
    global $db;

    $link_id = Core::getLinkId();
    $template_name = Core::getTemplateName();

    $table = $db->query("SELECT * FROM content WHERE link_id = '" . $db->escape($link_id) . "';");
    while ($row = $table->fetch())
    	Core::assign($row['name'], $row['content']);

	Core::renderTemplate($template_name);
});

?>
