<?php

declare(strict_types=1);

namespace spec\Roukmoute\HashidsBundle\ValueResolver;

use Hashids\HashidsInterface;
use PhpSpec\ObjectBehavior;
use Roukmoute\HashidsBundle\Attribute\Hashid;
use Roukmoute\HashidsBundle\ValueResolver\HashidsValueResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class HashidsValueResolverSpec extends ObjectBehavior
{
    private const ALPHABET = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';

    public function let(HashidsInterface $hashids): void
    {
        $hashids->decode('9x')->willReturn([42]);
        $hashids->decode('FooBar')->willReturn([]);
        $hashids->decode('invalid')->willReturn([]);

        $this->beConstructedWith(
            $hashids,
            false,
            false,
            self::ALPHABET
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(HashidsValueResolver::class);
    }

    public function it_hashes_when_hashid_is_in_request(): void
    {
        $request = new Request([], [], ['hashid' => '9x']);
        $argument = new ArgumentMetadata('parameterId', 'int', false, false, null);

        $result = $this->resolve($request, $argument);
        expect([...$result->getWrappedObject()])->toBe([42]);
    }

    public function it_hashes_when_id_is_in_request(): void
    {
        $request = new Request([], [], ['id' => '9x']);
        $argument = new ArgumentMetadata('id', 'int', false, false, null);

        $result = $this->resolve($request, $argument);
        expect([...$result->getWrappedObject()])->toBe([42]);
    }

    public function it_prevents_double_decode_for_aliases(HashidsInterface $hashids): void
    {
        $hashids->decode('9x')->shouldBeCalledOnce()->willReturn([42]);
        $this->beConstructedWith($hashids, false, false, self::ALPHABET);
        $request = new Request([], [], ['id' => '9x', 'hashid' => '9x']);

        $argumentId = new ArgumentMetadata('id', 'int', false, false, null);
        $result = $this->resolve($request, $argumentId);
        expect([...$result->getWrappedObject()])->toBe([42]);

        $argumentHashid = new ArgumentMetadata('hashid', 'int', false, false, null);
        $result = $this->resolve($request, $argumentHashid);
        expect([...$result->getWrappedObject()])->toBe([]);
    }

    public function it_hashes_when_variable_in_request_is_prefixed(): void
    {
        $request = new Request([], [], ['_hash_post' => '9x']);
        $argument = new ArgumentMetadata('post', 'int', false, false, null);

        $result = $this->resolve($request, $argument);
        expect([...$result->getWrappedObject()])->toBe([42]);
    }

    public function it_hashes_variable_in_request_when_auto_convert_is_activated(HashidsInterface $hashids): void
    {
        $hashids->decode('9x')->willReturn([42]);
        $this->beConstructedWith($hashids, false, true, self::ALPHABET);

        $request = new Request([], [], ['post' => '9x']);
        $argument = new ArgumentMetadata('post', 'int', false, false, null);

        $result = $this->resolve($request, $argument);
        expect([...$result->getWrappedObject()])->toBe([42]);
    }

    public function it_does_not_hash_variable_in_request_when_auto_convert_is_deactivated(): void
    {
        $request = new Request([], [], ['post' => '9x']);
        $argument = new ArgumentMetadata('post', 'int', false, false, null);

        $result = $this->resolve($request, $argument);
        expect([...$result->getWrappedObject()])->toBe([]);
    }

    public function it_throws_when_force_hash_variable_in_request_and_auto_convert_is_deactivated(HashidsInterface $hashids): void
    {
        $hashids->decode('FooBar')->willReturn([]);
        $this->beConstructedWith($hashids, false, false, self::ALPHABET);

        $request = new Request([], [], ['_hash_post' => 'FooBar']);
        $argument = new ArgumentMetadata('post', 'int', false, false, null);

        $this->shouldThrow(new \LogicException('Unable to decode parameter "post".'))->during('resolve', [$request, $argument]);
    }

    public function it_does_not_hash_when_there_is_no_hashid(): void
    {
        $request = new Request([], [], ['hashid' => 'not_an_hashid']);
        $argument = new ArgumentMetadata('', 'int', false, false, null);

        $result = $this->resolve($request, $argument);
        expect([...$result->getWrappedObject()])->toBe([]);
    }

    public function it_returns_value_when_passthrough_is_false(): void
    {
        $request = new Request([], [], ['hashid' => '9x']);
        $argument = new ArgumentMetadata('parameterId', 'int', false, false, null);

        $result = $this->resolve($request, $argument);
        expect([...$result->getWrappedObject()])->toBe([42]);
    }

    public function it_sets_attribute_and_returns_empty_when_passthrough_is_true(HashidsInterface $hashids): void
    {
        $hashids->decode('9x')->willReturn([42]);
        $this->beConstructedWith($hashids, true, false, self::ALPHABET);

        $request = new Request([], [], ['hashid' => '9x']);
        $argument = new ArgumentMetadata('parameterId', 'int', false, false, null);

        $result = $this->resolve($request, $argument);
        expect([...$result->getWrappedObject()])->toBe([]);
        expect($request->attributes->get('parameterId'))->toBe(42);
    }

    public function it_hashes_variable_in_request_when_hashid_attribute_is_present(HashidsInterface $hashids): void
    {
        $hashids->decode('9x')->willReturn([42]);
        $this->beConstructedWith($hashids, false, false, self::ALPHABET);

        $request = new Request([], [], ['post' => '9x']);
        $argument = new ArgumentMetadata('post', 'int', false, false, null, false, [new Hashid()]);

        $result = $this->resolve($request, $argument);
        expect([...$result->getWrappedObject()])->toBe([42]);
    }

    public function it_throws_when_hashid_attribute_present_and_decode_fails(HashidsInterface $hashids): void
    {
        $hashids->decode('invalid')->willReturn([]);
        $this->beConstructedWith($hashids, false, false, self::ALPHABET);

        $request = new Request([], [], ['post' => 'invalid']);
        $argument = new ArgumentMetadata('post', 'int', false, false, null, false, [new Hashid()]);

        $this->shouldThrow(new \LogicException('Unable to decode parameter "post".'))->during('resolve', [$request, $argument]);
    }

    public function it_uses_custom_parameter_name_from_hashid_attribute(HashidsInterface $hashids): void
    {
        $hashids->decode('9x')->willReturn([42]);
        $this->beConstructedWith($hashids, false, false, self::ALPHABET);

        $request = new Request([], [], ['hash' => '9x']);
        $argument = new ArgumentMetadata('id', 'int', false, false, null, false, [new Hashid(parameter: 'hash')]);

        $result = $this->resolve($request, $argument);
        expect([...$result->getWrappedObject()])->toBe([42]);
    }

    public function it_uses_custom_parameter_name_with_passthrough(HashidsInterface $hashids): void
    {
        $hashids->decode('9x')->willReturn([42]);
        $this->beConstructedWith($hashids, true, false, self::ALPHABET);

        $request = new Request([], [], ['hash' => '9x']);
        $argument = new ArgumentMetadata('id', 'int', false, false, null, false, [new Hashid(parameter: 'hash')]);

        $result = $this->resolve($request, $argument);
        expect([...$result->getWrappedObject()])->toBe([]);
        expect($request->attributes->get('id'))->toBe(42);
    }
}
