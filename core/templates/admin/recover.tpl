<h2>Password recovery</h2>
<div id="content" class="recovery">
	<a href="/<?php echo $_['base_url']; ?>admin/" class="recover">Login</a>
	<?php if (isset($_['recover'])): ?>
		<?php $_['recover']->render(); ?>
	<?php elseif (isset($_['reset'])): ?>
		<?php $_['reset']->render(); ?>
	<?php elseif (isset($_['sent'])): ?>
		<h3>Email sent</h3>
		<p>The password recovery email has been sent containing information on how to reset your password. You can go back to the <a href="/<?php echo $_['base_url']; ?>admin/">login</a> page.</p>
	<?php elseif (isset($_['success'])): ?>
		<h3>Password reset</h3>
		<p>Your password has successfully been reset, you can now <a href="/<?php echo $_['base_url']; ?>admin/">login</a> with your new password.</p>
	<?php elseif (isset($_['malformed'])): ?>
		<h3>Bad link</h3>
		<p>The password reset link is malformed. Did you copy-paste it correctly into your browser?</p>
		<p>Go to <a href="/<?php echo $_['base_url']; ?>admin/recover/">password recovery</a> to request a new link.</p>
	<?php elseif (isset($_['expired'])): ?>
		<h3>Link expired</h3>
		<p>The password reset link has expired, go to <a href="/<?php echo $_['base_url']; ?>admin/recover/">password recovery</a> to request a new link.</p>
	<?php endif; ?>
</div>