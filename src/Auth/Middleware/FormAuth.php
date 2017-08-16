<?php
    
    namespace WPKit\Auth\Middleware;
    
    use Closure;
    use Themosis\Foundation\Request;
	

	class FormAuth {
	    
	    protected static $settings = array();
	    
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
		    
		    add_filter( 'login_url', array($this, 'getLoginUrl'), 10, 3);
			add_action( 'login_init', array($this, 'maskLogin') );
			add_filter( 'login_redirect', array($this, 'loginRedirect'), 10, 3 );
			
			nocache_headers();
			
			$settings = static::mergeSettings(app('config.factory')->get('auth.token'));
			
			$is_allowed = $this->isAllowed();

            if ( ! is_user_logged_in() && ! $is_allowed ) {
                
                $current_url = get_current_url();
                
                wp_redirect( add_query_arg('redirect_to', urlencode($current_url), $settings['logout_redirect']) );
                
                exit();
                
            } else {
	            
	            return $next($request);
	            
            }
	        
	    }
    	
    	public static function mergeSettings($settings = array()) {
	    	
	    	return static::$settings = array_merge_recursive(array(
    			'allow' => array(),
    			'disallow' => array(),
    			'logout_redirect' => '/cms/wp-login.php',
    			'login_redirect' => home_url(),
    			'mask_wp_login' => false
			), $settings);

		}
		
		public function isAllowed() {
			
			$settings = static::$settings;
			
			extract($settings);
	    	
	    	if( ! $mask_wp_login && is_wp_login() ) {
		    	
		    	return true;
		    	
	    	}
	    	
	    	$is_allowed = is_user_logged_in() || is_page( $settings['logout_redirect'] ) || is_route( $settings['logout_redirect'] );
			
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
    	
    	public function getLoginUrl($login_url, $redirect, $force_reauth) {
	    	
	    	$settings = static::$settings;
        		
    		extract($settings);
			
			if( $logout_redirect && $mask_wp_login ) {
				
				$login_url = home_url($logout_redirect);

            	if ( ! empty($redirect) )
            		$login_url = add_query_arg('redirect_to', urlencode($redirect), $login_url);
            
            	if ( $force_reauth )
            		$login_url = add_query_arg('reauth', '1', $login_url);
            		
            	
    			
			}
    		
    		return $login_url;
    		
		}
        
        public function loginRedirect() {
	        
	        $settings = static::$settings;
        		
    		extract($settings);
			
			return ! empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : $loginRedirect;
			
		}
        
        public function maskLogin() {
	        
	        $settings = static::$settings;
        		
    		extract($settings);
	        
	        if( $mask_wp_login && is_wp_login() && empty ( $_REQUEST['interim-login'] ) ) {
	            
	            $args = array();
	            
	            if( ! empty( $_REQUEST['redirect_to'] ) ) {
		            
		            $args['redirect_to'] = $_REQUEST['redirect_to'];
		            
	            }
                
                wp_redirect( add_query_arg( $args, $logout_redirect ) );
                
                exit();
                
            }
	        
        }
    	
    }