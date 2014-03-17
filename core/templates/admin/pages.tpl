<h2>Pages</h2>
<a href="/<?php echo $_['base_url']; ?>admin/pages/new/" class="button indent"><i class="fa fa-plus"></i>&ensp;New page</a>
<ul id="pages" class="table">
	<li>
		<div style="width:80px;"></div>
		<div style="width:200px;">Title</div>
		<div style="width:200px;">Link</div>
		<div style="width:380px;">Content</div>
		<div style="width:40px;"></div>
	</li>
	<li id="load_status" class="api load-status">
		<div class="working"><i class="fa fa-cog fa-spin"></i></div>
		<div class="error"><i class="fa fa-times"></i></div>
		<div class="empty">empty</div>
	</li>
</ul>

<script id="page_item" type="text/x-dot-template">
	<li id="page_{{=it.link_id}}">
		<div style="width:80px;">
			<a href="/<?php echo $_['base_url']; ?>{{=it.url}}" class="list-button">
				<i class="fa fa-pencil"></i>&ensp;Edit
			</a>
		</div>
		<div style="width:200px;"><input name="title" type="text" value="{{=it.title}}" data-link-id="{{=it.link_id}}"></div>
		<div style="width:200px;"><input name="link" type="text" value="{{=it.url}}" data-link-id="{{=it.link_id}}"></div>
		<div style="width:380px;">{{=it.content}}</div>
		<div style="width:40px;">
			<a href="#" class="halt inline-rounded"><i class="fa fa-trash-o"></i></a>
			<a href="#" class="sure inline-rounded" data-tooltip="Click to confirm" data-link-id="{{=it.link_id}}"><i class="fa fa-trash-o"></i></a>
		</div>
	</li>
</script>

<script type="text/javascript">
	$(function() {
		var pages = $('#pages');
		var page_item = doT.template($('#page_item').text());
		apiLoadStatusWorking($('#load_status'));
		api('/' + base_url + 'api/core/pages/', {
			action: 'get_pages'
		}, function(data) {
			if (!data['pages'].length) {
				apiLoadStatusEmpty($('#load_status'));
				return;
			}

			apiLoadStatusSuccess($('#load_status'));
			$.each(data['pages'], function() {
				pages.append(page_item(this));
			});
		}, function() {
			apiLoadStatusError($('#load_status'));
		});

		pages.on('click', 'a.sure', function() {
			apiStatusWorking('Deleting page...');
			var item = $(this);
			api('/' + base_url + 'api/core/pages/', {
				action: 'delete_page',
				link_id: $(this).attr('data-link-id')
			}, function() {
				apiStatusSuccess('Deleted page');
				$('.dropdown-menu').fadeOut('fast');
				$('#page_' + item.attr('data-link-id')).remove();
			}, function() {
				apiStatusError('Deleting page failed');
			});
		});

		// TODO: refactor to reuse
		var savePageTimeout = null;
		pages.on('input', 'input', function(e) {
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
	});
</script>
