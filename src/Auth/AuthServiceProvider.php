<?php 
	
	namespace WPKit\Auth;

	use Illuminate\Support\ServiceProvider;
	use Illuminate\Contracts\Auth\Factory;
	use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
	use Illuminate\Auth\Middleware\Authenticate;
	use Themosis\Facades\Route;
	use WPKit\Auth\Middleware\WpLoginAuth;
	use WPKit\Auth\Middleware\RedirectIfAuthenticated;
	use Illuminate\Support\Facades\Facade;
	
	class AuthServiceProvider extends ServiceProvider {
		
		/**
	     * Boot the service provider.
	     *
	     * @return void
	     */
		public function boot() {
			
			$this->publishes([
				__DIR__.'/../../config/auth.config.php' => config_path('auth.config.php')
			], 'config');
		
		}
		
		/**
	     * Register the service provider.
	     *
	     * @return void
	     */
		public function register() {
			
			Facade::setFacadeApplication($this->app);

			Route::aliasMiddleware('auth.basic', AuthenticateWithBasicAuth::class);
			Route::aliasMiddleware('auth.wp_login', WpLoginAuth::class);
			Route::aliasMiddleware('auth', Authenticate::class);
			Route::aliasMiddleware('guest', RedirectIfAuthenticated::class);
			
			$this->app->auth->provider('wordpress', function() {
				
				return new WordpressUserProvider($this->app, $this->app->hash);
				
			});
			
			$this->app->singleton(Factory::class, function() {
				
				return $this->app->auth;
				
			});

		}

	}
