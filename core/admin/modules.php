<?php

if (!Session::isAdmin())
    user_error('Forbidden access (' . $_SERVER['REQUEST_URI'] . ')', ERROR);

Core::addStyle('vendor/popbox.min.css');
Core::addStyle('vendor/dropdown.min.css');

Hooks::emit('admin-header');
Core::render('admin/modules.tpl');
Hooks::emit('admin-footer');
exit;

?>