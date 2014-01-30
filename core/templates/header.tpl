<?php if (!headers_sent()): ?>
<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php if (isset($_['setting_description'])): ?><meta name="description" content="<?php echo $_['setting_description']; ?>"><?php endif; ?>
        <?php if (isset($_['setting_keywords'])): ?><meta name="keywords" content="<?php echo implode(',', json_decode($_['setting_keywords'])); ?>"><?php endif; ?>

        <title><?php if (isset($_['header_title'])) { echo $_['header_title']; } ?></title>

        <?php foreach ($_['header_external_styles'] as $external_style): ?><link rel="stylesheet" href="<?php echo $external_style; ?>"><?php endforeach; ?>
        <?php if (isset($_['header_style'])): ?><link rel="stylesheet" href="/<?php echo $_['base_url'] . $_['header_style']; ?>"><?php endif; ?>
        <link rel="shortcut icon" type="image/x-icon" href="/<?php echo $_['base_url']; ?>favicon.ico">
        <?php foreach ($_['header_external_scripts'] as $external_script): ?><script src="<?php echo $external_script; ?>"></script><?php endforeach; ?>
        <?php if (isset($_['header_script'])): ?><script src="/<?php echo $_['base_url'] . $_['header_script']; ?>"></script><?php endif; ?>

        <?php if (User::getTimeLeft() !== false) { ?>
        <script>
            var base_url = '<?php echo $_['base_url']; ?>';
            var link_id = '<?php echo $_['link_id']; ?>';
        </script>
        <?php } ?>
    </head>

    <?php ob_flush(); flush(); ?>
<?php endif; ?>

    <body>
        <?php if (User::loggedIn()) { ?>
        <div id="api_fatal"></div>
        <div id="api_status">
            <div class="working"><span></span>&ensp;<i class="fa fa-cog fa-2x fa-spin"></i></div>
            <div class="success"><span></span>&ensp;<i class="fa fa-check fa-2x"></i></div>
            <div class="error"><span></span>&ensp;<i class="fa fa-times fa-2x"></i></div>
        </div>
        <?php } ?>

        <?php if (User::getTimeLeft() !== false) { ?>
        <div id="admin-bar">
            <?php if (User::getTimeLeft() > 0) { ?>
            <a href="#" id="publish-site" data-tooltip="Publish and optimize the content of the site" data-action="publish_site"><i class="fa fa-fw fa-magic"></i>&ensp;Publish site</a>
            <div id="navigate"><?php if (Common::requestAdmin()) { echo '<a href="/' . $_['base_url'] . '">Site</a>'; } else { echo '<a href="/' . $_['base_url'] . 'admin/">Admin panel</a>'; } ?></div>
            <div id="current-user"><?php echo $_['username'] . ' (' . ucfirst($_['role']) . ')'; ?> <a id="log-out" href="#" data-tooltip="Logout"><i class="fa fa-fw fa-sign-out"></i></a></div>

            <?php } else { ?>
            <div id="navigate">You are logged out, <a href="/<?php echo $_['base_url']; ?>admin/">log back in</a></div>
            <div id="current-user"><a id="log-out" href="#" data-tooltip="Hide"><i class="fa fa-fw fa-chevron-up"></i></a></div>
            <?php } ?>
        </div>
        <?php } ?>
