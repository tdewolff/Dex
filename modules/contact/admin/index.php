<?php
Module::setModuleName('contact');

$form = new Form('contact');

$form->addSection(_('Contact details'), '');
$form->addText('fn', _('Name'), _('Your full name'), '', array('.*', 0, 20));
$form->addText('org', _('Organization'), '', '', array('.*', 0, 20));
$form->addText('url', _('URL'), _('URL to organization'), _('http://www.domain.com'), array('((https?):\/\/[^\s]*)?', 0, 50, _('Bad URL')));
$form->addEmail('email', _('Email address'), '');
$form->addTel('tel', _('Telephone'), '');
$form->addText('adr_street-address', _('Street address'), '', _('123 Main st.'), array('.*', 0, 20));
$form->addText('adr_locality', _('City'), '', _('Los Angeles'), array('.*', 0, 20));
$form->addText('adr_region', _('Region'), _('State or province'), _('CA'), array('.*', 0, 20));
$form->addText('adr_postal-code', _('Postal code'), '', _('91316'), array('.*', 0, 20));
$form->addText('adr_country-name', _('Country'), '', _('U.S.A'), array('.*', 0, 20));

$form->addSeparator();

$form->setResponse(_('Saved'), _('Not saved'));

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
