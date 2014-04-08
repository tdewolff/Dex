<h2>Pages</h2>
<a href="/<?php echo $_['base_url']; ?>admin/pages/new/" class="button indent"><i class="fa fa-plus"></i>&ensp;New page</a>
<ul id="pages" class="table">
	<li>
		<div></div>
		<div></div>
		<div>Title</div>
		<div>Link</div>
		<div>Content</div>
		<div></div>
	</li>
	<li id="load_status" class="dex-api load-status">
		<div class="working"><i class="fa fa-cog fa-spin"></i></div>
		<div class="error"><i class="fa fa-times"></i></div>
		<div class="empty">empty</div>
	</li>
</ul>

<script id="page_item" type="text/x-dot-template">
	<li id="page_{{=it.link_id}}" {{?it.url==''}}class="home"{{?}}>
		<div>
			<a href="/<?php echo $_['base_url']; ?>{{=it.url}}" class="list-button">
				<i class="fa fa-pencil"></i>&ensp;Edit
			</a>
		</div>
		<div><i class="fa fa-home"></i></div>
		<div><input name="title" type="text" value="{{=it.title}}" data-link-id="{{=it.link_id}}"></div>
		<div>
			<input name="url" type="text" value="{{=it.url}}" placeholder="(home)" data-link-id="{{=it.link_id}}" data-use-feed="{{?it.url=='' || it.url !== titleToUrl(it.title)}}false{{??}}true{{?}}"{{?it.url==''}} disabled{{?}}>
			<div class="input-error-right">
				<div class="box">
					<div class="arrow"></div>
					<div class="arrow-border"></div>
					<p>
						<i class="fa fa-exclamation-circle"></i>&ensp;<span></span>
					</p>
				</div>
			</div>
		</div>
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

			apiLoadStatusSuccess($('#load_status'));
			var items = '';
			$.each(data['pages'], function () {
				items += page_item(this);
			});
			pages.append(items);
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
				$('.dropdown-menu').fadeOut(100);
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
		var savePagesTimeout = null;
		pages.on('input', 'input', function (e) {
			clearTimeout(savePagesTimeout);
			savePagesTimeout = setTimeout(savePages, 1000, $(this));
		});

		var savePages = function savePages(element) {
			apiStatusWorking('Saving pages...');
			savePagesTimeout = null;

			pages.find('div.input-error-right').hide();

			var data = [];
			pages.find('li').each(function (i) {
				if (i > 0) {
					var li = $(this);
					data.push({
						link_id: li.find('input[name="title"]').attr('data-link-id'),
						title: li.find('input[name="title"]').val(),
						url: li.find('input[name="url"]').val()
					});
				}
			});

			console.log(data);

			api('/' + base_url + 'api/core/pages/', {
				action: 'edit_pages',
				pages: data
			}, function (data) {
				if (data['errors'].length) {
					apiStatusError('Saving pages failed');

					for (var i = 0; i < data['errors'].length; i++) {
						var li = $('#page_' + data['errors'][i]['link_id']);
						if (!li.hasClass('home')) {
							li.find('input[name="url"]').addClass('invalid');

							var error_box = li.find('div.input-error-right');
							if (error_box.find('span').text() != data['errors'][i]['error']) {
								error_box.hide();
								error_box.find('span').text(data['errors'][i]['error']);
							}
							error_box.fadeIn();
						}
					}
				} else {
					apiStatusSuccess('Saved pages');
				}
			}, function (error) {
				apiStatusError('Saving pages failed');
			});
		}

		pages.on('mousedown', 'li > div:nth-child(2)', function (e) {
			e.preventDefault();

			if (e.which === 1) {
				var li = $(this).closest('li');
				if (!li.hasClass('home')) {
					var old_li = pages.find('li.home').removeClass('home');
					if (old_li.length) {
						old_li.find('input[name="url"]').val(titleToUrl(old_li.find('input[name="title"]').val())).prop('disabled', false).attr('data-use-feed', 'true');
					}

					li.addClass('home');
					li.find('input[name="url"]').val('').prop('disabled', true).attr('data-use-feed', 'false').trigger('input');
				}
			}
		});

		pages.on('keyup', 'input[name="title"]', function () {
			var link_id = $(this).attr('data-link-id');
			if ($('#page_' + link_id + ' input[name="url"]').attr('data-use-feed') == 'true') {
				var link_url = titleToUrl($(this).val());
				$('#page_' + link_id + ' input[name="url"]').val(link_url);
				$('#page_' + $(this).attr('data-link-id') + ' > div:first > a').attr('href', '/' + base_url + link_url);
			}
		});

		pages.on('keyup', 'input[name="url"]', function () {
			var input = $(this);
			input.attr('data-use-feed', 'false');
			$('#page_' + input.attr('data-link-id') + ' > div:first > a').attr('href', '/' + base_url + input.val());
		});

		pages.on('blur', 'input[name="url"]', function () {
			var link_url = $(this).val();
			if (link_url.length && link_url[link_url.length - 1] != '/') {
				$(this).val(link_url + '/');
			}
		});
	});
</script>