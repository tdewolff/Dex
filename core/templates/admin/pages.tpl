<h2><?php echo __('Pages'); ?></h2>
<a href="/<?php echo $_['base_url']; ?>admin/pages/new/" class="button indent"><i class="fa fa-plus"></i>&ensp;<?php echo __('New page'); ?></a>
<ul id="pages" class="table">
	<li>
		<div></div>
		<div></div>
		<div><?php echo __('Title'); ?></div>
		<div><?php echo __('Link'); ?></div>
		<div><?php echo __('Content'); ?></div>
		<div></div>
	</li>
	<li id="load_status" class="dex-api load-status">
		<div class="working"><i class="fa fa-cog fa-spin"></i></div>
		<div class="error"><i class="fa fa-times"></i></div>
		<div class="empty"><?php echo __('empty'); ?></div>
	</li>
</ul>

<script id="page_item" type="text/x-dot-template">
	<li id="page_{{=it.link_id}}" {{?it.url==''}}class="home"{{?}}>
		<div>
			<a href="/<?php echo $_['base_url']; ?>{{=it.url}}" class="list-button">
				<i class="fa fa-pencil"></i>&ensp;<?php echo __('Edit'); ?>
			</a>
		</div>
		<div><i class="fa fa-home"></i></div>
		<div><input name="title" type="text" value="{{=it.title}}" maxlength="25" data-link-id="{{=it.link_id}}" data-error-position="left"></div>
		<div><input name="url" type="text" value="{{=it.url}}" placeholder="(home)" maxlength="50" data-link-id="{{=it.link_id}}" data-use-feed="{{?it.url=='' || it.url !== titleToUrl(it.title)}}false{{??}}true{{?}}" data-error-position="right"{{?it.url==''}} disabled{{?}}></div>
		<div>{{=it.content}}</div>
		<div>
			<a href="#" class="halt inline-rounded"><i class="fa fa-trash-o"></i></a>
			<a href="#" class="sure inline-rounded" data-tooltip="<?php echo __('Click to confirm'); ?>" data-link-id="{{=it.link_id}}"><i class="fa fa-trash-o"></i></a>
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
			var items = '';
			$.each(data['pages'], function () {
				items += page_item(this);
			});
			pages.append(items);
		}, function () {
			apiLoadStatusError($('#load_status'));
		});

		pages.on('click', 'a.sure', function () {
			apiStatusWorking('<?php echo __('Deleting page...'); ?>');
			var item = $(this);
			api('/' + base_url + 'api/core/pages/', {
				action: 'delete_page',
				link_id: $(this).attr('data-link-id')
			}, function () {
				apiStatusSuccess('<?php echo __('Deleted page'); ?>');
				$('.dropdown-menu').fadeOut(100);
				$('#page_' + item.attr('data-link-id')).remove();

				if (pages.find('li').length == 2) {
					apiLoadStatusEmpty($('#load_status'));
					$('#load_status').show();
				}
			}, function () {
				apiStatusError('<?php echo __('Deleting page failed'); ?>');
			});
		});

		new Save(pages[0]);
		pages.on('save', function () {
			apiStatusWorking('<?php echo __('Saving...'); ?>');

			hideInlineFormErrors(pages);

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

			api('/' + base_url + 'api/core/pages/', {
				action: 'edit_pages',
				pages: data
			}, function (data) {
				if (data['errors'].length) {
					apiStatusError('<?php echo __('Saving failed'); ?>');

					for (var i = 0; i < data['errors'].length; i++) {
						var li = $('#page_' + data['errors'][i]['link_id']);
						if (data['errors'][i]['name'] != 'url' || !li.hasClass('home')) {
							inlineFormError(li.find('input[name="' + data['errors'][i]['name'] + '"]'), data['errors'][i]['error']);
						}
					}
				} else {
					apiStatusSuccess('<?php echo __('Saved'); ?>');
				}
			}, function (error) {
				apiStatusError('<?php echo __('Saving failed'); ?>');
			});
		});

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