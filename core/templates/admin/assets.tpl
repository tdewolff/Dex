<h2>Assets</h2>
<div id="assets">
    <div id="create_directory">
        <input type="text"><a href="#" class="small-button"><i class="fa fa-asterisk"></i>&ensp;Create directory</a>
    </div>

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

    <div id="breadcrumbs">
    </div>

    <ul id="directories_assets" class="table">
        <li>
            <div style="width:460px;">Filename</div>
            <div style="width:100px;">Size</div>
            <div style="width:40px;"></div>
        </li>
        <li id="load_status_directories" class="api_load_status">
            <div class="working"><i class="fa fa-cog fa-spin"></i></div>
            <div class="error"><i class="fa fa-times"></i></div>
        </li>
    </ul>

    <ul id="images" class="grid">
        <li id="load_status_images" class="api_load_status">
            <div class="working"><i class="fa fa-cog fa-spin"></i></div>
            <div class="error"><i class="fa fa-times"></i></div>
        </li>
    </ul>
</div>

<script id="directory_item" type="text/x-dot-template">
    <li data-name="{{=it.name}}" class="directory">
        <div style="width:460px;"><img src="/<?php echo $_['base_url']; ?>res/core/images/icons/{{=it.icon}}" width="16" height="16"><a href="#!dir={{=it.dir}}" data-dir="{{=it.dir}}">{{=it.name}}</a></div>
        <div style="width:100px;">-</div>
        <div style="width:40px;">
            {{?it.dir.length}}
            <a href="#" class="halt inline-rounded"><i class="fa fa-trash-o"></i></a>
            <a href="#" class="sure inline-rounded" data-tooltip="Click to confirm" data-name="{{=it.name}}"><i class="fa fa-trash-o"></i></a>
            {{?}}
        </div>
    </li>
</script>

<script id="asset_item" type="text/x-dot-template">
    <li data-name="{{=it.name}}" class="asset">
        <div style="width:460px;"><img src="/<?php echo $_['base_url']; ?>res/core/images/icons/{{=it.icon}}" width="16" height="16">{{=it.title}}</div>
        <div style="width:100px;">{{=it.size}}</div>
        <div style="width:40px;">
            <a href="#" class="halt inline-rounded"><i class="fa fa-trash-o"></i></a>
            <a href="#" class="sure inline-rounded" data-tooltip="Click to confirm" data-name="{{=it.name}}"><i class="fa fa-trash-o"></i></a>
        </div>
    </li>
</script>

<script id="image_item" type="text/x-dot-template">
    <li data-name="{{=it.name}}">
        <div class="delete">
            <a href="#" class="halt inline-rounded"><i class="fa fa-trash-o"></i></a>
            <a href="#" class="sure inline-rounded" data-tooltip="Click to confirm" data-name="{{=it.name}}"><i class="fa fa-trash-o"></i></a>
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
    $(function() {
        // preliminaries
        var breadcrumbs = $('#breadcrumbs');
        var directories_assets = $('#directories_assets');
        var images = $('#images');

        var directory_item = doT.template($('#directory_item').text());
        var asset_item = doT.template($('#asset_item').text());
        var image_item = doT.template($('#image_item').text());

        // loading initial data
        var dir = '';
        function loadDir(newDir) {
            dir = newDir;

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
                    breadcrumbs.append('<a href="#!dir=' + this.dir + '" data-dir="' + this.dir + '">' + this.name + '</a>');
                });
            });

            apiLoadStatusWorking($('#load_status_directories'));
            api('/' + base_url + 'api/core/assets.php', {
                action: 'get_directories',
                dir: dir
            }, function(data) {
                apiLoadStatusSuccess($('#load_status_directories'));
                $.each(data['directories'], function() {
                    $(directory_item(this)).hide().appendTo(directories_assets).slideDown('fast');
                });
            }, function() {
                apiLoadStatusError($('#load_status_directories'));
            });

            apiLoadStatusWorking($('#load_status_images'));
            api('/' + base_url + 'api/core/assets.php', {
                action: 'get_assets',
                dir: dir
            }, function(data) {
                apiLoadStatusSuccess($('#load_status_images'));
                $.each(data['assets'], function() {
                    if (this.is_image)
                        $(image_item(this)).hide().appendTo(images).slideDown('fast');
                    else
                        $(asset_item(this)).hide().appendTo(directories_assets).slideDown('fast');
                });
            }, function() {
                apiLoadStatusError($('#load_status_images'));
            });
        }

        // use copy-pastable AJAX links for directory navigation
        if (window.location.hash.substr(0, 6) == '#!dir=')
            dir = window.location.hash.substr(6);
        loadDir(dir);

        // click events on directories, assets and images
        breadcrumbs.on('click', 'a', function() {
            loadDir($(this).attr('data-dir'));
        });

        directories_assets.on('click', '.directory a', function() {
            if (typeof $(this).attr('data-dir') !== 'undefined')
                loadDir($(this).attr('data-dir'));
        });

        // deleting directories, assets or images
        $('#directories_assets').on('click', 'li.directory a.sure', function() {
            apiStatusWorking('Deleting directory...');
            var item = $(this);
            api('/' + base_url + 'api/core/assets.php', {
                action: 'delete_directory',
                name: item.attr('data-name'),
                dir: dir
            }, function() {
                apiStatusSuccess('Deleted directory');
                item.closest('li').slideUp('fast', function() { $(this).remove(); });
            }, function() {
                apiStatusError('Deleting directory failed');
            });
        });

        $('#directories_assets').on('click', 'li.asset a.sure', function() {
            apiStatusWorking('Deleting asset...');
            var item = $(this);
            api('/' + base_url + 'api/core/assets.php', {
                action: 'delete_file',
                name: item.attr('data-name'),
                dir: dir
            }, function() {
                apiStatusSuccess('Deleted asset');
                item.closest('li').slideUp('fast', function() { $(this).remove(); });
            }, function() {
                apiStatusError('Deleting asset failed');
            });
        });

        $('#images').on('click', 'a.sure', function() {
            apiStatusWorking('Deleting image...');
            var item = $(this);
            api('/' + base_url + 'api/core/assets.php', {
                action: 'delete_file',
                name: item.attr('data-name'),
                dir: dir
            }, function() {
                apiStatusSuccess('Deleted image');
                item.closest('li').slideUp('fast', function() { $(this).remove(); });
            }, function() {
                apiStatusError('Deleting image failed');
            });
        });

        $('#create_directory').on('click', 'a', function() {
            apiStatusWorking('Creating directory...');
            api('/' + base_url + 'api/core/assets.php', {
                action: 'create_directory',
                name: $(this).prev('input').val(),
                dir: dir
            }, function(data) {
                apiStatusSuccess('Created directory');
                $('#create_directory input').val('');
                var item = directory_item(data['directory']);
                if (directories_assets.find('li.directory').length)
                    addAlphabetically(directories_assets.find('li.directory'), item, data['directory']['name']);
                else
                    $(item).hide().insertAfter(directories_assets.find('li:first')).slideDown('fast');
            }, function() {
                apiStatusError('Creating directory failed');
            });
        });

        $(function() {
            initializeUpload('#upload', function(data) {
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
    });
</script>