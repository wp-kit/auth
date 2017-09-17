<?php
    
    namespace WPKit\Auth\Middleware;
    
    use Closure;
    use Illuminate\Http\Request;
	use WPKit\WpLoginAuth\WpLoginAuth;

	class WpLoginAuth {
	    
	    /**
	     * Handle an incoming request.
	     *
	     * @param  \Illuminate\Http\Request  $request
	     * @param  \Closure  $next
	     * @param  string|null  $guard
	     * @return mixed
	     */
	    public function handle(Request $request, Closure $next, $guard = null)
	    {
		    
		    $settings = app('config.factory')->get('auth.wp_login');
		    
		    if( WpLoginAuth::boot($settings) ) {
			    
			    return $next($request);
			    
		    }
	        
	    }
    	
    }
