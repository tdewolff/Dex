<h2>Administration</h2>
<h3>Diskspace</h3>
<div id="diskspace">
</div>

<div id="diskspace_legend">
</div>

<script id="diskspace_item" type="text/x-dot-template">
	<div style="width:{{=it.width}}%;" data-tooltip="{{=it.size}}"></div>
</script>

<script id="diskspace_legend_item" type="text/x-dot-template">
	<div style="width:{{=it.width}}%;"><div></div>&ensp;{{=it.name}}&ensp;<span>{{=it.percentage}}%</span></div>
</script>

<h3>Maintenance</h3>
<div><a href="#" class="alert-button" data-action="clear_cache"><i class="fa fa-trash-o"></i>&ensp;Clear cache</a></div>
<div><a href="#" class="alert-button" data-action="clear_logs"><i class="fa fa-trash-o"></i>&ensp;Clear logs</a></div>

<script type="text/javascript">
	$(function() {
		var diskspace = $('#diskspace');
		var diskspace_legend = $('#diskspace_legend');
		var diskspace_item = doT.template($('#diskspace_item').text());
		var diskspace_legend_item = doT.template($('#diskspace_legend_item').text());

		function loadDiskusage() {
			diskspace.find('> div').slideUp('fast', function() { $(this).remove(); });
			diskspace_legend.find('> div').slideUp('fast', function() { $(this).remove(); });

			api('/' + base_url + 'api/core/admin/', {
				action: 'diskspace_usage'
			}, function(data) {
				$.each(data['diskspace'], function() {
					this.width = this.percentage;
					diskspace.append(diskspace_item(this));
				});

				var percentage = 100.0 / data['diskspace'].length;
				$.each(data['diskspace'], function() {
					this.width = percentage;
					this.percentage = parseFloat(this.percentage.toFixed(0));
					diskspace_legend.append(diskspace_legend_item(this));
				});
			});
		}
		loadDiskusage();

		$('a[data-action]').click(function() {
			var action = $(this).attr('data-action');
			if (action == 'clear_logs') {
				apiStatusWorking('Clearing logs...');
				api('/' + base_url + 'api/core/admin/', {
					action: action
				}, function(data) {
					apiStatusSuccess('Cleared logs');
				}, function() {
					apiStatusError('Clearing log failed');
				});
			} else if (action == 'clear_cache') {
				apiStatusWorking('Clearing cache...');
				api('/' + base_url + 'api/core/admin/', {
					action: action
				}, function(data) {
					apiStatusSuccess('Cleared cache');
					loadDiskusage();
				}, function() {
					apiStatusError('Clearing cache failed');
				});
			}
		});
	});
</script>