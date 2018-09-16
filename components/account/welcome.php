<?= $customer
    ? sprintf(lang('igniter.user::default.text_logged_out'), $customer->first_name, $__SELF__.'::onLogout')
    : sprintf(lang('igniter.user::default.text_logged_in'), $__SELF__->loginUrl());
?>
