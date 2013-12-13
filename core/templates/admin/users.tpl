<h2>Users</h2>
<a href="/<?php echo $_['base_url']; ?>admin/users/new/" class="button" style="margin-left:20px;"><i class="fa fa-plus"></i>&ensp;New user</a>
<ul id="users" class="table">
  <li>
	<div style="width:120px;"></div>
	<div style="width:200px;">Username</div>
	<div style="width:580px;">Permission level</div>
  </li>
</ul>

<script id="user_item" type="text/x-dot-template">
    <li id="user_{{=it.user_id}}">
        <div style="width:120px; overflow:visible;">
            <div class="dropdown">
            <a href="/<?php echo $_['base_url']; ?>admin/users/{{=it.user_id}}/" class="dropdown-select list-button"><i class="fa fa-pencil"></i>&ensp;Edit</a><a href="#" class="dropdown-toggle list-button"><i class="fa fa-caret-down"></i></a>
                <ul class="dropdown-menu" role="menu">
                    <li>
                        <a href="#" class="halt{{?it.current}}-stop{{?}}"><i class="fa fa-fw fa-trash-o"></i>&ensp;Delete</a>
                        <a href="#" class="sure" data-user-id="{{=it.user_id}}" title="Click to confirm"><i class="fa fa-fw fa-trash-o"></i>&ensp;Really?</a>
                    </li>
                </ul>
            </div>
        </div>
        <div style="width:200px;">{{=it.username}}</div>
        <div style="width:580px;">{{=it.permission}}</div>
    </li>
</script>

<script type="text/javascript">
    var users = $('#users');
    var user_item = doT.template($('#user_item').text());
    api('/' + base_url + 'api/core/users.php', {
        action: 'get_users'
    }, function(data) {
        $.each(data['users'], function() {
            users.append(user_item(this));
        });
    });

    users.on('click', 'a.sure', function() {
        var item = $(this);
        api({
            action: 'delete_user',
            user_id: item.attr('data-user-id')
        }, function() {
            $('.dropdown-menu').fadeOut('fast');
            $('#user_' + item.attr('data-user-id')).remove();
        });
    });
</script>
