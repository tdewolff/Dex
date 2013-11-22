<?php

if (!Session::isAdmin())
    user_error('Forbidden access (' . $_SERVER['REQUEST_URI'] . ')', ERROR);

Hooks::emit('admin-header');
Core::render('admin/templates.tpl');
Hooks::emit('admin-footer');
exit;

?>