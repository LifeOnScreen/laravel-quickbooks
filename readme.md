# laravel-quickbooks

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]

Take a look at [contributing.md](contributing.md) to see a to do list.

## Installation

Via Composer

``` bash
$ composer require lifeonscreen/laravel-quickbooks
```

Publish package and run migrations

``` bash
$ php artisan vendor:publish
$ php artisan migrate
```

## Usage

### Configuration

These are the variables you need to set in your .env.

```
# Client ID from the app's keys tab.
QB_CLIENT_ID=

# Client Secret from the app's keys tab.
QB_CLIENT_SECRET=

# The redirect URI provided on the Redirect URIs part under keys tab.
QB_REDIRECT_URI=

# Quickbooks scope com.intuit.quickbooks.accounting or com.intuit.quickbooks.payment
QB_SCOPE=

# Development/Production
QB_BASE_URL=
```

### Token Handling

Since every application is setup differently, you will need to create a class that extends `QuickBooksTokenHandler` to persist the tokens in your database. By default, the tokens are stored using the Laravel Cache API for 7 days.

For example, if you use the [Laravel Options](https://github.com/appstract/laravel-options) package you would create the following class somewhere in your project:

```php
namespace App\QuickBooks;

use LifeOnScreen\LaravelQuickBooks\QuickBooksTokenHandler;

class TokenHandler extends QuickBooksTokenHandler
{
    public function set($key, $value)
    {
        option([$key => $value]);
    }

    public function get($key)
    {
        return option($key);
    }
}
```

Then bind it in your `AppServiceProvider.php`:

```php
public function boot()
{        
    $this->app->bind(
        \LifeOnScreen\LaravelQuickBooks\QuickBooksTokenHandlerInterface::class, 
        \App\QuickBooks\TokenHandler::class
    );
}
```

### Connect QuickBooks account

To connect your application with your QuickBooks company you can use `QuickBooksAuthenticator` helper.
It has two methods:
* `getAuthorizationUrl()` - Returns redirect URL and puts `quickbooks_auth` cookie into Laravel cookie queue. 
Cookie is valid for 30 minutes.
* `processHook()` - Validates `quickbooks_auth` cookie and sets realm id, access token and refresh token.

Usage example:

```php
namespace App\Http\Controllers;

use LifeOnScreen\LaravelQuickBooks\QuickBooksAuthenticator;
use Cookie;

class QuickBooksController extends Controller
{
    public function connect()
    {
        return redirect(QuickBooksAuthenticator::getAuthorizationUrl())
            ->withCookies(Cookie::getQueuedCookies());
    }

    public function refreshTokens()
    {
        if (QuickBooksAuthenticator::processHook()) {
            return 'Tokens successfully refreshed.';
        }

        return 'There were some problems refreshing tokens.';
    }
}
```

### Sync Eloquent model to QuickBooks

You can either extend the `LifeOnScreen\LaravelQuickBooks\QuickBooksEntity` class which is already 
extending the Eloquent model or you can use the `LifeOnScreen\LaravelQuickBooks\SyncsToQuickBooks` trait.

Then you have to define:
 * `quickBooksResource` - One of the QuickBooks resources classes (e.g.. `\LifeOnScreen\LaravelQuickBooks\Resources\Company::class`).
 * `getQuickBooksArray()` - This method must return the associative array which will be synced to QuickBooks.
 * `quickBooksIdColumn` (optional) - The column to use for storing the QuickBooks ID (defaults to `quickbooks_id`)

Usage example:

```php
namespace App\Models\Company;

use LifeOnScreen\LaravelQuickBooks\QuickBooksEntity;
use LifeOnScreen\LaravelQuickBooks\Resources\Customer;

class Company extends QuickBooksEntity
{
    /**
     * Database column name
     * This is optional default value is 'quickbooks_id'
     * @var string
     */
    protected $quickBooksIdColumn = 'quickbooks_id';
        
    /**
     * Use one of LifeOnScreen\LaravelQuickBooks\Resources classes
     * @var array
     */
    protected $quickBooksResource = Customer::class;
    
    /**
     * @return array
     */
    protected function getQuickBooksArray(): array
    {
        return [
            'CompanyName'  => 'Example name',
            'DisplayName'  => 'Example display name',
            //...
        ];
    }
}
```
When you want to sync a resource you must call `syncToQuickBooks()`. Method returns true if syncing is successful.
You can get last QuickBooks error with method `getLastQuickBooksError()`.

Syncing example:

```php
/**
 * @return string
 * @throws \Exception
 */
public function syncExample()
{
    $company = Company::find(1);
    if ($company->syncToQuickBooks()){
        return 'Success';
    }
    return $company->getLastQuickBooksError();
}
```

### Using the QuickBooks Resource Classes

You can use the included resource classes in `LifeOnScreen\LaravelQuickBooks\Resources` to create, update, and query resources from QuickBooks. 

Examples:
```
$customer = new LifeOnScreen\LaravelQuickBooks\Resources\Customer;

// create
$customer->create([
    'GivenName'  => 'John',
    'FamilyName' => 'Smith',
]);

// update item with ID "123"
$customer->update(123, [
    'GivenName'  => 'John',
    'FamilyName' => 'Smith',
]);

// find by ID:
$customer->find(123);

// find by a specific field:
$customer->findBy('FamilyName', 'Smith');

// find multiple items:
$customer->query();
```

See `QuickBooksResource.php` for further documentation. 

## Changelog

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email author instead of using the issue tracker.

## Credits

- [Jani Cerar](https://github.com/janicerar), [Aaron Harp](https://github.com/arharp)

## License

MIT license. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/lifeonscreen/laravel-quickbooks.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/lifeonscreen/laravel-quickbooks.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/lifeonscreen/laravel-quickbooks
[link-downloads]: https://packagist.org/packages/lifeonscreen/laravel-quickbooks
[link-author]: https://github.com/LifeOnScreen