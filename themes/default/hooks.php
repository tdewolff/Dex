<?php

Hooks::attach('site-header', -1, function () {
	Core::addStyle('normalize.css');
	Theme::addStyle('style.css');
});

Hooks::attach('header', 0, function () {
	global $dex_settings;

	if (isset($dex_settings['title']))
		echo '<h1>' . $dex_settings['title'] . '</h1>';
	if (isset($dex_settings['subtitle']) && strlen($dex_settings['subtitle']))
		echo '<h2>' . nl2br($dex_settings['subtitle']) . '</h2>';
});
