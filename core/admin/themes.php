<?php

Core::addStyle('vendor/dropdown.css');

Hooks::emit('admin-header');
Core::render('admin/themes.tpl');
Hooks::emit('admin-footer');
exit;

?>
