<?php

namespace WPKit\Auth;

use Illuminate\Auth\SessionGuard as BaseSessionGuard;
use Illuminate\Http\Response;

class SessionGuard extends BaseSessionGuard
{

    /**
     * Get the response for basic authentication.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function failedBasicResponse()
    {
	    header('WWW-Authenticate: Basic realm="My Realm"');
        return new Response('Invalid credentials.', 401, ['WWW-Authenticate' => 'Basic']);
    }
    
}
