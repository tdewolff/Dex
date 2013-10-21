<?php
Module::set('pages');

use \Michelf\Markdown;
require_once('include/libs/markdown.php');
require_once('include/libs/smartypants.php');

if (!isset($url[3]) || $url[3] == 'remove')
{
    if (isset($url[3]) && $url[3] == 'remove' && isset($url[4]) && is_numeric($url[4]))
    {
        $page = $db->querySingle("SELECT * FROM module_pages WHERE module_pages_id = '" . $db->escape($url[4]) . "' LIMIT 1;");
        if ($page)
            $db->exec("
            DELETE FROM module_pages WHERE module_pages_id = '" . $db->escape($url[4]) . "';
            DELETE FROM link_module WHERE link_module_id = '" . $db->escape($page['link_module_id']) . "';");
    }

    $pages = array();
    $table = $db->query("SELECT * FROM module_pages
        JOIN link_module ON module_pages.link_module_id = link_module.link_module_id
        JOIN link ON link_module.link_id = link.link_id;");
    while ($row = $table->fetch())
    {
        $content = strip_tags($row['parsed_content']);
        $content = strlen($content) > 50 ? substr($content, 0, 50) . '...' : $content;

        $pages[] = array(
            'id' => $row['module_pages_id'],
            'url' => $row['url'],
            'title' => $row['title'],
            'content' => $content,
            'length' => Common::formatBytes(strlen($row['parsed_content']))
        );
    }

    Core::addStyle('popbox.css');
    Core::addStyle('dropdown.css');

    Hooks::emit('admin_header');

    Module::assign('pages', $pages);
    Module::render('admin/pages.tpl');

    Hooks::emit('admin_footer');
    exit;
}
else
{
    if ($url[3] != 'new')
    {
        $page = $db->querySingle("SELECT * FROM module_pages
            JOIN link_module ON module_pages.link_module_id = link_module.link_module_id
            JOIN link ON link_module.link_id = link.link_id
            WHERE module_pages.module_pages_id = '" . $db->escape($url[3]) . "' LIMIT 1;");
        if (!$page)
            user_error('Page with module_pages_id "' . $url[3] . '" doesn\'t exist', ERROR);
    }

    $form = new Form('page');
    $form->addSection('Page', '');
    $form->addText('title', 'Title', 'As displayed in the titlebar', '', array('[a-zA-Z0-9\s]*', 1, 20, 'May contain alphanumeric characters and spaces'));
    $form->addText('url', 'URL', $domain_url . $base_url, '', array('([a-zA-Z0-9\s_\\\\\/\[\]\(\)\|\?\+\-\*\{\},:\^=!\<\>#\$]*\/)?', 0, 50, 'Must be valid URL and end with /'));
    $form->addMarkdown('content', 'Content', '', array('[a-zA-Z0-9\s,\.\-\']*', 0, 80, 'May contain alphanumeric characters, spaces and (,\'-.)'));
    $form->addSeparator();
    $form->addSubmit('page', '<i class="icon-save"></i>&ensp;Save');

    if ($form->submittedBy('page'))
    {
        if ($form->verifyPost())
        {
            $url_base = substr($form->get('url'), 0, strpos($form->get('url'), '/') + 1);
            if ($db->querySingle("SELECT * FROM link WHERE url = '" . $db->escape($form->get('url')) . "' AND link_id IN (SELECT link_id FROM link_module WHERE module_name = 'pages')" . (isset($page) ? " AND link_id != '" . $db->escape($page['link_id']) . "'" : "") . " LIMIT 1;"))
                $form->setError('url', 'Already used');
            else if ($url_base == 'admin/' ||
                     $url_base == 'res/')
                $form->setError('url', 'Cannot start with "' . $url_base . '"');
            else
            {
                $parsed_content = $form->get('content');
                $parsed_content = Markdown::defaultTransform($parsed_content);
                $parsed_content = SmartyPants($parsed_content);

                if ($url[3] != 'new')
                {
                    $db->exec("
                    UPDATE link SET url = '" . $db->escape($form->get('url')) . "', title = '" . $db->escape($form->get('title')) . "' WHERE link_id = '" . $db->escape($page['link_id']) . "';
                    UPDATE module_pages SET
                        content = '" . $db->escape($form->get('content')) . "',
                        parsed_content = '" . $db->escape($parsed_content) . "'
                    WHERE module_pages_id = '" . $db->escape($url[3]) . "';");
                }
                else
                {
                    // don't make a new link if it already exists
                    $link_id = 0;
                    if ($existing_link = $db->querySingle("SELECT * FROM link WHERE url = '" . $db->escape($form->get('url')) . "' LIMIT 1"))
                        $link_id = $existing_link['id'];
                    else
                    {
                        $db->exec("
                        INSERT INTO link (url, title) VALUES (
                            '" . $db->escape($form->get('url')) . "',
                            '" . $db->escape($form->get('title')) . "'
                        );");
                        $link_id = $db->last_id();
                    }

                    $db->exec("
                    INSERT INTO link_module (link_id, module_name) VALUES (
                        '" . $db->escape($link_id) . "',
                        'pages'
                    );

                    INSERT INTO module_pages (link_module_id, content, parsed_content) VALUES (
                        last_insert_rowid(),
                        '" . $db->escape($form->get('content')) . "',
                        '" . $db->escape($parsed_content) . "'
                    );");
                    $form->setAction('/' . $base_url . 'admin/module/pages/' . $db->last_id() . '/');
                }

                $form->setResponse('<span class="passed_time" data-time="' . time() . '">(<span></span>)</span>');
            }
        }
        $form->postToSession();

        if ($url[2] != 'new')
            Module::assign('view', $form->get('url'));
    }
    else
    {
        if ($url[3] != 'new')
        {
            $form->set('title', $page['title']);
            $form->set('url', $page['url']);
            $form->set('content', $page['content']);

            Module::assign('view', $page['url']);
        }
    }

    Module::addStyle('markitup.css');
    Module::addStyle('markdown.css');
    Module::addDeferredScript('jquery.markitup.js');
    Module::addDeferredScript('jquery.markitup.markdown.js');

    Hooks::emit('admin_header');

    $form->sessionToForm();

    Module::assign('page', $form);
    Module::render('admin/page.tpl');

    Hooks::emit('admin_footer');
    exit;
}

?>
