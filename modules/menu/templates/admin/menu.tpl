<h2><?php echo _('Menu'); ?></h2>
<ul id="menu" class="table draggable">
	<li>
		<div></div>
		<div></div>
		<div><?php echo _('Name'); ?></div>
		<div><?php echo _('Page title'); ?></div>
		<div><?php echo _('Link'); ?></div>
	</li>
</ul>

<script id="menu_item" type="text/x-dot-template">
	<li id="menu_item_{{=it.link_id}}" data-link-id="{{=it.link_id}}" data-level="{{=it.level}}"{{?!it.enabled}} class="unused"{{?}}>
		<div><i class="fa fa-eye"></i></div>
		<div><i class="fa fa-long-arrow-right"></i><i class="fa fa-long-arrow-right"></i><i class="fa fa-bars"></i></div>
		<div><input type="text" value="{{=it.name}}" maxlength="25" data-error-position="right"{{?!it.enabled}} class="unused"{{?}}></div>
		<div>{{=it.title}}</div>
		<div><a href="/<?php echo $_['base_url']; ?>{{=it.url}}">{{?it.url != ''}}{{=it.url}}{{??}}<em>(<?php echo _('home'); ?>)</em>{{?}}</a></div>
	</li>
</script>

<script>
	$(function () {
		var menu = $('#menu');
		var menu_item = doT.template($('#menu_item').text());
		api('/' + base_url + 'api/module/menu/index/', {
			action: 'get_menu'
		}, function (data) {
			$.each(data['menu'], function () {
				var item = $(menu_item(this));
				item.find('.fa-long-arrow-right').hide();

				var level = item.attr('data-level');
				for (var i = 0; i < level; i++) {
					item.find('.fa-long-arrow-right').eq(i).show().css('display', 'inline-block');
				}
				menu.append(item);
			});
		});

		menu.on('save', function () {
			apiStatusWorking('<?php echo _('Saving...'); ?>');

			hideInlineFormErrors(menu);

			var i = 0;
			var data = {};
			var elements = $('li:not(:first)', menu).filter(function () {
				return !$(this).hasClass('placeholder');
			}).each(function () {
				var element = $(this);
				data[i] = {
					link_id: element.attr('data-link-id'),
					level: element.attr('data-level'),
					name: element.find('input').val(),
					enabled: (element.hasClass('unused') ? '0' : '1')
				};
				i++;
			});

			api('/' + base_url + 'api/module/menu/index/', {
				action: 'modify_menu',
				menu: data
			}, function (data) {
				if (data['errors'].length) {
					apiStatusError('<?php echo _('Saving failed'); ?>');

					for (var i = 0; i < data['errors'].length; i++) {
						console.log(data['errors'][i]);
						var li = $('#menu_item_' + data['errors'][i]['link_id']);
						inlineFormError(li.find('input'), data['errors'][i]['error']);
					}
				} else {
					apiStatusSuccess('<?php echo _('Saved'); ?>');
				}
			}, function () {
				apiStatusError('<?php echo _('Saving failed'); ?>');
			});
		});
	});
</script>