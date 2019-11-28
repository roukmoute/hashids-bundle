<?php

declare(strict_types=1);

namespace Roukmoute\HashidsBundle\Twig;

use Roukmoute\HashidsBundle\Hashids;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class HashidsExtension extends AbstractExtension
{
    /**
     * @var Hashids
     */
    private $hashids;

    public function __construct(Hashids $hashids)
    {
        $this->hashids = $hashids;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('hashids_encode', [$this, 'encode']),
            new TwigFilter('hashids_decode', [$this, 'decode']),
        ];
    }

    public function encode($number): string
    {
        return $this->hashids->encode($number);
    }

    public function decode($hash): array
    {
        return $this->hashids->decode($hash);
    }
}
