<h2><?php echo _('Log in'); ?></h2>
<div id="content">
	<a href="/<?php echo $_['base_url']; ?>admin/recover/" class="recover"><?php echo _('Recover password'); ?></a>
	<?php $_['login']->render(); ?>
</div>

<script>
$(function () {
	$('form:first *:input[type!=hidden]:first').focus();
});
</script>