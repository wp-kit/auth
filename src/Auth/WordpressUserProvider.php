<?php

namespace WPKit\Auth;

use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;

class WordpressUserProvider implements UserProvider
{
	
	/**
     * The container.
     *
     * @var \Illuminate\Contract\Container\Container
     */
    protected $container;
	

    /**
     * The hasher implementation.
     *
     * @var \Illuminate\Contracts\Hashing\Hasher
     */
    protected $hasher;

    /**
     * Create a new database user provider.
     *
     * @param  \Illuminate\Database\ConnectionInterface  $conn
     * @param  \Illuminate\Contracts\Hashing\Hasher  $hasher
     * @param  string  $table
     * @return void
     */
    public function __construct(Container $container, HasherContract $hasher)
    {
	    $this->container = $container;
        $this->hasher = $hasher;
    }
	
    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        return new User($identifier);
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
	    $user = new User($identifier);
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
    public function updateRememberToken(Authenticatable $user, $token)
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
	    $user = wp_signon([
	    	'user_login' => $credentials['user_login'], 
	    	'user_password' => $credentials['password']
	    ], $this->container['config.factory']->get('session')['secure'] ? true : false);
	    if( $user && ! is_wp_error( $user ) ) {
		    return new User($user->ID);
	    } else {
		    return new User();
	    }
    }
    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        return $this->hasher->check(
            $credentials['password'], $user->user_pass
        );
    }
	
}