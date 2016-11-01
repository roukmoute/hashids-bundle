<?php

namespace Roukmoute\HashidsBundle\ParamConverter;

use Doctrine\Common\Persistence\ManagerRegistry;
use Roukmoute\HashidsBundle\Hashids;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\DoctrineParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HashidsParamConverter extends DoctrineParamConverter implements ParamConverterInterface
{
    /**
     * @var Hashids
     */
    protected $hashids;
    /**
     * @var bool
     */
    private $autowire;

    public function __construct(Hashids $hashids, ManagerRegistry $registry, $autowire)
    {
        parent::__construct($registry);
        $this->hashids = $hashids;
        $this->autowire = $autowire;
    }

    /**
     * {@inheritdoc}
     *
     * @throws  \LogicException  When unable to guess how to get a id from the request information
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $exception = null;

        if ($this->autowire) {
            try {
                return parent::apply($request, $configuration);
            } catch (\Exception $exception) {
                $name = $configuration->getName();
                $options = $this->getOptions($configuration);

                if ($exception instanceof NotFoundHttpException) {
                    $hashid = $this->getIdentifier($request, $options, $name);
                } else {
                    $hashid = $this->getHashIdentifier($request, $options, $name);
                }
            }
        } else {
            $options = $configuration->getOptions();

            if (!isset($options['id']) || mb_strtolower(mb_substr($options['id'], -6)) !== 'hashid') {
                return false;
            }

            $hashid = $request->attributes->get($options['id']);
        }

        return $this->decodeHashid($request, $configuration, $hashid, $exception);
    }

    /**
     * @param Request $request
     * @param ParamConverter $configuration
     * @param $hashid
     * @param \Exception|null $exception
     *
     * @return bool
     * @throws \Exception
     */
    private function decodeHashid(Request $request, ParamConverter $configuration, $hashid, \Exception $exception = null)
    {
        $options = $configuration->getOptions();
        $decodeHashids = $this->hashids->decode($hashid);

        if (!is_array($decodeHashids)
            || !isset($decodeHashids[0])
            || false === ($id = $decodeHashids[0])
            || false === is_int($id)
        ) {
            if ($exception) {
                throw $exception;
            }
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

    /**
     * @param Request $request
     * @param $options
     * @param $name
     *
     * @return array|bool|mixed
     */
    private function getHashIdentifier(Request $request, $options, $name)
    {
        $id = $this->getIdentifier($request, $options, $name);

        if (!$id && $request->attributes->has('hashid')) {
            return $request->attributes->get('hashid');
        }

        return $id;
    }
}
