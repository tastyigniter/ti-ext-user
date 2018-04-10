<?= form_open(current_url(),
    [
        'role'         => 'form',
        'method'       => 'POST',
        'data-request' => 'account::onRegister',
    ]
); ?>

<div class="row">
    <div class="col-xs-12 col-sm-6 col-md-6">
        <div class="form-group">
            <input type="text"
                   id="first-name"
                   class="form-control input-lg"
                   value="<?= set_value('first_name'); ?>"
                   name="first_name"
                   placeholder="<?= lang('sampoyigi.account::default.settings.label_first_name'); ?>"
                   autofocus="">
            <?= form_error('first_name', '<span class="text-danger">', '</span>'); ?>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6">
        <div class="form-group">
            <input type="text"
                   id="last-name"
                   class="form-control input-lg"
                   value="<?= set_value('last_name'); ?>"
                   name="last_name"
                   placeholder="<?= lang('sampoyigi.account::default.settings.label_last_name'); ?>">
            <?= form_error('last_name', '<span class="text-danger">', '</span>'); ?>
        </div>
    </div>
</div>
<div class="form-group">
    <input type="text"
           id="email"
           class="form-control input-lg"
           value="<?= set_value('email'); ?>"
           name="email"
           placeholder="<?= lang('sampoyigi.account::default.settings.label_email'); ?>">
    <?= form_error('email', '<span class="text-danger">', '</span>'); ?>
</div>
<div class="row">
    <div class="col-xs-12 col-sm-6 col-md-6">
        <div class="form-group">
            <input type="password"
                   id="password"
                   class="form-control input-lg"
                   value=""
                   name="password"
                   placeholder="<?= lang('sampoyigi.account::default.login.label_password'); ?>">
            <?= form_error('password', '<span class="text-danger">', '</span>'); ?>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6">
        <div class="form-group">
            <input type="password"
                   id="password-confirm"
                   class="form-control input-lg"
                   name="password_confirm"
                   value=""
                   placeholder="<?= lang('sampoyigi.account::default.login.label_password_confirm'); ?>">
            <?= form_error('password_confirm', '<span class="text-danger">', '</span>'); ?>
        </div>
    </div>
</div>

<div class="form-group">
    <input
        type="text"
        id="telephone"
        class="form-control input-lg"
        value="<?= set_value('telephone'); ?>"
        name="telephone"
        placeholder="<?= lang('sampoyigi.account::default.settings.label_telephone'); ?>"
    >
    <?= form_error('telephone', '<span class="text-danger">', '</span>'); ?>
</div>

<div class="form-group">
<span class="button-checkbox">
    <button
        id="newsletter"
        type="button"
        class="btn"
        data-color="info"
        tabindex="7"
    >&nbsp;&nbsp;<?= lang('sampoyigi.account::default.login.button_subscribe'); ?></button>
    <input
        type="checkbox"
        name="newsletter"
        class="hidden"
        value="1" <?= set_checkbox('newsletter', '1'); ?>
    >
</span>
    <?= lang('sampoyigi.account::default.login.label_newsletter'); ?>
    <?= form_error('newsletter', '<span class="text-danger">', '</span>'); ?>
</div>

<?php if ($requireRegistrationTerms) { ?>
    <div class="form-group">
            <span class="button-checkbox">
                <button
                    id="terms-condition"
                    type="button"
                    class="btn"
                    data-color="info"
                    tabindex="7"
                >&nbsp;&nbsp;<?= lang('sampoyigi.account::default.login.button_terms_agree'); ?></button>
                <input
                    type="checkbox"
                    name="terms"
                    class="hidden"
                    value="1" <?= set_checkbox('terms', '1'); ?>
                >
            </span>
        <?= sprintf(lang('sampoyigi.account::default.login.label_terms'), $account->getRegistrationTermsUrl()); ?>
        <?= form_error('terms', '<span class="text-danger">', '</span>'); ?>
    </div>
    <div class="modal fade"
         id="terms-modal"
         tabindex="-1"
         role="dialog"
         aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<div class="row">
    <div class="col-xs-12 col-md-6">
        <button
            type="submit"
            class="btn btn-primary btn-block btn-lg"
        ><?= lang('sampoyigi.account::default.login.button_register'); ?></button>
    </div>
    <div class="col-xs-12 col-md-6">
        <a
            href="<?= site_url('account/login'); ?>"
            class="btn btn-default btn-block btn-lg"
        ><?= lang('sampoyigi.account::default.login.button_login'); ?></a>
    </div>
</div>
<?= form_close(); ?>
