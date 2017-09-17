<?php 
	
	namespace WPKit\Auth;

	use Illuminate\Support\ServiceProvider;
	use Illuminate\Contracts\Auth\Factory;
	use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
	use Themosis\Facades\Route;
	use WPKit\Auth\Middleware\WpLoginAuth;
	use WPKit\Auth\Middleware\TokenAuth;
	
	class AuthServiceProvider extends ServiceProvider {
		
		/**
	     * Register the service provider.
	     *
	     * @return void
	     */
		public function register() {

			Route::aliasMiddleware('auth.basic', AuthenticateWithBasicAuth::class);
			Route::aliasMiddleware('auth.wp_login', WpLoginAuth::class);
			Route::aliasMiddleware('auth.token', TokenAuth::class);
			
			$this->app->auth->provider('wordpress', function() {
				
				return new WordpressUserProvider($this->app, $this->app->hash);
				
			});
			
			$this->app->singleton(Factory::class, function() {
				
				return $this->app->auth;
				
			});

		}

	}
