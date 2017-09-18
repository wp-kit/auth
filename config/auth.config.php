<?php
	
	//inside themosis-theme/resources/config/auth.config.php

	return [
		
		/*
	    |--------------------------------------------------------------------------
	    | Authentication Defaults
	    |--------------------------------------------------------------------------
	    |
	    | This option controls the default authentication "guard" and password
	    | reset options for your application. You may change these defaults
	    | as required, but they're a perfect start for most applications.
	    |
	    */
	    'defaults' => [
	        'guard' => 'web'
	    ],
	    
	    /*
	    |--------------------------------------------------------------------------
	    | Authentication Guards
	    |--------------------------------------------------------------------------
	    |
	    | Next, you may define every authentication guard for your application.
	    | Of course, a great default configuration has been defined for you
	    | here which uses session storage and the Eloquent user provider.
	    |
	    | All authentication drivers have a user provider. This defines how the
	    | users are actually retrieved out of your database or other storage
	    | mechanisms used by this application to persist your user's data.
	    |
	    | Supported: "session", "token"
	    |
	    */
	    'guards' => [
	        'web' => [
	            'driver' => 'session',
	            'provider' => 'users',
	        ],
		    
		'api' => [
		    'driver' => 'token',
		    'provider' => 'users',
		],
	    ],
	    
	    /*
	    |--------------------------------------------------------------------------
	    | User Providers
	    |--------------------------------------------------------------------------
	    |
	    | All authentication drivers have a user provider. This defines how the
	    | users are actually retrieved out of your database or other storage
	    | mechanisms used by this application to persist your user's data.
	    |
	    |
	    */
	    'providers' => [
	        'users' => [
	            'driver' => 'wordpress'
	        ]
	    ],
	
	    /*
	    |--------------------------------------------------------------------------
	    | WP Login Auth Settings
	    |--------------------------------------------------------------------------
	    |
	    |
	    */
	
	    'wp_login' => [
		    'allow' => [],
		    'disallow' => [],
		    'logout_redirect' => '/cms/wp-login.php',
		    'login_redirect' => home_url(),
		    'mask_wp_login' => false
	    ],
	
	];
