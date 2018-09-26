<?php namespace Igniter\User\Components;

use Admin\Models\Customer_groups_model;
use Admin\Traits\ValidatesForm;
use ApplicationException;
use Auth;
use Cart;
use Event;
use Exception;
use Mail;
use Main\Traits\HasPageOptions;
use Redirect;
use Request;
use System\Models\Pages_model;

class Account extends \System\Classes\BaseComponent
{
    use ValidatesForm;
    use HasPageOptions;

    public function defineProperties()
    {
        return [
            'accountPage' => [
                'label' => 'The customer dashboard page',
                'type' => 'select',
                'default' => 'account/account',
                'options' => [static::class, 'getPageOptions'],
            ],
            'detailsPage' => [
                'label' => 'The customer details page',
                'type' => 'select',
                'default' => 'account/details',
                'options' => [static::class, 'getPageOptions'],
            ],
            'addressPage' => [
                'label' => 'The customer address page',
                'type' => 'select',
                'default' => 'account/address',
                'options' => [static::class, 'getPageOptions'],
            ],
            'ordersPage' => [
                'label' => 'The customer orders page',
                'type' => 'select',
                'default' => 'account/orders',
                'options' => [static::class, 'getPageOptions'],
            ],
            'reservationsPage' => [
                'label' => 'The customer reservations page',
                'type' => 'select',
                'default' => 'account/reservations',
                'options' => [static::class, 'getPageOptions'],
            ],
            'reviewsPage' => [
                'label' => 'The customer reviews page',
                'type' => 'select',
                'default' => 'account/reviews',
                'options' => [static::class, 'getPageOptions'],
            ],
            'inboxPage' => [
                'label' => 'The customer inbox page',
                'type' => 'select',
                'default' => 'account/inbox',
                'options' => [static::class, 'getPageOptions'],
            ],
            'loginPage' => [
                'label' => 'The account login page',
                'type' => 'select',
                'default' => 'account/login',
                'options' => [static::class, 'getPageOptions'],
            ],
            'agreeRegistrationTermsPage' => [
                'label' => 'Registration Terms',
                'type' => 'select',
                'options' => [static::class, 'getPagesOptions'],
                'comment' => 'Require customers to agree to terms before an account is registered',
            ],
            'redirectPage' => [
                'label' => 'Page to redirect to after successful login or registration',
                'type' => 'select',
                'default' => 'account/account',
                'options' => [static::class, 'getPageOptions'],
            ],
        ];
    }

    public function onRun()
    {
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
        $this->page['requireRegistrationTerms'] = (bool)$this->property('agreeRegistrationTermsPage');

        $this->page['customer'] = $this->customer();
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
        $termsPageId = $this->property('registrationTerms');
        $termsPage = Pages_model::find($termsPageId);

        return $this->controller->pageUrl('pages/pages', [
            'slug' => $termsPage ? $termsPage->permalink_slug : null,
        ]);
    }

    public function customer()
    {
        if (!Auth::check()) {
            return null;
        }

        return Auth::getUser();
    }

    public function getCustomerOrders()
    {
        return $this->customer()->orders()->with('status')->take(10)->get();
    }

    public function getCustomerReservations()
    {
        return $this->customer()->reservations()->with('status')->take(10)->get();
    }

    public function getCustomerMessages()
    {
        return $this->customer()->messages()->take(10)->get();
    }

    public function loginUrl()
    {
        $currentUrl = str_replace(Request::root(), '', Request::fullUrl());

        return $this->controller->pageUrl($this->property('loginPage')).'?redirect='.urlencode($currentUrl);
    }

    public function onLogin()
    {
        try {
            $namedRules = [
                ['email', 'lang:igniter.user::default.settings.label_email', 'required|email'],
                ['password', 'lang:igniter.user::default.login.label_password', 'required|min:6|max:32'],
                ['remember', 'lang:igniter.user::default.login.label_remember', 'integer'],
            ];

            $this->validate(post(), $namedRules);

            $remember = (bool)post('remember');
            $credentials = [
                'email' => post('email'),
                'password' => post('password'),
            ];

            Event::fire('igniter.user.beforeAuthenticate', [$this, $credentials]);

            if (!Auth::authenticate($credentials, $remember, TRUE))
                throw new ApplicationException(lang('igniter.user::default.login.alert_invalid_login'));

            activity()
                ->causedBy(Auth::getUser())
                ->log(lang('igniter.user::default.login.activity_logged_in'));

            if ($redirect = input('redirect'))
                return Redirect::to($this->controller->pageUrl($redirect));

            if ($redirectUrl = $this->controller->pageUrl($this->property('redirectPage')))
                return Redirect::intended($redirectUrl);
        }
        catch (Exception $ex) {
            flash()->warning($ex->getMessage());

            return Redirect::back()->withInput();
        }
    }

