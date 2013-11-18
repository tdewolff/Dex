<?php

Core::addStyle('vendor/popbox.css');
Core::addStyle('vendor/dropdown.css');

Hooks::emit('admin_header');
Core::render('admin/themes.tpl');
Hooks::emit('admin_footer');
exit;

?>
