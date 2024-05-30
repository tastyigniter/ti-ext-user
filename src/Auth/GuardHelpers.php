<?php

namespace Igniter\User\Auth;

use Igniter\User\Auth\Models\User;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

trait GuardHelpers
{
    public function login(AuthenticatableContract $user, $remember = false)
    {
        $user->beforeLogin();

        parent::login($user, $remember);

        $user->afterLogin();
    }

    /**
     * @return \Illuminate\Contracts\Auth\Authenticatable|\Igniter\User\Auth\Models\User
     */
    public function getById($identifier)
    {
        return $this->getProvider()->retrieveById($identifier);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getByToken($identifier, $token)
    {
        return $this->getProvider()->retrieveByToken($identifier, $token);
    }

    /**
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function getByCredentials(array $credentials)
    {
        return $this->getProvider()->retrieveByCredentials($credentials);
    }

    public function validateCredentials(User $user, $credentials)
    {
        return $this->getProvider()->validateCredentials($user, $credentials);
    }

    //
    // Impersonation
    //

    /**
     * Impersonates the given user and sets properties
     * in the session but not the cookie.
     *
     * @param \Igniter\User\Auth\Models\User $user
     *
     * @throws \Exception
     */
    public function impersonate($user)
    {
        $oldUserId = $this->session->get($this->getName()) ?? 0;
        $oldUser = !empty($oldUserId) ? $this->getById($oldUserId) : null;

        $user->fireEvent('model.auth.beforeImpersonate', [$oldUser]);
        $this->login($user);

        if (!$this->isImpersonator()) {
            $this->session->put($this->getName().'_impersonate', $oldUserId);
        }
    }

    public function stopImpersonate()
    {
        $currentUserId = $this->session->get($this->getName()) ?? 0;
        $currentUser = !empty($currentUserId) ? $this->getById($currentUserId) : null;

        $oldUserId = $this->session->pull($this->getName().'_impersonate');
        $oldUser = !empty($oldUserId) ? $this->getById($oldUserId) : null;

        $currentUser?->fireEvent('model.auth.afterImpersonate', [$oldUser]);

        $this->session->remove($this->getName());

        if ($oldUser) {
            $this->login($oldUser);
        }
    }

    public function isImpersonator()
    {
        return $this->session->has($this->getName().'_impersonate');
    }

    public function getImpersonator()
    {
        // Check supplied session/cookie is an array (user id, persist code)
        if (!$userId = $this->session->get($this->getName().'_impersonate')) {
            return false;
        }

        return $this->getById($userId);
    }
}
