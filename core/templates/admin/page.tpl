<a href="/<?php echo $_['base_url']; ?>admin/pages/" style="float:left;" class="button"><i class="icon-chevron-left"></i>&ensp;Back</a>
<?php if (isset($_['view'])): ?><a href="/<?php echo $_['base_url'] . $_['view']; ?>" style="float:right;" class="button"><i class="icon-eye-open"></i>&ensp;View page</a><?php endif; ?>
<h2>Page</h2>
<?php $_['page']->renderForm(); ?>