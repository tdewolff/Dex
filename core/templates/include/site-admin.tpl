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

<?php if (User::getTimeLeft() !== false) { ?>
<div class="dex admin-bar">
	<?php if (User::getTimeLeft() > 0) { ?>
	<div class="logged-in">
		<div class="navigate">Go to <?php if (Common::requestAdmin()) { echo '<a href="/' . $_['base_url'] . '">Site</a>'; } else { echo '<a href="/' . $_['base_url'] . 'admin/">Admin panel</a>'; } ?></div>
		<div class="current-user"><?php echo $_['username'] . ' (' . ucfirst($_['role']) . ')'; ?> <a href="<?php echo (Common::requestAdmin() ? '/' . $_['base_url'] . 'admin/' : '#'); ?>" data-tooltip="Logout"><i class="fa fa-fw fa-sign-out"></i></a></div>
	</div>
	<?php } ?>
	<div class="logged-out">
		<div class="navigate">You are logged out, <a href="/<?php echo $_['base_url']; ?>admin/">log back in</a></div>
		<?php if (!Common::requestAdmin()) { ?>
		<div class="current-user"><a href="#" data-tooltip="Hide"><i class="fa fa-fw fa-chevron-up"></i></a></div>
		<?php } ?>
	</div>
</div>
<?php } ?>