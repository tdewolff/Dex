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
		<?php foreach ($_['header_external_scripts'] as $external_script): ?><script src="<?php echo $external_script; ?>"></script><?php endforeach; ?>
		<?php if (isset($_['header_script'])): ?><script src="/<?php echo $_['base_url'] . $_['header_script']; ?>"></script><?php endif; ?>
		<script>
			var base_url = '<?php echo $_['base_url']; ?>';
			var session_time = '<?php echo $_['session_time']; ?>';
		</script>
	</head>

	<?php ob_flush(); flush(); ?>
<?php endif; ?>

	<body>
		<?php include(dirname($_SERVER['SCRIPT_FILENAME']) . '/core/templates/include/site-admin.tpl'); // absolute path needed for register_shutdown_function() ?>

		<?php if (isset($_['admin_links'])): ?>
		<section class="page-wrapper">
			<h1>Dex</h1>
			<div id="left-column">
				<ul>
					<?php foreach ($_['admin_links'] as $i => $link): ?>
					<?php if (empty($link)): ?>
					<li class="separator"></li>
					<?php elseif ($link['admin_only'] == 0 || $_['role'] == 'admin'): ?>
					<li id="admin_link_<?php echo $link['name']; ?>" <?php if (isset($link['enabled']) && $link['enabled'] == 0) { echo 'class="hidden"'; } ?>>
						<a href="/<?php echo $_['base_url'] . $link['url']; ?>" <?php if (isset($_['current_admin_i']) && $i == $_['current_admin_i']) { echo 'class="selected"'; } ?>>
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
			<h1>Dex</h1>
			<div id="main">
		<?php endif; ?>