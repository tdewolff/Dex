<?php if (User::getTimeLeft() !== false) { ?>

	<div id="edit"><a href="#edit"><i class="fa fa-fw fa-edit"></i>&ensp;Edit</a></div>
	<div id="save"><a href="#"><i class="fa fa-fw fa-save"></i>&ensp;Save</a></div>

<?php } ?>

<article role="main" class="main">
	<?php echo (isset($_['content']) ? $_['content'] : ''); ?>
</article>

<?php if (User::loggedIn()) { ?>
<script>
	/*
$(function() {
	$('#edit').on('click', 'a', function() {
		$('article').attr('contenteditable', 'true');
		grande.bind(document.querySelectorAll("article"));
		initializeUpload('[contenteditable="true"]');
	});

	$('#save').on('click', 'a', function() {
		apiStatusWorking('Saving page...');
		var item = $(this);
		api('/' + base_url + 'api/template/static/index/', {
			action: 'save_page',
			link_id: link_id,
			content: $('article').html()
		}, function() {
			apiStatusSuccess('Saved page <span data-time></span>');
		}, function() {
			apiStatusError('Saving page failed');
		});
	});

	$('article').on('keydown', function() {
		apiStatusClear();
	});

	$('#log-out').click(function() {
		$('article').attr('contenteditable', 'false');
	});
});
*/
</script>
<?php } ?>
