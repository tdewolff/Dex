<?php

if (isset($url[3]) && $url[3] == 'remove' && isset($url[4]) && is_numeric($url[4]))
{
    $db->exec("
    UPDATE menu SET
        position = position - 1
    WHERE position >= (SELECT position FROM menu WHERE id = '" . $db->escape($url[4]) . "' LIMIT 1);

    DELETE FROM menu WHERE id = '" . $db->escape($url[4]) . "';");
}
/*
$dropbox_links = array();
$links = $db->query("SELECT * FROM links ORDER BY link ASC;");
while ($link = $links->fetch())
    $dropbox_links[$link['id']] = $link['title'] . ' (/' . $link['link'] . ')';

$dropbox_action = array('after' => 'After:', 'before' => 'Before:', 'under' => 'Under:');
$dropbox_nodes = array();
$table = $db->query("SELECT * FROM menu ORDER BY position ASC;");
while ($row = $table->fetch())
    $dropbox_nodes[$row['id']] = $row['name'];

$form = new Form('menu');
$form->makeInline();

$form->addSection('Add menu item', '');
$form->addText('name', '', '', '', array('[a-zA-Z0-9\s]*', 1, 20, 'May contain alphanumeric characters and spaces'));
$form->addDropdown('link', '', '', $dropbox_links);
$form->addDropdown('action', '', '', $dropbox_action);
$form->addDropdown('action_item', '', '', $dropbox_nodes);

$form->addSeparator();
$form->addSubmit('menu', '<i class="icon-save"></i>&ensp;Add');

if ($form->submittedBy('menu'))
{
    if ($form->verifyPost())
    {
        $menu_item = $db->querySingle("SELECT * FROM menu WHERE id = '" . $db->escape($form->get('action_item')) . "' LIMIT 1;");
        if (!$menu_item)
            $form->setError('action_item', 'Could not find menu item');
        else
        {
            $parent = $menu_item['parent_id'];
            $position = $menu_item['position'];
            if ($form->get('action') == 'after') {
                $position++;
            } elseif ($form->get('action') == 'under') {
                $parent = $menu_item['id'];
                $position++;
            }

            // TODO: add new menu item to action_item dropdown...

            $db->exec("
            UPDATE menu SET
                position = position + 1
            WHERE position >= '" . $db->escape($position) . "';

            INSERT INTO menu (parent_id, link_id, name, position) VALUES (
                '" . $db->escape($parent) . "',
                '" . $db->escape($form->get('link')) . "',
                '" . $db->escape($form->get('name')) . "',
                '" . $db->escape($position) . "'
            );");
        }
    }
    $form->postToSession();
}*/

$menu = array();
$table = $db->query("SELECT * FROM menu
    JOIN link ON menu.link_id = link.link_id ORDER BY position ASC;");
while ($row = $table->fetch())
{
    $level = 0;
    $parent_id = $row['parent_menu_id'];
    while ($parent_id != 0)
    {
        $parent_id = $menu[$parent_id]['parent_menu_id'];
        $level++;
    }

    $menu[$row['parent_menu_id']][$row['menu_id']] = array(
        'level' => $level,
        'position' => $row['position'],
        'name' => $row['name'],
        'url' => $row['url'],
        'title' => $row['title']
    );
}

Core::addStyle('popbox.css');
Core::addStyle('dropdown.css');

Hooks::emit('admin_header');

//$form->sessionToForm();
//$form->setAction('/' . $base_url . 'admin/menu/');

//Dexterous::assign('menu_form', $form);
Core::assign('menu', $menu);
Core::render('admin/menu.tpl');

Hooks::emit('admin_footer');
exit;

?>
