<?php
	
	//inside themosis-theme/resources/config/auth.config.php

	return [
	
	    /*
	    |--------------------------------------------------------------------------
	    | Form Auth Settings
	    |--------------------------------------------------------------------------
	    |
	    |
	    */
	
	    'form' => [
		    'allow' => [],
		    'disallow' => [],
		    'logout_redirect' => '/wp-login.php',
		    'login_redirect' => home_url(),
		    'mask_wp_login' => false
	    ],
	
	    /*
	    |--------------------------------------------------------------------------
	    | Token Auth Settings
	    |--------------------------------------------------------------------------
	    |
	    |
	    */
	
	    'token' => [
		    'username' => 'login',
		    'limit' => 5,
		    'allow' => []
	    ]
	
	];