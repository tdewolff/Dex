<h2>Templates</h2>
<ul id="templates" class="table">
	<li>
		<div style="width:120px;"></div>
		<div style="width:120px;">Name</div>
		<div style="width:120px;">Author</div>
		<div style="width:540px;">Description</div>
	</li>
	<li id="load_status" class="api load-status">
		<div class="working"><i class="fa fa-cog fa-spin"></i></div>
		<div class="error"><i class="fa fa-times"></i></div>
		<div class="empty">empty</div>
	</li>
</ul>

<script id="template_item" type="text/x-dot-template">
	<li id="template_{{=it.name}}">
		<div style="width:120px;"></div>
		<div style="width:120px;">{{=it.title}}</div>
		<div style="width:120px;">{{=it.author}}</div>
		<div style="width:540px;">{{=it.description}}</div>
	</li>
</script>

<script type="text/javascript">
	$(function() {
		var templates = $('#templates');
		var template_item = doT.template($('#template_item').text());
		apiLoadStatusWorking($('#load_status'));
		api('/' + base_url + 'api/core/templates/', {
			action: 'get_templates'
		}, function(data) {
			if (!data['templates'].length) {
				apiLoadStatusEmpty($('#load_status'));
				return;
			}

			apiLoadStatusSuccess($('#load_status'));
			$.each(data['templates'], function() {
				templates.append(template_item(this));
			});
		}, function() {
			apiLoadStatusError($('#load_status'));
		});
	});
</script>
