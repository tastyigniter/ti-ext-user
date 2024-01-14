<?php

namespace Igniter\User;

use Igniter\Admin\Classes\MainMenuItem;
use Igniter\Admin\Classes\Navigation;
use Igniter\Admin\Facades\AdminMenu;
use Igniter\Flame\Igniter;
use Igniter\Local\Models\Location;
use Igniter\System\Models\Settings;
use Igniter\User\Classes\BladeExtension;
use Igniter\User\Console\Commands\AllocatorCommand;
use Igniter\User\Console\Commands\ClearUserStateCommand;
use Igniter\User\Facades\Auth;
use Igniter\User\Models\Customer;
use Igniter\User\Models\Observers\CustomerObserver;
use Igniter\User\Models\Observers\UserObserver;
use Igniter\User\Models\User;
use Igniter\User\Subscribers\AssigneeUpdatedSubscriber;
use Igniter\User\Subscribers\ConsoleSubscriber;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class Extension extends \Igniter\System\Classes\BaseExtension
{
    protected $subscribe = [
        AssigneeUpdatedSubscriber::class,
        ConsoleSubscriber::class,
    ];

    protected $observers = [
        Customer::class => CustomerObserver::class,
        User::class => UserObserver::class,
    ];

    protected array $morphMap = [
        'addresses' => \Igniter\User\Models\Address::class,
        'assignable_logs' => \Igniter\User\Models\AssignableLog::class,
        'customer_groups' => \Igniter\User\Models\CustomerGroup::class,
        'customers' => \Igniter\User\Models\Customer::class,
        'notifications' => \Igniter\User\Models\Notification::class,
        'user_groups' => \Igniter\User\Models\UserGroup::class,
        'users' => \Igniter\User\Models\User::class,
    ];

    public array $singletons = [
        Classes\PermissionManager::class,
    ];

    public function register()
    {
        parent::register();

        $this->app->register(\Igniter\User\Auth\AuthServiceProvider::class);

        $this->registerConsoleCommand('igniter.assignable.allocator', AllocatorCommand::class);
        $this->registerConsoleCommand('igniter.user-state.clear', ClearUserStateCommand::class);

        $this->registerGuards();

        AliasLoader::getInstance()->alias('Auth', \Igniter\User\Facades\Auth::class);
        AliasLoader::getInstance()->alias('AdminAuth', \Igniter\User\Facades\AdminAuth::class);

        $this->registerBladeDirectives();
        $this->registerSystemSettings();
        $this->registerEventGlobalParams();

        Route::pushMiddlewareToGroup('igniter:admin', \Igniter\User\Http\Middleware\Authenticate::class);
        Route::pushMiddlewareToGroup('igniter:admin', \Igniter\User\Http\Middleware\LogUserLastSeen::class);
    }

    public function boot()
    {
        $this->defineRoutes();
        $this->configureRateLimiting();

        Event::listen('igniter.user.register', function (Customer $customer, array $data) {
            Notifications\CustomerRegisteredNotification::make()->subject($customer)->broadcast();
        });

        $this->registerAdminUserPanel();

        Location::extend(function ($model) {
            $model->relation['morphedByMany']['users'] = [User::class, 'name' => 'locationable'];
        });
    }

    public function registerAutomationRules()
    {
        return [
            'events' => [
                'igniter.user.register' => \Igniter\User\AutomationRules\Events\CustomerRegistered::class,
            ],
            'actions' => [],
            'conditions' => [
                \Igniter\User\AutomationRules\Conditions\CustomerAttribute::class,
            ],
        ];
    }

    public function registerMailTemplates(): array
    {
        return [
            'igniter.user::mail.admin_password_reset' => 'lang:igniter.user::default.text_mail_admin_password_reset',
            'igniter.user::mail.admin_password_reset_request' => 'lang:igniter.user::default.text_mail_admin_password_reset_request',
            'igniter.user::mail.password_reset' => 'lang:igniter.user::default.text_mail_password_reset',
            'igniter.user::mail.password_reset_request' => 'lang:igniter.user::default.text_mail_password_reset_request',
            'igniter.user::mail.registration' => 'lang:igniter.user::default.text_mail_registration',
            'igniter.user::mail.registration_alert' => 'lang:igniter.user::default.text_mail_registration_alert',
            'igniter.user::mail.activation' => 'lang:igniter.user::default.text_mail_activation',
            'igniter.user::mail.invite' => 'lang:igniter.user::default.text_mail_invite',
            'igniter.user::mail.invite_customer' => 'lang:igniter.user::default.text_mail_invite_customer',
        ];
    }

    public function registerNavigation(): array
    {
        return [
            'customers' => [
                'priority' => 100,
                'class' => 'customers',
                'icon' => 'fa-user',
                'href' => admin_url('customers'),
                'title' => lang('igniter.user::default.text_side_menu_customer'),
                'permission' => 'Admin.Customers',
            ],
            'system' => [
                'child' => [
                    'users' => [
                        'priority' => 0,
                        'class' => 'users',
                        'href' => admin_url('users'),
                        'title' => lang('igniter.user::default.text_side_menu_user'),
                        'permission' => 'Admin.Staffs',
                    ],
                ],
            ],
        ];
    }

    public function registerSystemSettings()
    {
        Settings::registerCallback(function (Settings $manager) {
            $manager->registerSettingItems('core', [
                'user' => [
                    'label' => 'lang:igniter.user::default.text_tab_user',
                    'description' => 'lang:igniter.user::default.text_tab_desc_user',
                    'icon' => 'fa fa-users-gear',
                    'priority' => 2,
                    'permission' => ['Site.Settings'],
                    'url' => admin_url('settings/edit/user'),
                    'form' => 'igniter.user::/models/usersettings',
                    'request' => \Igniter\User\Requests\UserSettingsRequest::class,
                ],
            ]);
        });
    }

    public function registerPermissions(): array
    {
        return [
            'Admin.CustomerGroups' => [
                'label' => 'igniter.user::default.text_permission_customer_groups',
                'group' => 'user',
            ],
            'Admin.Customers' => [
                'label' => 'igniter.user::default.text_permission_customers',
                'group' => 'user',
            ],
            'Admin.Impersonate' => [
                'label' => 'igniter.user::default.text_permission_impersonate_staff',
                'group' => 'user',
            ],
            'Admin.ImpersonateCustomers' => [
                'label' => 'igniter.user::default.text_permission_impersonate_customers',
                'group' => 'user',
            ],
            'Admin.StaffGroups' => [
                'label' => 'igniter.user::default.text_permission_user_groups',
                'group' => 'user',
            ],
            'Admin.Staffs' => [
                'label' => 'igniter.user::default.text_permission_staffs',
                'group' => 'user',
            ],
            'Admin.DeleteStaffs' => [
                'label' => 'igniter.user::default.text_permission_delete_staffs',
                'group' => 'user',
            ],
        ];
    }

    public function registerFormWidgets(): array
    {
        return [
            \Igniter\User\FormWidgets\PermissionEditor::class => [
                'label' => 'Permission Editor',
                'code' => 'permissioneditor',
            ],
        ];
    }

    protected function registerEventGlobalParams()
    {
        if (class_exists(\Igniter\Automation\Classes\EventManager::class)) {
            resolve(\Igniter\Automation\Classes\EventManager::class)->registerCallback(function ($manager) {
                $manager->registerGlobalParams([
                    'customer' => Auth::customer(),
                ]);
            });
        }
    }

    protected function registerRequestRebindHandler()
    {
        $this->app->rebinding('request', function ($app, $request) {
            $request->setUserResolver(function () use ($app) {
                if (!Igniter::runningInAdmin()) {
                    return $app['admin.auth']->getUser();
                }

                return $app['main.auth']->user();
            });
        });
    }

    protected function configureRateLimiting()
    {
        RateLimiter::for('web', function (\Illuminate\Http\Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->getKey() ?: $request->ip());
        });

        if (Igniter::runningInAdmin()) {
            return;
        }

        $this->app->make(\Illuminate\Contracts\Http\Kernel::class)
            ->appendMiddlewareToGroup('web', \Igniter\User\Http\Middleware\ThrottleRequests::class);

        Event::listen('igniter.user.beforeThrottleRequest', function ($request, $params) {
            $handler = str_after($request->header('x-igniter-request-handler'), '::');
            if (in_array($handler, [
                'onLogin',
                'onRegister',
                'onActivate',
                'onForgotPassword',
                'onResetPassword',
            ])) {
                $params->maxAttempts = 6;
                $params->decayMinutes = 1;

                return true;
            }
        });
    }

    protected function registerBladeDirectives()
    {
        $this->callAfterResolving('blade.compiler', function ($compiler, $app) {
            (new BladeExtension())->register();
        });
    }

    protected function registerGuards(): void
    {
        $this->app->singleton('main.auth', function () {
            return resolve('auth')->guard(config('igniter-auth.guards.web', 'web'));
        });

        $this->app->singleton('admin.auth', function () {
            return resolve('auth')->guard(config('igniter-auth.guards.admin', 'web'));
        });
    }

    protected function registerAdminUserPanel()
    {
        if (!Igniter::runningInAdmin()) {
            return;
        }

        AdminMenu::registerCallback(function (Navigation $manager) {
            $manager->registerMainItems([
                MainMenuItem::widget('notifications', \Igniter\User\MainMenuWidgets\NotificationList::class)
                    ->priority(15)
                    ->permission('Admin.Notifications'),
                MainMenuItem::widget('user', \Igniter\User\MainMenuWidgets\UserPanel::class)
                    ->mergeConfig([
                        'links' => [
                            'account' => [
                                'label' => 'igniter::admin.text_edit_details',
                                'iconCssClass' => 'fa fa-user fa-fw',
                                'url' => admin_url('users/account'),
                                'priority' => 10,
                            ],
                            'logout' => [
                                'label' => 'igniter::admin.text_logout',
                                'cssClass' => 'text-danger',
                                'iconCssClass' => 'fa fa-power-off fa-fw',
                                'url' => admin_url('logout'),
                                'priority' => 999,
                            ],
                        ],
                    ])
                    ->priority(999),
            ]);
        });
    }

    protected function defineRoutes()
    {
        if (app()->routesAreCached()) {
            return;
        }

        Route::group([], function ($router) {
            (new Classes\RouteRegistrar($router))->all();
        });
    }
}
