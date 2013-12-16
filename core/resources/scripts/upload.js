function initializeUpload(upload, done) {
    upload = $(upload);

    var total_loaded = 0, total_size = 0;
    upload.find('#big-knob input, #small-knob input').knob();

    var upload_i = 0;
    var done_n = 0;
    upload.find('#drop a').click(function() {
        $(this).parent().find('input').click();
    });

    upload.fileupload({
        dropZone: upload,
        dataType: 'json',
        sequentialUploads: true,

        add: function (e, data) {
            if (upload_i == done_n) {
                upload.find('ul li').remove();
                total_loaded = 0;
                total_size = 0;
            }
            total_size += data.files[0].size;

            data.i = upload_i;
            upload_i++;

            $('<li id="upload_' + data.i + '"><i class="fa fa-fw fa-cog fa-spin"></i>&ensp;' + data.files[0].name + '</li>').hide().appendTo('#upload ul').slideDown();
            var jqXHR = data.submit();
        },
        progress: function(e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            var bigProgress = (total_size > 0 ? parseInt((total_loaded + data.loaded) / total_size * 100, 10) : 100);
            upload.find('#small-knob input').val(progress).change();
            upload.find('#big-knob input').val(bigProgress).change();
        },
        always: function(e, data) {
            total_loaded += data.total;
            done_n++;
        },
        done: function(e, data) {
            if (typeof data.response().result['upload_error'] !== 'undefined')
                upload.find('#upload_' + data.i).addClass('fail').append(' (' + data.response().result['upload_error'] + ')').find('i').attr('class', 'fa fa-fw fa-times');
            else {
                upload.find('#upload_' + data.i).addClass('done').find('i').attr('class', 'fa fa-fw fa-check');
                done(data.response().result);
            }
        },
        fail: function(e, data) {
            if (typeof data.response().jqXHR['responseText'] !== 'undefined')
                apiFatal(data.response().jqXHR['responseText']);
            upload.find('#upload_' + data.i).addClass('fail').append(' (Unknown error)').find('i').attr('class', 'fa fa-fw fa-times');
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
}