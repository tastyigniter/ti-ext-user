<?php

namespace Igniter\User\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Igniter\User\Models\Customer|null customer()
 * @method static bool isLogged()
 * @method static null|int getId()
 * @method static null|string getFullName()
 * @method static null|string getFirstName()
 * @method static null|string getLastName()
 * @method static null|string getEmail()
 * @method static null|string getTelephone()
 * @method static null|string getAddressId()
 * @method static null|string getGroupId()
 * @method static \Igniter\User\Models\Customer|null user()
 * @method static int|string|null id()
 * @method static bool once(array $credentials = [])
 * @method static \Igniter\User\Models\Customer|false onceUsingId(mixed $id)
 * @method static bool validate(array $credentials = [])
 * @method static \Symfony\Component\HttpFoundation\Response|null basic(string $field = 'email', array $extraConditions = [])
 * @method static \Symfony\Component\HttpFoundation\Response|null onceBasic(string $field = 'email', array $extraConditions = [])
 * @method static bool attempt(array $credentials = [], bool $remember = false)
 * @method static bool attemptWhen(array $credentials = [], array|callable|null $callbacks = null, bool $remember = false)
 * @method static \Igniter\User\Models\Customer|false loginUsingId(mixed $id, bool $remember = false)
 * @method static void login(\Igniter\User\Models\Customer $user, bool $remember = false)
 * @method static void logout()
 * @method static void logoutCurrentDevice()
 * @method static \Igniter\User\Models\Customer|null logoutOtherDevices(string $password)
 * @method static void attempting(mixed $callback)
 * @method static \Igniter\User\Models\Customer getLastAttempted()
 * @method static string getName()
 * @method static string getRecallerName()
 * @method static bool viaRemember()
 * @method static \Igniter\User\Auth\CustomerGuard setRememberDuration(int $minutes)
 * @method static \Illuminate\Contracts\Cookie\QueueingFactory getCookieJar()
 * @method static void setCookieJar(\Illuminate\Contracts\Cookie\QueueingFactory $cookie)
 * @method static \Illuminate\Contracts\Events\Dispatcher getDispatcher()
 * @method static void setDispatcher(\Illuminate\Contracts\Events\Dispatcher $events)
 * @method static \Illuminate\Contracts\Session\Session getSession()
 * @method static \Igniter\User\Models\Customer|null getUser()
 * @method static \Igniter\User\Auth\CustomerGuard setUser(\Igniter\User\Models\Customer $user)
 * @method static \Symfony\Component\HttpFoundation\Request getRequest()
 * @method static \Igniter\User\Auth\CustomerGuard setRequest(\Symfony\Component\HttpFoundation\Request $request)
 * @method static \Illuminate\Support\Timebox getTimebox()
 * @method static \Igniter\User\Models\Customer authenticate()
 * @method static bool hasUser()
 * @method static bool check()
 * @method static bool guest()
 * @method static \Igniter\User\Auth\CustomerGuard forgetUser()
 * @method static \Illuminate\Contracts\Auth\UserProvider getProvider()
 * @method static void setProvider(\Illuminate\Contracts\Auth\UserProvider $provider)
 * @method static void macro(string $name, object|callable $macro)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 * @method static \Igniter\User\Models\Customer getById(void $identifier)
 * @method static mixed getByToken(void $identifier, void $token)
 * @method static \Igniter\User\Models\Customer|null getByCredentials(array $credentials)
 * @method static void validateCredentials(\Igniter\User\Models\Customer $user, void $credentials)
 * @method static void impersonate(\Igniter\User\Models\Customer $user)
 * @method static void stopImpersonate()
 * @method static void isImpersonator()
 * @method static void getImpersonator()
 *
 * @see \Igniter\User\Auth\CustomerGuard
 */
class Auth extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'main.auth';
    }
}
