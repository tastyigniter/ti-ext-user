<div class="container-fluid">
    <div class="login-container">
        <div class="card">
            <div class="card-body">
                {!! form_open([
                    'id' => 'edit-form',
                    'role' => 'form',
                    'method' => 'POST',
                    'data-request' => $createSuperAdmin ? 'onCreateAccount' : 'onLogin',
                ]) !!}
                    @if($createSuperAdmin)
                        <h3>@lang('igniter.user::default.login.text_create_super_admin')</h3>
                        <p>@lang('igniter.user::default.login.help_create_super_admin')</p>

                        <div class="form-group">
                            <label
                                for="input-name"
                                class="form-label"
                            >@lang('igniter::admin.label_name')</label>
                            <input name="name" type="text" id="input-name" class="form-control"/>
                            {!! form_error('name', '<span class="text-danger">', '</span>') !!}
                        </div>
                        <div class="form-group">
                            <label
                                for="input-email"
                                class="form-label"
                            >@lang('igniter.user::default.login.label_email')</label>
                            <input name="email" type="email" id="input-email" class="form-control"/>
                            {!! form_error('email', '<span class="text-danger">', '</span>') !!}
                        </div>
                        <div class="form-group">
                            <label
                                for="input-password"
                                class="form-label"
                            >@lang('igniter.user::default.login.label_password')</label>
                            <input name="password" type="password" id="input-password" class="form-control"/>
                            {!! form_error('password', '<span class="text-danger">', '</span>') !!}
                        </div>
                        <div class="form-group">
                            <label
                                for="input-password-confirm"
                                class="form-label"
                            >@lang('igniter.user::default.staff.label_confirm_password')</label>
                            <input name="password_confirm" type="password" id="input-password-confirm" class="form-control"/>
                            {!! form_error('password_confirm', '<span class="text-danger">', '</span>') !!}
                        </div>
                        <div class="form-group">
                            <button
                                type="submit"
                                class="btn btn-primary btn-block"
                                data-attach-loading=""
                            >@lang('igniter.user::default.login.button_create_account')
                            </button>
                        </div>
                    @else
                        <h3 class="mb-4">@lang('igniter.user::default.login.text_title')</h3>
                        <div class="form-group mb-0">
                            <label
                                for="input-email"
                                class="form-label"
                            >@lang('igniter.user::default.login.label_email')</label>
                            <input name="email" type="email" id="input-email" class="form-control"/>
                            {!! form_error('email', '<span class="text-danger">', '</span>') !!}
                        </div>
                        <div class="form-group">
                            <label
                                for="input-password"
                                class="form-label"
                            >@lang('igniter.user::default.login.label_password')</label>
                            <input name="password" type="password" id="input-password" class="form-control"/>
                            {!! form_error('password', '<span class="text-danger">', '</span>') !!}
                        </div>
                        <div class="form-group">
                            <button
                                type="submit"
                                class="btn btn-primary btn-block"
                                data-attach-loading=""
                            ><i class="fa fa-sign-in fa-fw"></i>&nbsp;&nbsp;&nbsp;@lang('igniter.user::default.login.button_login')
                            </button>
                        </div>

                        <div class="form-group">
                            <p class="reset-password text-right">
                                <a href="{{ admin_url('login/reset') }}">
                                    @lang('igniter.user::default.login.text_forgot_password')
                                </a>
                            </p>
                        </div>
                    @endif

                {!! form_close() !!}
            </div>
        </div>
    </div>
</div>
