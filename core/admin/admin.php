<?php

Hooks::emit('admin-header');

Core::render('admin/admin.tpl');

Hooks::emit('admin-footer');
exit;

?>
