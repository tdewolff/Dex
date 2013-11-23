<div class="popup-wrapper">
    <div class="popup">
        <div id="assets">
            <h2>Assets</h2>
            <div id="breadcrumbs">
            </div>
            <ul id="directories_assets" class="small-table">
                <li>
                    <div style="width:400px;">Filename</div>
                    <div style="width:100px;">Size</div>
                </li>
            </ul>
        </div>
        <div>
            <h2>Properties</h2>
            <p><label class="empty_small">Title</label> <input id="insert_title" type="text"></p>
            <p><label class="empty_small">URL</label> <input id="insert_url" type="text"></p>
            <p><label>Text <span class="small">Clickable text</span></label><input id="insert_text" type="text"></p>
            <input id="insert_submit" type="hidden">
            <a href="#" class="insert form_button button"><i class="icon-check"></i>&ensp;Insert</a>
        </div>
    </div>
</div>

<script id="directory_item" type="text/x-dot-template">
    <li data-name="{{=it.name}}" class="directory">
        <div style="width:400px;"><img src="/<?php echo $_['base_url']; ?>res/core/images/icons/{{=it.icon}}" width="16" height="16"><a href="#" data-dir="{{=it.dir}}">{{=it.name}}</a></div>
        <div style="width:100px;">-</div>
    </li>
</script>

<script id="asset_item" type="text/x-dot-template">
    <li  data-title="{{=it.title}}" data-url="/<?php echo $_['base_url']; ?>{{=it.url}}" class="asset">
        <div style="width:400px;"><img src="/<?php echo $_['base_url']; ?>res/core/images/icons/{{=it.icon}}" width="16" height="16">{{=it.title}}</div>
        <div style="width:100px;">{{=it.size}}</div>
    </li>
</script>

<script type="text/javascript">
    // preliminaries
    var breadcrumbs = $('#breadcrumbs');
    var directories_assets = $('#directories_assets');

    var directory_item = doT.template($('#directory_item').text());
    var asset_item = doT.template($('#asset_item').text());

    // loading initial data
    function loadDir(dir) {
        directories_assets.find('li:not(:first)').slideUp('fast', function() { $(this).remove(); });

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
                if (!this.is_image)
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

    var popup = $('.popup');
    directories_assets.on('click', '.asset', function() {
        $('#insert_title').val($(this).attr('data-title'));
        $('#insert_url').val($(this).attr('data-url'));
        popup.animate({
            'margin-left': '-600px'
        });
    });

    popup.on('click', 'a.insert', function() {
        $('#insert_submit').val('1');
        parent.$.fancybox.close();
    });
</script>