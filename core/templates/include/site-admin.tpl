<?php if (User::loggedIn()) { ?>
<div class="dex-api">
	<div class="fatal"></div>
	<div class="status">
		<div class="working"><span></span>&ensp;<i class="fa fa-cog fa-2x fa-spin"></i></div>
		<div class="success"><span></span>&ensp;<i class="fa fa-check fa-2x"></i></div>
		<div class="error"><span></span>&ensp;<i class="fa fa-times fa-2x"></i></div>
	</div>
</div>
<?php } ?>

<?php if (User::getTimeLeft() !== false || Common::requestAdmin()) { ?>
<div class="dex-admin-bar">
	<?php if (Db::isValid()) { if (User::getTimeLeft() > 0) { ?>
	<div class="logged-in">
		<div class="navigate"><span class="mobile-hide"><?php if (Common::requestAdmin() && isset($_SESSION['last_site_request']) || !Common::requestAdmin()) { echo __('Go back to'); } else { echo __('Go to'); } ?>&ensp;</span><?php if (Common::requestAdmin()) {
			echo '<a href="/' . $_['base_url'] . (isset($_SESSION['last_site_request']) ? $_SESSION['last_site_request'] : '') . '">' . __('Site') . '</a>';
		} else {
			echo '<a href="/' . $_['base_url'] . (isset($_SESSION['last_admin_request']) ? $_SESSION['last_admin_request'] : 'admin/') . '">' . __('Admin panel') . '
			</a>';
		} ?></div>
		<?php if (!Common::requestAdmin()) { ?>
		<div class="dexedit-insert"><span><?php echo __('Insert'); ?>:</span>&ensp;<a class="dexedit-menu-link" href="#"><i class="fa fa-fw fa-link"></i></a>&ensp;<a class="dexedit-menu-image" href="#"><i class="fa fa-fw fa-picture-o"></i></a>&ensp;<a class="dexedit-menu-asset" href="#"><i class="fa fa-fw fa fa-download"></i></a></div>
		<?php } ?>
		<div class="current-user"><span class="mobile-hide"><?php echo $_['username'] . ' (' . ucfirst($_['role']) . ')'; ?>&ensp;</span><a href="#" data-admin="<?php echo (Common::requestAdmin() ? '1' : '0'); ?>" data-tooltip="<?php echo __('Log out'); ?>"><i class="fa fa-fw fa-sign-out"></i></a></div>
	</div>
	<?php } if (!Common::requestAdmin() || User::loggedIn()) { ?>
	<div class="logged-out">
		<div class="navigate"><?php echo __('You are logged out, %slog back in%s', '<a href="/' . $_['base_url'] . 'admin/r=' . rawurlencode(rawurlencode(Common::$request_url)) /* twice for Apache bug */ . '/">', '</a>'); ?></div>
		<?php if (!Common::requestAdmin()) { ?>
		<div class="current-user"><a href="#" data-tooltip="<?php echo __('Hide'); ?>"><i class="fa fa-fw fa-chevron-up"></i></a></div>
		<?php } ?>
	</div>
	<?php } else { ?>
	<div class="logged-out">
		<div class="navigate"><span class="mobile-hide"><?php if (Common::requestAdmin() && isset($_SESSION['last_site_request']) || !Common::requestAdmin()) { echo __('Go back to'); } else { echo __('Go to'); } ?>&ensp;</span><?php if (Common::requestAdmin()) {
			echo '<a href="/' . $_['base_url'] . (isset($_SESSION['last_site_request']) ? $_SESSION['last_site_request'] : '') . '">' . __('Site') . '</a>';
		} else {
			echo '<a href="/' . $_['base_url'] . (isset($_SESSION['last_admin_request']) ? $_SESSION['last_admin_request'] : 'admin/') . '">' . __('Admin panel') . '
			</a>';
		} ?></div>
	</div>
	<?php } } ?>
</div>
<?php } ?>