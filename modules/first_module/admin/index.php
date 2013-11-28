<?php
Module::set('first_module');

$form = new Form('first_module');

$form->addSection('Message to the World', '');
$form->addText('message', 'Message', 'Your message to the world', '', array('.*', 0, 50));

$form->setSubmit('<i class="icon-save"></i>&ensp;Save');
$form->setResponse('<span class="passed_time">(saved<span></span>)</span>', '(not saved)');

if ($form->submitted()) {
    if ($form->validate()) {
        $db->exec('
            UPDATE module_first_module
            SET value = "' . $db->escape($form->get('message')) . '"
            WHERE key = "message";');
    }
    $form->finish();
}

$currentMessageQuerry = $db->query('SELECT value FROM module_first_module WHERE key = "message" LIMIT 1');
if ($currentMessageQuerry) {
    if ($row = $currentMessageQuerry->fetch()) {
        $form->set("message", $row['value']);
    }
}

Hooks::emit('admin-header');

Module::assign('first_module', $form);
Module::render('admin/first_module.tpl');

Hooks::emit('admin-footer');
exit;
