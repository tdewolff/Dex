<h2>Themes</h2>
<ul id="themes" class="grid">
	<li id="load_status" class="dex-api load-status">
		<div class="working"><i class="fa fa-cog fa-spin"></i></div>
		<div class="error"><i class="fa fa-times"></i></div>
		<div class="empty">empty</div>
	</li>
</ul>

<script id="theme_item" type="text/x-dot-template">
	<li id="theme_{{=it.name}}" data-theme-name="{{=it.name}}"{{?it.current}} class="current"{{?}}>
		<h4>{{=it.title}}</h4>
		<div>({{=it.author}})</div>
		<img src="/<?php echo $_['base_url']; ?>res/theme/{{=it.name}}/preview.png/t={{=it.mtime}}/" alt="{{=it.name}}" width="256" height="256">
	</li>
</script>

<script type="text/javascript">
	$(function () {
		var themes = $('#themes');
		var theme_item = doT.template($('#theme_item').text());
		apiLoadStatusWorking($('#load_status'));
		api('/' + base_url + 'api/core/themes/', {
			action: 'get_themes'
		}, function (data) {
			if (!data['themes'].length) {
				apiLoadStatusEmpty($('#load_status'));
				return;
			}

			apiLoadStatusSuccess($('#load_status'));
			var items = '';
			$.each(data['themes'], function () {
				items += theme_item(this);
			});
			themes.append(items);
		}, function () {
			apiLoadStatusError($('#load_status'));
		});

		themes.on('click', 'li', function () {
			var item = $(this);
			if (!item.hasClass('current')) {
				apiStatusWorking('Switching theme...');
				api('/' + base_url + 'api/core/themes/', {
					action: 'change_theme',
					theme_name: item.attr('data-theme-name')
				}, function () {
					apiStatusSuccess('Switched theme');
					themes.find('li.current').removeClass('current');
					item.addClass('current');
				}, function () {
					apiStatusError('Switching theme failed');
				});
			}
		});
	});
</script>