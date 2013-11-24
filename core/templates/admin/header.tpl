<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title><?php if (isset($_['header_title'])) { echo $_['header_title']; } ?></title>

        <?php foreach ($_['header_external_styles'] as $external_style): ?><link rel="stylesheet" href="<?php echo $external_style; ?>"><?php endforeach; ?>
        <?php if (isset($_['header_style'])): ?><link rel="stylesheet" href="/<?php echo $_['base_url'] . $_['header_style']; ?>"><?php endif; ?>
        <?php foreach ($_['header_external_scripts'] as $external_script): ?><script type="text/javascript" src="<?php echo $external_script; ?>"></script><?php endforeach; ?>
        <?php if (isset($_['header_script'])): ?><script type="text/javascript" src="/<?php echo $_['base_url'] . $_['header_script']; ?>"></script><?php endif; ?>
        <script type="text/javascript">
            var base_url = '<?php echo $_['base_url']; ?>';
        </script>
    </head>

    <?php ob_flush(); flush(); ?>

    <body>
        <a href="#api_error" id="api_error_link" class="fancybox"></a>
        <div id="api_error"></div>

        <?php if (isset($_['admin_links'])): ?>
        <section class="page-wrapper">
            <h1>Dexterous</h1>
            <div id="user"><?php echo $_['username'] . ' (' . $_['permission'] . ')'; ?></div>
            <div id="left-column">
                <ul>
                    <?php foreach ($_['admin_links'] as $i => $link): ?>
                    <?php if (empty($link)): ?>
                    <li class="separator"></li>
                    <?php elseif ($link['admin_only'] == 0 || $_['is_admin']): ?>
                    <li id="admin_link_<?php echo $link['name']; ?>" <?php if (isset($link['enabled']) && $link['enabled'] == 0) { echo 'class="hidden"'; } ?>>
                        <a href="/<?php echo $_['base_url'] . $link['url']; ?>" <?php if (isset($_['current_admin_i']) && $i == $_['current_admin_i']) { echo 'id="selected"'; } ?>>
                            <i class="icon-fixed-width <?php echo (strlen($link['icon']) ? $link['icon'] : 'icon-sign-blank'); ?>"></i>&ensp;<?php echo $link['title']; ?>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div id="right-column">
        <?php else: ?>
        <section class="page-wrapper-slim">
            <h1>Dexterous</h1>
        <?php endif; ?>