<?php if (!headers_sent()): ?>
<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title><?php if (isset($_['header_title'])) { echo $_['header_title']; } ?></title>

        <?php foreach ($_['header_external_styles'] as $external_style): ?><link rel="stylesheet" href="<?php echo $external_style; ?>"><?php endforeach; ?>
        <?php if (isset($_['header_style'])): ?><link rel="stylesheet" href="/<?php echo $_['base_url'] . $_['header_style']; ?>"><?php endif; ?>
        <link rel="shortcut icon" type="image/x-icon" href="/<?php echo $_['base_url']; ?>favicon.ico">
        <?php foreach ($_['header_external_scripts'] as $external_script): ?><script type="text/javascript" src="<?php echo $external_script; ?>"></script><?php endforeach; ?>
        <?php if (isset($_['header_script'])): ?><script type="text/javascript" src="/<?php echo $_['base_url'] . $_['header_script']; ?>"></script><?php endif; ?>
        <script type="text/javascript">
            var base_url = '<?php echo $_['base_url']; ?>';
        </script>
    </head>

    <?php ob_flush(); flush(); ?>
<?php endif; ?>

    <body>
        <div id="api_fatal"></div>
        <div id="api_status">
            <div class="working"><span></span>&ensp;<i class="fa fa-cog fa-2x fa-spin"></i></div>
            <div class="success"><span></span>&ensp;<i class="fa fa-check fa-2x"></i></div>
            <div class="error"><span></span>&ensp;<i class="fa fa-times fa-2x"></i></div>
        </div>

        <?php if (isset($_['admin_links'])): ?>
        <section class="page-wrapper">
            <h1>Dexterous</h1>
            <div id="current-user"><?php echo $_['username'] . ' (' . $_['permission'] . ')'; ?></div>
            <div id="left-column">
                <ul>
                    <?php foreach ($_['admin_links'] as $i => $link): ?>
                    <?php if (empty($link)): ?>
                    <li class="separator"></li>
                    <?php elseif ($link['admin_only'] == 0 || $_['is_admin']): ?>
                    <li id="admin_link_<?php echo $link['name']; ?>" <?php if (isset($link['enabled']) && $link['enabled'] == 0) { echo 'class="hidden"'; } ?>>
                        <a href="/<?php echo $_['base_url'] . $link['url']; ?>" <?php if (isset($_['current_admin_i']) && $i == $_['current_admin_i']) { echo 'id="selected"'; } ?>>
                            <i class="fa fa-fw <?php echo (strlen($link['icon']) ? $link['icon'] : 'fa-sign-blank'); ?>"></i>&ensp;<?php echo $link['title']; ?>
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
            <div id="main">
        <?php endif; ?>