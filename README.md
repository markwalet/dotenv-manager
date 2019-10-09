# PHP Dotenv manager

[![Build Status](https://travis-ci.com/markwalet/dotenv-manager.svg?branch=master)](https://travis-ci.com/markwalet/dotenv-manager)
[![codecov](https://codecov.io/gh/markwalet/dotenv-manager/branch/master/graph/badge.svg)](https://codecov.io/gh/markwalet/dotenv-manager)
[![StyleCI](https://github.styleci.io/repos/142404454/shield?branch=master)](https://github.styleci.io/repos/142404454)
[![Total Downloads](https://poser.pugx.org/markwalet/dotenv-manager/downloads)](https://packagist.org/packages/markwalet/dotenv-manager)
[![Latest Stable Version](https://poser.pugx.org/markwalet/dotenv-manager/v/stable)](https://packagist.org/packages/markwalet/dotenv-manager)
[![License](https://poser.pugx.org/markwalet/dotenv-manager/license)](https://packagist.org/packages/markwalet/dotenv-manager)

A PHP package that helps you edit the .env file programmatically. Works great with software like an installation CLI.

## Installation
You can install this package with composer.

```shell
composer require markwalet/dotenv-manager
```

This package can be used in every PHP project.

### Laravel
The optional `DotenvManagerServiceProvider` makes this package package suitable for Laravel projects.

From Laravel 5.5 and up you don't have to register your service provider anymore because of the package auto-discovery feature.

If you want to register the service provider manually (required for older versions), add the following line to your `config/app.php` file:

```php
MarkWalet\DotenvManager\DotenvManagerServiceProvider::class
```

The service provider is also compatible with Laravel Lumen. Just add the following line to your `bootstrap/app.php` file:
```php
$app->register(\MarkWalet\DotenvManager\DotenvManagerServiceProvider::class);
```

## Usage
You can get the dotenv manager by resolving it with the app Laravel service container. 
This will give you a singleton `MarkWalet\DotenvManager\DotenvManager` instance with the right dependencies.
```php
$dotenv = app(DotenvManager::class);
```

You can also manually set up an `DotenvManager` class when you are not using Laravel.

Once you have a dotenv instance you can add a new value to the dotenv file:

```php
$dotenv->add("FOO", "Bar")->after("EXISTING_KEY");
```

If you don't specify a location for the new value, the value will be added at the end of the file.

You can also edit dotenv variables:

```php
$dotenv->update("EXISTING_KEY", "updatedValue");
```

This will replace the original value of `EXISTING_KEY` with `updatedValue`.

All values will be automatically formatted to a valid dotenv value.

### Mutating multiple lines
You can use the `mutate()` method when you want to apply multiple changes to the file. The syntax looks a lot like the syntax in Laravel migrations:

```php
/**
 * Original content: 
 *
 * TEST1=value1
 * TEST2=value2
 * TEST3=value3
 */
 
$dotenv->mutate(function(DotenvBuilder $builder){
    $builder->add('TEST4', 'escaped value');
    $builder->update('TEST2', 'updated')->after('TEST3');
    $builder->delete('TEST1');
});

/**
 * New content: 
 *
 * TEST3=value3
 * TEST2=updated
 * TEST4="escaped value"
 */
```

### Available methods
Below you will find every available method and its underlying class. These methods can be called on the `DotenvManager` class itself and also on the `DotenvBuilder` you get when mutating the dotenv file.

Method  |  Returns
------------- | -------------
`add(string $key, $value = null)` | [Addition](src/Changes/Addition.php)
`create(string $key, $value = null)` | [Addition](src/Changes/Addition.php)
`set(string $key, $value = null)` | [Update](src/Changes/Update.php)
`update(string $key, $value = null)` | [Update](src/Changes/Update.php)
`move(string $key)` | [Move](src/Changes/Move.php)
`delete(string $key)` | [Delete](src/Changes/Delete.php)
`unset(string $key)` | [Delete](src/Changes/Delete.php)

## Extending the builder
You can also extend the builder with your own implementation of the `Change` class. In the example below we are making a class that can increment a value quickly:

```php
use MarkWalet\DotenvManager\Changes\Change;

class Increment extends Change
{
    use HasKey;

    function __construct(string $key)
    {
        $this->key = $key;
    }

    public function apply(string $content): string
    {
        $search = '/'.$this->getKey().'=(.*)/';
        preg_match($search, $content, $matches);
        $value = $matches[1];

        $replacement = $this->getKey().'='.($value + 1);

        return preg_replace($search, $replacement, $content);
    }
}

$dotenv->extend('increment', Increment::class);
```

After we extended the builder we can call it through the `DotenvManager` instance:

```php
/**
 * Original content: 
 *
 * TEST1=value1
 * INCREMENT=56
 */
$dotenv->increment('INCREMENT');

/**
 * New content: 
 *
 * TEST1=value1
 * INCREMENT=57
 */
```
