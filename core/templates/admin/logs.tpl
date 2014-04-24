<h2><?php echo _('Logs'); ?></h2>
<a href="/<?php echo $_['base_url']; ?>admin/" class="button indent"><i class="fa fa-chevron-left"></i> <?php echo _('Back'); ?></a>
<ul id="logs" class="table">
	<li>
		<div><?php echo _('Date time'); ?></div>
		<div><?php echo _('IP address'); ?></div>
		<div><?php echo _('Message'); ?></div>
	</li>
	<li id="load_status" class="dex-api load-status">
		<div class="working"><i class="fa fa-cog fa-spin"></i></div>
		<div class="error"><i class="fa fa-times"></i></div>
		<div class="empty"><?php echo _('empty'); ?></div>
	</li>
</ul>

<script id="log_item" type="text/x-dot-template">
	<li data-html="{{=it.html}}" class="{{=it.type.toLowerCase()}}">
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
			var items = '';
			$.each(data['logs'], function () {
				items += log_item(this);
			});
			logs.append(items);
		}, function () {
			apiLoadStatusError($('#load_status'));
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