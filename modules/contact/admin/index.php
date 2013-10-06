<?php

$form = new Form('contact', 'Contact');

$form->addSection('Contact details', '');
$form->addText('url', 'URL', 'URL to organization', array('.*', 1, 50, ''));
$form->addText('organization', 'Organization', '', array('[a-zA-Z0-9\s]*', 1, 20, 'May contain alphanumeric characters and spaces'));
$form->addText('name', 'Name', 'Your full name', array('[a-zA-Z\s]*', 1, 20, 'May contain alphabetic characters and spaces'));
$form->addText('tel', 'Telephone', '', array('.*', 1, 20, ''));
$form->addText('email', 'Emailaddress', '', array('.*', 1, 50, ''));

$form->addSeparator();
$form->addSubmit('contact', '<i class="icon-save"></i>&ensp;Save');

if ($form->submittedBy('contact'))
{
    if ($form->verifyPost())
    {
        $db->exec("
        UPDATE module_contact SET
            value = '" . $db->escape($form->get('url')) . "'
        WHERE key = 'url';

        UPDATE module_contact SET
            value = '" . $db->escape($form->get('organization')) . "'
        WHERE key = 'organization';

        UPDATE module_contact SET
            value = '" . $db->escape($form->get('name')) . "'
        WHERE key = 'name';

        UPDATE module_contact SET
            value = '" . $db->escape($form->get('tel')) . "'
        WHERE key = 'tel';

        UPDATE module_contact SET
            value = '" . $db->escape($form->get('email')) . "'
        WHERE key = 'email';");

        $form->setResponse('<span class="passed_time" data-time="' . time() . '">(<span></span>)</span>');
    }
    $form->postToSession();
}

Hooks::emit('admin_header');

$form->sessionToForm();

Dexterous::assign('contact', $form);
Dexterous::renderModule('contact', 'admin/contact.tpl');

Hooks::emit('admin_footer');
exit;

?>
