# wp-kit/auth

This is a [```Themosis```](http://framework.themosis.com/) PHP Component that handles authentication.

Currently there are no authentication middleware built into ```Themosis``` however with [```illuminate/routing```](https://github.com/illuminate/routing) built into ```Themosis```, we are able to run ```Middleware``` on ```Routes``` and ```Route Groups```.

```wp-kit/auth``` comes with three types of ```Middleware``` that integrate directly with Wordpress to authenticate users:

* Basic Authentication: [auth.basic](https://github.com/wp-kit/auth/blob/master/src/Auth/Middleware/BasicAuth.php)
* Form Authentication: [auth.form](https://github.com/wp-kit/auth/blob/master/src/Auth/Middleware/FormAuth.php)
* Token Authentication: [auth.token](https://github.com/wp-kit/auth/blob/master/src/Auth/Middleware/TokenAuth.php)

**This will be changing soon, Form and Token Middleware will be removed an replaced with a Wordpress UserProvider Guard so that [Illuminate\Auth\Middleware\Authenticate](https://github.com/illuminate/auth/blob/master/Middleware/Authenticate.php) and [Illuminate\Auth\TokenGuard](https://github.com/illuminate/auth/blob/master/TokenGuard.php) can be used.**

## Installation

Install via [```Composer```](https://getcomposer.org/) in the root of your ```Themosis``` installation:

```php
composer require "wp-kit/auth"
```

## Setup

### Add Service Provider

Just register the service provider and facade in the providers config and theme config:

```php
//inside themosis-theme/resources/config/providers.config.php

return [
    //
    WPKit\Hashing\HashingServiceProvider::class, // we need this too
    Illuminate\Cookie\CookieServiceProvider::class, // we need this too
    WPKit\Auth\AuthServiceProvider::class,
    //
];
```

### Add Config File

> **Note:** This will be changing to a traditional config file similar to that found in Laravel once the ```UserProvider``` Guard has been built

The recommended method of installing config files for WPKit Components is via ```wp-kit/vendor-publish``` command.

First, [install WP CLI](http://wp-cli.org/), and then install the package via:

```wp package install wp-kit/vendor-publish```

Once installed you can run:

```wp kit vendor:publish```

For more information, please visit [wp-kit/vendor-publish](https://github.com/wp-kit/vendor-publish).

Alternatively, you can place the [config file(s)](config) in your ```theme/resources/config``` directory manually.

### Allowing Headers

If using [```BasicAuth```](https://github.com/wp-kit/auth/blob/master/src/Auth/Middleware/BasicAuth.php) middleware, make sure you add the following line to your ```.htaccess``` file to allow Authorization headers:

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
		        'web.session',
	        	'auth.basic',
				//'auth.form',
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
    
})->middleware('auth.token');
```

### Config

Please install and study the default [config file](config/auth.config.php) as described above to learn how to use this component.

## Requirements

Wordpress 4+

PHP 5.6+

## License

wp-kit/auth is open-sourced software licensed under the MIT License.
