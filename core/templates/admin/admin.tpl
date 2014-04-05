<h2>Administration</h2>
<h3>Diskspace</h3>
<div id="diskspace-total"></div>
<div id="diskspace">
	<span id="load_diskspace" class="dex-api load-status">
		<div class="working"><i class="fa fa-cog fa-spin"></i></div>
		<div class="error"><i class="fa fa-times"></i></div>
		<div class="empty">empty</div>
	</span>
</div>
<div id="diskspace-legend"></div>

<script id="diskspace-item" type="text/x-dot-template">
	<div style="width:{{=it.width}}%;" data-tooltip="{{=it.size}}"></div>
</script>

<script id="diskspace-legend-item" type="text/x-dot-template">
	<div style="width:{{=it.width}}%;"><div></div>&ensp;{{=it.name}}&ensp;<span>{{=it.percentage}}%</span></div>
</script>

<h3>Maintenance</h3>
<div>
	<a href="#" class="button" data-tooltip="Optimize images and scripts of the site" data-action="optimize_size"><i class="fa fa-fw fa-magic"></i>&ensp;Optimize site</a>&nbsp;
	<a href="#" class="alert-button" data-action="clear_cache"><i class="fa fa-trash-o"></i>&ensp;Clear cache</a>&nbsp;
	<a href="#" class="alert-button" data-action="clear_logs"><i class="fa fa-trash-o"></i>&ensp;Clear logs</a>
</div>

<h3>Warnings</h3>
<ul id="warnings">
	<li id="load_warnings" class="dex-api load-status">
		<div class="working"><i class="fa fa-cog fa-spin"></i></div>
		<div class="error"><i class="fa fa-times"></i></div>
		<div class="empty">empty</div>
	</li>
	<?php foreach ($_['warnings'] as $warning) {
		echo '<li>' . $warning . '</li>';
	} if (!count($_['warnings'])) {
		echo '<li class="empty">none</li>';
	} ?>
</ul>

<script type="text/javascript">
	$(function () {
		var diskspace = $('#diskspace');
		var diskspace_legend = $('#diskspace-legend');
		var diskspace_item = doT.template($('#diskspace-item').text());
		var diskspace_legend_item = doT.template($('#diskspace-legend-item').text());

		function loadDiskusage() {
			diskspace.find('> div').slideUp(100, function () { $(this).remove(); });
			diskspace_legend.find('> div').slideUp(100, function () { $(this).remove(); });

			apiLoadStatusWorking($('#load_diskspace'));
			api('/' + base_url + 'api/core/admin/', {
				action: 'diskspace_usage'
			}, function (data) {
				apiLoadStatusSuccess($('#load_diskspace'));

				$('#diskspace-total').html('Total disk usage: ' + parseFloat((data['diskspace_total'] / 1024 / 1024).toFixed(0)) + 'MB');

				var items = '';
				$.each(data['diskspace'], function () {
					this.width = this.percentage;
					items += diskspace_item(this);
				});
				$(items).hide().appendTo(diskspace).slideDown(100);

				var percentage = 100.0 / data['diskspace'].length;
				var items = '';
				$.each(data['diskspace'], function () {
					this.width = percentage;
					this.percentage = parseFloat(this.percentage.toFixed(0));
					items += diskspace_legend_item(this);
				});
				$(items).hide().appendTo(diskspace_legend).slideDown(100);
			}, function () {
				apiLoadStatusError($('#load_diskspace'));
			});
		}
		loadDiskusage();

		$('a[data-action]').click(function () {
			var action = $(this).attr('data-action');
			if (action == 'optimize_size') {
				$.fancybox.open({
					content: '<textarea class="dex-api console" readonly></textarea>',
					helpers:  {
						overlay: {
							locked: false
						}
					}
				});

				apiStatusWorking('Publishing site...');
				apiUpdateConsole($('.dex-api.console'));
				api('/' + base_url + 'api/core/optimize-site/', {
				}, function (data) {
					apiStopConsole();
					apiStatusSuccess('Published site');
				}, function () {
					apiStopConsole();
					apiStatusError('Publishing site failed');
					return false;
				});
			} else if (action == 'clear_logs') {
				apiStatusWorking('Clearing logs...');
				api('/' + base_url + 'api/core/admin/', {
					action: action
				}, function (data) {
					apiStatusSuccess('Cleared logs');
				}, function () {
					apiStatusError('Clearing log failed');
				});
			} else if (action == 'clear_cache') {
				apiStatusWorking('Clearing cache...');
				api('/' + base_url + 'api/core/admin/', {
					action: action
				}, function (data) {
					apiStatusSuccess('Cleared cache');
					loadDiskusage();
				}, function () {
					apiStatusError('Clearing cache failed');
				});
			}
		});
	});
</script>