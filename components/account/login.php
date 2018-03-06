<?= form_open(current_url(),
    [
        'role' => 'form',
        'method' => 'POST',
        'data-request' => 'account::onLogin',
    ]
); ?>

<div class="form-group">
    <div class="input-group">
        <input
            type="text"
            name="email"
            id="login-email"
            class="form-control input-lg"
            placeholder="<?= lang('main::account.label_email'); ?>"
            autofocus="" required/>
        <span class="input-group-addon">@</span>
    </div>
    <?= form_error('email', '<span class="text-danger">', '</span>'); ?>
</div>

<div class="form-group">
    <div class="input-group">
        <input
            type="password"
            name="password"
            id="login-password"
            class="form-control input-lg"
            placeholder="<?= lang('main::account.label_password'); ?>" required/>
        <span class="input-group-addon"><i class="fa fa-lock"></i></span>
    </div>
    <?= form_error('password', '<span class="text-danger">', '</span>'); ?>
</div>

<div class="form-group">
    <span class="button-checkbox">
        <button id="remember"
                type="button"
                class="btn"
                data-color="default"
                tabindex="7">
            &nbsp;&nbsp;<?= lang('main::account.login.text_remember'); ?>
        </button>
        <input type="checkbox"
               name="remember"
               class="hidden"
               value="1" <?= set_checkbox('remember', '1'); ?>>
    </span>
</div>

<div class="form-group">
    <div class="row">
        <div class="col-md-12">
            <button
                type="submit"
                class="btn btn-primary btn-block btn-lg"
            ><?= lang('main::account.login.button_login'); ?></button>
        </div>
    </div>
</div>

<?= form_close(); ?>
