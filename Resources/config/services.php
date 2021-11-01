<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Hashids\Hashids;
use Hashids\HashidsInterface;
use Roukmoute\HashidsBundle\ParamConverter\HashidsParamConverter;
use Roukmoute\HashidsBundle\Twig\HashidsExtension;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set(HashidsInterface::class, Hashids::class)
            ->args([param('hashids.salt'), param('hashids.min_hash_length'), param('hashids.alphabet')])

        ->set('hashids.converter', HashidsParamConverter::class)
            ->args([service(HashidsInterface::class), param('hashids.passthrough'), param('hashids.auto_convert'), param('hashids.alphabet')])
            ->tag('request.param_converter', ['priority' => 1, 'converter' => 'hashids.converter'])

        ->set('hashids.twig.extension', HashidsExtension::class)
            ->args([service(HashidsInterface::class)])
            ->tag('twig.extension')
    ;
};
