<h2>Admin panel</h2>
<div class="halfwidth-column">
  <h3>Logs</h3>
  <div id="logs_bar" class="progress_bar">
    <div class="bar <?php if ($_['logs_size_percentage'] > 80) { echo 'bar_alert'; } ?>" style="width:<?php echo ($_['logs_size_percentage'] > 100 ? '100' : $_['logs_size_percentage']); ?>%;"></div>
  </div>
  <div class="left"><?php echo $_['logs_size']; ?></div>
  <div class="text-right"><?php echo $_['logs_size_percentage']; ?>%</div>

  <div style="margin-top:10px;">
    <div style="text-align:right; float:right;">
      <?php if ($_['isAdmin']): ?>
      <span class="popbox">
        <a href="#" class="open small-alert-button"><i class="icon-trash"></i>&ensp;Clear logs</a>
        <div class="box">
          <div class="arrow"></div>
          <div class="arrow-border"></div>

          <p class="no_wrap">Do you really want to clear all log data?</p>
          <p class="centered"><a href="#" class="small-alert-button" onclick="ajax(this, 'POST', {subject: 'logs'}, function() {
              $('.popbox .box').fadeOut('fast');
              $('#logs_bar > div').css('width', '0%').removeClass('bar_alert');
              $('#logs_bar').next().text('0 B').next().text('0%');
          });">Clear</a><a href="#" class="close small-button">Cancel</a></p>
        </div>
      </span>
      <?php endif; ?>
    </div>
    <div><a href="/<?php echo $_['base_url']; ?>admin/index/logs/view/" class="small-button"><i class="icon-list-alt"></i>&ensp;View <?php echo $_['log_name_current']; ?></a></div>
  </div>
</div>

<div class="halfwidth-column">
  <h3>Cache</h3>
  <div id="cache_bar" class="progress_bar">
    <div class="bar <?php if ($_['cache_size_percentage'] > 80) { echo 'bar_alert'; } ?>" style="width:<?php echo ($_['cache_size_percentage'] > 100 ? '100' : $_['cache_size_percentage']); ?>%;"></div>
  </div>
  <div class="left"><?php echo $_['cache_size']; ?></div>
  <div class="text-right"><?php echo $_['cache_size_percentage']; ?>%</div>

  <div style="margin-top:10px;">
    <div style="text-align:right; float:right;">
      <?php if ($_['isAdmin']): ?>
      <span class="popbox">
        <a href="#" class="open small-alert-button"><i class="icon-trash"></i>&ensp;Clear cache</a>
        <div class="box">
          <div class="arrow"></div>
          <div class="arrow-border"></div>

          <p class="no_wrap">Do you really want to clear all cache data?</p>
          <p class="centered"><a href="#" class="small-alert-button" onclick="ajax(this, 'POST', {subject: 'cache'}, function() {
              $('.popbox .box').fadeOut('fast');
              $('#cache_bar > div').css('width', '0%').removeClass('bar_alert');
              $('#cache_bar').next().text('0 B').next().text('0%');
          });">Clear</a><a href="#" class="close small-button">Cancel</a></p>
        </div>
      </span>
      <?php endif; ?>
    </div>
    <div class="clear"></div>
  </div>
</div>
<div class="clear"></div>