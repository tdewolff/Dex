<?php
Module::set('menu');

Module::addStyle('draggable.css');
Module::addDeferredScript('draggable.js');

Hooks::emit('admin_header');
Module::render('admin/menu.tpl');
Hooks::emit('admin_footer');
exit;

?>
