<?php 
	
	namespace WPKit\Auth;

	use Illuminate\Support\ServiceProvider;
	use Illuminate\Support\Facades\Facade;
	use Themosis\Facades\Route;
	
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
			\Illuminate\Cache\CacheServiceProvider::class, // we need this too
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
			
			$this->publishes([
				__DIR__.'/../../config/cache.config.php' => config_path('cache.config.php')
			], 'config');
			
			$this->publishes([
				__DIR__.'/../../config/app.config.php' => config_path('app.config.php')
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

			Route::aliasMiddleware('auth.basic', \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class);
			Route::aliasMiddleware('auth.wp_login', \WPKit\Auth\Middleware\WpLoginAuth::class);
			Route::aliasMiddleware('auth', \Illuminate\Auth\Middleware\Authenticate::class);
			Route::aliasMiddleware('guest', \WPKit\Auth\Middleware\RedirectIfAuthenticated::class);
			Route::aliasMiddleware('start_session', \Illuminate\Session\Middleware\StartSession::class);
			
			$this->app->instance('path.lang', resources_path('lang'));
			
			$this->app->auth->provider('wordpress', function() {
				
				return new WordpressUserProvider($this->app, $this->app->hash);
				
			});
			
			$this->app->singleton(\Illuminate\Contracts\Auth\Factory::class, function() {
				
				return $this->app->auth;
				
			});
			
			$this->app->singleton(\Illuminate\Contracts\Validation\Factory::class, function() {
				
				return $this->app->validator;
				
			});
			
			$this->app->singleton(\Illuminate\Contracts\Cache\Repository::class, function() {
				
				return $this->app['cache.store'];
				
			});

		}

	}
