<?php
use \Michelf\Markdown;
require_once('include/libs/markdown.php');
require_once('include/libs/smartypants.php');

if (!isset($url[2]))
{
    if (Common::isMethod('POST'))
    {
        $data = Common::getMethodData();
        if (!isset($data['link_id']))
            user_error('No link ID set', ERROR);

        $db->exec("
            DELETE FROM content WHERE link_id = '" . $db->escape($data['link_id']) . "';
            DELETE FROM link WHERE link_id = '" . $db->escape($data['link_id']) . "';");
        exit;
    }

    $pages = array();
    $table = $db->query("SELECT * FROM link;");
    while ($row = $table->fetch())
    {
        $ini_filename = 'templates/' . $row['template_name'] . '/config.ini';
        if (file_exists($ini_filename) !== false && ($ini = parse_ini_file($ini_filename)) !== false)
            $row['template_name'] = Common::tryOrEmpty($ini, 'title');

        $row['content'] = array();
        $table2 = $db->query("SELECT * FROM content WHERE link_id = '" . $row['link_id'] . "';");
        while ($row2 = $table2->fetch())
            $row['content'][] = $row2['content'];
        $row['content'] = strip_tags(implode(' ', $row['content']));
        $row['content'] = strlen($row['content']) > 50 ? substr($row['content'], 0, 50) . '...' : $row['content'];
        $row['length'] = Common::formatBytes(strlen($row['content']));
        $pages[] = $row;
    }

    Core::addStyle('popbox.css');
    Core::addStyle('dropdown.css');

    Hooks::emit('admin_header');

    Core::assign('pages', $pages);
    Core::render('admin/pages.tpl');

    Hooks::emit('admin_footer');
    exit;
}

$form = new Form('page');
$form->addSection(($url[2] == 'new' ? 'New page' : 'Edit page'), '');
$form->addText('title', 'Title', 'As displayed in the titlebar', '', array('[a-zA-Z0-9\s]*', 1, 20, 'May contain alphanumeric characters and spaces'));
$form->addLinkUrl('url', 'URL', $domain_url . $base_url, 'title');

if ($url[2] == 'new')
{
    $templates = array();
    $handle = opendir('templates/');
    while (($template_name = readdir($handle)) !== false)
        if (is_dir('templates/' . $template_name) && $template_name != '.' && $template_name != '..')
        {
            $ini_filename = 'templates/' . $template_name . '/config.ini';
            if (file_exists($ini_filename) !== false && ($ini = parse_ini_file($ini_filename)) !== false)
                $templates[$template_name] = Common::tryOrEmpty($ini, 'title');
        }
    $form->addDropdown('template_name', 'Template', 'Determine page type', $templates);

    $form->addSeparator();
    $form->addSubmit('page', '<i class="icon-asterisk"></i>&ensp;Create', '(created)', '(not created)');

    if ($form->submittedBy('page'))
    {
        if ($form->validateInput())
            if (($error = Core::verifyLinkUrl($form->get('url'))) !== true)
                 $form->setError('url', $error);
            else
            {
                $link_id = 0;
                $link = $db->querySingle("
                    SELECT * FROM link WHERE url = '" . $db->escape($form->get('url')) . "' LIMIT 1");
                if ($link)
                {
                    if ($form->get('title') != $link['title'])
                        $db->exec("
                            UPDATE link SET
                                title = '" . $db->escape($form->get('title')) . "',
                                template_name = '" . $db->escape($form->get('template_name')) . "'
                            WHERE link_id = '" . $db->escape($link['link_id']) . "';");
                    $link_id = $link['link_id'];
                }
                else
                {
                    $db->exec("
                        INSERT INTO link (url, title, template_name) VALUES (
                            '" . $db->escape($form->get('url')) . "',
                            '" . $db->escape($form->get('title')) . "',
                            '" . $db->escape($form->get('template_name')) . "'
                        );");
                    $link_id = $db->last_id();
                }
                $form->setRedirect('/' . $base_url . 'admin/pages/' . $link_id);
            }
        $form->returnJSON();
    }
}
else
{
    $link = $db->querySingle("SELECT * FROM link WHERE link_id = '" . $db->escape($url[2]) . "' LIMIT 1;");
    if (!$link)
        user_error('Link with link_id "' . $url[2] . '" doesn\'t exist', ERROR);

    $content = array();
    $table = $db->query("SELECT * FROM content WHERE link_id = '" . $db->escape($url[2]) . "';");
    while ($row = $table->fetch())
        $content[$row['name']] = $row['content'];


    $form->addSeparator();
    if (!file_exists('templates/' . $link['template_name'] . '/form.php'))
        user_error('Template name not set', ERROR);
    include_once('templates/' . $link['template_name'] . '/form.php');

    $form->addSeparator();
    $form->addSubmit('page', '<i class="icon-save"></i>&ensp;Save', '<span class="passed_time">(saved<span></span>)</span>', '(not saved)');

    if ($form->submittedBy('page'))
    {
        if ($form->validateInput())
        {
            if (($error = Core::verifyLinkUrl($form->get('url'), $url[2])) !== true)
                 $form->setError('url', $error);
            else
            {
                $db->exec("
                    UPDATE link SET
                        url = '" . $db->escape($form->get('url')) . "',
                        title = '" . $db->escape($form->get('title')) . "'
                    WHERE link_id = '" . $db->escape($url[2]) . "';");

                foreach ($form->getAll() as $name => $value)
                {
                    if ($name == 'title' || $name == 'url')
                        continue;

                    if (isset($content[$name]))
                        $db->exec("
                            UPDATE content SET
                                content = '" . $db->escape($value) . "'
                            WHERE link_id = '" . $db->escape($url[2]) . "' AND name = '" . $db->escape($name) . "';");
                    else
                        $db->exec("
                            INSERT INTO content (link_id, name, content) VALUES (
                                '" . $db->escape($url[2]) . "',
                                '" . $db->escape($name) . "',
                                '" . $db->escape($value) . "'
                            );");
                }
            }
        }
        $form->returnJSON();
    }

    $form->set('title', $link['title']);
    $form->set('url', $link['url']);
    $form->setAll($content);

    CORE::assign('view', $link['url']);
}

Core::addStyle('markitup.css');
Core::addStyle('markdown.css');
Core::addDeferredScript('jquery.markitup.js');
Core::addDeferredScript('jquery.markitup.markdown.js');

Hooks::emit('admin_header');

Core::assign('page', $form);
Core::render('admin/page.tpl');

Hooks::emit('admin_footer');
exit;

?>
