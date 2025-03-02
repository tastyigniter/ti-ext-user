<?php

namespace Igniter\User\Auth;

use Igniter\User\Auth\Models\User;

trait GuardHelpers
{
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
        $oldSession = $this->session->get($this->getName());
        $oldUser = !empty($oldSession[0]) ? $this->getById($oldSession[0]) : false;

        $user->fireEvent('model.auth.beforeImpersonate', [$oldUser]);
        $this->login($user);

        if (!$this->isImpersonator()) {
            $this->session->put($this->getName().'_impersonate', $oldSession);
        }
    }

    public function stopImpersonate()
    {
        $currentSession = $this->session->get($this->getName());
        $currentUser = !empty($currentSession[0]) ? $this->getById($currentSession[0]) : false;

        $oldSession = $this->session->pull($this->getName().'_impersonate');
        $oldUser = !empty($oldSession[0]) ? $this->getById($oldSession[0]) : false;

        if ($currentUser) {
            $currentUser->fireEvent('model.auth.afterImpersonate', [$oldUser]);
        }

        if ($oldSession) {
            $this->session->put($this->getName(), $oldSession);
        }
    }

    public function isImpersonator()
    {
        return $this->session->has($this->getName().'_impersonate');
    }

    public function getImpersonator()
    {
        $impersonateArray = $this->session->get($this->getName().'_impersonate');

        // Check supplied session/cookie is an array (user id, persist code)
        if (!is_array($impersonateArray) || count($impersonateArray) !== 2) {
            return false;
        }

        $id = $impersonateArray[0];

        return $this->getById($id);
    }
}
