<?php

namespace App\Providers;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

class AdminEloquentProvider extends EloquentUserProvider
{

    /**
     * Validate a user against the given credentials.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param array $credentials
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        $authPassword = $user->getAuthPassword();
        $checkPassword = md5(md5($credentials['password'] . $authPassword['salt']));
        return  $checkPassword == $authPassword['password'];
    }
}
