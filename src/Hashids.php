<?php

namespace Roukmoute\HashidsBundle;

class Hashids extends \Hashids\Hashids
{
    /**
     * Sets the minimum hash length.
     *
     * @param int $minHashLength The minimum hash length.
     *
     * @return $this
     */
    public function setMinHashLength($minHashLength)
    {
        $this->minHashLength = (int) $minHashLength;

        return $this;
    }

    /**
     * Encode parameters to generate a hash with custom minimum hash length.
     *
     * @param int   $minHashLength  The minimum hash length.
     * @param array ...$numbers     parameters to encode
     *
     * @return string
     */
    public function encodeWithCustomHashLength($minHashLength, ...$numbers)
    {
        $newHashLength = $this->updateMinHashLength($minHashLength);
        $hashids = $this->encode($numbers);
        $this->restoreMinHashLength($newHashLength);

        return $hashids;
    }

    /**
     * Decode parameter to generate a decoded hash with custom minimum hash length.
     *
     * @param int    $minHashLength  The minimum hash length.
     * @param string $hash           parameters to encode
     *
     * @return array
     */
    public function decodeWithCustomHashLength($minHashLength, $hash)
    {
        $originalHashLength = $this->updateMinHashLength($minHashLength);
        $hashids = $this->decode($hash);
        $this->restoreMinHashLength($originalHashLength);

        return $hashids;
    }

    /**
     * @param string $newMinHashLength
     *
     * @return int
     */
    protected function updateMinHashLength($newMinHashLength)
    {
        $originalMinHashLength = $this->minHashLength;

        $this->minHashLength = (int) $newMinHashLength;

        return $originalMinHashLength;
    }

    /**
     * @param string $originalMinHashLength
     */
    protected function restoreMinHashLength($originalMinHashLength)
    {
        $this->minHashLength = $originalMinHashLength;
    }
}
