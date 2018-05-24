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

### Connect QuickBooks account

Go to `{yourdomain}/quickbooks/connect` to 
connect your QuickBooks account with your application.

### Sync Eloquent model to QuickBooks

You need to extend `LifeOnScreen\LaravelQuickBooks\QuickBooksEntity` class which is already 
extending the Eloquent model.

Then you have to define:
 * `quickBooksIdColumn` -> default value is quickbooks_id (if want to use different column to store QuickBooks id in database change this value.)
 * `quickBooksResource` -> Use one of `LifeOnScreen\LaravelQuickBooks\QuickBooksResources` constants.
 * `getQuickBooksArray()` -> This method must return the associative array which will be synced to QuickBooks.

Usage example:

```php
namespace App\Models\Company;

use LifeOnScreen\LaravelQuickBooks\QuickBooksEntity;
use LifeOnScreen\LaravelQuickBooks\QuickBooksResources;

class Company extends QuickBooksEntity
{
    /**
     * Database column name
     * This is optional default value is 'quickbooks_id'
     * @var string
     */
    protected $quickBooksIdColumn = 'quickbooks_id';
        
    /**
     * Use one of LifeOnScreen\LaravelQuickBooks\QuickBooksResources constants
     * @var array
     */
    protected $quickBooksResource = QuickBooksResources::CUSTOMER;
    
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
When you want to sync resource you must call `syncToQuickbooks()`. Method returns true if syncing is successful.
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
    if($company->syncToQuickbooks()){
        return 'Success';
    }
    return $company->getLastQuickBooksError()->getOAuthHelperError();
}
```

## Changelog

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email author instead of using the issue tracker.

## Credits

- [Jani Cerar](https://github.com/janicerar)

## License

MIT license. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/lifeonscreen/laravel-quickbooks.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/lifeonscreen/laravel-quickbooks.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/lifeonscreen/laravel-quickbooks
[link-downloads]: https://packagist.org/packages/lifeonscreen/laravel-quickbooks
[link-author]: https://github.com/LifeOnScreen