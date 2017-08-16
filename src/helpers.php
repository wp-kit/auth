<?php
	
	/*----------------------------------------------*\
    	#IS WP_LOGIN
    \*----------------------------------------------*/
    
    if ( ! function_exists('is_wp_login') ) {
    
	    function is_wp_login() {
	        
	        return in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php', 'wp-activate.php', 'wp-signup.php' ) );
	        
	    }
	    
	}
	
	/*----------------------------------------------*\
    	#ROUTES
    \*----------------------------------------------*/
    
    use Themosis\Facades\Input;
	
	if( ! function_exists('get_current_url') ) {
		
		function get_current_url() {
			
			return get_home_url( 1, Input::getRequestUri() );
			
		}
		
	}
	
	if( ! function_exists('get_current_url_path') ) {
		
		function get_current_url_path() {
			
			return rtrim( explode('?', get_current_url())[0], '/');
			
		}
		
	}
	
	if ( ! function_exists('is_route') ) {
    
	    function is_route( $path ) {
		    
		    if( strpos( $path, '*' ) !== false ) {
			    
			    $is_route = strpos( get_current_url_path(), home_url( str_replace( '*', '', $path ) ) ) !== false;
			    
		    } else {
		    
		    	$is_route = home_url( $path ) == get_current_url_path();
		    	
		    }
			return $is_route;
		    
	    }
	    
	}