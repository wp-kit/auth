<?php 
	
	namespace WPKit\Auth;

	use Illuminate\Support\ServiceProvider;
	use Illuminate\Contracts\Auth\Factory;
	use Illuminate\Contracts\Validation\Factory as ValidationFactory;
	use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
	use Illuminate\Auth\Middleware\Authenticate;
	use Themosis\Facades\Route;
	use WPKit\Auth\Middleware\WpLoginAuth;
	use WPKit\Auth\Middleware\RedirectIfAuthenticated;
	use Illuminate\Support\Facades\Facade;
	
	class AuthServiceProvider extends ServiceProvider {
		
		/**
	     * Additional providers.
	     *
	     * @var array
	     */
		protected $providers = [
			\WPKit\Config\ConfigServiceProvider::class, // we need this too
			\WPKit\Kernel\KernelServiceProvider::class, // we need this too
			\WPKit\Hashing\HashingServiceProvider::class, // we need this too
			\Illuminate\Cookie\CookieServiceProvider::class, // we need this too
			\Illuminate\Session\SessionServiceProvider::class, // we need this too
			\Illuminate\Filesystem\FilesystemServiceProvider::class, // we need this too
			\Illuminate\Translation\TranslationServiceProvider::class, // we need this too
			\Illuminate\Validation\ValidationServiceProvider::class, // we need this too
		];
		
		/**
	     * Boot the service provider.
	     *
	     * @return void
	     */
		public function boot() {
			
			$this->publishes([
				__DIR__.'/../../config/auth.config.php' => config_path('auth.config.php')
			], 'config');
			
			$this->publishes([
				__DIR__.'/../../config/session.config.php' => config_path('session.config.php')
			], 'config');
		
		}
		
		/**
	     * Register the service provider.
	     *
	     * @return void
	     */
		public function register() {
			
			foreach($this->providers as $provider) {
				
				$this->app->register($provider);
				
			}
			
			Facade::setFacadeApplication($this->app);

			Route::aliasMiddleware('auth.basic', AuthenticateWithBasicAuth::class);
			Route::aliasMiddleware('auth.wp_login', WpLoginAuth::class);
			Route::aliasMiddleware('auth', Authenticate::class);
			Route::aliasMiddleware('guest', RedirectIfAuthenticated::class);
			
			$this->app->instance('path.lang', resources_path('lang'));
			
			$this->app->auth->provider('wordpress', function() {
				
				return new WordpressUserProvider($this->app, $this->app->hash);
				
			});
			
			$this->app->singleton(Factory::class, function() {
				
				return $this->app->auth;
				
			});
			
			$this->app->singleton(ValidationFactory::class, function() {
				
				return $this->app->validator;
				
			});

		}

	}
