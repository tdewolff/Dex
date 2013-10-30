<h2>Menu</h2>

<div class="response">
    <span class="loading"></span>
    <span class="success passed_time">(saved <span></span>)</span>
    <span class="error">(not saved)</span>
</div>

<ul class="table draggable">
  <li>
    <div style="width:120px;">&nbsp;</div>
    <div style="width:200px;">Name</div>
    <div style="width:120px;">Title</div>
    <div style="width:460px;">Link</div>
  </li>
  <?php foreach ($_['menu'] as $id => $item): ?>
  <li data-link-id="<?php echo $item['link_id']; ?>" data-level="<?php echo $item['level']; ?>">
    <div style="width:120px;"><i class="icon-long-arrow-right"></i><i class="icon-long-arrow-right"></i><i class="icon-reorder"></i></div>
    <div style="width:200px;"><span><?php echo $item['name']; ?></span><input type="text" value="<?php echo $item['name']; ?>" onkeyup="editingName(this);" /></div>
    <div style="width:120px;"><?php echo Common::tryOrDefault($item, 'title', '&nbsp;'); ?></div>
    <div style="width:460px;">/<?php echo $_['base_url'] . $item['url']; ?></div>
  </li>
  <?php endforeach; ?>

  <li class="empty"></li>
  <li class="border"></li>
  <li class="empty unused"></li>

  <?php foreach ($_['non_menu'] as $id => $item): ?>
  <li data-link-id="<?php echo $item['link_id']; ?>" data-level="0" class="unused">
    <div style="width:120px;"><i class="icon-long-arrow-right"></i><i class="icon-long-arrow-right"></i><i class="icon-reorder"></i></div>
    <div style="width:200px;"><span><?php echo $item['title']; ?></span><input type="text" value="<?php echo $item['title']; ?>" onkeyup="editingName(this);" /></div>
    <div style="width:120px;"><?php echo Common::tryOrDefault($item, 'title', '&nbsp;'); ?></div>
    <div style="width:460px;">/<?php echo $_['base_url'] . $item['url']; ?></div>
  </li>
  <?php endforeach; ?>
</ul>