<?php namespace SamPoyigi\Account\Components;

use Admin\Models\Pages_model;
use Admin\Traits\ValidatesForm;
use App;
use Auth;
use Exception;
use Redirect;
use Request;
use SamPoyigi\Account\Classes\AccountComponent;

class Account extends \System\Classes\BaseComponent
{
    use AccountComponent;
    use ValidatesForm;

    public function onRun()
    {
        parent::onRun();

        $customer = null;
        if ($this->property('context') == 'user' AND !$customer = Auth::user()) {
            flash()->danger(lang('main::account.login.alert_expired_login'));

            return Redirect::guest($this->controller->pageUrl('account/login'));
//        } else if ($this->property('context') == 'guest' AND !$customer = Auth::user()) {
//            return Redirect::guest($this->controller->pageUrl('account/login'));
        }

        $this->setCustomer($customer);

        $this->prepareVars();
    }

    public function prepareVars()
    {
//        $this->page['accountTitle'] = $this->property('heading', lang('account::default.text_heading'));

//        $this->page['customer'] = $this->getCustomer();

        $this->page['inboxCount'] = $this->countInbox();
        $this->page['cartItems'] = 0; //$this->cart->total_items();
        $this->page['cartTotal'] = currency_format(1000);

        $this->page['customerDetails'] = $this->getDetails();
        $this->page['customerAddress'] = $this->getAddress();
        $this->page['orders'] = $this->listFrontEnd('orders');
        $this->page['reservations'] = $this->listFrontEnd('reservations');
        $this->page['messages'] = $this->listFrontEnd('messages');

        // Registration
        $termsPageUrl = setting('registration_terms', FALSE);
        if (is_numeric($termsPageUrl)) {
            $termsPage = Pages_model::find($termsPageUrl);
            $termsPageUrl = $this->controller->pageUrl($termsPage->permalink_slug, ['popup' => 'show']);
        }

        $this->page['registrationTermsUrl'] = $termsPageUrl;

        $captchaProvider = App::make('Main\Services\Captcha\Provider');
        $this->page['captcha'] = $captcha = $captchaProvider->instance();

        if ($captcha->usesReCaptcha())
            $this->addJs($this->captcha->getScriptSrc());
    }

    public function onLogin()
    {
        try {
            $namedRules = [
                ['email', 'lang:main::account.login.label_email', 'required|email'],
                ['password', 'lang:main::account.login.label_password', 'required|min:6|max:32'],
                ['remember', 'lang:main::account.login.label_remember', 'integer'],
            ];

            $this->validate(Request::all(), $namedRules);

            $remember = (bool)post('remember');
            $credentials = [
                'email'    => post('email'),
                'password' => post('password'),
            ];

            if (!Auth::authenticate($credentials, $remember, TRUE)) {
                flash()->danger(lang('main::account.login.alert_invalid_login'));

                return Redirect::back();
            }
            else {
//                dd(Auth::getUser());
                activity()
                    ->causedBy(Auth::getUser())
                    ->log(lang('system::activities.activity_logged_in'));

                if ($redirectUrl = get('redirect')) {
                    return Redirect::to($this->controller->pageUrl($redirectUrl));
                }

                return Redirect::intended($this->controller->pageUrl('account/account'));
            }
        } catch (Exception $ex) {
            if (Request::ajax()) throw $ex;
            else return Redirect::back();
        }
    }

    public function onRegister()
    {
    }

    public function onForgotPassword()
    {
    }
}