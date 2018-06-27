<p><?= lang('sampoyigi.account::default.reset.text_summary'); ?></p>

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
    <a
        class="btn btn-link btn-lg pull-left"
        href="<?= site_url('account/login'); ?>"
    ><?= lang('sampoyigi.account::default.reset.button_login'); ?></a>
    <button
        type="submit"
        class="btn btn-primary btn-lg pull-right"
    ><?= lang('sampoyigi.account::default.reset.button_reset'); ?></button>
</div>

<?= form_close(); ?>
