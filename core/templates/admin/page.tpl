<a href="/<?php echo $_['base_url']; ?>admin/pages/" class="button left"><i class="fa fa-chevron-left"></i>&ensp;Back</a>
<?php if (isset($_['view'])): ?><a href="/<?php echo $_['base_url'] . $_['view']; ?>" class="button right"><i class="fa fa-eye"></i>&ensp;View page</a><?php endif; ?>
<h2>Page</h2>
<?php $_['page']->render(); ?>

<script type="text/javascript">
	$(function () {
		var use_feed = true;
		$('#url-feed').on('keyup', function () {
			if (use_feed) {
				var link_url = titleToUrl($(this).val());
				$('#url').val(link_url);
			}
		});

		$('#url').on('keyup', function () {
			use_feed = false;
		});

		$('#url').on('blur', function () {
			var link_url = $(this).val();
			if (link_url.length && link_url[link_url.length - 1] != '/') {
				$(this).val(link_url + '/');
			}
		});
	});
</script>
