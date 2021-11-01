[![SymfonyInsight](https://insight.symfony.com/projects/be961d5c-da56-44b1-a094-e27066802a2d/mini.svg)](https://insight.symfony.com/projects/be961d5c-da56-44b1-a094-e27066802a2d)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/roukmoute/hashids-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/roukmoute/hashids-bundle/?branch=master)
![Packagist Downloads](https://img.shields.io/packagist/dt/roukmoute/hashids-bundle)

# HashidsBundle

Integrates [hashids/hashids](https://github.com/ivanakimov/hashids.php) in a Symfony project.

## Installation using composer

These commands requires you to have Composer installed globally.  
Open a command console, enter your project directory and execute the following 
commands to download the latest stable version of this bundle:

### Using Symfony Flex

```
    composer config extra.symfony.allow-contrib true
    composer req roukmoute/hashids-bundle
```

### Using Symfony Framework only

```
    composer require roukmoute/hashids-bundle
```

If this has not been done automatically, enable the bundle by adding the 
following line in the `config/bundles.php` file of your project:

```php
<?php

return [
    …,
    Roukmoute\HashidsBundle\RoukmouteHashidsBundle::class => ['all' => true],
];
```

## Configuration

The configuration (`config/packages/roukmoute_hashids.yaml`) looks as follows :

```yaml
roukmoute_hashids:

    # if set, the hashids will differ from everyone else's
    salt:            ""

    # if set, will generate minimum length for the id
    # 0 — meaning hashes will be the shortest possible length
    min_hash_length: 0

    # if set, will use only characters of alphabet string
    alphabet:        "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890"

    # if set to true, it will continue with the next available param converters
    passthrough:     false

    # if set to true, it tries to convert all arguments passed to the controller
    auto_convert:    false
```

## Usage

```php

use Hashids\HashidsInterface;

public function postShow(HashidsInterface $hashids): Response
{
    $hashids->…
}
```

Next it's the same things of [official documentation](https://hashids.org/php/).

## Hashids Converter

Converter Name: `hashids.converter`

The hashids converter attempts to convert any attribute set in the route into 
an integer parameter.

You could use `hashid` or `id`:

```php
/**
 * @Route("/users/{hashid}")
 */
public function getAction(int $user)
{
}
```

or

```php
/**
 * @Route("/users/{id}")
 */
public function getAction(int $user)
{
}
```

You could have several hashids in the same URL prefixed with  `_hash_`.

```php
/**
 * @Route("/users/{_hash_user}/status/{_hash_status}")
 */
public function getAction(int $user, int $status)
{
}
```

The keys must be the same as in parameters controller:

```php
/**
 *                          _hash_user _hash_status
 *                                 ↕            ↕
 * public function getAction(int $user, int $status)
 */
```

You will receive a `LogicException` if a hash could not be decoded correctly.

## Using auto_convert

`auto_convert` tries to convert all arguments in controller.

```yaml
roukmoute_hashids:
  auto_convert: true
```

Base on the example above:

```php
/**
 * @Route("/users/{user}/status/{status}")
 */
public function getAction(int $user, int $status)
{
}
```

It will not be possible to get an exception of type `LogicException` from the 
bundle if it is activated.

## Using passthrough

`passthrough` allows to continue with the next available param converters.  
So if you would like to retrieve an object instead of an integer, just active 
passthrough :

```yaml
roukmoute_hashids:
    passthrough: true
```

Base on the example above:

```php
/**
 * @Route("/users/{hashid}")
 */
public function getAction(User $user)
{
}
```

As you can see, the passthrough feature allows to use `DoctrineParamConverter` 
or any another `ParamConverterInterface` you would have created.

## Twig Extension
### Usage

```twig
{{ path('users.show', {'hashid': user.id | hashids_encode }) }}
{{ app.request.query.get('hashid') | hashids_decode }}
```
