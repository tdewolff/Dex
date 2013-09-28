<h1>Admin panel</h1>
<div class="halfwidth-column">
  <h2>Logs</h2>
  <div class="progress_bar">
    <div class="bar {if $logs_size_percentage > 80}bar_alert{/if}" style="width:{if $logs_size_percentage > 100}100{else}{$logs_size_percentage}{/if}%;"></div>
  </div>
  <div class="left">{$logs_size}</div>
  <div class="text-right">{$logs_size_percentage}%</div>

  <div style="margin-top:10px;">
    <div style="text-align:right; float:right;">
      {if $isAdmin}
      <span class="popbox">
        <a href="#" class="open small-alert-button"><i class="icon-trash"></i>&ensp;Clear logs</a>
        <div class="box">
          <div class="arrow"></div>
          <div class="arrow-border"></div>

          <p class="no_wrap">Do you really want to clear all log data?</p>
          <p class="centered"><a href="{$base_url}admin/index/logs/clear/" class="small-alert-button">Clear</a><a href="#" class="close small-button">Cancel</a></p>
        </div>
      </span>
      {/if}
    </div>
    <div><a href="{$base_url}admin/index/logs/view/" class="small-button"><i class="icon-list-alt"></i>&ensp;View {$log_name_current}</a></div>
  </div>
</div>

<div class="halfwidth-column">
  <h2>Cache</h2>
  <div class="progress_bar">
    <div class="bar {if $cache_size_percentage > 80}bar_alert{/if}" style="width:{if $cache_size_percentage > 100}100{else}{$cache_size_percentage}{/if}%;"></div>
  </div>
  <div class="left">{$cache_size}</div>
  <div class="text-right">{$cache_size_percentage}%</div>

  <div style="margin-top:10px;">
    <div style="text-align:right; float:right;">
      {if $isAdmin}
      <span class="popbox">
        <a href="#" class="open small-alert-button"><i class="icon-trash"></i>&ensp;Clear cache</a>
        <div class="box">
          <div class="arrow"></div>
          <div class="arrow-border"></div>

          <p class="no_wrap">Do you really want to clear all cache data?</p>
          <p class="centered"><a href="{$base_url}admin/index/cache/clear/" class="small-alert-button">Clear</a><a href="#" class="close small-button">Cancel</a></p>
        </div>
      </span>
      {/if}
    </div>
    <div class="clear"></div>
  </div>
</div>
<div class="clear"></div>