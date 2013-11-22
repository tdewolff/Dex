            <?php if (isset($_['admin_links'])): ?></div><?php endif; ?>
        </section>

        <?php foreach ($_['footer_external_scripts'] as $external_script): ?><script type="text/javascript" src="<?php echo $external_script; ?>"></script><?php endforeach; ?>
        <?php if (isset($_['footer_script'])): ?><script type="text/javascript" src="/<?php echo $_['base_url'] . $_['footer_script']; ?>"></script><?php endif; ?>
    </body>
</html>