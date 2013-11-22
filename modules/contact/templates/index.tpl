<?php if (count($_['contact'])): ?>

<address class="vcard contact" role="contentinfo">
    <div class="fn"><?php echo (isset($_['contact']['fn']) ? $_['contact']['fn'] : ''); ?></div>

    <?php if (isset($_['contact']['url']) && isset($_['contact']['org'])): ?>
    <div><a class="org url organization-name" href="<?php echo $_['contact']['url']; ?>" title="<?php echo $_['contact']['org']; ?>">
        <?php echo $_['contact']['org']; ?>
    </a></div>
    <?php elseif (isset($_['contact']['url'])): ?>
    <div><a class="url" href="<?php echo $_['contact']['url']; ?>"><?php echo $_['contact']['url']; ?></a></div>
    <?php elseif (isset($_['contact']['org'])): ?>
    <div class="org organization-name"><?php echo $_['contact']['org']; ?></div>
    <?php endif; ?>

    <?php if (isset($_['contact']['tel'])): ?>
    <div><a class="tel" href="tel:<?php echo $_['contact']['tel']; ?>"><?php echo $_['contact']['tel']; ?></a></div>
    <?php endif; ?>

    <?php if (isset($_['contact']['email'])): ?>
    <div><a class="email" href="mailto:<?php echo $_['contact']['email']; ?>"><?php echo $_['contact']['email']; ?></a></div>
    <?php endif; ?>

    <?php if (isset($_['contact']['adr'])): $adr = $_['contact']['adr']; ?>
    <div class="adr">
        <?php if (isset($adr['street-address'])): ?><div class="street-address"><?php echo $adr['street-address']; ?></div><?php endif; ?>
        <?php if (isset($adr['locality'])): ?><span class="locality"><?php echo $adr['locality']; ?></span>,<?php endif; ?>
        <?php if (isset($adr['region'])): ?><abbr class="region"><?php echo $adr['region']; ?></abbr>,<?php endif; ?>
        <?php if (isset($adr['postal-code'])): ?><span class="postal-code"><?php echo $adr['postal-code']; ?></span><?php endif; ?>
        <?php if (isset($adr['country-name'])): ?><div class="country-name"><?php echo $adr['country-name']; ?></div><?php endif; ?>
    </div>
    <?php endif; ?>
</address>

<?php endif; ?>