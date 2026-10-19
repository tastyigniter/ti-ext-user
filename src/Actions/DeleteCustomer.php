<?php

declare(strict_types=1);

namespace Igniter\User\Actions;

use Igniter\Flame\Exception\ApplicationException;
use Igniter\User\Facades\Auth;
use Igniter\User\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class DeleteCustomer
{
    public function handle(Customer $customer, ?string $password = null): void
    {
        throw_if(Auth::isImpersonator(), new ApplicationException(
            lang('igniter.user::default.alert_delete_account_impersonating'),
        ));

        if (!is_null($password) && !Hash::check($password, (string)$customer->password)) {
            throw ValidationException::withMessages([
                'password' => lang('igniter.user::default.alert_delete_account_password'),
            ]);
        }

        Event::dispatch('igniter.user.beforeDeleteAccount', [$customer]);

        $signedIn = Auth::customer()?->getKey() === $customer->getKey();

        DB::transaction(fn() => $customer->delete());

        if ($signedIn) {
            Auth::logout();
            Session::invalidate();
            Session::regenerateToken();
        }

        Event::dispatch('igniter.user.deleteAccount', [$customer]);

        flash()->success(lang('igniter.user::default.alert_delete_account_success'));
    }
}
