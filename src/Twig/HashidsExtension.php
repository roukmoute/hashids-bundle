<?php

declare(strict_types=1);

namespace Roukmoute\HashidsBundle\Twig;

use Hashids\HashidsInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class HashidsExtension extends AbstractExtension
{
    private HashidsInterface $hashids;

    public function __construct(HashidsInterface $hashids)
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

    public function encode(int $number): string
    {
        return $this->hashids->encode($number);
    }

    /**
     * @return array<int, ?int>
     */
    public function decode(string $hash): array
    {
        return $this->hashids->decode($hash);
    }
}
