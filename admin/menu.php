<?php

if (isset($uri[3]) && $uri[3] == 'remove' && isset($uri[4]) && is_numeric($uri[4]))
{
    $db->exec("
    UPDATE module_menu SET
        position = position - 1
    WHERE position >= (SELECT position FROM module_menu WHERE id = '" . $db->escape($uri[4]) . "' LIMIT 1);

    DELETE FROM module_menu WHERE id = '" . $db->escape($uri[4]) . "';");
}

$dropbox_links = array();
$links = $db->query("SELECT * FROM links ORDER BY link ASC;");
while ($link = $links->fetch())
    $dropbox_links[$link['id']] = $link['title'] . ' (/' . $link['link'] . ')';

$dropbox_action = array('after' => 'After:', 'before' => 'Before:', 'under' => 'Under:');
$dropbox_nodes = array();
$table = $db->query("SELECT id, name FROM module_menu ORDER BY position ASC;");
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
        $menu_item = $db->querySingle("SELECT * FROM module_menu WHERE id = '" . $db->escape($form->get('action_item')) . "' LIMIT 1;");
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
            UPDATE module_menu SET
                position = position + 1
            WHERE position >= '" . $db->escape($position) . "';

            INSERT INTO module_menu (parent_id, link_id, name, position) VALUES (
                '" . $db->escape($parent) . "',
                '" . $db->escape($form->get('link')) . "',
                '" . $db->escape($form->get('name')) . "',
                '" . $db->escape($position) . "'
            );");
        }
    }
    $form->postToSession();
}

$menu = array();
$table = $db->query("SELECT * FROM module_menu ORDER BY position ASC;");
while ($row = $table->fetch())
    if ($link = $db->querySingle("SELECT * FROM links WHERE id = '" . $db->escape($row['link_id']) . "' LIMIT 1;"))
    {
        $level = 0;
        $parent_id = $row['parent_id'];
        while ($parent_id != 0)
        {
            $parent_id = $menu[$parent_id]['parent_id'];
            $level++;
        }

        $menu[$row['id']] = array(
            'parent_id' => $row['parent_id'],
            'level' => $level,
            'position' => $row['position'],
            'name' => $row['name'],
            'link' => $link['link'],
            'title' => $link['title']
        );
    }

Dexterous::addStyle('resources/styles/popbox.css');
Dexterous::addStyle('resources/styles/dropdown.css');
Dexterous::addDeferredScript('resources/scripts/popbox.js');
Dexterous::addDeferredScript('resources/scripts/dropdown.js');

Hooks::emit('admin_header');

$form->sessionToForm();

Dexterous::assign('form_action', $base_url . 'admin/menu/');
Dexterous::assign('menu', $form);
Dexterous::assign('menu_items', $menu);
Dexterous::render('menu', 'admin/menu.tpl');

Hooks::emit('admin_footer');
exit;

?>
