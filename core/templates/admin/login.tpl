<h2><?php echo __('Log in'); ?></h2>
<div id="content">
	<a href="/<?php echo $_['base_url']; ?>admin/recover/" class="recover"><?php echo __('Recover password'); ?></a>
	<?php $_['login']->render(); ?>
</div>

<script>
$(function () {
	$('form:first *:input[type!=hidden]:first').focus();
});
</script>