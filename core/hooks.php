<?php

function main_header() {
	$titles = Core::getTitles();
	$styles = Core::getStyles();
	$scripts = Core::getScripts('header');

	if (count($titles))
		Core::assign('header_title', implode(' - ', array_reverse($titles)));
	if (count($styles))
		Core::assign('header_style', Common::concatenateFiles($styles, 'css'));
	if (count($scripts))
		Core::assign('header_script', Common::concatenateFiles($scripts, 'js'));
}

function main_footer() {
	$scripts = Core::getScripts('footer');
	if (count($scripts))
		Core::assign('footer_script', Common::concatenateFiles($scripts, 'js'));
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

Hooks::attach('navigation', 0, function($parameter) {
    global $db;
    $current_link = $parameter['link_id'];

    $menu = array();
    $table = $db->query("SELECT * FROM menu
        JOIN link ON menu.link_id = link.link_id ORDER BY position ASC;");
    while ($row = $table->fetch())
        $menu[$row['parent_id']][$row['id']] = array(
            'name' => $row['name'],
            'url' => $row['url'],
            'selected' => ($current_link == $row['link_id'] ? '1' : '0')
        );

    Core::assign('menu', $menu);
    Core::render('menu.tpl');
});

?>
