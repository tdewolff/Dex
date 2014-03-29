<h2>Menu</h2>
<ul id="menu" class="table draggable">
    <li>
        <div></div>
        <div></div>
        <div>Name</div>
        <div>Page title</div>
        <div>Link</div>
    </li>
</ul>

<script id="menu_item" type="text/x-dot-template">
    <li data-link-id="{{=it.link_id}}" data-level="{{=it.level}}" {{?!it.enabled}}class="unused"{{?}}>
        <div><i class="fa fa-eye"></i></div>
        <div><i class="fa fa-long-arrow-right"></i><i class="fa fa-long-arrow-right"></i><i class="fa fa-bars"></i></div>
        <div><input type="text" value="{{=it.name}}" {{?!it.enabled}}class="unused"{{?}}></div>
        <div>{{=it.title}}</div>
        <div><a href="/<?php echo $_['base_url']; ?>{{=it.url}}">{{?it.url != ''}}{{=it.url}}{{??}}<em>(home)</em>{{?}}</a></div>
    </li>
</script>

<script>
    $(function () {
        var menu = $('#menu');
        var menu_item = doT.template($('#menu_item').text());
        api('/' + base_url + 'api/module/menu/index/', {
            action: 'get_menu'
        }, function (data) {
            $.each(data['menu'], function () {
                var item = $(menu_item(this));
                item.find('.fa-long-arrow-right').hide();

                var level = item.attr('data-level');
                for (var i = 0; i < level; i++) {
                    item.find('.fa-long-arrow-right').eq(i).show().css('display', 'inline-block');
                }
                menu.append(item);
            });
        });
    });
</script>