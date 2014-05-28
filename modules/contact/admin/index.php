<?php
Module::setModuleName('contact');

$form = new Form('contact');

$form->addSection(__('Contact details'), '');
$form->addText('fn', __('Name'), __('Your full name'), '', array('.*', 0, 20));
$form->addText('org', __('Organization'), '', '', array('.*', 0, 20));
$form->addText('url', __('URL'), __('URL to organization'), __('http://www.domain.com'), array('((https?):\/\/[^\s]*)?', 0, 50, __('Bad URL')));
$form->addEmail('email', __('Email address'), '');
$form->addTel('tel', __('Telephone'), '');
$form->addText('adr_street-address', __('Street address'), '', __('123 Main st.'), array('.*', 0, 20));
$form->addText('adr_locality', __('City'), '', __('Los Angeles'), array('.*', 0, 20));
$form->addText('adr_region', __('Region'), __('State or province'), __('CA'), array('.*', 0, 20));
$form->addText('adr_postal-code', __('Postal code'), '', __('91316'), array('.*', 0, 20));
$form->addText('adr_country-name', __('Country'), '', __('U.S.A'), array('.*', 0, 20));

$form->addSeparator();
$form->setResponse(__('Saved'), __('Not saved'));

$form->optional(array('org', 'url', 'email', 'tel'));
$form->optionalTogether(array('adr_street-address', 'adr_locality', 'adr_region', 'adr_postal-code', 'adr_country-name'));

$contact = array();
$table = Db::query("SELECT * FROM module_contact;");
while ($row = $table->fetch())
	$contact[$row['key']] = $row['value'];

if ($form->submitted())
{
	if ($form->validate())
		foreach ($contact as $key => $value)
			Db::exec("
			UPDATE module_contact SET
				value = '" . Db::escape($form->get($key)) . "'
			WHERE key = '" . Db::escape($key) . "';");
	$form->finish();
}

foreach ($contact as $key => $value)
	$form->set($key, $value);

Hooks::emit('admin-header');

Module::set('contact', $form);
Module::render('admin/contact.tpl');

Hooks::emit('admin-footer');
exit;
