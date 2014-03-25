<article role="main" class="main" data-dexeditable>
	<?php echo (isset($_['content']) ? $_['content'] : ''); ?>
</article>

<?php if (User::loggedIn()) { ?>
<script>
	$(document).on('save', function () {
		api('/' + base_url + 'api/template/static/index/', {
			action: 'save',
			link_id: link_id,
			content: getDexEditContent('article.main')
		});
	});

	<?php if (isset($_['time'])) { ?>
	$(function () {
		var time = new Date(new Date(0).setUTCSeconds(<?php echo $_['time']; ?>));
		apiStatusSuccess('<?php echo (isset($_['author']) ? $_['author'] : ''); ?> (' + time.getDate() + '-' + padZero(time.getMonth() + 1) + ' ' + time.getHours() + ':' + padZero(time.getMinutes()) + ')');
	});
	<?php } ?>
</script>
<?php } ?>