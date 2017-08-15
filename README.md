# WPKit Auth

This is a Themosis PHP Component that handles Authentication.

Currently there are no Authentication Middleware's built into Themosis however with Illuminate Routing built into Themosis, we are able to run Middleware on Routes and Route Groups.

WPKit Auth comes with three types of Auth Middleware that integrate directly with Wordpress to authenticate users:

* Basic Authentication: auth.basic
* Form Authentication: auth.form
* Token Authentication: auth.token

**This will be changing soon, Form and Token Middleware will be removed an replaced with a Wordpress UserProvider Guard so that Illuminate Authenticate and TokenGuard can be used.**

## Installation

If you're using Themosis, install via composer in the Themosis route folder, otherwise install in your theme folder:

```php
composer require "wp-kit/auth"
```

## Registering Service Provider

**Within Themosis Theme**

Just register the service provider and facade in the providers config and theme config:

```php
//inside themosis-theme/resources/config/providers.config.php

return [
    //
    WPKit\Hashing\HashingServiceProvider::class, // we need this too
    WPKit\Auth\AuthServiceProvider::class,
    //
];
```

## Config

> **Note:** This will be changing to a traditional config file similar to that found in Laravel once the UserProvider Guard has been built

The recommended method of installing config files for WPKit Components is via ```wp-kit/vendor-publish``` command.

First, [install WP CLI](http://wp-cli.org/), and then install the package via:

```wp package install wp-kit/vendor-publish```

Once installed you can run:

```wp kit vendor:publish```

For more information, please visit [wp-kit/vendor-publish](https://github.com/wp-kit/vendor-publish).

Alternatively, you can place the [config file(s)](config) in your ```theme/resources/config``` directory manually.


## Using Middleware

You can activate middleware on the route group or route itself:

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
	        'middleware' => 'auth.basic',
	        //'middleware' => 'auth.form',
	        //'middleware' => 'auth.token',
            'namespace' => 'Theme\Controllers'
        ], function () {
            require themosis_path('theme.resources').'routes.php';
        });
    }
}
```

```php
// inside themosis-theme/resources/routes.php

Route::get('home', function(Input $request)
{

    return view('welcome');
    
})->middleware('auth.token');
```

## Requirements

Wordpress 4+

PHP 5.6+

## License

WPKit Auth is open-sourced software licensed under the MIT License.
