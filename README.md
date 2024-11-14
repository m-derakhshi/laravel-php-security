# Laravel Security Check Package

This package helps you quickly assess and improve the security settings of your PHP environment within a Laravel application. It checks essential security configurations and provides recommendations to enhance security by adjusting your `php.ini` file.

## Installation

### Step 1: Install the Package

To include this package in your Laravel project:

```bash
composer require m-derakhshi/laravel-php-security
```

### Step 2: Publish the Views (Optional)

To customize the view template, publish the package views:

```bash
php artisan vendor:publish --tag=views --provider="MDerakhshi\SecurityCheck\LaravelPHPSecurityServiceProvider"
```

## Usage

### Route and Controller

1. Create a route and controller action for the security check. For example, you might use a `SecurityCheckController`:

```php
// In web.php
Route::get('/security-check', [SecurityCheckController::class, 'index']);
```

2. Create the controller to handle the package:

```php
<?php

namespace App\Http\Controllers;

use MDerakhshi\SecurityCheck\LaravelPHPSecurityCheck;

class SecurityCheckController extends Controller
{
    public function index(LaravelPHPSecurityCheck $securityCheck)
    {
        $result = $securityCheck->checkSettings();
        return view('laravel-php-security-check::laravel-php-security-check', $result);
    }
}
```

### Displaying the Security Check

Visit `/security-check` in your Laravel application to view the PHP security settings and recommended adjustments.

## Features

- **PHP Extensions Check**: Confirms essential extensions (like GD, intl, mbstring, etc.) are enabled.
- **Security Settings Check**: Validates critical PHP settings such as `register_argc_argv`, `display_errors`, `expose_php`, and more.
- **Recommended php.ini Adjustments**: Displays suggestions for modifying `php.ini` settings to enhance security.

## Customization

After publishing, the view files are located in `resources/views/vendor/laravel-php-security-check/` and can be modified to adjust the display as needed.

## License

This package is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).
