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

    public $paramCode;

    public function defineProperties()
    {
        return [
            'paramName' => [
                'label'   => 'The parameter name used for the password reset code',
                'type'    => 'text',
                'default' => 'code',
            ],
        ];
    }

    public function onRun()
    {
        $routeParameter = $this->property('paramName');
        $this->paramCode = $this->param($routeParameter);
    }

    public function onForgotPassword()
    {
        try {
            $namedRules = [
                ['email', 'lang:main::account.label_email', 'required|email|between:6,255']
            ];

            $this->validate(post(), $namedRules);

            if (!$customer = Customers_model::whereEmail(post('email'))->first())
                throw new ApplicationException(lang('main::account.reset.alert_reset_error'));

            $link = $this->controller->currentPageUrl([
                $this->property('paramName') => $customer->resetPassword()
            ]);

            $data = [
                'first_name' => $customer->first_name,
                'last_name' => $customer->last_name,
                'reset_link' => $link,
                'account_login_link' => site_url('account/login'),
            ];

//        Mail::send('', $data, function($message) use ($customer) {
//            $message->to($customer->email, $customer->customer_name);
//        });

            return Redirect::to($link);
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
                ['password_confirm', 'lang:main::account.reset.label_password_confirm', 'required']
            ];

            $this->validate(post(), $namedRules);

            $customer = Customers_model::whereResetCode($code = post('code'))->first();

            if (!$customer->completeResetPassword($code, post('password')))
                throw new ApplicationException(lang('main::account.reset.alert_reset_failed'));

            flash()->success(lang('main::account.reset.alert_reset_success'));

            return Redirect::back();

        } catch (Exception $ex) {
            flash()->warning($ex->getMessage());

            return Redirect::back()->withInput();
        }
    }
}