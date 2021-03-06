<h2><?php echo __('Templates'); ?></h2>
<ul id="templates" class="table">
	<li>
		<div></div>
		<div><?php echo __('Name'); ?></div>
		<div><?php echo __('Author'); ?></div>
		<div><?php echo __('Description'); ?></div>
	</li>
	<li id="load_status" class="dex-api load-status">
		<div class="working"><i class="fa fa-cog fa-spin"></i></div>
		<div class="error"><i class="fa fa-times"></i></div>
		<div class="empty"><?php echo __('empty'); ?></div>
	</li>
</ul>

<script id="template_item" type="text/x-dot-template">
	<li id="template_{{=it.name}}">
		<div></div>
		<div>{{=it.title}}</div>
		<div>{{=it.author}}</div>
		<div>{{=it.description}}</div>
	</li>
</script>

<script type="text/javascript">
	$(function () {
		var templates = $('#templates');
		var template_item = doT.template($('#template_item').text());
		apiLoadStatusWorking($('#load_status'));
		api('/' + base_url + 'api/core/templates/', {
			action: 'get_templates'
		}, function (data) {
			if (!data['templates'].length) {
				apiLoadStatusEmpty($('#load_status'));
				return;
			}

			apiLoadStatusSuccess($('#load_status'));
			var items = '';
			$.each(data['templates'], function () {
				items += template_item(this);
			});
			templates.append(items);
		}, function () {
			apiLoadStatusError($('#load_status'));
		});
	});
</script>