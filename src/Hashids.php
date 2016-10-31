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
}
