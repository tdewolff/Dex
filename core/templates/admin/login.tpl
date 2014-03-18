<h2>Login to admin panel</h2>
<div id="content">
	<a href="/<?php echo $_['base_url']; ?>admin/recover/" class="recover">Recover password</a>
	<?php $_['login']->render(); ?>
</div>

<script>
$(function () {
	$('form:first *:input[type!=hidden]:first').focus();
});
</script>