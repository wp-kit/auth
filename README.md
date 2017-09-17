# wp-kit/auth

This is a wp-kit component that handles authentication.

```wp-kit/auth``` was built to work with [```Themosis```](http://framework.themosis.com/) as currently there are no authentication middlewares built into ```Themosis``` however with [```illuminate/routing```](https://github.com/illuminate/routing) built into ```Themosis```, we are able to run ```Middleware``` on ```Routes``` and ```Route Groups```.

```wp-kit/auth``` achives compatibility with [```illuminate/auth```](https://github.com/illuminate/auth) by providing a UserProvider that integrates directly with WordPress to authenticate users.

```wp-kit/auth``` comes aliased with three types of ```Middleware```:

* Authentication (Illuminate): [auth](https://github.com/illuminate/auth/blob/master/Middleware/Authenticate.php)
 * Token Guard (Illuminate): [auth:api](https://github.com/illuminate/auth/blob/master/TokenGuard.php)
 * Session Guard (Illuminate): [auth:web](https://github.com/illuminate/auth/blob/master/SessionGuard.php)
* Basic Authentication (Illuminate): [auth.basic](https://github.com/illuminate/auth/blob/master/Middleware/AuthenticateWithBasicAuth.php)
* WP Login Authentication (WP Kit): [auth.wp_login](https://github.com/wp-kit/auth/blob/master/src/Auth/Middleware/WpLoginAuth.php)

```wp-kit/auth``` comes with a [`AuthenticatesUsers`](blob/master/src/Auth/Traits/AuthenticatesUsers.php) Trait just like [`wp-kit/foundation`](https://github.com/laravel/framework/blob/5.5/src/Illuminate/Foundation/Auth/AuthenticatesUsers.php) so you can use this trait inside [Controllers](https://github.com/laravel/laravel/blob/master/app/Http/Controllers/Auth/LoginController.php) so you can use traditional form authentication just like in Laravel. 

## Installation

Install via [```Composer```](https://getcomposer.org/) in the root of your ```Themosis``` installation:

```php
composer require "wp-kit/auth"
```

## Setup

### Add Service Provider(s)

Just register the service provider and facade in the providers config and theme config:

```php
//inside theme/resources/config/providers.config.php

return [
    //
    WPKit\Config\ConfigServiceProvider::class, // we need this too
    WPKit\Kernel\KernelServiceProvider::class, // we need this too
    WPKit\Hashing\HashingServiceProvider::class, // we need this too
    Illuminate\Cookie\CookieServiceProvider::class, // we need this too
    Illuminate\Auth\AuthServiceProvider::class, // we need this too
    Illuminate\Filesystem\FilesystemServiceProvider::class, // specify the driver provider, if using BasicAuth
    Illuminate\Session\SessionServiceProvider::class, // we need this too, if using BasicAuth
    WPKit\Auth\AuthServiceProvider::class
    //
];
```

### Add Config File

> **Note:** This will be changing to a traditional config file similar to that found in Laravel once the ```UserProvider``` Guard has been built

The recommended method of installing config files for ```wp-kit``` components is via ```wp kit vendor:publish``` command.

First, [install WP CLI](http://wp-cli.org/), and then install this component, ```wp kit vendor:publish``` will automatically be installed with ```wp-kit/utils```, once installed you can run:

```wp kit vendor:publish```

For more information, please visit [```wp-kit/utils```](https://github.com/wp-kit/utils#commands).

Alternatively, you can place the [config file(s)](config) in your ```theme/resources/config``` directory manually.

### Allowing Headers

If using [```BasicAuth```](https://github.com/wp-kit/auth/blob/master/src/Auth/Middleware/BasicAuth.php) middleware, make sure you add the following line to your ```.htaccess``` file to allow ```Authorization``` headers:

```RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization},L]```

## Usage

You can activate ```Middleware``` on the route group or route itself:

### Middleware on Group

```php
// inside themosis-theme/resources/providers/RoutingService.php

namespace Theme\Providers;

use Themosis\Facades\Route;
use Themosis\Foundation\ServiceProvider;

class RoutingService extends ServiceProvider
{
    /**
     * Define theme routes namespace.
     */
    public function register()
    {
        Route::group([
	        'middleware' => [
	        	'auth.basic',
				//'auth.wp_login',
				//'auth.token'
			],
            'namespace' => 'Theme\Controllers'
        ], function () {
            require themosis_path('theme.resources').'routes.php';
        });
    }
}
```

### Middleware on Route

```php
// inside themosis-theme/resources/routes.php

Route::get('home', function(Input $request)
{
    return view('welcome');
    
})->middleware('auth.wp_login');
```

### Config

Please install and study the default [config file](config/auth.config.php) as described above to learn how to use this component.

## Get Involved

To learn more about how to use ```wp-kit``` check out the docs:

[View the Docs](https://github.com/wp-kit/theme/tree/docs/README.md)

Any help is appreciated. The project is open-source and we encourage you to participate. You can contribute to the project in multiple ways by:

- Reporting a bug issue
- Suggesting features
- Sending a pull request with code fix or feature
- Following the project on [GitHub](https://github.com/wp-kit)
- Sharing the project around your community

For details about contributing to the framework, please check the [contribution guide](https://github.com/wp-kit/theme/tree/docs/Contributing.md).

## Requirements

Wordpress 4+

PHP 5.6+

## License

wp-kit/auth is open-sourced software licensed under the MIT License.
