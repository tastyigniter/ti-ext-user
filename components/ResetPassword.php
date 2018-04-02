<?php

namespace SamPoyigi\Account\Components;

use Admin\Models\Customers_model;
use Admin\Traits\ValidatesForm;
use ApplicationException;
use Exception;
use Mail;
use Redirect;
use System\Classes\BaseComponent;

class ResetPassword extends BaseComponent
{
    use ValidatesForm;

    public function defineProperties()
    {
        return [
            'resetPage' => [
                'label'   => 'The reset password page',
                'type'    => 'text',
                'default' => 'account/reset',
            ],
            'loginPage' => [
                'label'   => 'The login page',
                'type'    => 'text',
                'default' => 'account/login',
            ],
            'paramName' => [
                'label'   => 'The parameter name used for the password reset code',
                'type'    => 'text',
                'default' => 'code',
            ],
        ];
    }

    /**
     * Returns the reset password code from the URL
     * @return string
     */
    public function resetCode()
    {
        $routeParameter = $this->property('paramName');

        if ($code = $this->param($routeParameter)) {
            return $code;
        }

        return get('reset');
    }

    public function onForgotPassword()
    {
        try {
            $namedRules = [
                ['email', 'lang:main::account.label_email', 'required|email|between:6,255'],
            ];

            $this->validate(post(), $namedRules);

            if (!$customer = Customers_model::whereEmail(post('email'))->first())
                throw new ApplicationException(lang('main::account.reset.alert_reset_error'));

            $link = $this->makeResetUrl($code = $customer->resetPassword());

            $this->sendResetPasswordMail($customer, $code, $link);

            flash()->success(lang('main::account.reset.alert_reset_request_success'));

            return Redirect::back();
        } catch (Exception $ex) {
            flash()->warning($ex->getMessage());

            return Redirect::back()->withInput();
        }
    }

    public function onResetPassword()
    {
        try {
            $namedRules = [
                ['code', 'lang:main::account.reset.label_code', 'required'],
                ['password', 'lang:main::account.reset.label_password', 'required|same:password_confirm'],
                ['password_confirm', 'lang:main::account.reset.label_password_confirm', 'required'],
            ];

            $this->validate(post(), $namedRules);

            $customer = Customers_model::whereResetCode($code = post('code'))->first();

            if (!$customer->completeResetPassword($code, post('password')))
                throw new ApplicationException(lang('main::account.reset.alert_reset_failed'));

            flash()->success(lang('main::account.reset.alert_reset_success'));

            return Redirect::to($this->pageUrl($this->property('loginPage')));
        } catch (Exception $ex) {
            flash()->warning($ex->getMessage());

            return Redirect::back()->withInput();
        }
    }

    protected function makeResetUrl($code)
    {
        $params = [
            $this->property('paramName') => $code
        ];

        if ($pageName = $this->property('resetPage')) {
            $url = $this->pageUrl($pageName, $params);
        }
        else {
            $url = $this->currentPageUrl($params);
        }

        if (strpos($url, $code) === false) {
            $url .= '?reset=' . $code;
        }

        return $url;
    }

    protected function sendResetPasswordMail($customer, $code, $link)
    {
        $data = [
            'first_name'         => $customer->first_name,
            'last_name'          => $customer->last_name,
            'reset_code'         => $code,
            'reset_link'         => $link,
            'account_login_link' => site_url($this->property('loginPage')),
        ];

        Mail::send('sampoyigi.account::mail.password_reset_request', $data, function ($message) use ($customer) {
            $message->to($customer->email, $customer->full_name);
        });
    }
}