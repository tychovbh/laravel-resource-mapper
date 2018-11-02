# laravel-resource-mapper

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]


Laravel Resource Mapper is created by, and is maintained by Tycho, and is a Laravel/Lumen package to map data from external Resources. Feel free to check out the [change log](CHANGELOG.md), [releases](https://github.com/tychovbh/laravel-bluebillywig/releases), [license](LICENSE.md), and [contribution guidelines](CONTRIBUTING.md)


## Install

Laravel Resource Mapper requires PHP 7.1 or 7.2. This particular version supports Laravel 5.5 - 5.7 only and Lumen.

To get the latest version, simply require the project using Composer.

``` bash
$ composer require tychovbh/laravel-resource-mapper
```

Once installed, if you are not using automatic package discovery, then you need to register the `Tychovbh\ResourceMapper\ResourceMapperServiceProvider` service provider in your `config/app.php`.

In Lumen add de Service Provider in `bootstrap/app.php`:
```php
$app->register(\Tychovbh\ResourceMapper\ResourceMapperServiceProvider::class);
```

## Configuration

Laravel Resource Mapper has mapping configuration.

To get started, you'll can publish all vendor assets:

``` bash
$ php artisan vendor:publish --tag=laravel-resource-mapper
```

This will create a `config/resource-mapper.php` file in your app that you can modify to set your configuration. Also, make sure you check for changes to the original config file in this package between releases.

In lumen you have to create the configuration file manually since `vendor:publish` is not available. Create the file `config/resource-mapper.php` and copy paste the [example file](https://github.com/tychovbh/laravel-resource-mapper/blob/master/config/resource-mapper.php).

You can add as many mappings as you wish give them a name that looks like your API endpoint:
````php
use Tychovbh\ResourceMapper\RawResource;

// Let's say i'm retrieving a User, Company and Person from an api:
return [
    'user' => [
        // this will map ['RawEmail' => 'some@email.com'] to ['email' => 'some@email.com']
        'email' => 'RawEmail'
    ]
    'company' => [
        // You can map recursive resource value as well via dot notation
        // this will map: ['company' => ['name' => 'Bespoke Web']] to ['company' => 'Bespoke Web'] 
        'company' => 'company.name'
    ]
    'person' => [
        // You can also set a callback, then a RawResource object will be available.
        // You can use RawResource to access all resource values and map is as you like.
        // this will map ['RawFirstname' => 'john'] to ['firstname' => 'John']
        'firstname' => function (RawResource $resource) {
            return ucfirst($resource->get('RawFirstname'));
        }
        // this will map ['firstname' => 'John', 'suffix' => 'v.', 'lastname' => 'Doe'] to ['fullname' => 'John v. Doe']
        'fullname' => function (RawResource $resource) {
            return $resource->join(' ', 'firstname', 'suffix', 'lastname');
        }
    ]
    // You can also do some recursive array mapping
    'company' => [
        'user' => function (RawResource $resource) {
            return $this->config('user')->map($resource->get('RawCompany.RawUser'));
        }
    ],
    'users' => [
        'items' => function (RawResource $resource) {
            return $this->config('user')->mapCollection($resource->get('RawItems'));
        }
    ]
];
````


## Usage

##### Real Examples
Instantiate ResourceMapper class:
``` php
use Tychovbh\ResourceMapper\ResourceMapper;

// Use class injection
Route::get('/user', function(ResourceMapper $mapper) {
    $res = $guzzleClient->request('GET', 'https://some-api.com/user');
    $result = json_decode($res->getBody()->getContents(), true);
    return $mapper->config('user')->map($result);
});

// Or use Laravel helper app()
Route::get('/user', function() {
    $mapper = app('resource-mapper');
    $res = $guzzleClient->request('GET', 'https://some-api.com/user');
    $result = json_decode($res->getBody()->getContents(), true);
    return $mapper->config('user')->map($result);
});
```
Available ResourceMapper methods:
``` php
// Map via config with $RawResource as array
$mapped = $mapper->config('user')->map($RawResource)

// Map via config with $RawResource as json
$mapped = $mapper->config('user')->mapJson($RawResource)

// Skip config file and use custom mapping
$mapped = $mapper->mapping([
    'title' => 'RawTitle'
])->map($RawResource)

// Map via config with a $RawResource as a Collection of items
$mapped = $mapper->config('user')->mapCollection([
    [
        'RawFistname' => 'John',
    ],
    [
        'RawFistname' => 'Alex',
    ],
])
```

Available RawResource methods:
```php
$RawResource = new RawResource([
    'title' => 'my new website',
    'firstname' => 'John',
    'prefix' => 'v',
    'lastname' => 'Doe',
    'company' => [
        'name' => 'Bespoke Web'
    ]
])

// Get value from array
$title = $RawResource->get('title')
echo $title; // my new website

// Get recursive value from array
$company = $RawResource->get('company.name')
echo $company; // Bespoke Web

// Join values from array with a delimiter
$title = $RawResource->join(' ', 'firstname', 'prefix, 'lastname')
echo $title; // John v Doe
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email info@bespokeweb.nl instead of using the issue tracker.

## Credits

- [Tycho][link-author]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/tychovbh/laravel-resource-mapper.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/tychovbh/laravel-resource-mapper/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/tychovbh/laravel-resource-mapper.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/tychovbh/laravel-resource-mapper.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/tychovbh/laravel-resource-mapper.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/tychovbh/laravel-resource-mapper
[link-travis]: https://travis-ci.org/tychovbh/laravel-resource-mapper
[link-scrutinizer]: https://scrutinizer-ci.com/g/tychovbh/laravel-resource-mapper/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/tychovbh/laravel-resource-mapper
[link-downloads]: https://packagist.org/packages/tychovbh/laravel-resource-mapper
[link-author]: https://github.com/tychovbh
[link-contributors]: ../../contributors
