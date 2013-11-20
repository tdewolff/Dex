<?php

if (!Session::isAdmin())
    user_error('Forbidden access (' . $_SERVER['REQUEST_URI'] . ')', ERROR);

Hooks::emit('admin_header');
Core::render('admin/popup/markdown_link.tpl');
Hooks::emit('admin_footer');
exit;

?>