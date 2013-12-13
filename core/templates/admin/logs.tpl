<h2>Logs</h2>
<a href="/<?php echo $_['base_url']; ?>admin/" class="button" style="margin-left:20px;"><i class="fa fa-chevron-left"></i> Back</a>
<ul id="logs" class="table">
    <li>
        <div style="width:150px;">Date time</div>
        <div style="width:120px;">IP Address</div>
        <div style="width:630px;">Message</div>
    </li>
</ul>

<script id="log_item" type="text/x-dot-template">
    <li>
        <div style="width:150px;">{{=it.datetime}}</div>
        <div style="width:120px;">{{=it.ipaddress}}</div>
        <div style="width:630px;" title="{{=it.message}}">{{=it.message}}</div>
    </li>
</script>

<script type="text/javascript">
    var logs = $('#logs');
    var log_item = doT.template($('#log_item').text());
    api('/' + base_url + 'api/core/index.php', {
        action: 'get_logs'
    }, function(data) {
        $.each(data['logs'], function() {
            var item = $(log_item(this));
            if (this['type'] == 'ERROR')
                item = item.addClass('error');
            else if (this['type'] == 'WARNING')
                item = item.addClass('warning');
            else if (this['type'] == 'NOTICE')
                item = item.addClass('notice');
            else if (this['type'] == 'REQUEST')
                item = item.addClass('request');
            else if (this['type'] == 'CACHING')
                item = item.addClass('caching');
            else
                item = item.addClass('empty');
            logs.append(item);
        });
    });
</script>