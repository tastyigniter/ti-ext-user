<?php namespace SamPoyigi\Account\Components;

use Admin\Models\Customer_groups_model;
use Admin\Traits\ValidatesForm;
use ApplicationException;
use Auth;
use Cart;
use Event;
use Exception;
use Mail;
use Redirect;
use Request;
use SamPoyigi\Pages\Models\Pages_model;

class Account extends \System\Classes\BaseComponent
{
    use ValidatesForm;

    public $customer;

    public function defineProperties()
    {
        return [
            'security'     => [
                'label'   => 'Who can access this page',
                'type'    => 'string',
                'default' => 'all',
            ],
            'redirectPage' => [
                'label'   => 'Page to redirect to after successful login or registration',
                'type'    => 'text',
                'default' => 'account/account',
            ],
            'loginPage'    => [
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
            flash()->danger(lang('sampoyigi.account::default.login.alert_expired_login'));

            return Redirect::guest($this->pageUrl($this->property('loginPage')));
        }

        if ($this->property('security') == 'guest' AND $this->customer) {
            return Redirect::to($this->pageUrl($this->property('redirectPage')));
        }

        $this->prepareVars();
    }

    public function prepareVars()
    {
        $this->page['accountPage'] = $this->property('accountPage');
        $this->page['detailsPage'] = $this->property('detailsPage');
        $this->page['addressPage'] = $this->property('addressPage');
        $this->page['ordersPage'] = $this->property('ordersPage');
        $this->page['reservationsPage'] = $this->property('reservationsPage');
        $this->page['reviewsPage'] = $this->property('reviewsPage');
        $this->page['inboxPage'] = $this->property('inboxPage');

        $this->page['customer'] = $this->customer();
        $this->page['requireRegistrationTerms'] = (bool)setting('registration_terms');
    }

    public function cartCount()
    {
        return Cart::count();
    }

    public function cartTotal()
    {
        return Cart::total();
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

    public function customer()
    {
        if (!Auth::check()) {
            return null;
        }

        return Auth::getUser();
    }

    public function loginUrl()
    {
        $currentUrl = str_replace(Request::root(), '', Request::fullUrl());
        return $this->pageUrl($this->property('loginPage')).'?redirect='.urlencode($currentUrl);
    }

    public function onLogin()
    {
        try {
            $namedRules = [
                ['email', 'lang:sampoyigi.account::default.settings.label_email', 'required|email'],
                ['password', 'lang:sampoyigi.account::default.login.label_password', 'required|min:6|max:32'],
                ['remember', 'lang:sampoyigi.account::default.login.label_remember', 'integer'],
            ];

            $this->validate(post(), $namedRules);

            $remember = (bool)post('remember');
            $credentials = [
                'email'    => post('email'),
                'password' => post('password'),
            ];

            Event::fire('sampoyigi.account.beforeAuthenticate', [$this, $credentials]);

            if (!Auth::authenticate($credentials, $remember, TRUE))
                throw new ApplicationException(lang('sampoyigi.account::default.login.alert_invalid_login'));

            activity()
                ->causedBy(Auth::getUser())
                ->log(lang('sampoyigi.account::default.login.activity_logged_in'));

            if ($redirect = get('redirect'))
                return Redirect::to($this->pageUrl($redirect));

            if ($redirectUrl = $this->pageUrl($this->property('redirectPage')))
                return Redirect::intended($redirectUrl);
        } catch (Exception $ex) {
            flash()->warning($ex->getMessage());

            return Redirect::back()->withInput();
        }
    }

    public function onLogout()
    {
        $user = Auth::getUser();

        Auth::logout();

        if ($user) {
            Event::fire('sampoyigi.account.logout', [$user]);
        }

        $url = post('redirect', Request::fullUrl());

        flash()->success(lang('sampoyigi.account::default.alert_logout_success'));

        return Redirect::to($url);
    }

    public function onRegister()
    {
        try {
            $data = post();

            $rules = [
                ['first_name', 'lang:sampoyigi.account::default.settings.label_first_name', 'required|min:2|max:32'],
                ['last_name', 'lang:sampoyigi.account::default.settings.label_last_name', 'required|min:2|max:32'],
                ['email', 'lang:sampoyigi.account::default.settings.label_email', 'required|email|unique:customers,email'],
                ['password', 'lang:sampoyigi.account::default.login.label_password', 'required|min:6|max:32|same:password_confirm'],
                ['password_confirm', 'lang:sampoyigi.account::login.settings.label_password_confirm', 'required'],
                ['telephone', 'lang:sampoyigi.account::default.settings.label_telephone', 'required'],
                ['newsletter', 'lang:sampoyigi.account::default.login.label_subscribe', 'integer'],
            ];

            if ((bool)setting('registration_terms'))
                $rules[] = ['terms', 'lang:sampoyigi.account::default.login.label_i_agree', 'required|integer'];

            $this->validate($data, $rules);

            Event::fire('sampoyigi.account.beforeRegister', [&$data]);

            $data['customer_group_id'] = $defaultCustomerGroupId = setting('customer_group_id');
            $customerGroup = Customer_groups_model::find($defaultCustomerGroupId);
            $requireActivation = ($customerGroup AND $customerGroup->requiresApproval());
            $data['status'] = ($requireActivation) ? 0 : 1;
            $customer = Auth::register(array_except($data, ['password_confirm', 'terms']));

            Event::fire('sampoyigi.account.register', [$customer, $data]);

            if (!$requireActivation) {
                $this->sendRegistrationEmail($customer);

                Auth::login($customer);

                flash()->success(lang('sampoyigi.account::default.login.alert_account_created'));
            }

            activity()
                ->causedBy($customer)
                ->log(lang('sampoyigi.account::default.login.activity_registered_account'));

            $redirectUrl = $this->pageUrl($this->property('redirectPage'));

            if ($redirectUrl = get('redirect', $redirectUrl))
                return Redirect::intended($redirectUrl);
        } catch (Exception $ex) {
            flash()->warning($ex->getMessage());

            return Redirect::back()->withInput();
        }
    }

    public function onUpdate()
    {
        if (!$customer = $this->customer())
            return;

        try {
            $data = post();

            $rules = [
                ['first_name', 'lang:sampoyigi.account::default.label_first_name', 'required|min:2|max:32'],
                ['last_name', 'lang:sampoyigi.account::default.label_last_name', 'required|min:2|max:32'],
                ['old_password', 'lang:sampoyigi.account::default.label_email', 'sometimes'],
                ['new_password', 'lang:sampoyigi.account::default.label_password', 'required_with:old_password|min:6|max:32|same:confirm_new_password'],
                ['confirm_new_password', 'lang:sampoyigi.account::default.label_password_confirm', 'required_with:old_password'],
                ['telephone', 'lang:sampoyigi.account::default.label_telephone', 'required'],
                ['newsletter', 'lang:sampoyigi.account::default.login.label_subscribe', 'integer'],
            ];

            $this->validateAfter(function ($validator) {
                if ($message = $this->passwordDoesNotMatch()) {
                    $validator->errors()->add('old_password', $message);
                }
            });

            $this->validate($data, $rules);

            $passwordChanged = false;
            if (strlen(post('old_password')) AND strlen(post('new_password'))) {
                $data['password'] = post('new_password');
                $passwordChanged = true;
            }

            if (!array_key_exists('newsletter', $data))
                $data['newsletter'] = 0;

            $customer->fill(array_except($data, ['old_password', 'new_password', 'confirm_new_password']));
            $customer->save();

            if ($passwordChanged) {
                Auth::login($customer, TRUE);
            }

            flash()->success(lang('sampoyigi.account::default.settings.alert_updated_success'));

            return Redirect::back();

        } catch (Exception $ex) {
            flash()->warning($ex->getMessage());

            return Redirect::back()->withInput();
        }
    }

    protected function sendRegistrationEmail($customer)
    {
        $data = [
            'first_name'         => $customer->first_name,
            'last_name'          => $customer->last_name,
            'account_login_link' => $this->pageUrl('account/login'),
        ];

        $settingRegistrationEmail = setting('registration_email');
        is_array($settingRegistrationEmail) OR $settingRegistrationEmail = [];

        if (in_array('customer', $settingRegistrationEmail)) {
            Mail::send('sampoyigi.account::mail.registration', $data, function ($message) use ($customer) {
                $message->to($customer->email, $customer->name);
            });
        }

        if (in_array('admin', $settingRegistrationEmail)) {
            Mail::send('sampoyigi.account::mail.registration_alert', $data, function ($message) {
                $message->to(setting('site_email'), setting('site_name'));
            });
        }
    }

    protected function passwordDoesNotMatch()
    {
        if (!strlen($password = post('old_password')))
            return false;

        $credentials = ['password' => $password];
        if (!Auth::validateCredentials($this->customer(), $credentials)) {
            return 'Password does not match';
        }

        return FALSE;
    }
}