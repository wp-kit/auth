<?php
    
    namespace WPKit\Auth\Middleware;
    
    use Closure;
	use Illuminate\Http\Request;
	use Illuminate\Http\JsonResponse;
	use Illuminate\Support\Facades\Route;
	use Illuminate\Support\Facades\Input;

	class TokenAuth {
	    
	    /**
	     * The settings of the middleware
	     *
	     * @var array
	     */
	    protected static $settings = array();
	    
	    /**
	     * Add routes for issue token
	     *
	     * @return void
	     */
	    public static function routes() {
		    
		    Route::post( '/token', static::class . '@issueToken' );
		    
	    }
	    
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
			
			nocache_headers();
			
			$settings = static::mergeSettings(app('config.factory')->get('auth.token'));
			
			if( $is_allowed = $this->isAllowed() ) {
				
				return $next($request);
				
			}
	
			// here
			
			$token = $request->get('access_token');
			
			if( empty($token) && $request->bearerToken() ) {
				
				$token = $request->bearerToken();
				
			}
			
			if( ! $token ) {
				
				return new JsonResponse(['success' => false, 'data' => 'No access token provided.'], 401);
				
			}
			
			$users = get_users(array(
				'fields' => 'ids',
				'meta_key' => 'access_token',
				'meta_value' => $token
			));
			
			if( $users && ! is_wp_error( $users ) ) {
				
				$user_id = reset( $users );
				
				wp_set_current_user ( $user_id );
				
				return $next($request);
				
			} else {
				
				return new JsonResponse(['success' => false, 'data' => 'Invalid access token provided.'], 401);
				
			}
	        
	    }
    	
    	/**
	     * Merge settings into stored settings
	     *
	     * @param array $settings
	     * @return array
		 */
    	public static function mergeSettings( $settings = array() ) {
	    	
	    	return static::$settings = array_merge_recursive(array(
    			'username' => 'login',
    			'response' => array(static::class, 'respondToAccessTokenRequest'),
    			'limit' => 5,
    			'allow' => array(
	    			'/token'
    			)
			), $settings);

		}
		
		/**
	     * Check if current route is allowed
	     *
	     * @return boolean
		 */
		public function isAllowed() {
			
			$settings = static::$settings;

	    	$is_allowed = is_user_logged_in();
			
			if( ! $is_allowed ) {
				
				if( ! empty( $settings['disallow'] ) ) {
					
					$is_allowed = true;
					
					foreach($settings['disallow'] as $page) {
	    			
		    			$is_allowed = is_page( $page ) || is_route( $page ) ? false : $is_allowed;
		    			
		    			if( ! $is_allowed ) {
			    			
			    			break;
			    			
		    			}
		    			
					}
				
				} else {
					
					foreach($settings['allow'] as $page) {
	    			
		    			$is_allowed = is_page( $page ) || is_route( $page ) ? true : $is_allowed;
		    			
		    			if( $is_allowed ) {
			    			
			    			break;
			    			
		    			}
		    			
					}
					
				}
				
			}
			
			return $is_allowed;
	    	
    	}
    	
    	/**
	     * Issue a token to the client
	     *
	     * @param \Illuminate\Http\Request $request
	     * @return string
		 */
    	public static function issueToken(Input $request) {
	    	
	    	$settings = static::mergeSettings(app('config.factory')->get('auth.token'));
	    	
	    	if( ! $username = $request->get('username') ) {
		    	
		    	return new JsonResponse(['success' => false, 'data' => 'Missing parameter: username'], 401);
				
			}
			
			if( ! $password = $request->get('password') ) {
				
				return new JsonResponse(['success' => false, 'data' => 'Missing parameter: password'], 401);
				
			}
			
			if( is_array( $settings['username'] ) ) {
					
				foreach($settings['username'] as $property) {
					
					if( $user = get_user_by( $property, $username ) ) {
						
						break;
						
					}
					
				}
				
			} else {
			
				$user = get_user_by( $settings['username'], $username );
				
			}
			
			$is_authenticated = wp_authenticate($user->user_login, $password);
			
			if ( ! is_wp_error( $is_authenticated ) ) {
				
				$token = wp_generate_password( 40, false, false );
				
				$tokens = get_user_meta( $user->ID, 'access_token', false );
				
				if( count($tokens) >= 5  ) {
					
					delete_user_meta( $user->ID, 'access_token', reset($tokens) );
					
				}
				
				add_user_meta( $user->ID, 'access_token', $token );
				
				return new JsonResponse(['success' => false, 'data' => call_user_func( $settings['response'], $token, $user )], 200);
				
			} else {
				
				return new JsonResponse(['success' => false, 'data' => $is_authenticated->get_error_message()], 401);
				
			}
			
		}
		
		/**
	     * Create token for the client
	     *
	     * @param string $token
	     * @param \WP_User $user
	     * @return array
		 */
		public static function respondToAccessTokenRequest( $token, \WP_User $user ) {
			
			return array(
				'access_token' => $token
			);
			
		}
    	
    }