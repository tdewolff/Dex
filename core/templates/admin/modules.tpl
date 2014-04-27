<h2><?php echo __('Modules'); ?></h2>
<ul id="modules" class="table">
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

<script id="module_item" type="text/x-dot-template">
	<li id="module_{{=it.module_name}}"{{? !it.enabled}} class="disabled"{{?}}>
		<div>
			<div class="dropdown">
				<a href="/<?php echo $_['base_url']; ?>admin/module/{{=it.module_name}}/" class="dropdown-select list-button">
					<i class="fa fa-arrow-right"></i>&ensp;<?php echo __('Go to'); ?>
				</a><a href="#" class="dropdown-toggle list-button">
					<i class="fa fa-caret-down"></i>
				</a>
				<ul class="dropdown-menu" role="menu">
					<li>
						<a href="#" class="enable" data-module-name="{{=it.module_name}}"><i class="fa fa-fw fa-check"></i>&ensp;<?php echo __('Enable'); ?></a>
						<a href="#" class="disable" data-module-name="{{=it.module_name}}"><i class="fa fa-fw fa-ban"></i>&ensp;<?php echo __('Disable'); ?></a>
					</li>
					<li>
						<a href="#" class="halt"><i class="fa fa-fw fa-refresh"></i>&ensp;<?php echo __('Reinstall'); ?></a>
						<a href="#" class="sure" data-module-name="{{=it.module_name}}" title="<?php echo __('Click to confirm'); ?>"><i class="fa fa-fw fa-refresh"></i>&ensp;<?php echo __('Really?'); ?></a>
					</li>
				</ul>
			</div>
		</div>
		<div>{{=it.title}}</div>
		<div>{{=it.author}}</div>
		<div>{{=it.description}}</div>
	</li>
</script>

<script type="text/javascript">
	$(function () {
		var modules = $('#modules');
		var module_item = doT.template($('#module_item').text());
		apiLoadStatusWorking($('#load_status'));
		api('/' + base_url + 'api/core/modules/', {
			action: 'get_modules'
		}, function (data) {
			if (!data['modules'].length) {
				apiLoadStatusEmpty($('#load_status'));
				return;
			}

			apiLoadStatusSuccess($('#load_status'));
			$.each(data['modules'], function () {
				var item = $(module_item(this));
				if (item.hasClass('disabled'))
					item.find('a.disable').hide();
				else
					item.find('a.enable').hide();
				modules.append(item);
			});
		}, function () {
			apiLoadStatusError($('#load_status'));
		});

		modules.on('click', 'li.disabled a.dropdown-select', function (e) {
			e.preventDefault();
		});

		modules.on('click', 'a.enable', function () {
			apiStatusWorking('<?php echo __('Enabling module...'); ?>');
			var item = $(this);
			api('/' + base_url + 'api/core/modules/', {
				action: 'enable_module',
				module_name: item.attr('data-module-name')
			}, function () {
				apiStatusSuccess('<?php echo __('Enabled module'); ?>');
				$('.dropdown-menu').hide();
				$('#module_' + item.attr('data-module-name')).removeClass('disabled');
				$('#admin_link_module_' + item.attr('data-module-name')).slideDown();
				item.hide();
				item.parent().find('.disable').show();
			}, function () {
				apiStatusError('<?php echo __('Enabling module failed'); ?>');
			});
		});

		modules.on('click', 'a.disable', function () {
			apiStatusWorking('<?php echo __('Disabling module...'); ?>');
			var item = $(this);
			api('/' + base_url + 'api/core/modules/', {
				action: 'disable_module',
				module_name: item.attr('data-module-name')
			}, function () {
				apiStatusSuccess('<?php echo __('Disabled module'); ?>');
				$('.dropdown-menu').hide();
				$('#module_' + item.attr('data-module-name')).addClass('disabled');
				$('#admin_link_module_' + item.attr('data-module-name')).slideUp();
				item.hide();
				item.parent().find('.enable').show();
			}, function () {
				apiStatusError('<?php echo __('Disabling module failed'); ?>');
			});
		});

		modules.on('click', 'a.sure', function () {
			apiStatusWorking('<?php echo __('Reinstalling module...'); ?>');
			var item = $(this);
			api('/' + base_url + 'api/core/modules/', {
				action: 'reinstall_module',
				module_name: item.attr('data-module-name')
			}, function () {
				apiStatusSuccess('<?php echo __('Reinstalled module'); ?>');
				$('.dropdown-menu').hide();
			}, function () {
				apiStatusError('<?php echo __('Reinstalling module failed'); ?>');
			});
		});
	});
</script>