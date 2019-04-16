<?php

declare(strict_types=1);

namespace Roukmoute\HashidsBundle\ParamConverter;

use Roukmoute\HashidsBundle\Hashids;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class HashidsParamConverter implements ParamConverterInterface
{
    /**
     * @var Hashids
     */
    protected $hashids;
    /**
     * @var bool
     */
    private $passthrough;

    public function __construct(Hashids $hashids, bool $passthrough)
    {
        $this->hashids = $hashids;
        $this->passthrough = $passthrough;
    }

    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $this->setHashid($request, $configuration);
        $this->removeHashidOption($configuration);

        return $this->continueWithNextParamConverters();
    }

    public function supports(ParamConverter $configuration): bool
    {
        return true;
    }

    private function setHashid(Request $request, ParamConverter $configuration): void
    {
        $hashids = $this->hashids->decode(
            $this->getIdentifier(
                $request,
                array_replace(['hashid' => null], $configuration->getOptions()),
                (string) $configuration->getName()
            )
        );

        if ($this->hasHashidDecoded($hashids)) {
            $request->attributes->set($configuration->getName(), current($hashids));
        }
    }

    private function getIdentifier(Request $request, $options, string $name): string
    {
        if ($options['hashid'] && !is_array($options['hashid'])) {
            $name = $options['hashid'];
        }

        if ($request->attributes->has($name)) {
            return (string) $request->attributes->get($name);
        }

        foreach (['id', 'hashid'] as $item) {
            if ($request->attributes->has($item) && !$options['hashid']) {
                return (string) $request->attributes->get($item);
            }
        }

        return '';
    }

    private function hasHashidDecoded($hashids): bool
    {
        return $hashids && is_iterable($hashids);
    }

    private function removeHashidOption(ParamConverter $configuration): void
    {
        $options = $configuration->getOptions();

        if (isset($options['hashid'])) {
            unset($options['hashid']);
            $configuration->setOptions($options);
        }
    }

    private function continueWithNextParamConverters(): bool
    {
        return !$this->passthrough;
    }
}
