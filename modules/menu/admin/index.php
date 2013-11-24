<?php
Module::set('menu');

Module::addStyle('draggable.min.css');
Module::addDeferredScript('draggable.min.js');

Hooks::emit('admin-header');
Module::render('admin/menu.tpl');
Hooks::emit('admin-footer');
exit;

?>
