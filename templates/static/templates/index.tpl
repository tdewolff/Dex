<article role="main" class="main">
    <?php echo (isset($_['content']) ? $_['content'] : ''); ?>
</article>

<?php if (User::loggedIn()) { ?>
<script>
$(function() {
    $('#edit').on('click', 'a', function() {
        $('article').attr('contenteditable', 'true');
        grande.bind(document.querySelectorAll("article"));
    });

    $('#save').on('click', 'a', function() {
        apiStatusWorking('Saving page...');
        var item = $(this);
        api('/' + base_url + 'api/template/static/index/', {
            action: 'save_page',
            link_id: link_id,
            content: $('article').html()
        }, function() {
            apiStatusSuccess('Saved page <span data-time></span>');
        }, function() {
            apiStatusError('Saving page failed');
        });
    });

    $('article').on('keydown', function() {
        apiStatusClear();
    });
});
</script>
<?php } ?>