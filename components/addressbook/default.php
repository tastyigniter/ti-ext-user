<div id="address-book">
    <?php if ($addressIdParam) { ?>
        <?= partial('@form') ?>
    <?php } else { ?>
        <?= partial('@list') ?>
    <?php } ?>
</div>
