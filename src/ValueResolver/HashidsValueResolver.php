<?php

declare(strict_types=1);

namespace Roukmoute\HashidsBundle\ValueResolver;

use Hashids\HashidsInterface;
use Roukmoute\HashidsBundle\Attribute\Hashid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class HashidsValueResolver implements ValueResolverInterface
{
    public function __construct(
        private readonly HashidsInterface $hashids,
        private readonly bool $passthrough,
        private readonly bool $autoConvert,
        private readonly string $alphabet,
    ) {
    }

    /**
     * @return iterable<int>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $name = $argument->getName();
        $hashidAttribute = $this->getHashidAttribute($argument);
        $routeParameter = $hashidAttribute?->parameter ?? $name;
        [$hash, $isExplicit] = $this->getHash($request, $routeParameter, $hashidAttribute !== null);

        if ($this->isSkippable($hash)) {
            return [];
        }

        $hashids = $this->hashids->decode($hash);

        if ($this->hasHashidDecoded($hashids)) {
            /** @var int $decodedValue */
            $decodedValue = reset($hashids);

            if ($this->passthrough) {
                $request->attributes->set($name, $decodedValue);

                return [];
            }

            return [$decodedValue];
        }

        if ($isExplicit) {
            throw new \LogicException(sprintf('Unable to decode parameter "%s".', $name));
        }

        return [];
    }

    private function getHashidAttribute(ArgumentMetadata $argument): ?Hashid
    {
        /** @var Hashid[] $attributes */
        $attributes = $argument->getAttributes(Hashid::class, ArgumentMetadata::IS_INSTANCEOF);

        return $attributes[0] ?? null;
    }

    /**
     * @return array{0: string, 1: bool}
     */
    private function getHash(Request $request, string $name, bool $hasHashidAttribute): array
    {
        if (empty($name)) {
            return ['', false];
        }

        $hash = $request->attributes->get('_hash_' . $name);
        if (isset($hash) && is_string($hash)) {
            return [$hash, true];
        }

        if ($this->autoConvert || $hasHashidAttribute) {
            $hash = $request->attributes->get($name);
            if (is_string($hash)) {
                return [$hash, $hasHashidAttribute];
            }
        }

        $hash = $this->getHashFromAliases($request);
        if ($hash !== '') {
            return [$hash, true];
        }

        return ['', false];
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
        return (bool) preg_match(sprintf('{^[%s]+$}', preg_quote($this->alphabet, '{')), $hash);
    }

    /**
     * @param array<int, ?int> $hashids
     */
    private function hasHashidDecoded(array $hashids): bool
    {
        return is_int(reset($hashids));
    }
}
