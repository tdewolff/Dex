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
        {
            Module::detachFromLink($page['link_module_id']);
            $db->exec("DELETE FROM module_pages WHERE module_pages_id = '" . $db->escape($url[4]) . "';");
        }
        exit;
    }

    $pages = array();
    $table = $db->query("SELECT * FROM module_pages;");
    while ($row = $table->fetch())
    {
        $row += Module::getAttachedLinkData($row['link_module_id']);

        $row['content'] = strip_tags($row['parsed_content']);
        $row['content'] = strlen($row['content']) > 50 ? substr($row['content'], 0, 50) . '...' : $row['content'];
        $row['length'] = Common::formatBytes(strlen($row['parsed_content']));
        $pages[] = $row;
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
        $page = $db->querySingle("SELECT * FROM module_pages WHERE module_pages.module_pages_id = '" . $db->escape($url[3]) . "' LIMIT 1;");
        if (!$page)
            user_error('Page with module_pages_id "' . $url[3] . '" doesn\'t exist', ERROR);
        $page += Module::getAttachedLinkData($page['link_module_id']);
    }

    $form = new Form('page');
    if ($url[3] != 'new')
        $form->useAjax();

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
            $link_id = ($url[3] != 'new' ? $page['link_id'] : 0);
            if (($error = Module::verifyUrl($link_id, $form->get('url'))) !== true)
                 $form->setError('url', $error);
            else
            {
                $parsed_content = $form->get('content');
                $parsed_content = Markdown::defaultTransform($parsed_content);
                $parsed_content = SmartyPants($parsed_content);

                if ($url[3] != 'new')
                {
                    Module::updateLink($link_id, $form->get('url'), $form->get('title'));

                    $db->exec("
                    UPDATE module_pages SET
                        content = '" . $db->escape($form->get('content')) . "',
                        parsed_content = '" . $db->escape($parsed_content) . "'
                    WHERE module_pages_id = '" . $db->escape($url[3]) . "';");
                }
                else
                {
                    $link_id = Module::getLink($form->get('url'), $form->get('title'));
                    $link_module_id = Module::attachToLink($link_id);

                    $db->exec("
                    INSERT INTO module_pages (link_module_id, content, parsed_content) VALUES (
                        '" . $db->escape($link_module_id) . "',
                        '" . $db->escape($form->get('content')) . "',
                        '" . $db->escape($parsed_content) . "'
                    );");
                    $form->setAction('/' . $base_url . 'admin/module/pages/' . $db->last_id() . '/');
                }

                $form->setResponse('<span class="passed_time" data-time="' . time() . '">(saved <span></span>)</span>');
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
