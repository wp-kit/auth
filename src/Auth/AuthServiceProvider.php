<?php 
	
	namespace WPKit\Auth;

	use Illuminate\Support\ServiceProvider;
	use Illuminate\Support\Facades\Facade;
	use Illuminate\Routing\Redirector;
	use Illuminate\Routing\UrlGenerator;
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
			
			$this->app->singleton(\Illuminate\Session\SessionManager::class, function() {
				
				return $this->app->session;
				
			});
			
			$this->registerRedirector();
			$this->registerUrlGenerator();

		}
		
		/**
	     * Register the URL generator service.
	     *
	     * @return void
	     */
	    protected function registerUrlGenerator()
	    {
	        $this->app->singleton('url', function ($app) {
	            $routes = $app['router']->getRoutes();
	            // The URL generator needs the route collection that exists on the router.
	            // Keep in mind this is an object, so we're passing by references here
	            // and all the registered routes will be available to the generator.
	            $app->instance('routes', $routes);
	            $url = new UrlGenerator(
	                $routes, $app->rebinding(
	                    'request', $this->requestRebinder()
	                )
	            );
	            $url->setSessionResolver(function () {
	                return $this->app['session'];
	            });
	            // If the route collection is "rebound", for example, when the routes stay
	            // cached for the application, we will need to rebind the routes on the
	            // URL generator instance so it has the latest version of the routes.
	            $app->rebinding('routes', function ($app, $routes) {
	                $app['url']->setRoutes($routes);
	            });
	            return $url;
	        });
	    }
		
		/**
	     * Register the Redirector service.
	     *
	     * @return void
	     */
	    protected function registerRedirector()
	    {
	        $this->app->singleton('redirect', function ($app) {
	            $redirector = new Redirector($app['url']);
	            // If the session is set on the application instance, we'll inject it into
	            // the redirector instance. This allows the redirect responses to allow
	            // for the quite convenient "with" methods that flash to the session.
	            if (isset($app['session.store'])) {
	                $redirector->setSession($app['session.store']);
	            }
	            return $redirector;
	        });
	    }
	    
	    /**
	     * Get the URL generator request rebinder.
	     *
	     * @return \Closure
	     */
	    protected function requestRebinder()
	    {
	        return function ($app, $request) {
	            $app['url']->setRequest($request);
	        };
	    }

	}
