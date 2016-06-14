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
    alphabet:           ""
```

## Usage

```php
$hashids = $this->get('hashids');
```

Next it's the same things of [official documentation][2]

[1]: https://github.com/ivanakimov/hashids.php
[2]: http://hashids.org/php/
