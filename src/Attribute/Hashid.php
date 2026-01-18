<?php

declare(strict_types=1);

namespace Roukmoute\HashidsBundle\Attribute;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
final class Hashid
{
    public function __construct(
        public readonly ?string $parameter = null,
    ) {
    }
}
