<?php

if (!isset($url[2]))
{
    Core::addStyle('vendor/popbox.min.css');
    Core::addStyle('vendor/dropdown.min.css');

    Hooks::emit('admin-header');
    Core::render('admin/pages.tpl');
    Hooks::emit('admin-footer');
    exit;
}

$form = new Form('page');

$form->addSection(($url[2] == 'new' ? 'New page' : 'Edit page'), '');
$form->addText('title', 'Title', 'As displayed in the titlebar', '', array('[a-zA-Z0-9\s]*', 0, 20, 'Only alphanumeric characters and spaces allowed'));
$form->addLinkUrl('url', 'URL', ($base_url === '/' ? '' : $base_url), 'title');

$form->setId('title', 'url-feed');
$form->setId('url', 'url');

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

    $form->setSubmit('<i class="icon-asterisk"></i>&ensp;Create');
    $form->setResponse('(created)', '(not created)');

    if ($form->submitted())
    {
        if ($form->validate())
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
        $form->finish();
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

    $form->setSubmit('<i class="icon-save"></i>&ensp;Save');
    $form->setResponse('<span class="passed_time">(saved<span></span>)</span>', '(not saved)');

    if ($form->submitted())
    {
        if ($form->validate())
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
        $form->finish();
    }

    $form->set('title', $link['title']);
    $form->set('url', $link['url']);
    $form->setAll($content);

    Core::assign('view', $link['url']);
}

Core::addStyle('vendor/markitup.min.css');
Core::addStyle('vendor/markdown.min.css');
Core::addDeferredScript('vendor/jquery.markitup.min.js');
Core::addDeferredScript('include/jquery.markitup.markdown.min.js');

Hooks::emit('admin-header');

Core::assign('page', $form);
Core::render('admin/page.tpl');

Hooks::emit('admin-footer');
exit;

?>
