<?php

Core::addStyle('vendor/dropdown.css');

Hooks::emit('admin-header');

Core::set('current_theme', $dex_settings['theme']);
Core::render('admin/themes.tpl');

Hooks::emit('admin-footer');
exit;
