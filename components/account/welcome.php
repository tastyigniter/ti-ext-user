<div class="well">
    <?= $customer
        ? sprintf(lang('sampoyigi.account::default.text_logged_out'), $customer->first_name, $__SELF__.'::onLogout')
        : sprintf(lang('sampoyigi.account::default.text_logged_in'), $__SELF__->loginUrl());
    ?>
</div>