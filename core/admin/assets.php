<?php

Hooks::emit('admin-header');
Core::render('admin/assets.tpl');
Hooks::emit('admin-footer');
exit;
