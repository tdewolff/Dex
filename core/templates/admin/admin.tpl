<h2>Administration</h2>
<h3>Logs</h3>
<div id="logs_bar" class="progress_bar">
    <div class="bar <?php if ($_['logs_size_percentage'] > 80) { echo 'bar_alert'; } ?>" style="width:<?php echo ($_['logs_size_percentage'] > 100 ? '100' : $_['logs_size_percentage']); ?>%;"></div>
</div>
<div class="left"><?php echo $_['logs_size']; ?></div>
<div class="text-right">
    <?php echo $_['logs_size_percentage']; ?>%
    <?php if ($_['role'] == 'admin'): ?>
    <a href="#" class="halt inline-rounded"><i class="fa fa-trash-o"></i></a>
    <a href="#" class="sure inline-rounded" data-tooltip="Click to confirm" data-action="clear_logs"><i class="fa fa-trash-o"></i></a>
    <?php endif; ?>
</div>

<div style="margin-top:10px;">
    <a href="/<?php echo $_['base_url']; ?>admin/logs/" class="small-button"><i class="fa fa-list-alt"></i>&ensp;View <?php echo $_['log_name']; ?></a>
</div>

<h3>Cache</h3>
<div id="cache_bar" class="progress_bar">
    <div class="bar <?php if ($_['cache_size_percentage'] > 80) { echo 'bar_alert'; } ?>" style="width:<?php echo ($_['cache_size_percentage'] > 100 ? '100' : $_['cache_size_percentage']); ?>%;"></div>
</div>
<div class="left"><?php echo $_['cache_size']; ?></div>
<div class="text-right">
    <?php echo $_['cache_size_percentage']; ?>%
    <?php if ($_['role'] == 'admin'): ?>
    <a href="#" class="halt inline-rounded"><i class="fa fa-trash-o"></i></a>
    <a href="#" class="sure inline-rounded" data-tooltip="Click to confirm" data-action="clear_cache"><i class="fa fa-trash-o"></i></a>
    <?php endif; ?>
</div>

<script type="text/javascript">
    function resetBar(bar) {
        bar.find('div').css('width', '0%').removeClass('bar_alert');
        bar.next().text('0 B').next().text('0%');
    }

    $('a[data-action]').click(function() {
        var action = $(this).attr('data-action');
        if (action == 'clear_logs') {
            apiStatusWorking('Clearing logs...');
            api('/' + base_url + 'api/core/index/', {
                action: action
            }, function(data) {
                apiStatusSuccess('Cleared logs');
                resetBar($('#logs_bar'));
            }, function() {
                apiStatusError('Clearing log failed');
            });
        } else if (action == 'clear_cache') {
            apiStatusWorking('Clearing cache...');
            api('/' + base_url + 'api/core/index/', {
                action: action
            }, function(data) {
                apiStatusSuccess('Cleared cache');
                resetBar($('#cache_bar'));
            }, function() {
                apiStatusError('Clearing cache failed');
            });
        }
    });
</script>