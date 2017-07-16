<?php
    
    namespace WPKit\Auth\Middleware;
    
    use Closure;
	use Illuminate\Support\Facades\Route;
	use Illuminate\Container\Container;
	use Illuminate\Http\Request;

	class TokenAuth {
		
		/**
	     * The guard factory instance.
	     *
	     * @var \Illuminate\Contracts\Auth\Factory
	     */
	    protected $app;
	    
	    protected $settings = array();
	
	    /**
	     * Create a new middleware instance.
	     *
	     * @param  \Illuminate\Contracts\Auth\Factory  $auth
	     * @return void
	     */
	    public function __construct(Container $app)
	    {
		    $this->app = $app;
	        $this->mergeSettings($this->app['config.factory']->get('auth.token'));
	    }
	    
	    public static function routes() {
		    
		    Route::post( '/token', [ $this, 'issueToken' ] );
		    
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
			
			if( $is_allowed = $this->isAllowed() ) {
				
				return true;
				
			}
	
			// here
			
			$token = $request->get('access_token');
			
			if( empty($token) && $request->bearerToken() ) {
				
				$token = $request->bearerToken();
				
			}
			
			if( ! $token ) {
				
				status_header(401);
				
				wp_send_json_error( 'no access token provided' );
				
			}
			
			$users = get_users(array(
				'fields' => 'ids',
				'meta_key' => 'access_token',
				'meta_value' => $token
			));
			
			if( $users && ! is_wp_error( $users ) ) {
				
				$user_id = reset( $users );
				
				wp_set_current_user ( $user_id );
				
				$next($request);
				
			} else {
				
				status_header(401);
				
				wp_send_json_error( 'invalid access token provided' );
				
			}
	        
	    }
    	
    	public function mergeSettings( $settings = array() ) {
	    	
	    	$this->settings = array_merge(array(
    			'username' => 'login',
    			'response' => array($this, 'respondToAccessTokenRequest'),
    			'limit' => 5,
    			'allow' => array()
			), $settings);
			
			$settings['allow'][] = '/token';
			
			return $this;

		}
		
		public function isAllowed() {
			
			extract($this->settings);
	    	
	    	if( ! $mask_wp_login && is_wp_login() ) {
		    	
		    	return true;
		    	
	    	}
	    	
	    	$is_allowed = is_user_logged_in() || is_page( $this->settings['logout_redirect'] ) || is_route( $this->settings['logout_redirect'] );
			
			if( ! $is_allowed ) {
				
				if( ! empty( $this->settings['disallow'] ) ) {
					
					$is_allowed = true;
					
					foreach($this->settings['disallow'] as $page) {
	    			
		    			$is_allowed = is_page( $page ) || is_route( $page ) ? false : $is_allowed;
		    			
		    			if( ! $is_allowed ) {
			    			
			    			break;
			    			
		    			}
		    			
					}
				
				} else {
					
					foreach($this->settings['allow'] as $page) {
	    			
		    			$is_allowed = is_page( $page ) || is_route( $page ) ? true : $is_allowed;
		    			
		    			if( $is_allowed ) {
			    			
			    			break;
			    			
		    			}
		    			
					}
					
				}
				
			}
			
			return $is_allowed;
	    	
    	}
    	
    	public function issueToken(Request $request) {
	    	
	    	if( ! $username = $request->get('username') ) {
						
				status_header(401);
				
				wp_send_json_error( 'Missing parameter: username' );
				
			}
			
			if( ! $password = $request->get('password') ) {
				
				status_header(401);
				
				wp_send_json_error( 'Missing parameter: password' );
				
			}
			
			if( is_array( $this->settings['username'] ) ) {
					
				foreach($this->settings['username'] as $property) {
					
					if( $user = get_user_by( $property, $username ) ) {
						
						break;
						
					}
					
				}
				
			} else {
			
				$user = get_user_by( $this->settings['username'], $username );
				
			}
			
			$is_authenticated = wp_authenticate($user->user_login, $password);
			
			if ( ! is_wp_error( $is_authenticated ) ) {
				
				$token = wp_generate_password( 40, false, false );
				
				$tokens = get_user_meta( $user->ID, 'access_token', false );
				
				if( count($tokens) >= 5  ) {
					
					delete_user_meta( $user->ID, 'access_token', reset($tokens) );
					
				}
				
				add_user_meta( $user->ID, 'access_token', $token );
				
				status_header(200);
			
				wp_send_json_success( $this->app->call( $this->settings['response'], $token, $user ) );
				
			} else {
				
				status_header(401);
				
				wp_send_json_error( $is_authenticated->get_error_message() );
				
			}
			
		}
		
		public function respondToAccessTokenRequest( $token, \WP_User $user ) {
			
			return array(
				'access_token' => $token
			);
			
		}
    	
    }