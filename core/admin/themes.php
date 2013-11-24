<?php

Core::addStyle('vendor/popbox.min.css');
Core::addStyle('vendor/dropdown.min.css');

Hooks::emit('admin-header');
Core::render('admin/themes.tpl');
Hooks::emit('admin-footer');
exit;

?>
