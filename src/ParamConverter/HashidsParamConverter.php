<?php

declare(strict_types=1);

namespace Roukmoute\HashidsBundle\ParamConverter;

use Hashids\HashidsInterface;
use LogicException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class HashidsParamConverter implements ParamConverterInterface
{
    private string $alphabet;
    private bool $autoConvert;
    private HashidsInterface $hashids;
    private bool $passthrough;

    public function __construct(HashidsInterface $hashids, bool $passthrough, bool $autoConvert, string $alphabet)
    {
        $this->hashids = $hashids;
        $this->passthrough = $passthrough;
        $this->autoConvert = $autoConvert;
        $this->alphabet = $alphabet;
    }

    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $this->decode($request, $configuration);

        return $this->continueWithNextParamConverters();
    }

    public function supports(ParamConverter $configuration): bool
    {
        return true;
    }

    private function decode(Request $request, ParamConverter $configuration): void
    {
        $hash = $this->getHash($request, $configuration);

        if ($this->isSkippable($hash)) {
            return;
        }

        $name = $configuration->getName();
        $hashids = $this->hashids->decode($hash);

        if ($this->hasHashidDecoded($hashids)) {
            $request->attributes->set($name, current($hashids));
        }

        if ($this->autoConvert && !$this->hasHashidDecoded($hashids)) {
            throw new LogicException(sprintf('Unable to decode parameter "%s".', $name));
        }
    }

    /**
     * We check in order if we find in request:
     * - "_hash_$name"
     * - $name (if autoconvert)
     * - hashid/id
     */
    private function getHash(Request $request, ParamConverter $configuration): string
    {
        $name = $configuration->getName();

        if (empty($name)) {
            return '';
        }

        $hash = $request->attributes->get('_hash_' . $name);

        if (!isset($hash) && $this->autoConvert) {
            $hash = $request->attributes->get($name);
            if (!is_string($hash)) {
                $hash = null;
            }
        }

        if (!isset($hash)) {
            $hash = $this->getHashFromAliases($request);
        }

        if (!is_string($hash)) {
            $hash = '';
        }

        return $hash;
    }

    private function getHashFromAliases(Request $request): string
    {
        $hash = '';

        if (!$request->attributes->has('hashids_prevent_alias')) {
            foreach (['hashid', 'id'] as $alias) {
                if ($request->attributes->has($alias)) {
                    $aliasAttribute = $request->attributes->get($alias);
                    if (!is_string($aliasAttribute)) {
                        continue;
                    }
                    $hash = $aliasAttribute;
                    $request->attributes->set('hashids_prevent_alias', true);
                    break;
                }
            }
        }

        return $hash;
    }

    private function isSkippable(string $hash): bool
    {
        return empty($hash) || !$this->allCharsAreInAlphabet($hash);
    }

    private function allCharsAreInAlphabet(string $hash): bool
    {
        return (bool) preg_match(sprintf('{^[%s]+$}', $this->alphabet), $hash);
    }

    /**
     * @param array<int, ?int> $hashids
     */
    private function hasHashidDecoded(array $hashids): bool
    {
        return is_int(reset($hashids));
    }

    private function continueWithNextParamConverters(): bool
    {
        return !$this->passthrough;
    }
}
