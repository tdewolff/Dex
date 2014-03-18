<h2>Admin panel</h2>

<h3>Visitors</h3>
<div id="load_stats" class="api load-status">
	<div class="working"><i class="fa fa-cog fa-spin"></i></div>
	<div class="error"><i class="fa fa-times"></i></div>
</div>
<iframe class="stats-frame" src="/<?php echo $_['base_url']; ?>admin/auxiliary/stats/" marginwidth="0" marginheight="0" scrolling="no"></iframe>

<h3>Latest errors</h3>
<ul id="latest-logs" class="table">
	<li>
		<div>Date time</div>
		<div>Message</div>
	</li>
	<li id="load_logs" class="api load-status">
		<div class="working"><i class="fa fa-cog fa-spin"></i></div>
		<div class="error"><i class="fa fa-times"></i></div>
		<div class="empty">empty</div>
	</li>
</ul>

<a href="/<?php echo $_['base_url']; ?>admin/logs/" class="button indent view-logs"><i class="fa fa-list-alt"></i>&ensp;View <?php echo $_['log_name']; ?></a>

<script id="log_item" type="text/x-dot-template">
	<li>
		<div>{{=it.datetime}}</div>
		<div title="{{=it.message}}">{{=it.message}}</div>
	</li>
</script>

<script type="text/javascript">
	$(function () {
		apiLoadStatusWorking($('#load_stats'));
		$('.stats-frame').load(function () {
			$('.stats-frame').ready(function () {
				api('/' + base_url + 'api/core/stats/', {
					action: 'page-visits'
				}, function (data) {
					apiLoadStatusSuccess($('#load_stats'));
					$('.stats-frame').slideDown();
					$('.stats-frame')[0].contentWindow.drawStats(data['page-visits']);
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
			$.each(data['logs'], function () {
				var item = $(log_item(this));
				if (this['type'] == 'ERROR')
					item = item.addClass('error');
				else if (this['type'] == 'WARNING')
					item = item.addClass('warning');
				logs.append(item);
			});
		}, function () {
			apiLoadStatusError($('#load_logs'));
		});
	});
</script>