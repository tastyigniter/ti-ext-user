<p class="text-center"><?= lang('sampoyigi.account::default.reset.text_summary'); ?></p>

<?= form_open(current_url(),
    [
        'role'         => 'form',
        'method'       => 'POST',
        'data-request' => $__SELF__.'::onForgotPassword',
    ]
); ?>
<div class="form-group">
    <input
        name="email"
        type="text"
        id="email"
        class="form-control input-lg"
        value="<?= set_value('email'); ?>"
        placeholder="<?= lang('sampoyigi.account::default.reset.label_email'); ?>"
    />
    <?= form_error('email', '<span class="text-danger">', '</span>'); ?>
</div>

<div class="clearfix">
    <button
        type="submit"
        class="btn btn-primary btn-lg pull-left"
    ><?= lang('sampoyigi.account::default.reset.button_reset'); ?></button>
    <a
        class="btn btn-default btn-lg pull-right"
        href="<?= site_url('account/login'); ?>"
    ><?= lang('sampoyigi.account::default.reset.button_login'); ?></a>
</div>

<?= form_close(); ?>
