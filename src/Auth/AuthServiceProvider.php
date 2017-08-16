<?php 
	
	namespace WPKit\Auth;

	use Illuminate\Auth\AuthServiceProvider as BaseAuthServiceProvider;
	use Illuminate\Contracts\Auth\Factory;
	use Themosis\Facades\Route;
	use WPKit\Auth\Middleware\FormAuth;
	use WPKit\Auth\Middleware\TokenAuth;
	use WPKit\Auth\Middleware\BasicAuth;
	use Illuminate\Auth\Guard;
	
	class AuthServiceProvider extends BaseAuthServiceProvider {
		
		/**
		* Boot the service provider
		*
		* @return void
		*/
		public function boot() {

			$this->publishes([
				__DIR__.'/../../config/auth.config.php' => config_path('auth.config.php')
			], 'config');

		}

		public function registerAuthenticator() {
			
			$this->app->singleton('auth', function ($app) {
	            // Once the authentication service has actually been requested by the developer
	            // we will set a variable in the application indicating such. This helps us
	            // know that we need to set any queued cookies in the after event later.
	            $app['auth.loaded'] = true;
	            return new AuthManager($app);
	        });
	        
	        $this->app->singleton('auth.driver', function ($app) {
		        
	            return $app['auth']->guard();
	            
	        });

			Route::aliasMiddleware('auth.basic', BasicAuth::class);
			Route::aliasMiddleware('auth.form', FormAuth::class);
			Route::aliasMiddleware('auth.token', TokenAuth::class);
			
			$this->app->auth->provider('wordpress', function() {
				
				return new WordpressUserProvider($this->app, $this->app->hash);
				
			});
			
			$this->app->singleton(Factory::class, function() {
				
				return $this->app->auth;
				
			});

		}

	}
