[![SensioLabsInsight](https://insight.sensiolabs.com/projects/e79d4122-c9ad-454f-a1ac-981dd683144f/mini.png)](https://insight.sensiolabs.com/projects/e79d4122-c9ad-454f-a1ac-981dd683144f)

# HashidsBundle

Integrates [hashids/hashids][1] in a Symfony2 project.

## Installation

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```
    composer require roukmoute/hashids-bundle
```

This command requires you to have Composer installed globally.

## Enable the Bundle

Then, enable the bundle by adding the following line in the ``app/AppKernel.php``
file of your project:

```php
<?php
// app/AppKernel.php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // …
            new Roukmoute\HashidsBundle\RoukmouteHashidsBundle()
        );
        // …
```

The configuration looks as follows :

```yaml
roukmoute_hashids:

    # if set, the hashids will differ from everyone else's
    salt:               ""

    # if set, will generate minimum length for the id
    # 0 — meaning hashes will be the shortest possible length
    min_hash_length:    0

    # if set, will use only characters of alphabet string
    alphabet:           "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890"

    # if set to true, guess automatically hashid
    autowire:           false
```

## Usage

```php
$hashids = $this->get('hashids');
```

Next it's the same things of [official documentation][2].

## Other Features

The `Roukmoute\HashidsBundle\Hashids` has extra features:

```php
$minHashLength = 42;

// Edit the minimum hash length.
$this->get('hashids')->setMinHashLength($minHashLength)->encode(1, 2, 3);

// Encode with a custom minimum hash length.
$this->get('hashids')->encodeWithCustomHashLength($minHashLength, 1, 2, 3);
```

Hashids Converter
=================

Converter Name: `hashids.converter`

The hashids converter attempts to convert request hashid attributes to a
id for fetch a Doctrine entity. 

For specify to use hashids converter just add `"id" = "hashid"` in 
options.

```php
/**
 * @Route("/users/{hashid}")
 * @ParamConverter("user", class="RoukmouteBundle\Entity\User", options={"id" = "hashid"})
 */
public function getAction(User $user)
{
}
```

You could have several hashids one the same URL.
Just finish your word option with "hashid":

```php
/**
 * @Route("/users/{userHashid}/status/{statusHashid}")
 * @ParamConverter("user", class="RoukmouteBundle\Entity\User", options={"id" = "userHashid"})
 * @ParamConverter("status", class="RoukmouteBundle\Entity\Notification", options={"id" = "statusHashid"})
 */
public function getAction(User $user, Status $status)
{
}
```

Defining Hashid Automatically (Autowiring)
==========================================

Autowiring allows to guess hashid with minimal configuration.
It automatically resolves the variable in route.
The ParamConverter component will be able to automatically guess
the hashid when configuration has autowire to true.

```
roukmoute_hashids:
    autowire: true
```

The autowiring subsystem will detect the hashid.

Base on the example above:

```php
/**
 * @Route("/users/{hashid}")
 * @ParamConverter("user", class="RoukmouteBundle\Entity\User", options={"id" = "hashid"})
 */
public function getAction(User $user)
{
}
```

Now you can do simply that:

```php
/**
 * @Route("/users/{hashid}")
 */
public function getAction(User $user)
{
}
```

Or can directly use `id` now !

```php
/**
 * @Route("/users/{id}")
 */
public function getAction(User $user)
{
}
```

As you can see, the autowiring feature reduces the amount of 
configuration required to define a hashid.

# Twig Extension
## Usage
```twig
{{ path('users.show', {'hashid': user.id | hashids_encode }) }}
{{ app.request.query.get('hashid') | hashids_decode }}
```

[1]: https://github.com/ivanakimov/hashids.php
[2]: http://hashids.org/php/
