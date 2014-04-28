<h2><?php echo __('Admin panel'); ?></h2>

<h3><?php echo __('Visitors'); ?></h3>
<div id="load_stats" class="dex-api load-status">
	<div class="working"><i class="fa fa-cog fa-spin"></i></div>
	<div class="error"><i class="fa fa-times"></i></div>
</div>
<iframe class="stats-frame" src="/<?php echo $_['base_url']; ?>admin/auxiliary/stats/"></iframe>

<h3><?php echo __('Latest errors'); ?></h3>
<ul id="latest-logs" class="table">
	<li>
		<div><?php echo __('Date time'); ?></div>
		<div><?php echo __('Message'); ?></div>
	</li>
	<li id="load_logs" class="dex-api load-status">
		<div class="working"><i class="fa fa-cog fa-spin"></i></div>
		<div class="error"><i class="fa fa-times"></i></div>
		<div class="empty"><?php echo __('empty'); ?></div>
	</li>
</ul>

<a href="/<?php echo $_['base_url']; ?>admin/logs/" class="button indent view-logs"><i class="fa fa-list-alt"></i>&ensp;<?php echo __('View') . ' ' . $_['log_name']; ?></a>

<script id="log_item" type="text/x-dot-template">
	<li data-html="{{=it.html}}" class="{{=it.type.toLowerCase()}}">
		<div>{{=it.datetime}}</div>
		<div>{{=it.message}}</div>
	</li>
</script>

<script type="text/javascript">
	$(function () {
		apiLoadStatusWorking($('#load_stats'));
		$('.stats-frame').load(function () {
			$('.stats-frame').ready(function () {
				api('/' + base_url + 'api/core/stats/', {
					action: 'get_visits'
				}, function (data) {
					apiLoadStatusSuccess($('#load_stats'));
					$('.stats-frame').slideDown(100);
					$('.stats-frame')[0].contentWindow.drawStats(data['visits']);
				}, function () {
					apiLoadStatusError($('#load_stats'));
				});
			});
		});

		var logs = $('#latest-logs');
		var log_item = doT.template($('#log_item').text());
		apiLoadStatusWorking($('#load_logs'));
		api('/' + base_url + 'api/core/logs/', {
			action: 'get',
			lines: 10,
			errors: true
		}, function (data) {
			if (!data['logs'].length) {
				apiLoadStatusEmpty($('#load_logs'));
				return;
			}

			apiLoadStatusSuccess($('#load_logs'));
			var items = '';
			$.each(data['logs'], function () {
				items += log_item(this);
			});
			logs.append(items);
		}, function () {
			apiLoadStatusError($('#load_logs'));
		});

		logs.on('click', 'li', function () {
			$.fancybox.open({
				content: $(this).attr('data-html'),
				beforeShow: function () {
					this.skin.addClass('dex-api-error');
				},
				overlay: {
					closeClick: true,
					locked: false
				}
			});
		});
	});
</script>