<a href="/<?php echo $_['base_url']; ?>admin/pages/" style="float:left;" class="button"><i class="icon-chevron-left"></i>&ensp;Back</a>
<?php if (isset($_['view'])): ?><a href="/<?php echo $_['base_url'] . $_['view']; ?>" style="float:right;" class="button"><i class="icon-eye-open"></i>&ensp;View page</a><?php endif; ?>
<h2>Page</h2>
<?php $_['page']->render(); ?>

<script type="text/javascript">
    var use_feed = true;
    $('#url-feed').on('keyup', function() {
        if (use_feed) {
            var link_url = $(this).val().toLowerCase().replace(/\s/, '-').replace(/[^a-z0-9\-_]+/, '');
            $('#url').val(link_url + '/');
        }
    });

    $('#url').on('keyup', function() {
        use_feed = false;
    });

    $('#url').on('blur', function() {
        var link_url = $(this).val();
        if (link_url.length && link_url[link_url.length - 1] != '/')
            $(this).val(link_url + '/');
    });
</script>