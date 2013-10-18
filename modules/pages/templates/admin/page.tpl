<a href="/<?php echo $_['base_url']; ?>admin/module/pages/" style="float:left;"><i class="icon-chevron-left"></i>&ensp;Back</a>
<?php if (isset($_['view'])): ?><a href="/<?php echo $_['base_url'] . $_['view']; ?>" style="float:right;"><i class="icon-eye-open"></i>&ensp;View page</a><?php endif; ?>
<h2>Edit page</h2>
<?php $_['page']->renderForm(); ?>