<?php

namespace Roukmoute\HashidsBundle\Twig;

use Roukmoute\HashidsBundle\Hashids;

class HashidsExtension extends \Twig_Extension
{
    /**
     * @var Hashids
     */
    private $hashids;

    public function __construct(Hashids $hashids)
    {
        $this->hashids = $hashids;
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('hashids_encode', [$this, 'encode']),
            new \Twig_SimpleFilter('hashids_decode', [$this, 'decode']),
        ];
    }

    public function encode($number)
    {
        return $this->hashids->encode($number);
    }

    public function decode($hash)
    {
        return $this->hashids->decode($hash);
    }

}
