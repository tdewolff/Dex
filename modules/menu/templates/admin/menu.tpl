<h2>Menu</h2>
<ul id="menu" class="table draggable">
    <li>
        <div style="width:40px;"></div>
        <div style="width:120px;"></div>
        <div style="width:240px;">Name</div>
        <div style="width:200px;">Title</div>
        <div style="width:300px;">Link</div>
    </li>
</ul>

<script id="menu_item" type="text/x-dot-template">
    <li data-link-id="{{=it.link_id}}" data-level="{{=it.level}}" {{?!it.enabled}}class="unused"{{?}}>
        <div style="width:40px;"><i class="fa fa-eye"></i></div>
        <div style="width:120px;"><i class="fa fa-long-arrow-right"></i><i class="fa fa-long-arrow-right"></i><i class="fa fa-bars"></i></div>
        <div style="width:240px;"><input type="text" value="{{=it.name}}" {{?!it.enabled}}class="unused"{{?}}></div>
        <div style="width:200px;">{{=it.title}}</div>
        <div style="width:300px;"><a href="/<?php echo $_['base_url']; ?>{{=it.url}}">{{?it.url != ''}}/{{=it.url}}{{??}}(root){{?}}</a></div>
    </li>
</script>

<script type="text/javascript">
    $(function() {
        var menu = $('#menu');
        var menu_item = doT.template($('#menu_item').text());
        api('/' + base_url + 'api/module/menu/index.php', {
            action: 'get_menu'
        }, function(data) {
            $.each(data['menu'], function() {
                var item = $(menu_item(this));

                item.find('.fa-long-arrow-right').hide();
                var level = item.attr('data-level');
                for (var i = 0; i < level; i++)
                    item.find('.fa-long-arrow-right').eq(i).show();

                menu.append(item);
            });
        });
    });
</script>