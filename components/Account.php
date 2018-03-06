<?php namespace SamPoyigi\Account\Components;

use Admin\Models\Customer_groups_model;
use Admin\Models\Pages_model;
use Admin\Traits\ValidatesForm;
use ApplicationException;
use Auth;
use Cart;
use Captcha;
use Event;
use Exception;
use Redirect;

class Account extends \System\Classes\BaseComponent
{
    use ValidatesForm;

    public $customer;

    public function defineProperties()
    {
        return [
            'security'        => [
                'label'   => 'Who can access this page',
                'type'    => 'string',
                'default' => 'guest',
            ],
            'redirectPage'   => [
                'label'   => 'Page to redirect to after successful login or registration',
                'type'    => 'text',
                'default' => 'account/account',
            ],
            'loginPage'      => [
                'label'   => 'Page to redirect to when checkout is successful',
                'type'    => 'text',
                'default' => 'account/login',
            ],
        ];
    }

    public function onRun()
    {
        $this->page['customer'] = $this->customer = Auth::user();
        if ($this->property('security') == 'customer' AND !$this->customer) {
            flash()->danger(lang('main::account.login.alert_expired_login'));

            return Redirect::guest($this->pageUrl($this->property('loginPage')));
        }
        else if ($this->property('security') == 'guest' AND $this->customer) {
            return Redirect::to($this->pageUrl($this->property('redirectPage')));
        }

        $this->prepareVars();
    }

    public function prepareVars()
    {
        $this->page['customer'] = $this->customer();
        $this->page['cartCount'] = Cart::count();
        $this->page['cartTotal'] = Cart::total();
        $this->page['requireRegistrationTerms'] = (bool)setting('registration_terms');
        $this->page['captchaMode'] = $captchaMode = setting('captcha_mode', 'none');
        $this->page['showCaptcha'] = $showCaptcha = ($captchaMode != 'none');

        Captcha::mode($captchaMode);
        if (Captcha::usesAssets())
            $this->addJs(Captcha::getAssets());
    }

    public function customer()
    {
        if (!Auth::check()) {
            return null;
        }

        return Auth::getUser();
    }

    public function getRegistrationTermsUrl()
    {
        $termsPageId = setting('registration_terms');
        $termsPage = Pages_model::find($termsPageId);

        return $this->pageUrl('pages/pages', [
            'slug'  => $termsPage->permalink_slug,
            'popup' => 'show',
        ]);
    }

    public function renderCaptcha()
    {
        return Captcha::render();
    }

    public function onLogin()
    {
        try {
            $namedRules = [
                ['email', 'lang:main::account.login.label_email', 'required|email'],
                ['password', 'lang:main::account.login.label_password', 'required|min:6|max:32'],
                ['remember', 'lang:main::account.login.label_remember', 'integer'],
            ];

            $this->validate(post(), $namedRules);

            $remember = (bool)post('remember');
            $credentials = [
                'email'    => post('email'),
                'password' => post('password'),
            ];

            Event::fire('sampoyigi.account.beforeAuthenticate', [$this, $credentials]);

            if (!Auth::authenticate($credentials, $remember, TRUE))
                throw new ApplicationException(lang('main::account.login.alert_invalid_login'));

            activity()
                ->causedBy(Auth::getUser())
                ->log(lang('main::account.login.activity_logged_in'));

            $redirectUrl = $this->pageUrl($this->property('redirectPage'));
            if ($redirectUrl = get('redirect', $redirectUrl))
                return Redirect::intended($redirectUrl);
        } catch (Exception $ex) {
            flash()->warning($ex->getMessage());

            return Redirect::back()->withInput();
        }
    }

    public function onRegister()
    {
        try {
            $data = post();

            $rules = [
                ['first_name', 'lang:main::account.label_first_name', 'required|min:2|max:32'],
                ['last_name', 'lang:main::account.label_last_name', 'required|min:2|max:32'],
                ['email', 'lang:main::account.label_email', 'required|email|unique:customers,email'],
                ['password', 'lang:main::account.label_password', 'required|min:6|max:32|same:password_confirm'],
                ['password_confirm', 'lang:main::account.label_password_confirm', 'required'],
                ['telephone', 'lang:main::account.label_telephone', 'required'],
                ['newsletter', 'lang:main::account.label_subscribe', 'integer'],
            ];

            if ((bool)setting('registration_terms'))
                $rules[] = ['terms', 'lang:main::account.label_i_agree', 'sometimes|integer'];

            $this->validate($data, $rules);

            Event::fire('sampoyigi.account.beforeRegister', [&$data]);

            $data['customer_group_id'] = $defaultCustomerGroupId = setting('customer_group_id');
            $customerGroup = Customer_groups_model::find($defaultCustomerGroupId);
            $requireActivation = ($customerGroup AND $customerGroup->requiresApproval());
            $data['status'] = ($requireActivation) ? 0 : 1;
            $customer = Auth::register(array_except($data, ['password_confirm']));

            Event::fire('sampoyigi.account.register', [$customer, $data]);

            if (!$requireActivation) {
                $this->sendActivationEmail($customer);

                Auth::login($customer);

                flash()->success(lang('main::account.login.alert_account_created'));
            }

            activity()
                ->causedBy($customer)
                ->log(lang('main::account.login.activity_registered_account'));

            $redirectUrl = $this->pageUrl($this->property('redirectPage'));

            if ($redirectUrl = get('redirect', $redirectUrl))
                return Redirect::intended($redirectUrl);
        } catch (Exception $ex) {
            flash()->warning($ex->getMessage());

            return Redirect::back()->withInput();
        }
    }

    protected function sendActivationEmail($user)
    {
    }
}