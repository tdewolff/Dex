<h2><?php echo __('Password recovery'); ?></h2>
<div id="content" class="recovery">
	<a href="/<?php echo $_['base_url']; ?>admin/" class="recover"><?php echo __('Log in'); ?></a>
	<?php if (isset($_['recover'])): ?>
		<?php $_['recover']->render(); ?>
	<?php elseif (isset($_['reset'])): ?>
		<?php $_['reset']->render(); ?>
	<?php elseif (isset($_['sent'])): ?>
		<h3><?php echo __('Email sent'); ?></h3>
		<p><?php echo __('A password recovery email has been sent containing further information on how to reset your password.'); ?></p>
	<?php elseif (isset($_['success'])): ?>
		<h3><?php echo __('Password reset'); ?></h3>
		<p><?php echo __('Your password has successfully been reset, you can now %slog in%s with your new password.', '<a href="/' . $_['base_url'] . 'admin/">', '</a>'); ?></p>
	<?php elseif (isset($_['malformed'])): ?>
		<h3><?php echo __('Bad link'); ?></h3>
		<p><?php echo __('The password reset link is malformed. Did you copy-paste it correctly into your browser?'); ?></p>
		<p><?php echo __('Go to %spassword recovery%s to request a new link.', '<a href="/' . $_['base_url'] . 'admin/recover/">', '</a>'); ?></p>
	<?php elseif (isset($_['expired'])): ?>
		<h3><?php echo __('Link expired'); ?></h3>
		<p><?php echo __('The password reset link has expired, go to %spassword recovery%s to request a new link.', '<a href="/' . $_['base_url'] . 'admin/recover/">', '</a>'); ?></p>
	<?php endif; ?>
</div>