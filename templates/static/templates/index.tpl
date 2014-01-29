<?php if (User::getTimeLeft() !== false) { ?>
    <div id="edit"><a href="#edit"><i class="fa fa-fw fa-edit"></i>&ensp;Edit</a></div>
    <div id="save"><a href="#"><i class="fa fa-fw fa-save"></i>&ensp;Save</a></div>
<?php } ?>
<article role="main" class="main">
    <?php echo (isset($_['content']) ? $_['content'] : ''); ?>
</article>
