<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title><?php if (isset($_['header_title'])) { echo $_['header_title']; } ?></title>

        <?php if (isset($_['header_style'])): ?><link type="text/css" rel="stylesheet" href="/<?php echo $_['base_url'] . $_['header_style']; ?>"><?php endif; ?>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <?php if (isset($_['api_url'])): ?><script type="text/javascript">
            var apiUrl = '<?php echo $_['api_url']; ?>';
        </script><?php endif; ?>
        <?php if (isset($_['header_script'])): ?><script type="text/javascript" src="/<?php echo $_['base_url'] . $_['header_script']; ?>"></script><?php endif; ?>
    </head>

    <?php ob_flush(); flush(); ?>

    <body>
        <?php if (isset($_['admin_links'])): ?>
        <section class="page-wrapper">
            <h1>Dexterous</h1>
            <div id="user"><?php echo $_['username'] . ' (' . $_['permission'] . ')'; ?></div>
            <div id="left-column">
                <ul>
                    <?php foreach ($_['admin_links'] as $i => $link): ?>
                    <?php if (empty($link)): ?>
                    <li class="separator"></li>
                    <?php elseif ($link['admin_only'] == 0 || ($link['admin_only'] == 1 && $_['is_admin'])): ?>
                    <?php if ($_['is_admin'] || (!$_['is_admin'] && (!isset($link['enabled']) || $link['enabled'] == 1))): ?>
                    <li <?php if (isset($link['enabled']) && $link['enabled'] == 0) { echo 'class="disabled"'; } ?>>
                        <a href="/<?php echo $_['base_url'] . $link['url']; ?>" <?php if (isset($_['current_admin_i']) && $i == $_['current_admin_i']) { echo 'id="selected"'; } ?>>
                            <i class="icon-fixed-width <?php echo (strlen($link['icon']) ? $link['icon'] : 'icon-sign-blank'); ?>"></i>&ensp;<?php echo $link['title']; ?>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div id="right-column">
        <?php else: ?>
        <section class="page-wrapper-slim">
            <h1>Dexterous</h1>
        <?php endif; ?>