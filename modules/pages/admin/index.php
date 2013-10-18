<?php

use \Michelf\Markdown;
require_once('include/libs/markdown.php');
require_once('include/libs/smartypants.php');

if (!isset($uri[3]) || $uri[3] == 'remove')
{
    if (isset($uri[3]) && $uri[3] == 'remove' && isset($uri[4]) && is_numeric($uri[4]))
    {
        $page = $db->querySingle("SELECT * FROM module_pages WHERE id = '" . $db->escape($uri[4]) . "' LIMIT 1;");
        if ($page)
        {
            $link_module = $db->query("SELECT COUNT(*) AS rows, link_id FROM link_modules WHERE link_id IN (SELECT link_id FROM link_modules WHERE id = '" . $db->escape($page['link_module_id']) . "' LIMIT 1);");
            if ($link_module && $link_module['rows'] == 1)
                $db->exec("
                DELETE FROM links WHERE id = '" . $db->escape($link_module['link_id']) . "';");

            $db->exec("
            DELETE FROM link_modules WHERE id = '" . $db->escape($page['link_module_id']) . "';
            DELETE FROM module_pages WHERE id = '" . $db->escape($uri[4]) . "';");
        }
    }

    $pages = array();
    $table = $db->query("SELECT * FROM module_pages;");
    while ($row = $table->fetch())
        if ($link_module = $db->querySingle("SELECT * FROM link_modules WHERE id = '" . $db->escape($row['link_module_id']) . "' LIMIT 1;"))
            if ($link = $db->querySingle("SELECT * FROM links WHERE id = '" . $db->escape($link_module['link_id']) . "' LIMIT 1;"))
                $pages[] = array(
                    'id' => $row['id'],
                    'link' => $link['link'],
                    'title' => $link['title'],
                    'content' => strip_tags($row['parsed_content'])
                );

    Dexterous::addStyle('resources/styles/popbox.css');
    Dexterous::addStyle('resources/styles/dropdown.css');
    Dexterous::addDeferredScript('resources/scripts/popbox.js');
    Dexterous::addDeferredScript('resources/scripts/dropdown.js');

    Hooks::emit('admin_header');

    Dexterous::assign('pages', $pages);
    Dexterous::renderModule('pages', 'admin/pages.tpl');

    Hooks::emit('admin_footer');
    exit;
}
else
{
    if ($uri[3] != 'new')
    {
        $page = $db->querySingle("SELECT * FROM module_pages WHERE id = '" . $db->escape($uri[3]) . "' LIMIT 1;");
        if (!$page)
            user_error('Page with id "' . $uri[3] . '" doesn\'t exist');

        $link_module = $db->querySingle("SELECT * FROM link_modules WHERE id = '" . $db->escape($page['link_module_id']) . "' LIMIT 1;");
        if (!$link_module)
            user_error('Link-module relation with id "' . $page['link_module_id'] . '" doesn\'t exist');

        $link = $db->querySingle("SELECT * FROM links WHERE id = '" . $db->escape($link_module['link_id']) . "' LIMIT 1;");
        if (!$link)
            user_error('Link to page with link id "' . $link_module['link_id'] . '" doesn\'t exist');
    }

    $form = new Form('page');

    $form->addSection('Page', '');
    $form->addText('title', 'Title', 'As displayed in the titlebar', '', array('[a-zA-Z0-9\s]*', 1, 20, 'May contain alphanumeric characters and spaces'));
    $form->addText('link', 'Link', $domain_url . $base_url, '', array('([a-zA-Z0-9\s_\\\\\/\[\]\(\)\|\?\+\-\*\{\},:\^=!\<\>#\$]*\/)?', 0, 50, 'Must be valid link and end with /'));
    $form->addMarkdown('content', 'Content', '', array('[a-zA-Z0-9\s,\.\-\']*', 0, 80, 'May contain alphanumeric characters, spaces and (,\'-.)'));

    $form->addSeparator();
    $form->addSubmit('page', '<i class="icon-save"></i>&ensp;Save');

    if ($form->submittedBy('page'))
    {
        if ($form->verifyPost())
        {
            $link_base = substr($form->get('link'), 0, strpos($form->get('link'), '/') + 1);
            if ($db->querySingle("SELECT * FROM links WHERE link = '" . $db->escape($form->get('link')) . "' AND id IN (SELECT link_id FROM link_modules WHERE module_name = 'pages')" . (isset($link) ? " AND id != '" . $db->escape($link['id']) . "'" : "") . " LIMIT 1;"))
                $form->setError('link', 'Already used');
            else if ($link_base == 'admin/' ||
                     $link_base == 'resources/' ||
                     $link_base == 'themes/' ||
                     $link_base == 'media/')
                $form->setError('link', 'Cannot start with "' . $link_base . '"');
            else
            {
                $parsed_content = $form->get('content');
                $parsed_content = Markdown::defaultTransform($parsed_content);
                $parsed_content = SmartyPants($parsed_content);

                if ($uri[3] != 'new')
                {
                    $db->exec("
                    UPDATE links SET link = '" . $db->escape($form->get('link')) . "', title = '" . $db->escape($form->get('title')) . "' WHERE id = '" . $db->escape($link_module['link_id']) . "';
                    UPDATE module_pages SET
                        content = '" . $db->escape($form->get('content')) . "',
                        parsed_content = '" . $db->escape($parsed_content) . "'
                    WHERE id = '" . $db->escape($uri[3]) . "';");
                }
                else
                {
                    $db->exec("
                    INSERT INTO links (link, title) VALUES (
                        '" . $db->escape($form->get('link')) . "',
                        '" . $db->escape($form->get('title')) . "'
                    );

                    INSERT INTO link_modules (link_id, module_name) VALUES (
                        last_insert_rowid(),
                        'pages'
                    );

                    INSERT INTO module_pages (link_module_id, content, parsed_content) VALUES (
                        last_insert_rowid(),
                        '" . $db->escape($form->get('content')) . "',
                        '" . $db->escape($parsed_content) . "'
                    );");

                    $page_id = $db->last_id();
                    Dexterous::assign('form_action', $base_url . 'admin/module/pages/' . $page_id . '/');
                }

                $form->setResponse('<span class="passed_time" data-time="' . time() . '">(<span></span>)</span>');
            }
        }
        $form->postToSession();

        if ($uri[2] != 'new')
            Dexterous::assign('view', $form->get('link'));
    }
    else
    {
        if ($uri[3] != 'new')
        {
            $form->set('title', $link['title']);
            $form->set('link', $link['link']);
            $form->set('content', $page['content']);

            Dexterous::assign('view', $link['link']);
        }
    }

    Dexterous::addStyle('resources/styles/markitup/simple.css');
    Dexterous::addStyle('resources/styles/markitup/markdown.css');
    Dexterous::addDeferredScript('resources/scripts/jquery.markitup.js');
    Dexterous::addDeferredScript('resources/scripts/jquery.markitup.markdown.js');

    Hooks::emit('admin_header');

    $form->sessionToForm();

    Dexterous::assign('page', $form);
    Dexterous::renderModule('pages', 'admin/page.tpl');

    Hooks::emit('admin_footer');
    exit;
}

?>
