<?php
Module::setModuleName('menu');

Module::addStyle('draggable.css');
Module::addDeferredScript('draggable.js');

Hooks::emit('admin-header');
Module::render('admin/menu.tpl');
Hooks::emit('admin-footer');
exit;
