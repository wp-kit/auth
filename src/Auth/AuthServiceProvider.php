<?php 
	
	namespace WPKit\Auth;

	use Illuminate\Auth\AuthServiceProvider as BaseAuthServiceProvider;
	use Illuminate\Support\Facades\Route;
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

			Route::aliasMiddleware('auth.basic', BasicAuth::class);
			Route::aliasMiddleware('auth.form', FormAuth::class);
			Route::aliasMiddleware('auth.token', TokenAuth::class);

			$this->app['auth']->extend('wordpress', function($app) {

				$model = $app['config.factory']->get('auth.model');

				$provider = new UserProvider($app['hash'], $model);

				return new Guard($provider, $app['config.factory']->get('session.store'));

			});

		}

	}