    public function onRegister()
    {
        try {
            $data = post();

            $rules = [
                ['first_name', 'lang:igniter.user::default.settings.label_first_name', 'required|min:2|max:32'],
                ['last_name', 'lang:igniter.user::default.settings.label_last_name', 'required|min:2|max:32'],
                ['email', 'lang:igniter.user::default.settings.label_email', 'required|email|unique:customers,email'],
                ['password', 'lang:igniter.user::default.login.label_password', 'required|min:6|max:32|same:password_confirm'],
                ['password_confirm', 'lang:igniter.user::default.login.label_password_confirm', 'required'],
                ['telephone', 'lang:igniter.user::default.settings.label_telephone', 'required'],
                ['newsletter', 'lang:igniter.user::default.login.label_subscribe', 'integer'],
            ];

            if (is_numeric($this->property('registrationTerms')))
                $rules[] = ['terms', 'lang:igniter.user::default.login.label_i_agree', 'required|integer'];

            $this->validate($data, $rules);

            Event::fire('igniter.user.beforeRegister', [&$data]);

            $data['customer_group_id'] = $defaultCustomerGroupId = setting('customer_group_id');
            $customerGroup = Customer_groups_model::find($defaultCustomerGroupId);
            $requireActivation = ($customerGroup AND $customerGroup->requiresApproval());
            $data['status'] = ($requireActivation) ? 0 : 1;
            $customer = Auth::register(array_except($data, ['password_confirm', 'terms']));

            Event::fire('igniter.user.register', [$customer, $data]);

            if (!$requireActivation) {
                $this->sendRegistrationEmail($customer);

                Auth::login($customer);

                flash()->success(lang('igniter.user::default.login.alert_account_created'));
            }

            activity()
                ->causedBy($customer)
                ->log(lang('igniter.user::default.login.activity_registered_account'));

            $redirectUrl = $this->controller->pageUrl($this->property('redirectPage'));

            if ($redirectUrl = get('redirect', $redirectUrl))
                return Redirect::intended($redirectUrl);
        }
        catch (Exception $ex) {
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
                ['first_name', 'lang:igniter.user::default.label_first_name', 'required|min:2|max:32'],
                ['last_name', 'lang:igniter.user::default.label_last_name', 'required|min:2|max:32'],
                ['old_password', 'lang:igniter.user::default.label_email', 'sometimes'],
                ['new_password', 'lang:igniter.user::default.label_password', 'required_with:old_password|min:6|max:32|same:confirm_new_password'],
                ['confirm_new_password', 'lang:igniter.user::default.label_password_confirm', 'required_with:old_password'],
                ['telephone', 'lang:igniter.user::default.label_telephone', 'required'],
                ['newsletter', 'lang:igniter.user::default.login.label_subscribe', 'integer'],
            ];

            $this->validateAfter(function ($validator) {
                if ($message = $this->passwordDoesNotMatch()) {
                    $validator->errors()->add('old_password', $message);
                }
            });

            $this->validate($data, $rules);

            $passwordChanged = FALSE;
            if (strlen(post('old_password')) AND strlen(post('new_password'))) {
                $data['password'] = post('new_password');
                $passwordChanged = TRUE;
            }

            if (!array_key_exists('newsletter', $data))
                $data['newsletter'] = 0;

            $customer->fill(array_except($data, ['old_password', 'new_password', 'confirm_new_password']));
            $customer->save();

            if ($passwordChanged) {
                Auth::login($customer, TRUE);
            }

            flash()->success(lang('igniter.user::default.settings.alert_updated_success'));

            return Redirect::back();
        }
        catch (Exception $ex) {
            flash()->warning($ex->getMessage());

            return Redirect::back()->withInput();
        }
    }

    protected function sendRegistrationEmail($customer)
    {
        $data = [
            'first_name' => $customer->first_name,
            'last_name' => $customer->last_name,
            'account_login_link' => $this->controller->pageUrl('account/login'),
        ];

        $settingRegistrationEmail = setting('registration_email');
        is_array($settingRegistrationEmail) OR $settingRegistrationEmail = [];

        if (in_array('customer', $settingRegistrationEmail)) {
            Mail::send('igniter.user::mail.registration', $data, function ($message) use ($customer) {
                $message->to($customer->email, $customer->name);
            });
        }

        if (in_array('admin', $settingRegistrationEmail)) {
            Mail::send('igniter.user::mail.registration_alert', $data, function ($message) {
                $message->to(setting('site_email'), setting('site_name'));
            });
        }
    }

    protected function passwordDoesNotMatch()
    {
        if (!strlen($password = post('old_password')))
            return FALSE;

        $credentials = ['password' => $password];
        if (!Auth::validateCredentials($this->customer(), $credentials)) {
            return 'Password does not match';
        }

        return FALSE;
    }
}