<?php

declare(strict_types=1);

namespace Roukmoute\HashidsBundle;

class Hashids extends \Hashids\Hashids
{
    /**
     * Sets the minimum hash length.
     */
    public function setMinHashLength(int $minHashLength): void
    {
        $this->minHashLength = $minHashLength;
    }

    /**
     * Encode parameters to generate a hash with custom minimum hash length.
     *
     * @param array ...$numbers parameters to encode
     */
    public function encodeWithCustomHashLength(int $minHashLength, int ...$numbers): string
    {
        $originalHashLength = $this->minHashLength;
        $this->setMinHashLength($minHashLength);
        $hashids = $this->encode($numbers);
        $this->restoreMinHashLength($originalHashLength);

        return $hashids;
    }

    /**
     * Decode parameter to generate a decoded hash with custom minimum hash length.
     */
    public function decodeWithCustomHashLength(int $minHashLength, string $hash): array
    {
        $originalHashLength = $this->minHashLength;
        $this->setMinHashLength($minHashLength);
        $hashids = $this->decode($hash);
        $this->restoreMinHashLength($originalHashLength);

        return $hashids;
    }

    protected function restoreMinHashLength(int $originalMinHashLength): void
    {
        $this->minHashLength = $originalMinHashLength;
    }
}
