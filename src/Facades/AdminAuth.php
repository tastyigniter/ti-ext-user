<?php

namespace Igniter\User\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void isLogged()
 * @method static void isSuperUser()
 * @method static \Igniter\User\Models\User|\Illuminate\Contracts\Auth\Authenticatable staff()
 * @method static \Illuminate\Database\Eloquent\Collection locations()
 * @method static void getId()
 * @method static void getUserName()
 * @method static void getUserEmail()
 * @method static void getStaffName()
 * @method static void getStaffEmail()
 * @method static \Illuminate\Contracts\Auth\Authenticatable|null user()
 * @method static int|string|null id()
 * @method static bool once(array $credentials = [])
 * @method static \Illuminate\Contracts\Auth\Authenticatable|false onceUsingId(mixed $id)
 * @method static bool validate(array $credentials = [])
 * @method static \Symfony\Component\HttpFoundation\Response|null basic(string $field = 'email', array $extraConditions = [])
 * @method static \Symfony\Component\HttpFoundation\Response|null onceBasic(string $field = 'email', array $extraConditions = [])
 * @method static bool attempt(array $credentials = [], bool $remember = false)
 * @method static bool attemptWhen(array $credentials = [], array|callable|null $callbacks = null, bool $remember = false)
 * @method static \Illuminate\Contracts\Auth\Authenticatable|false loginUsingId(mixed $id, bool $remember = false)
 * @method static void login(\Illuminate\Contracts\Auth\Authenticatable $user, bool $remember = false)
 * @method static void logout()
 * @method static void logoutCurrentDevice()
 * @method static \Illuminate\Contracts\Auth\Authenticatable|null logoutOtherDevices(string $password)
 * @method static void attempting(mixed $callback)
 * @method static \Illuminate\Contracts\Auth\Authenticatable getLastAttempted()
 * @method static string getName()
 * @method static string getRecallerName()
 * @method static bool viaRemember()
 * @method static \Igniter\User\Auth\UserGuard setRememberDuration(int $minutes)
 * @method static \Illuminate\Contracts\Cookie\QueueingFactory getCookieJar()
 * @method static void setCookieJar(\Illuminate\Contracts\Cookie\QueueingFactory $cookie)
 * @method static \Illuminate\Contracts\Events\Dispatcher getDispatcher()
 * @method static void setDispatcher(\Illuminate\Contracts\Events\Dispatcher $events)
 * @method static \Illuminate\Contracts\Session\Session getSession()
 * @method static \Illuminate\Contracts\Auth\Authenticatable|null getUser()
 * @method static \Igniter\User\Auth\UserGuard setUser(\Illuminate\Contracts\Auth\Authenticatable $user)
 * @method static \Symfony\Component\HttpFoundation\Request getRequest()
 * @method static \Igniter\User\Auth\UserGuard setRequest(\Symfony\Component\HttpFoundation\Request $request)
 * @method static \Illuminate\Support\Timebox getTimebox()
 * @method static \Illuminate\Contracts\Auth\Authenticatable authenticate()
 * @method static bool hasUser()
 * @method static bool check()
 * @method static bool guest()
 * @method static \Igniter\User\Auth\UserGuard forgetUser()
 * @method static \Illuminate\Contracts\Auth\UserProvider getProvider()
 * @method static void setProvider(\Illuminate\Contracts\Auth\UserProvider $provider)
 * @method static void macro(string $name, object|callable $macro)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 * @method static \Illuminate\Contracts\Auth\Authenticatable|\Igniter\User\Auth\Models\User getById(void $identifier)
 * @method static mixed getByToken(void $identifier, void $token)
 * @method static \Illuminate\Contracts\Auth\Authenticatable|null getByCredentials(array $credentials)
 * @method static void validateCredentials(\Igniter\User\Auth\Models\User $user, void $credentials)
 * @method static void impersonate(\Igniter\User\Auth\Models\User $user)
 * @method static void stopImpersonate()
 * @method static void isImpersonator()
 * @method static void getImpersonator()
 *
 * @see \Igniter\User\Auth\UserGuard
 */
class AdminAuth extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     * @see \Igniter\User\Auth\UserGuard
     */
    protected static function getFacadeAccessor()
    {
        return 'admin.auth';
    }
}
