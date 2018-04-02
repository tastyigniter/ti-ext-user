<?php if ($saleIdParam) { ?>
    <?= partial('@form') ?>
<?php } else { ?>
    <?= partial('@list') ?>
<?php } ?>
