var total_loaded = 0, total_size = 0;
$('#big-knob input, #small-knob input').knob();

var upload_i = 0;
var done_n = 0;
$(function() {
    $('#drop a').click(function() {
        $(this).parent().find('input').click();
    });

    $('#upload').fileupload({
        dropZone: $('#upload'),
        dataType: 'json',
        sequentialUploads: true,

        add: function (e, data) {
            if (upload_i == done_n) {
                $('#upload ul li').remove();
                total_loaded = 0;
                total_size = 0;
            }
            total_size += data.files[0].size;

            data.i = upload_i;
            upload_i++;

            $('<li id="upload_' + data.i + '"><span></span>&ensp;' + data.files[0].name + '</li>').hide().appendTo('#upload ul').slideDown();
            var jqXHR = data.submit();
        },
        progress: function(e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            var bigProgress = (total_size > 0 ? parseInt((total_loaded + data.loaded) / total_size * 100, 10) : 100);
            $('#small-knob input').val(progress).change();
            $('#big-knob input').val(bigProgress).change();
        },
        always: function(e, data) {
            console.log(data);
            console.log(data.response());
            total_loaded += data.total;
            done_n++;
        },
        done: function(e, data) {
            $('#upload_' + data.i).addClass('done');
            if (data.response().result.isImage) {
                if (data.response().result.width > 200) {
                    $('<li id="' + data.response().result.name + '" class="no_wrap centered vertical_top">\
                        <div class="assets_caption">\
                            <strong>' + data.response().result.title + '</strong>&nbsp;\
                            <a href="#" class="halt"><i class="icon-fixed-width icon-trash"></i></a>\
                            <a href="#" class="sure" onclick="ajax(this, \'POST\', {asset_name: \'' + data.response().result.name + '\'}, function() {\
                                hideRow(\'' + data.response().result.name + '\');\
                            });"><i class="icon-fixed-width icon-question"></i></a>\
                        </div>\
                        <a href="' + data.response().result.url + '" data-fancybox-group="gallery" class="fancybox">\
                            <img src="' + data.response().result.url + '?w=200"\
                                 alt=""\
                                 title="' + data.response().result.title + '"\
                                 class="assets_image"\
                                 ' + data.response().result.widthAttr + '>\
                        </a>\
                    </li>').hide().appendTo('ul.table').fadeIn();
                } else {
                    $('<li id="' + data.response().result.name + '" class="no_wrap centered vertical_top">\
                        <div class="assets_caption">\
                            <strong>' + data.response().result.title + '</strong>&nbsp;\
                            <a href="#" class="halt"><i class="icon-fixed-width icon-trash"></i></a>\
                            <a href="#" class="sure" onclick="ajax(this, \'POST\', {asset_name: \'' + data.response().result.name + '\'}, function() {\
                                hideRow(\'' + data.response().result.name + '\');\
                            });"><i class="icon-fixed-width icon-question"></i></a>\
                        </div>\
                        <a href="' + data.response().result.url + '">\
                            <img src="' + data.response().result.url + '"\
                                 alt=""\
                                 title="' + data.response().result.title + '"\
                                 class="assets_image_small">\
                        </a>\
                    </li>').hide().appendTo('ul.table').fadeIn();
                }
            } else {
                $('<li id="' + data.response().result.name + '">\
                    <div style="width:460px;"><img src="' + data.response().result.icon + '" class="assets_icon" width="16" height="16">' + data.response().result.title + '</div>\
                    <div style="width:100px;">' + data.response().result.size + '</div>\
                    <div style="width:40px;">\
                        <a href="#" class="halt"><i class="icon-fixed-width icon-trash"></i></a>\
                        <a href="#" class="sure" onclick="ajax(this, \'POST\', {asset_name: \'' + data.response().result.name + '\'}, function() {\
                            hideRow(\'' + data.response().result.name + '\');\
                        });"><i class="icon-fixed-width icon-question"></i></a>\
                    </div>\
                </li>').hide().appendTo('ul.table').slideDown();
            }
            updateHalt();
        },
        fail: function(e, data) {
            $('#upload_' + data.i).addClass('fail');
        }
    });

    $(document).on('drop dragover', function (e) {
        e.preventDefault();
    });

    function formatFileSize(bytes) {
        if (typeof bytes !== 'number')
            return '';
        if (bytes >= 1000000000)
            return (bytes / 1000000000).toFixed(2) + ' GB';
        if (bytes >= 1000000)
            return (bytes / 1000000).toFixed(2) + ' MB';
        return (bytes / 1000).toFixed(2) + ' KB';
    }
});