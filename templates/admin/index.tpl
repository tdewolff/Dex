<h1>Admin panel</h1>
<div class="halfwidth-column">
  <h2>Logs</h2>
  <div class="progress_bar">
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
          <p class="centered"><a href="/<?php echo $_['base_url']; ?>admin/index/logs/clear/" class="small-alert-button">Clear</a><a href="#" class="close small-button">Cancel</a></p>
        </div>
      </span>
      <?php endif; ?>
    </div>
    <div><a href="/<?php echo $_['base_url']; ?>admin/index/logs/view/" class="small-button"><i class="icon-list-alt"></i>&ensp;View <?php echo $_['log_name_current']; ?></a></div>
  </div>
</div>

<div class="halfwidth-column">
  <h2>Cache</h2>
  <div class="progress_bar">
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
          <p class="centered"><a href="/<?php echo $_['base_url']; ?>admin/index/cache/clear/" class="small-alert-button">Clear</a><a href="#" class="close small-button">Cancel</a></p>
        </div>
      </span>
      <?php endif; ?>
    </div>
    <div class="clear"></div>
  </div>
</div>
<div class="clear"></div>