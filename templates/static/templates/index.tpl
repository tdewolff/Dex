<article role="main" class="main" data-dexeditable>
	<?php echo (isset($_['content']) ? $_['content'] : ''); ?>
</article>

<?php if (User::loggedIn()) { ?>
<script>
	$(document).on('save', function () {
		api('/' + base_url + 'api/template/static/index/', {
			action: 'save',
			link_id: link_id,
			content: $('article.main').html()
		});
	});
</script>
<?php } ?>