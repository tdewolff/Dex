<h2>Assets</h2>
<div id="assets">
    <form id="upload" method="post" action="/<?php echo $_['base_url']; ?>api/core/assets.php" enctype="multipart/form-data">
        <input type="hidden" name="dir" value="">
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

    <div id="breadcrumbs">
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
    <li data-name="{{=it.name}}" class="directory">
        <div style="width:460px;"><img src="/<?php echo $_['base_url']; ?>res/core/images/icons/{{=it.icon}}" width="16" height="16"><a href="#" data-dir="{{=it.dir}}">{{=it.name}}</a></div>
        <div style="width:100px;">-</div>
        <div style="width:40px;">&nbsp;</div>
    </li>
</script>

<script id="asset_item" type="text/x-dot-template">
    <li data-name="{{=it.name}}" class="asset">
        <div style="width:460px;"><img src="/<?php echo $_['base_url']; ?>res/core/images/icons/{{=it.icon}}" width="16" height="16">{{=it.title}}</div>
        <div style="width:100px;">{{=it.size}}</div>
        <div style="width:40px;">
            <a href="#" class="halt inline-rounded"><i class="icon-trash"></i></a>
            <a href="#" class="sure inline-rounded" data-name="{{=it.name}}" title="Click to confirm"><i class="icon-trash"></i></a>
        </div>
    </li>
</script>

<script id="image_item" type="text/x-dot-template">
    <li data-name="{{=it.name}}">
        <div class="delete">
            <a href="#" class="halt inline-rounded"><i class="icon-trash"></i></a>
            <a href="#" class="sure inline-rounded" data-name="{{=it.name}}" title="Click to confirm"><i class="icon-trash"></i></a>
        </div>
        <div class="caption"><strong>{{=it.title}}</strong></div>
        {{? it.width > 200}}
        <a href="/<?php echo $_['base_url']; ?>res/assets/{{=it.url}}" data-fancybox-group="gallery" class="fancybox">
            <img src="/<?php echo $_['base_url']; ?>res/assets/{{=it.url}}?w=200"
                 alt=""
                 title="{{=it.title}}">
        </a>
        {{??}}
        <img src="/<?php echo $_['base_url']; ?>res/assets/{{=it.url}}"
             alt=""
             title="{{=it.title}}"
             class="small">
        {{?}}
    </li>
</script>

<script type="text/javascript">
    // preliminaries
    var breadcrumbs = $('#breadcrumbs');
    var directories_assets = $('#directories_assets');
    var images = $('#images');

    var directory_item = doT.template($('#directory_item').text());
    var asset_item = doT.template($('#asset_item').text());
    var image_item = doT.template($('#image_item').text());

    // loading initial data
    function loadDir(dir) {
        directories_assets.find('li:not(:first)').slideUp('fast', function() { $(this).remove(); });
        images.find('li').slideUp('fast', function() { $(this).remove(); });

        $('#upload input[name="dir"]').val(dir);

        api('/' + base_url + 'api/core/assets.php', {
            action: 'get_breadcrumbs',
            dir: dir
        }, function(data) {
            breadcrumbs.empty();
            $.each(data['breadcrumbs'], function(i) {
                if (i)
                    breadcrumbs.append('&gt;');
                breadcrumbs.append('<a href="#" data-dir="' + this.dir + '">' + this.name + '</a>');
            });
        });

        api('/' + base_url + 'api/core/assets.php', {
            action: 'get_directories',
            dir: dir
        }, function(data) {
            $.each(data['directories'], function() {
                $(directory_item(this)).hide().appendTo(directories_assets).slideDown('fast');
            });
        });

        api('/' + base_url + 'api/core/assets.php', {
            action: 'get_assets',
            dir: dir
        }, function(data) {
            $.each(data['assets'], function() {
                if (this.is_image)
                    $(image_item(this)).hide().appendTo(images).slideDown('fast');
                else
                    $(asset_item(this)).hide().appendTo(directories_assets).slideDown('fast');
            });
        });
    }
    loadDir('');

    // click events on directories, assets and images
    breadcrumbs.on('click', 'a', function() {
        loadDir($(this).attr('data-dir'));
    });

    directories_assets.on('click', '.directory a', function() {
        loadDir($(this).attr('data-dir'));
    });

    $('#directories_assets, #images').on('click', 'a.sure', function() {
        var item = $(this);
        api('/' + base_url + 'api/core/assets.php', {
            action: 'delete_file',
            name: item.attr('data-name')
        }, function() {
            item.closest('li').slideUp('fast', function() { $(this).remove(); });
        });
    });

    // adding directories, assets or images
    function addAlphabetically(list, item, name) {
        item = $(item).hide();

        var added = false;
        list.each(function() {
            if ($(this).attr('data-name') > name)
            {
                item.insertBefore(this).slideDown('fast');
                added = true;
                return false;
            }
        });

        if (!added)
            item.insertAfter(list.last()).slideDown('fast');
    }

    $('#create_directory').on('click', 'a', function() {
        api('/' + base_url + 'api/core/assets.php', {
            action: 'create_directory',
            name: $(this).prev('input').val()
        }, function(data) {
            $('#create_directory input').val('');
            var item = directory_item(data['directory']);
            if (directories_assets.find('li.directory').length)
                addAlphabetically(directories_assets.find('li.directory'), item, data['directory']['name']);
            else
                $(item).hide().insertAfter(directories_assets.find('li:first')).slideDown('fast');
        });
    });

    $(function() {
        initializeUpload('#upload', function(data) {
            console.log(data);
            if (!data['file'].is_image)
            {
                var item = asset_item(data['file']);
                if (directories_assets.find('li.asset').length)
                    addAlphabetically(directories_assets.find('li.asset'), item, data['file']['name']);
                else
                    $(item).hide().insertAfter(directories_assets.find('.directory:last')).slideDown('fast');
            }
            else
            {
                var item = image_item(data['file']);
                if (images.find('li').length)
                    addAlphabetically(images.find('li'), item, data['file']['name']);
                else
                    $(item).hide().appendTo(images).slideDown('fast');
            }
        });
    });
</script>