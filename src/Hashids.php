<?php

declare(strict_types=1);

namespace Roukmoute\HashidsBundle;

use Hashids\HashidsInterface;
use ReflectionObject;
use ReflectionProperty;
use RuntimeException;

class Hashids implements HashidsInterface
{
    /** @var HashidsInterface */
    private $hashids;

    /** The minimum hash length. */
    private $minHashLength;

    /** @var ReflectionProperty */
    private $minHashLengthReflection;

    public function __construct(HashidsInterface $hashids)
    {
        $this->hashids = $hashids;
    }

    public function encode(...$numbers)
    {
        return $this->hashids->encode($numbers);
    }

    public function decode($hash)
    {
        return $this->hashids->decode($hash);
    }

    public function encodeHex($str)
    {
        return $this->hashids->encodeHex($str);
    }

    public function decodeHex($hash)
    {
        return $this->hashids->decodeHex($hash);
    }

    /**
     * Encode parameters to generate a hash with custom minimum hash length.
     *
     * @param array ...$numbers parameters to encode
     */
    public function encodeWithCustomHashLength(int $minHashLength, int ...$numbers): string
    {
        $this->setMinHashLength($minHashLength);

        $hashid = $this->hashids->encode($numbers);

        $this->restoreMinHashLength();

        return $hashid;
    }

    /**
     * Decode parameter to generate a decoded hash with custom minimum hash length.
     */
    public function decodeWithCustomHashLength(int $minHashLength, string $hash): array
    {
        $this->setMinHashLength($minHashLength);

        $decodesHashids = $this->hashids->decode($hash);

        $this->restoreMinHashLength();

        return $decodesHashids;
    }

    public function setMinHashLength(int $minHashLength): void
    {
        $hashIdsReflection = new ReflectionObject($this->hashids);
        if (!$hashIdsReflection->hasProperty('minHashLength')) {
            throw new RuntimeException(sprintf('Missing "minHashLength" property in class "%s"', get_class($this->hashids)));
        }

        $this->minHashLengthReflection = new ReflectionProperty(get_class($this->hashids), 'minHashLength');
        $this->minHashLengthReflection->setAccessible(true);
        $this->minHashLength = $this->minHashLengthReflection->getValue($this->hashids);
        $this->minHashLengthReflection->setValue($this->hashids, $minHashLength);
    }

    private function restoreMinHashLength(): void
    {
        $this->minHashLengthReflection->setValue($this->hashids, $this->minHashLength);
        $this->minHashLengthReflection->setAccessible(false);
    }
}
