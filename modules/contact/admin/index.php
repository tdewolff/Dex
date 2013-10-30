<?php
Module::set('contact');

$form = new Form('contact');
$form->usePUT();

$form->addSection('Contact details', '');
$form->addText('url', 'URL', 'URL to organization', 'http://www.domain.com', array('((https?|ftp):\/\/[^\s\/\$\.\?#]\.[^\s]*)?', 0, 50, 'Bad URL'));
$form->addText('organization', 'Organization', '', '', array('[a-zA-Z0-9\s]*', 0, 20, 'May contain alphanumeric characters and spaces'));
$form->addText('name', 'Name', 'Your full name', '', array('[a-zA-Z\s]*', 0, 20, 'May contain alphabetic characters and spaces'));
$form->addTel('tel', 'Telephone', '');
$form->addEmail('email', 'Emailaddress', '');

$form->allowEmpty(array('tel', 'email'));

$form->addSeparator();
$form->addSubmit('contact', '<i class="icon-save"></i>&ensp;Save', '<span class="passed_time">(saved <span></span>)</span>', '(not saved)');

if ($form->submittedBy('contact'))
{
    if ($form->validateInput())
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
    $form->returnJSON();
}

$contact_settings = $db->query("SELECT * FROM module_contact;");
while ($contact_setting = $contact_settings->fetch())
    $form->set($contact_setting['key'], $contact_setting['value']);

Hooks::emit('admin_header');

Module::assign('contact', $form);
Module::render('admin/contact.tpl');

Hooks::emit('admin_footer');
exit;

?>
