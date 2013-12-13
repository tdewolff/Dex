<?php
Module::set('contact');

$form = new Form('contact');

$form->addSection('Contact details', '');
$form->addText('fn', 'Name', 'Your full name', '', array('.*', 0, 20));
$form->addText('org', 'Organization', '', '', array('.*', 0, 20));
$form->addText('url', 'URL', 'URL to organization', 'http://www.domain.com', array('((https?):\/\/[^\s]*)?', 0, 50, 'Bad URL'));
$form->addEmail('email', 'Email address', '');
$form->addTel('tel', 'Telephone', '');
$form->addText('adr_street-address', 'Street address', '', '123 Main st.', array('.*', 0, 20));
$form->addText('adr_locality', 'City', '', 'Los Angeles', array('.*', 0, 20));
$form->addText('adr_region', 'Region', 'State or province', 'CA', array('.*', 0, 20));
$form->addText('adr_postal-code', 'Postal code', '', '91316', array('.*', 0, 20));
$form->addText('adr_country-name', 'Country', '', 'U.S.A', array('.*', 0, 20));

$form->addSeparator();

$form->setSubmit('<i class="fa fa-save"></i>&ensp;Save');
$form->setResponse('<span class="passed_time">(saved<span></span>)</span>', '(not saved)');

$form->optional(array('org', 'url', 'email', 'tel'));
$form->optionalTogether(array('adr_street-address', 'adr_locality', 'adr_region', 'adr_postal-code', 'adr_country-name'));

$contact = array();
$table = $db->query("SELECT * FROM module_contact;");
while ($row = $table->fetch())
    $contact[$row['key']] = $row['value'];

if ($form->submitted())
{
    if ($form->validate())
        foreach ($contact as $key => $value)
            $db->exec("
            UPDATE module_contact SET
                value = '" . $db->escape($form->get($key)) . "'
            WHERE key = '" . $db->escape($key) . "';");
    $form->finish();
}

foreach ($contact as $key => $value)
    $form->set($key, $value);

Hooks::emit('admin-header');

Module::assign('contact', $form);
Module::render('admin/contact.tpl');

Hooks::emit('admin-footer');
exit;

?>
