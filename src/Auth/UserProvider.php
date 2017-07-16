<?php

namespace WPKit\Auth;

use Illuminate\Auth\DatabaseUserProvider;
use Themosis\User\User;

class UserProvider extends DatabaseUserProvider
{
	
    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        return $this->getGenericUser($identifier);
    }
    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed  $identifier
     * @param  string  $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
	    $user = $this->getGenericUser($user);
	    if( $user->remember_token == $token) {
		    return $user;
	    }
	    return false;
    }
    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  string  $token
     * @return void
     */
    public function updateRememberToken(User $user, $token)
    {
        update_user_meta( $user->ID, 'remember_token', $token );
    }
    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
	    $users = get_users($credentials);
        $user = reset( $users );
        return $this->getGenericUser($user->ID);
    }
    /**
     * Get the generic user.
     *
     * @param  mixed  $user
     * @return \Illuminate\Auth\GenericUser|null
     */
    protected function getGenericUser($user)
    {
        if (! is_null($user)) {
            return new User((array) $user);
        }
    }
    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(User $user, array $credentials)
    {
        return $this->hasher->check(
            $credentials['password'], $user->user_pass
        );
    }
	
}