<?php if ($__SELF__->resetCode()) { ?>
    <?= partial('@reset'); ?>
<?php } else { ?>
    <?= partial('@forgot'); ?>
<?php } ?>
