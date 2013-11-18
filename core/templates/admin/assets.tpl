<h2>Assets</h2>
<div id="assets">
    <form id="upload" method="post" action="/<?php echo $_['base_url']; ?>admin/assets/" enctype="multipart/form-data">
        <div id="drop">
            <span>Drop Here</span><br>
            <a class="small-button">Browse</a>
            <input type="file" name="upload" multiple>
            <div id="knob">
                <div id="big-knob"><input type="text" value="0" data-width="64" data-height="64" data-thickness=".23" data-fgColor="#477725" data-readOnly="1" data-displayInput=false data-bgColor="#FFFFFF"></div>
                <div id="small-knob"><input type="text" value="0" data-width="48" data-height="48" data-thickness=".25" data-fgColor="#477725" data-readOnly="1" data-displayInput=false data-bgColor="#F0F0F0"></div>
            </div>
        </div>
        <ul></ul>
    </form>

    <div id="create_directory">
        <input type="text"><a href="#" class="small-button"><i class="icon-asterisk"></i>&ensp;Create directory</a>
    </div>

    <ul id="directories_assets" class="table">
        <li>
            <div style="width:460px;">Filename</div>
            <div style="width:100px;">Size</div>
            <div style="width:40px;">&nbsp;</div>
        </li>
    </ul>

    <ul id="images" class="grid">
    </ul>
</div>

<script id="directory_item" type="text/x-dot-template">
    <li id="directory_{{=it.id}}" data-name="{{=it.name}}" class="directory">
        <div style="width:460px;"><img src="/<?php echo $_['base_url']; ?>res/core/images/icons/{{=it.icon}}" width="16" height="16"><a href="/<?php echo $_['base_url']; ?>admin/assets/{{=it.url}}">{{=it.title}}</a></div>
        <div style="width:100px;">-</div>
        <div style="width:40px;">&nbsp;</div>
    </li>
</script>

<script id="asset_item" type="text/x-dot-template">
    <li id="asset_{{=it.id}}" class="asset">
        <div style="width:460px;"><img src="/<?php echo $_['base_url']; ?>res/core/images/icons/{{=it.icon}}" width="16" height="16">{{=it.title}}</div>
        <div style="width:100px;">{{=it.size}}</div>
        <div style="width:40px;">
            <a href="#" class="halt"><i class="icon-fixed-width icon-trash"></i></a>
            <a href="#" class="sure" data-id="{{=it.id}}" data-name="{{=it.name}}"><i class="icon-fixed-width icon-question"></i></a>
        </div>
    </li>
</script>

<script id="image_item" type="text/x-dot-template">
    <li id="image_{{=it.id}}">
        <div class="delete">
            <a href="#" class="halt"><i class="icon-fixed-width icon-trash"></i></a>
            <a href="#" class="sure" data-id="{{=it.id}}" data-name="{{=it.name}}"><i class="icon-fixed-width icon-question"></i></a>
        </div>
        <div class="caption"><strong>{{=it.title}}</strong></div>
        {{? it.width > 200}}
        <a href="/<?php echo $_['base_url']; ?>{{=it.url}}" data-fancybox-group="gallery" class="fancybox">
            <img src="/<?php echo $_['base_url']; ?>{{=it.url}}?w=200"
                 alt=""
                 title="{{=it.title}}">
        </a>
        {{??}}
        <img src="/<?php echo $_['base_url']; ?>{{=it.url}}"
             alt=""
             title="{{=it.title}}"
             class="small">
        {{?}}
    </li>
</script>

<script type="text/javascript">
    var directories_assets = $('#directories_assets');
    var images = $('#images');

    var directory_item = doT.template($('#directory_item').text());
    var asset_item = doT.template($('#asset_item').text());
    var image_item = doT.template($('#image_item').text());

    api(null, function(data) {
        $.each(data['directories'], function() {
            directories_assets.append(directory_item(this));
        });

        $.each(data['assets'], function() {
            directories_assets.append(asset_item(this));
        });

        $.each(data['images'], function() {
            images.append(image_item(this));
        });
    });

    directories_assets.on('click', '.asset a.sure', function() {
        var item = $(this);
        api({
            action: 'delete_file',
            name: item.attr('data-name')
        }, function() {
            $('#asset_' + item.attr('data-id')).remove();
        });
    });

    images.on('click', 'a.sure', function() {
        var item = $(this);
        api({
            action: 'delete_file',
            name: item.attr('data-name')
        }, function() {
            $('#image_' + item.attr('data-id')).remove();
        });
    });

    $('#create_directory').on('click', 'a', function() {
        api({
            action: 'create_directory',
            dir_name: $(this).prev('input').val()
        }, function(data) {
            // append directory at the right place, alphabetically
            var appended = false;
            var directories = directories_assets.find('.directory');

            directories.each(function() {
                if ($(this).attr('data-name') > data['directory']['name'])
                {
                    $(this).before(directory_item(data['directory']));
                    appended = true;
                    return false;
                }
            });

            if (!appended)
                directories.last().after(directory_item(data['directory']));
        });
    });
</script>