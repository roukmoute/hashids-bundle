<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Hashids\Hashids;
use Hashids\HashidsInterface;
use Roukmoute\HashidsBundle\Twig\HashidsExtension;
use Roukmoute\HashidsBundle\ValueResolver\HashidsValueResolver;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set(HashidsInterface::class, Hashids::class)
            ->args([param('hashids.salt'), param('hashids.min_hash_length'), param('hashids.alphabet')])

        ->set('hashids.value_resolver', HashidsValueResolver::class)
            ->args([service(HashidsInterface::class), param('hashids.passthrough'), param('hashids.auto_convert'), param('hashids.alphabet')])
            ->tag('controller.argument_value_resolver', ['priority' => 150])

        ->set('hashids.twig.extension', HashidsExtension::class)
            ->args([service(HashidsInterface::class)])
            ->tag('twig.extension')
    ;
};
