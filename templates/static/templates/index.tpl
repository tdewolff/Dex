<div data-dexeditable>
	<?php echo (isset($_['content']) ? $_['content'] : ''); ?>
</div>

<?php if (User::loggedIn()) { ?>
<script>
	$('[data-dexeditable]').on('save', function () {
		if (typeof DexEdit.getContent !== 'undefined') {
			api('/' + base_url + 'api/template/static/index/', {
				action: 'save',
				link_id: link_id,
				content: DexEdit.getContent('div[data-dexeditable]')
			});
		}
	});

	<?php if (isset($_['author'])) { ?>
	var author = '<?php echo (isset($_['author']) ? $_['author'] : ''); ?>';
	var last_save = <?php echo (isset($_['last_save']) ? 'new Date(new Date(0).setUTCSeconds(' . $_['last_save'] . '))' : 'null'); ?>;
	<?php } ?>
</script>
<?php } ?>