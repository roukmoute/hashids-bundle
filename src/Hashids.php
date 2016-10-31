<?php

namespace Roukmoute\HashidsBundle;

final class Hashids extends \Hashids\Hashids
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
        $minHashLengthBak = $this->minHashLength;
        $this->minHashLength = (int) $minHashLength;
        $hashids = $this->encode($numbers);
        $this->minHashLength = $minHashLengthBak;

        return $hashids;
    }
}
