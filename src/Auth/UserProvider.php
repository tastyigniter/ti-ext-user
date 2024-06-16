<?php

namespace Igniter\User\Auth;

use Igniter\Flame\Database\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;

class UserProvider implements \Illuminate\Contracts\Auth\UserProvider
{
    protected $config;

    /**
     * CustomerProvider constructor.
     */
    public function __construct($config = null)
    {
        $this->config = $config;
    }

    public function retrieveById($identifier)
    {
        return $this->createModelQuery()->find($identifier);
    }

    public function retrieveByToken($identifier, $token)
    {
        $query = $this->createModelQuery();
        $model = $query->getModel();

        return $query
            ->where($model->getAuthIdentifierName(), $identifier)
            ->where($model->getRememberTokenName(), $token)
            ->first();
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        $user->setRememberToken($token);

        $timestamps = $user->timestamps;

        $user->timestamps = false;

        $user->save();

        $user->timestamps = $timestamps;
    }

    public function retrieveByCredentials(array $credentials)
    {
        $query = $this->createModelQuery();

        foreach ($credentials as $key => $value) {
            if (!str_contains($key, 'password')) {
                $query->where($key, $value);
            }
        }

        return $query->first();
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        if (is_null($plain = $credentials['password'])) {
            return false;
        }

        // Backward compatibility to turn SHA1 passwords to BCrypt
        if ($this->hasShaPassword($user, $credentials)) {
            $user->forceFill([
                $user->getAuthPasswordName() => Hash::make($credentials['password']),
                'salt' => null,
            ])->save();
        }

        return Hash::check($plain, $user->getAuthPassword());
    }

    public function hasShaPassword(Authenticatable $user, array $credentials)
    {
        if (is_null($user->salt)) {
            return false;
        }

        return $user->password === sha1($user->salt.sha1($user->salt.sha1($credentials['password'])));
    }

    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false)
    {
        if (!Hash::needsRehash($user->getAuthPassword()) && !$force) {
            return;
        }

        $user->forceFill([
            $user->getAuthPasswordName() => Hash::make($credentials['password']),
        ])->save();
    }

    public function register(array $attributes, $activate = false)
    {
        return $this->createModel()->register($attributes, $activate);
    }

    /**
     * Prepares a query derived from the user model.
     */
    protected function createModelQuery()
    {
        $model = $this->createModel();
        $query = $model->newQuery();

        $model->extendUserQuery($query);

        return $query;
    }

    protected function createModel(): Model
    {
        return new $this->config['model'];
    }
}
