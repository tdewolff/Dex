<h2>Modules</h2>
<ul id="modules" class="table">
  <li>
	<div style="width:120px;">&nbsp;</div>
	<div style="width:120px;">Name</div>
	<div style="width:120px;">Author</div>
	<div style="width:540px;">Description</div>
  </li>
</ul>

<script id="module_item" type="text/x-dot-template">
    <li id="module_{{=it.module_name}}" {{? !it.enabled}}class="disabled"{{?}}>
        <div style="width:120px; overflow:visible;">
            <div class="dropdown">
                <a href="/<?php echo $_['base_url']; ?>admin/module/{{=it.module_name}}/" class="dropdown-select list-button">
                    <i class="icon-arrow-right"></i>&ensp;Go to</a><a href="#" class="dropdown-toggle list-button"><i class="icon-caret-down"></i>
                </a>
                <ul class="dropdown-menu" role="menu">
                    <li>
                        <a href="#" class="enable" data-module-name="{{=it.module_name}}"><i class="icon-fixed-width icon-ok"></i>&ensp;Enable</a>
                        <a href="#" class="disable" data-module-name="{{=it.module_name}}"><i class="icon-fixed-width icon-ban-circle"></i>&ensp;Disable</a>
                    </li>
                </ul>
            </div>
        </div>
        <div style="width:120px;">{{=it.title}}</div>
        <div style="width:120px;">{{=it.author}}</div>
        <div style="width:540px;">{{=it.description}}</div>
    </li>
</script>

<script type="text/javascript">
    var modules = $('#modules');
    var module_item = doT.template($('#module_item').text());
    api(null, function(data) {
        $.each(data['modules'], function() {
            var item = $(module_item(this));
            if (item.hasClass('disabled'))
                item.find('a.disable').hide();
            else
                item.find('a.enable').hide();
            modules.append(item);
        });
    });

    modules.on('click', 'a.enable', function() {
        var item = $(this);
        api({
            action: 'enable_module',
            module_name: item.attr('data-module-name')
        }, function() {
            $('.dropdown-menu').hide();
            $('#module_' + item.attr('data-module-name')).removeClass('disabled');
            item.hide();
            item.parent().find('.disable').show();
        });
    });

    modules.on('click', 'a.disable', function() {
        var item = $(this);
        api({
            action: 'disable_module',
            module_name: item.attr('data-module-name')
        }, function() {
            $('.dropdown-menu').hide();
            $('#module_' + item.attr('data-module-name')).addClass('disabled');
            item.hide();
            item.parent().find('.enable').show();
        });
    });
</script>