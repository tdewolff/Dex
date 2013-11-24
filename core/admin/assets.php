<?php

Core::addDeferredScript('vendor/jquery.ui.widget.min.js');
Core::addDeferredScript('vendor/jquery.iframe-transport.min.js');
Core::addDeferredScript('vendor/jquery.fileupload.min.js');
Core::addDeferredScript('vendor/jquery.knob.min.js');
Core::addDeferredScript('upload.min.js');

Hooks::emit('admin-header');
Core::render('admin/assets.tpl');
Hooks::emit('admin-footer');
exit;

?>
