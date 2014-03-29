<h2>Pages</h2>
<a href="/<?php echo $_['base_url']; ?>admin/pages/new/" class="button indent"><i class="fa fa-plus"></i>&ensp;New page</a>
<ul id="pages" class="table">
	<li>
		<div></div>
		<div>Title</div>
		<div>Link</div>
		<div>Content</div>
		<div></div>
	</li>
	<li id="load_status" class="api load-status">
		<div class="working"><i class="fa fa-cog fa-spin"></i></div>
		<div class="error"><i class="fa fa-times"></i></div>
		<div class="empty">empty</div>
	</li>
</ul>

<script id="page_item" type="text/x-dot-template">
	<li id="page_{{=it.link_id}}">
		<div>
			<a href="/<?php echo $_['base_url']; ?>{{=it.url}}" class="list-button">
				<i class="fa fa-pencil"></i>&ensp;Edit
			</a>
		</div>
		<div><input name="title" type="text" value="{{=it.title}}" data-link-id="{{=it.link_id}}"></div>
		<div><input name="url" type="text" value="{{=it.url}}" placeholder="(root)" data-link-id="{{=it.link_id}}" data-use-feed="true"></div>
		<div>{{=it.content}}</div>
		<div>
			<a href="#" class="halt inline-rounded"><i class="fa fa-trash-o"></i></a>
			<a href="#" class="sure inline-rounded" data-tooltip="Click to confirm" data-link-id="{{=it.link_id}}"><i class="fa fa-trash-o"></i></a>
		</div>
	</li>
</script>

<script type="text/javascript">
	$(function () {
		var pages = $('#pages');
		var page_item = doT.template($('#page_item').text());

		apiLoadStatusWorking($('#load_status'));
		$('#load_status').show();
		api('/' + base_url + 'api/core/pages/', {
			action: 'get_pages'
		}, function (data) {
			if (!data['pages'].length) {
				apiLoadStatusEmpty($('#load_status'));
				return;
			}

			$('#load_status').hide();
			$.each(data['pages'], function () {
				pages.append(page_item(this));
			});
		}, function () {
			apiLoadStatusError($('#load_status'));
		});

		pages.on('click', 'a.sure', function () {
			apiStatusWorking('Deleting page...');
			var item = $(this);
			api('/' + base_url + 'api/core/pages/', {
				action: 'delete_page',
				link_id: $(this).attr('data-link-id')
			}, function () {
				apiStatusSuccess('Deleted page');
				$('.dropdown-menu').fadeOut('fast');
				$('#page_' + item.attr('data-link-id')).remove();

				if (pages.find('li').length == 2) {
					apiLoadStatusEmpty($('#load_status'));
					$('#load_status').show();
				}
			}, function () {
				apiStatusError('Deleting page failed');
			});
		});

		// TODO: refactor to reuse
		var savePageTimeout = null;
		pages.on('input', 'input', function (e) {
			clearTimeout(savePageTimeout);
			savePageTimeout = setTimeout(savePage, 1000, $(this));
		});

		var savePage = function savePage(element) {
			apiStatusWorking('Saving page...');
			savePageTimeout = null;
			var link_id = element.attr('data-link-id');
			api('/' + base_url + 'api/core/pages/', {
				action: 'edit_page',
				link_id: link_id,
				title: $('#page_' + link_id + ' input[name="title"]').val(),
				url: $('#page_' + link_id + ' input[name="url"]').val()
			}, function () {
				apiStatusSuccess('Saved page');
			}, function () {
				apiStatusError('Saving page failed');
			});
		}

		pages.on('keyup', 'input[name="title"]', function () {
			var link_id = $(this).attr('data-link-id');
			if ($('#page_' + link_id + ' input[name="url"]').attr('data-use-feed') == 'true') {
				var link_url = $(this).val().toLowerCase().replace(/\s/, '-').replace(/[^a-z0-9\-_]+/, '');
				$('#page_' + link_id + ' input[name="url"]').val(link_url + '/');
				$('#page_' + $(this).attr('data-link-id') + ' > div:first > a').attr('href', '/' + base_url + link_url + '/');
			}
		});

		pages.on('keyup', 'input[name="url"]', function () {
			$(this).attr('data-use-feed', 'false');
			var link_url = $(this).val().replace(/\s/, '-').replace(/[^a-z0-9\-_\/]+/, '');
			$(this).val(link_url);
			$('#page_' + $(this).attr('data-link-id') + ' > div:first > a').attr('href', '/' + base_url + link_url);
		});

		pages.on('blur', 'input[name="url"]', function () {
			var link_url = $(this).val();
			if (link_url.length && link_url[link_url.length - 1] != '/') {
				$(this).val(link_url + '/');
			}
		});
	});
</script>
