<div class="popup-wrapper">
    <div class="popup">
        <div id="assets">
            <h2>Images</h2>
            <div id="external-link">
                <input type="text" placeholder="http://www.domain.com/"><a href="#" class="properties inline-button">Properties&ensp;<i class="fa fa-arrow-right"></i></a>
            </div>

            <form id="upload" method="post" action="/<?php echo $_['base_url']; ?>api/core/assets/" enctype="multipart/form-data">
                <input type="hidden" name="dir" value="">
                <div id="drop">
                    <span>Drop Here</span><br>
                    <a class="inline-button">Browse</a>
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
            <ul id="directories_assets" class="small-table">
                <li>
                    <div style="width:360px;">Filename</div>
                    <div style="width:100px;">Size</div>
                    <div style="width:40px;"></div>
                </li>
            </ul>

            <ul id="images" class="grid">
            </ul>
        </div>
        <div>
            <h2>Properties</h2>
            <form>
                <p><label>Title</label><input id="insert_title" type="text"></p>
                <p><label>URL</label><input id="insert_url" type="text"></p>
                <p><label>Text</label><input id="insert_text" type="text" data-tooltip="Alternative text"></p>
                <p><label>Width</label><input id="insert_width" type="text" data-tooltip="In pixels"></p>
                <p><label>Position</label><select id="insert_position"><option value="">Normal</option><option value="left">Left</option><option value="right">Right</option></select></p>
                <input id="insert_submit" type="hidden">
                <a href="#" class="insert form_button button"><i class="fa fa-check"></i>&ensp;Insert</a>
            </form>
        </div>
    </div>
</div>

<script id="directory_item" type="text/x-dot-template">
    <li data-name="{{=it.name}}" class="directory">
        <div style="width:360px;"><img src="/<?php echo $_['base_url']; ?>res/core/images/icons/{{=it.icon}}" width="16" height="16"><a href="#" data-dir="{{=it.dir}}">{{=it.name}}</a></div>
        <div style="width:100px;">-</div>
        <div style="width:40px;"></div>
    </li>
</script>

<script id="image_item" type="text/x-dot-template">
    <li data-title="{{=it.title}}" data-url="/<?php echo $_['base_url']; ?>res/assets/{{=it.url}}">
        <div class="caption"><strong>{{=it.title}}</strong></div>
        {{? it.width > 100}}
        <img src="/<?php echo $_['base_url']; ?>res/assets/{{=it.url}}?w=100"
             alt="{{=it.name}}"
             title="{{=it.title}}"
             {{=it.attr}}>
        {{??}}
        <img src="/<?php echo $_['base_url']; ?>res/assets/{{=it.url}}"
             alt="{{=it.name}}"
             title="{{=it.title}}"
             {{=it.attr}}
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
    var image_item = doT.template($('#image_item').text());

    // loading initial data
    function loadDir(dir) {
        directories_assets.find('li:not(:first)').slideUp('fast', function() { $(this).remove(); });
        images.find('li').slideUp('fast', function() { $(this).remove(); });

        api('/' + base_url + 'api/core/assets/', {
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

        api('/' + base_url + 'api/core/assets/', {
            action: 'get_directories',
            dir: dir
        }, function(data) {
            $.each(data['directories'], function() {
                $(directory_item(this)).hide().appendTo(directories_assets).slideDown('fast');
            });
        });

        api('/' + base_url + 'api/core/assets/', {
            action: 'get_assets',
            dir: dir,
            max_width: 100
        }, function(data) {
            $.each(data['assets'], function() {
                if (this.is_image)
                    $(image_item(this)).hide().appendTo(images).slideDown('fast');
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
    images.on('click', 'li', function() {
        $('#insert_title').val($(this).attr('data-title'));
        $('#insert_url').val($(this).attr('data-url'));
        switchPopupFrame(popup);
    });

    popup.on('click', '#external-link a', function() {
        $('#insert_url').val($('#external-link input').val());
        switchPopupFrame(popup);
    });

    popup.on('click', 'a.insert', function() {
        $('#insert_submit').val('1');
        parent.$.fancybox.close();
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
</script>