<h2>Menu</h2>

<div class="response">
    <span class="loading"></span>
    <span class="success passed_time">(saved<span></span>)</span>
    <span class="error">(not saved)</span>
</div>

<ul class="table draggable">
  <li>
    <div style="width:40px;">&nbsp;</div>
    <div style="width:120px;">&nbsp;</div>
    <div style="width:240px;">Name</div>
    <div style="width:120px;">Title</div>
    <div style="width:380px;">Link</div>
  </li>
  <?php foreach ($_['menu'] as $item): ?>
  <li data-link-id="<?php echo $item['link_id']; ?>" data-level="<?php echo $item['level']; ?>" <?php if (!$item['enabled']) { echo 'class="unused"'; } ?>>
    <div style="width:40px;"><i class="icon-eye-open"></i></div>
    <div style="width:120px;"><i class="icon-long-arrow-right"></i><i class="icon-long-arrow-right"></i><i class="icon-reorder"></i></div>
    <div style="width:240px;"><input type="text" value="<?php echo $item['name']; ?>" onkeyup="editingName(this);" <?php if (!$item['enabled']) { echo 'class="unused"'; } ?> /></div>
    <div style="width:120px;"><?php echo Common::tryOrDefault($item, 'title', '&nbsp;'); ?></div>
    <div style="width:380px;"><a href="/<?php echo $_['base_url'] . $item['url']; ?>">/<?php echo $_['base_url'] . $item['url']; ?></a></div>
  </li>
  <?php endforeach; ?>
</ul>