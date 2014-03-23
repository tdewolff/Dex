<h2>Logs</h2>
<a href="/<?php echo $_['base_url']; ?>admin/" class="button indent"><i class="fa fa-chevron-left"></i> Back</a>
<ul id="logs" class="table">
	<li>
		<div>Date time</div>
		<div>IP Address</div>
		<div>Message</div>
	</li>
	<li id="load_status" class="api load-status">
		<div class="working"><i class="fa fa-cog fa-spin"></i></div>
		<div class="error"><i class="fa fa-times"></i></div>
		<div class="empty">empty</div>
	</li>
</ul>

<script id="log_item" type="text/x-dot-template">
	<li data-html="{{=it.html}}">
		<div>{{=it.datetime}}</div>
		<div>{{=it.ipaddress}}</div>
		<div>{{=it.message}}</div>
	</li>
</script>

<script type="text/javascript">
	$(function () {
		var logs = $('#logs');
		var log_item = doT.template($('#log_item').text());
		apiLoadStatusWorking($('#load_status'));
		api('/' + base_url + 'api/core/logs/', {
			action: 'get',
			lines: 100
		}, function (data) {
			if (!data['logs'].length) {
				apiLoadStatusEmpty($('#load_status'));
				return;
			}

			apiLoadStatusSuccess($('#load_status'));
			$.each(data['logs'], function () {
				var item = $(log_item(this));
				if (this['type'] == 'ERROR')
					item = item.addClass('error');
				else if (this['type'] == 'WARNING')
					item = item.addClass('warning');
				else if (this['type'] == 'NOTICE')
					item = item.addClass('notice');
				else if (this['type'] == 'REQUEST')
					item = item.addClass('request');
				else if (this['type'] == 'CACHING')
					item = item.addClass('caching');
				else
					item = item.addClass('empty');
				logs.append(item);
			});
		}, function () {
			apiLoadStatusError($('#load_status'));
		});

		logs.on('click', 'li', function () {
			$.fancybox.open({
				content: $(this).attr('data-html'),
				beforeShow: function () {
					this.skin.addClass('api-error');
				},
				overlay: {
					closeClick: true,
					locked: false
				}
			});
		});
	});
</script>