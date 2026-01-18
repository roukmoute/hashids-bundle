[![SymfonyInsight](https://insight.symfony.com/projects/be961d5c-da56-44b1-a094-e27066802a2d/mini.svg)](https://insight.symfony.com/projects/be961d5c-da56-44b1-a094-e27066802a2d)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/roukmoute/hashids-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/roukmoute/hashids-bundle/?branch=master)
![Packagist Downloads](https://img.shields.io/packagist/dt/roukmoute/hashids-bundle)

# HashidsBundle

> **Note:** This bundle is maintained, but for **new projects**, consider using [roukmoute/sqids-bundle](https://github.com/roukmoute/sqids-bundle) instead.
> [Sqids](https://sqids.org/) is the official successor to Hashids, featuring a simpler algorithm, consistent cross-language output, and a built-in profanity blocklist.
> However, Sqids is **not a drop-in replacement** — it produces different IDs. Only migrate if you don't rely on previously generated Hashids.
> See the [official Hashids recommendation](https://github.com/hashids/.github/blob/main/profile/README.md) for more details.

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

    # if set to true, it will continue with the next available value resolvers
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

## Hashids Value Resolver

The hashids value resolver attempts to convert any attribute set in the route into
an integer parameter.

### Using the `#[Hashid]` attribute

The recommended way is to use the `#[Hashid]` attribute on your controller parameter:

```php
use Roukmoute\HashidsBundle\Attribute\Hashid;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/posts/{id}')]
public function show(#[Hashid] int $id): Response
{
    // $id is automatically decoded from the hashid
}
```

### Using route aliases

You can also use `hashid` or `id` as route parameter names:

```php
#[Route('/users/{hashid}')]
public function getAction(int $user): Response
{
}
```

or

```php
#[Route('/users/{id}')]
public function getAction(int $user): Response
{
}
```

### Using the `_hash_` prefix

You can have several hashids in the same URL using the `_hash_` prefix:

```php
#[Route('/users/{_hash_user}/status/{_hash_status}')]
public function getAction(int $user, int $status): Response
{
}
```

The keys must match the controller parameter names:

```php
//                          _hash_user  _hash_status
//                                 ↕            ↕
public function getAction(int $user, int $status)
```

You will receive a `LogicException` if an explicit hash could not be decoded correctly.

## Using auto_convert

`auto_convert` tries to convert all arguments in controller.

```yaml
roukmoute_hashids:
  auto_convert: true
```

Based on the example above:

```php
#[Route('/users/{user}/status/{status}')]
public function getAction(int $user, int $status): Response
{
}
```

It will not be possible to get an exception of type `LogicException` from the
bundle if it is activated.

## Using passthrough

`passthrough` allows to continue with the next available value resolvers.
So if you would like to retrieve an object instead of an integer, just activate
passthrough:

```yaml
roukmoute_hashids:
    passthrough: true
```

Based on the example above:

```php
#[Route('/users/{hashid}')]
public function getAction(User $user): Response
{
}
```

As you can see, the passthrough feature allows to use `EntityValueResolver`
or any other `ValueResolverInterface` you would have created.

## Twig Extension
### Usage

```twig
{{ path('users.show', {'hashid': user.id | hashids_encode }) }}
{{ app.request.query.get('hashid') | hashids_decode }}
```
