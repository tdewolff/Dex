<h2><?php echo __('Users'); ?></h2>
<a href="/<?php echo $_['base_url']; ?>admin/users/new/" class="button indent"><i class="fa fa-plus"></i>&ensp;<?php echo __('New user'); ?></a>
<ul id="users" class="table">
	<li>
		<div></div>
		<div><?php echo __('Username'); ?></div>
		<div><?php echo __('Email address'); ?></div>
		<div><?php echo __('Role'); ?></div>
	</li>
	<li id="load_status" class="dex-api load-status">
		<div class="working"><i class="fa fa-cog fa-spin"></i></div>
		<div class="error"><i class="fa fa-times"></i></div>
		<div class="empty"><?php echo __('empty'); ?></div>
	</li>
</ul>

<script id="user_item" type="text/x-dot-template">
	<li id="user_{{=it.user_id}}">
		<div>
			{{?!it.current}}
			<div class="dropdown">
				<a href="/<?php echo $_['base_url']; ?>admin/users/{{=it.user_id}}/" class="dropdown-select list-button"><i class="fa fa-pencil"></i>&ensp;<?php echo __('Edit'); ?></a><a href="#" class="dropdown-toggle list-button"><i class="fa fa-caret-down"></i></a>
				<ul class="dropdown-menu" role="menu">
					<li>
						<a href="#" class="halt"><i class="fa fa-fw fa-trash-o"></i>&ensp;<?php echo __('Delete'); ?></a>
						<a href="#" class="sure" data-user-id="{{=it.user_id}}" title="Click to confirm"><i class="fa fa-fw fa-trash-o"></i>&ensp;<?php echo __('Really?'); ?></a>
					</li>
				</ul>
			</div>
			{{??}}
			<a href="/<?php echo $_['base_url']; ?>admin/users/{{=it.user_id}}/" class="list-button"><i class="fa fa-pencil"></i>&ensp;<?php echo __('Edit'); ?></a></a>
			{{?}}
		</div>
		<div>{{=it.username}}</div>
		<div>{{=it.email}}</div>
		<div>{{=it.role}}</div>
	</li>
</script>

<script type="text/javascript">
	$(function () {
		var users = $('#users');
		var user_item = doT.template($('#user_item').text());
		apiLoadStatusWorking($('#load_status'));
		api('/' + base_url + 'api/core/users/', {
			action: 'get_users'
		}, function (data) {
			if (!data['users'].length) {
				apiLoadStatusEmpty($('#load_status'));
				return;
			}

			apiLoadStatusSuccess($('#load_status'));
			var items = '';
			$.each(data['users'], function () {
				items += user_item(this);
			});
			users.append(items);
		}, function () {
			apiLoadStatusError($('#load_status'));
		});

		users.on('click', 'a.sure', function () {
			apiStatusWorking('<?php echo __('Deleting user...'); ?>');
			var item = $(this);
			api('/' + base_url + 'api/core/users/', {
				action: 'delete_user',
				user_id: item.attr('data-user-id')
			}, function () {
				apiStatusSuccess('<?php echo __('Deleted user'); ?>');
				$('.dropdown-menu').fadeOut(100);
				$('#user_' + item.attr('data-user-id')).remove();
			}, function () {
				apiStatusError('<?php echo __('Deleting user failed'); ?>');
			});
		});
	});
</script>