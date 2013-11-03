<h2>Assets</h2>

<form id="upload" method="post" action="/<?php echo $_['base_url']; ?>admin/assets/" enctype="multipart/form-data">
    <div id="drop">
        <span>Drop Here</span><br>
        <a class="small-button">Browse</a>
        <input type="file" name="upload" multiple>
        <div id="knob">
            <div id="big-knob"><input type="text" value="0" data-width="64" data-height="64" data-thickness=".23" data-fgColor="#477725" data-readOnly="1" data-displayInput=false data-bgColor="#FFFFFF"></div>
            <div id="small-knob"><input type="text" value="0" data-width="48" data-height="48" data-thickness=".25" data-fgColor="#477725" data-readOnly="1" data-displayInput=false data-bgColor="#F0F0F0"></div>
        </div>
    </div>
    <ul></ul>
</form>

<ul class="table" style="width:602px; margin-bottom:30px;">
    <li>
        <div style="width:460px;">Filename</div>
        <div style="width:100px;">Size</div>
        <div style="width:40px;">&nbsp;</div>
    </li>
    <?php foreach ($_['directories'] as $i => $item): ?>
    <li>
        <div style="width:460px;"><img src="/<?php echo $_['base_url'] . 'res/core/images/icons/' . $item['icon']; ?>" class="assets_icon" width="16" height="16"><a href="/<?php echo $_['base_url'] . 'admin/assets/' . $item['url']; ?>"><?php echo $item['title']; ?></a></div>
        <div style="width:100px;">-</div>
        <div style="width:40px;">&nbsp;</div>
    </li>
    <?php endforeach; ?>
    <?php foreach ($_['assets'] as $i => $item): ?>
    <li id="<?php echo $item['name']; ?>">
        <div style="width:460px;"><img src="/<?php echo $_['base_url'] . 'res/core/images/icons/' . $item['icon']; ?>" class="assets_icon" width="16" height="16"><?php echo $item['title']; ?></div>
        <div style="width:100px;"><?php echo $item['size']; ?></div>
        <div style="width:40px;">
            <a href="#" class="halt"><i class="icon-fixed-width icon-trash"></i></a>
            <a href="#" class="sure" onclick="ajax(this, 'POST', {asset_name: '<?php echo $item['name']; ?>'}, function() {
                hideRow('<?php echo $item['name']; ?>');
            });"><i class="icon-fixed-width icon-question"></i></a>
        </div>
    </li>
    <?php endforeach; ?>
</ul>

<ul class="grid">
    <?php foreach ($_['images'] as $i => $item): ?>
    <li id="<?php echo $item['name']; ?>" class="no_wrap centered vertical_top">
        <div class="assets_caption">
            <strong><?php echo $item['title']; ?></strong>&nbsp;
            <a href="#" class="halt"><i class="icon-fixed-width icon-trash"></i></a>
            <a href="#" class="sure" onclick="ajax(this, 'POST', {asset_name: '<?php echo $item['name']; ?>'}, function() {
                hideRow('<?php echo $item['name']; ?>');
            });"><i class="icon-fixed-width icon-question"></i></a>
        </div>
        <?php if ($item['width'] > 200): ?>
        <a href="/<?php echo $_['base_url'] . $item['url']; ?>" data-fancybox-group="gallery" class="fancybox">
            <img src="/<?php echo $_['base_url'] . $item['url']; ?>?w=200"
                 alt=""
                 title="<?php echo $item['title']; ?>"
                 class="assets_image"
                 <?php echo Common::imageSizeAttributes($item['url'], 200); ?>>
        </a>
        <?php else: ?>
        <img src="/<?php echo $_['base_url'] . $item['url']; ?>"
             alt=""
             title="<?php echo $item['title']; ?>"
             class="assets_image_small">
        <?php endif; ?>

    </li>
    <?php endforeach; ?>
</ul>