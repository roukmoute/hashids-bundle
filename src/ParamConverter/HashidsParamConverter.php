<?php

namespace Roukmoute\HashidsBundle\ParamConverter;

use Doctrine\Common\Persistence\ManagerRegistry;
use Roukmoute\HashidsBundle\Hashids;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\DoctrineParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class HashidsParamConverter extends DoctrineParamConverter implements ParamConverterInterface
{
    protected $hashids;

    public function __construct(Hashids $hashids, ManagerRegistry $registry)
    {
        parent::__construct($registry);
        $this->hashids = $hashids;
    }

    /**
     * {@inheritdoc}
     *
     * @throws  \LogicException  When unable to guess how to get a id from the request information
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $options = $configuration->getOptions();

        if (!isset($options['id']) || mb_strtolower(mb_substr($options['id'], -6)) !== 'hashid') {
            return false;
        }

        $hashid = $request->attributes->get($options['id']);
        $decodeHashids = $this->hashids->decode($hashid);
        if (!is_array($decodeHashids)
            || !isset($decodeHashids[0])
            || false === ($id = $decodeHashids[0])
            || false === is_int($id)
        ) {
            throw new \LogicException('Unable to guess hashid from the request information.');
        }
        $request->attributes->set('id', $id);
        unset($options['id']);
        $configuration->setOptions($options);
        $configuration->setIsOptional(true);
        parent::apply($request, $configuration);
        
        $name = $configuration->getName();
        if (!$request->attributes->get($name)) {
            throw new \LogicException(sprintf('%s "%s" not found.', ucfirst($name), $hashid));
        }

        return true;
    }
}
