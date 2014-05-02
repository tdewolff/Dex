<?php if (!headers_sent()): ?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,initial-scale=1">
		<?php if (isset($_['header_description'])): ?><meta name="description" content="<?php echo htmlspecialchars($_['header_description']); ?>"><?php endif; ?>
		<?php if (isset($_['header_keywords'])): ?><meta name="keywords" content="<?php echo htmlspecialchars($_['header_keywords']); ?>"><?php endif; ?>

		<title><?php if (isset($_['header_title'])) { echo htmlspecialchars($_['header_title']); } ?></title>

		<?php foreach ($_['header_external_styles'] as $external_style): ?><link rel="stylesheet" href="<?php echo $external_style; ?>"><?php endforeach; ?>
		<?php if (isset($_['header_style'])): ?><link rel="stylesheet" href="/<?php echo $_['base_url'] . $_['header_style']; ?>"><?php endif; ?>
		<link rel="shortcut icon" type="image/x-icon" href="/<?php echo $_['base_url']; ?>favicon.ico">
		<?php foreach ($_['header_external_scripts'] as $external_script): ?><script src="<?php echo $external_script; ?>"></script><?php endforeach; ?>
		<?php if (isset($_['header_script'])): ?><script src="/<?php echo $_['base_url'] . $_['header_script']; ?>"></script><?php endif; ?>

		<?php if (User::getTimeLeft() !== false) { ?>
		<script>
			var base_url = '<?php echo $_['base_url']; ?>';
			var link_id = '<?php echo (isset($_['link_id']) ? $_['link_id'] : 0); ?>';
			var session_time = '<?php echo $_['session_time']; ?>';
		</script>
		<?php } ?>
	</head>

	<?php ob_flush(); flush(); ?>
<?php endif; ?>

	<body>
		<?php include(dirname($_SERVER['SCRIPT_FILENAME']) . '/core/templates/include/site-admin.tpl'); // absolute path needed for register_shutdown_function() ?>