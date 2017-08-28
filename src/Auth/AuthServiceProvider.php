<?php 
	
	namespace WPKit\Auth;

	use Illuminate\Support\ServiceProvider;
	use Illuminate\Contracts\Auth\Factory;
	use Themosis\Facades\Route;
	use WPKit\Auth\Middleware\FormAuth;
	use WPKit\Auth\Middleware\TokenAuth;
	use WPKit\Auth\Middleware\BasicAuth;
	
	class AuthServiceProvider extends ServiceProvider {
		
		/**
	     * Register the service provider.
	     *
	     * @return void
	     */
		public function register() {

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
