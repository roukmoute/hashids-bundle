[![SensioLabsInsight](https://insight.symfony.com/projects/815f344f-3f92-4d44-963b-c0d34599f0ce/mini.svg)](https://insight.symfony.com/account/widget?project=815f344f-3f92-4d44-963b-c0d34599f0ce) [![Scrutinizer](https://scrutinizer-ci.com/g/roukmoute/HashidsBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/roukmoute/HashidsBundle/)

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
```

## Usage

```php

use Hashids\HashidsInterface;

public function postShow(HashidsInterface $hashids): Response
{
    $hashids->…
}
```

Next it's the same things of [official documentation](http://hashids.org/php/).

## Other Features

The `Roukmoute\HashidsBundle\Hashids` has extra features:

```php
$minHashLength = 42;

// Edit the minimum hash length.
$hashids->setMinHashLength($minHashLength)->encode(1, 2, 3);

// Encode with a custom minimum hash length.
$hashids->encodeWithCustomHashLength($minHashLength, 1, 2, 3);
```

## Hashids Converter

Converter Name: `hashids.converter`

The hashids converter attempts to convert any attribute set in the route into an integer parameter.

You could use `hashid` or `id` to add :

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

You could have several hashids one the same URL:

```php
/**
 * @Route("/users/{user}/status/{status}")
 */
public function getAction(int $user, int $status)
{
}
```

## Using Passthrough

`Passthrough` allows to continue with the next available param converters.  
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
or any another `ParamConverter` you would have created.

## Twig Extension
### Usage

```twig
{{ path('users.show', {'hashid': user.id | hashids_encode }) }}
{{ app.request.query.get('hashid') | hashids_decode }}
```
