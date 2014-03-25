<?php if (User::loggedIn()) { ?>
<div class="dex api">
	<div class="fatal"></div>
	<div class="status">
		<div class="working"><span></span>&ensp;<i class="fa fa-cog fa-2x fa-spin"></i></div>
		<div class="success"><span></span>&ensp;<i class="fa fa-check fa-2x"></i></div>
		<div class="error"><span></span>&ensp;<i class="fa fa-times fa-2x"></i></div>
	</div>
</div>
<?php } ?>

<?php if (User::getTimeLeft() !== false || Common::requestAdmin()) { ?>
<div class="dex admin-bar">
	<?php if (Db::isValid()) { if (User::getTimeLeft() > 0) { ?>
	<div class="logged-in">
		<div class="navigate">Go<?php if (Common::requestAdmin() && isset($_SESSION['last_site_request']) || !Common::requestAdmin()) { echo ' back'; } ?> to <?php if (Common::requestAdmin()) {
			echo '<a href="/' . $_['base_url'] . (isset($_SESSION['last_site_request']) ? $_SESSION['last_site_request'] : '') . '">Site</a>';
		} else {
			echo '<a href="/' . $_['base_url'] . (isset($_SESSION['last_admin_request']) ? $_SESSION['last_admin_request'] : 'admin/') . '">Admin panel</a>';
		} ?></div>
		<div class="current-user"><?php echo $_['username'] . ' (' . ucfirst($_['role']) . ')'; ?> <a href="#" data-admin="<?php echo (Common::requestAdmin() ? '1' : '0'); ?>" data-tooltip="Logout"><i class="fa fa-fw fa-sign-out"></i></a></div>
	</div>
	<?php } if (!Common::requestAdmin() || User::loggedIn()) { ?>
	<div class="logged-out">
		<div class="navigate">You are logged out, <a href="/<?php echo $_['base_url']; ?>admin/return/">log back in</a></div>
		<?php if (!Common::requestAdmin()) { ?>
		<div class="current-user"><a href="#" data-tooltip="Hide"><i class="fa fa-fw fa-chevron-up"></i></a></div>
		<?php } ?>
	</div>
	<?php } } ?>
</div>
<?php } ?>