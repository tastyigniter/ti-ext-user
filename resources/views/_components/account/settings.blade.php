<form
    role="form"
    method="POST"
    accept-charset="utf-8"
    action="{{ current_url() }}"
    data-request="{{ $__SELF__.'::onUpdate' }}"
>
    <div class="form-row">
        <div class="col col-sm-6">
            <div class="form-group">
                <input
                    type="text"
                    class="form-control"
                    value="{{ set_value('first_name', $customer->first_name) }}"
                    name="first_name"
                    placeholder="@lang('igniter.user::default.settings.label_first_name')"
                >
                {!! form_error('first_name', '<span class="text-danger">', '</span>') !!}
            </div>
        </div>
        <div class="col col-sm-6">
            <div class="form-group">
                <input
                    type="text"
                    class="form-control"
                    value="{{ set_value('last_name', $customer->last_name) }}"
                    name="last_name"
                    placeholder="@lang('igniter.user::default.settings.label_last_name')"
                >
                {!! form_error('last_name', '<span class="text-danger">', '</span>') !!}
            </div>
        </div>
    </div>
    <div class="form-row">
        <div class="col col-sm-6">
            <div class="form-group">
                <input
                    type="text"
                    class="form-control"
                    value="{{ set_value('telephone', $customer->telephone) }}"
                    name="telephone"
                    placeholder="@lang('igniter.user::default.settings.label_telephone')"
                >
                {!! form_error('telephone', '<span class="text-danger">', '</span>') !!}
            </div>
        </div>
        <div class="col col-sm-6">
            <div class="form-group">
                <input
                    type="text"
                    class="form-control"
                    value="{{ set_value('email', $customer->email) }}"
                    name="email"
                    placeholder="@lang('igniter.user::default.settings.label_email')"
                    disabled
                >
                {!! form_error('email', '<span class="text-danger">', '</span>') !!}
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="custom-control custom-checkbox">
            <input
                type="checkbox"
                name="newsletter"
                id="newsletter"
                class="custom-control-input"
                value="1"
                {!! set_checkbox('newsletter', '1', (bool)$customer->newsletter) !!}
            >
            <label for="newsletter" class="custom-control-label">
                @lang('igniter.user::default.settings.label_newsletter')
            </label>
        </div>
        {!! form_error('newsletter', '<span class="text-danger">', '</span>') !!}
    </div>

    <div class="buttons">
        <button
            type="submit"
            class="btn btn-primary"
        >@lang('igniter.user::default.settings.button_save')</button>
        <button
            type="button"
            class="btn btn-link pull-right"
            data-bs-toggle="modal"
            data-bs-target="#changePasswordModal"
        >@lang('igniter.user::default.settings.text_password_heading')</button>
    </div>
</form>

<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form
            method="POST"
            data-request="{{ $__SELF__.'::onChangePassword' }}"
        >
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">
                        @lang('igniter.user::default.settings.text_password_heading')
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <input
                            type="password"
                            name="old_password"
                            class="form-control"
                            value=""
                            placeholder="@lang('igniter.user::default.settings.label_old_password')"
                        />
                        {!! form_error('old_password', '<span class="text-danger">', '</span>') !!}
                    </div>

                    <div class="form-row">
                        <div class="col col-sm-6">
                            <div class="form-group">
                                <input
                                    type="password"
                                    class="form-control"
                                    value=""
                                    name="new_password"
                                    placeholder="@lang('igniter.user::default.settings.label_password')"
                                >
                                {!! form_error('new_password', '<span class="text-danger">', '</span>') !!}
                            </div>
                        </div>
                        <div class="col col-sm-6">
                            <div class="form-group">
                                <input
                                    type="password"
                                    class="form-control"
                                    name="confirm_new_password"
                                    value=""
                                    placeholder="@lang('igniter.user::default.settings.label_password_confirm')"
                                >
                                {!! form_error('confirm_new_password', '<span class="text-danger">', '</span>') !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button
                        type="submit"
                        class="btn btn-primary"
                    >@lang('igniter.user::default.settings.button_save')</button>
                </div>
            </div>
        </form>
    </div>
</div>
