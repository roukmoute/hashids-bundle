<?php

declare(strict_types=1);

namespace spec\Roukmoute\HashidsBundle\ParamConverter;

use Hashids\HashidsInterface;
use PhpSpec\ObjectBehavior;
use Roukmoute\HashidsBundle\ParamConverter\HashidsParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class HashidsParamConverterSpec extends ObjectBehavior
{
    private const ALPHABET = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';

    public function let()
    {
        $this->beConstructedWith(
            new \Hashids\Hashids(),
            false,
            false,
            self::ALPHABET
        );
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(HashidsParamConverter::class);
    }

    public function it_hashes_when_hashid_is_in_request()
    {
        $request = new Request([], [], ['hashid' => '9x']);
        $configuration = new ParamConverter([]);
        $configuration->setName('parameterId');

        $this->apply($request, $configuration);
        expect($request->attributes->get('parameterId'))->toBe(42);
    }

    public function it_hashes_when_id_is_in_request()
    {
        $request = new Request([], [], ['id' => '9x']);
        $configuration = new ParamConverter([]);
        $configuration->setName('id');

        $this->apply($request, $configuration);
        expect($request->attributes->get('id'))->toBe(42);
    }

    public function it_prevents_double_decode_for_aliases(HashidsInterface $hashids)
    {
        $hashids->decode('9x')->shouldBeCalledOnce()->willReturn([42]);
        $this->beConstructedWith($hashids, false, false, self::ALPHABET);
        $request = new Request([], [], ['id' => '9x', 'hashid' => '9x']);
        $configuration = new ParamConverter([]);

        $configuration->setName('id');
        $this->apply($request, $configuration);
        expect($request->attributes->get('id'))->toBe(42);

        $configuration->setName('hashid');
        $this->apply($request, $configuration);
        expect($request->attributes->get('hashid'))->toBe('9x');
    }

    public function it_hashes_when_variable_in_request_is_prefixed()
    {
        $request = new Request([], [], ['_hash_post' => '9x']);
        $configuration = new ParamConverter([]);
        $configuration->setName('post');

        $this->apply($request, $configuration);
        expect($request->attributes->get('post'))->toBe(42);
    }

    public function it_hashes_variable_in_request_when_auto_convert_is_activated()
    {
        $this->beConstructedWith(new \Hashids\Hashids(), false, true, self::ALPHABET);

        $request = new Request([], [], ['post' => '9x']);
        $configuration = new ParamConverter([]);
        $configuration->setName('post');

        $this->apply($request, $configuration);
        expect($request->attributes->get('post'))->toBe(42);
    }

    public function it_does_not_hash_variable_in_request_when_auto_convert_is_deactivated()
    {
        $request = new Request([], [], ['post' => '9x']);
        $configuration = new ParamConverter([]);
        $configuration->setName('post');

        $this->apply($request, $configuration);
        expect($request->attributes->get('post'))->toBe('9x');
    }

    public function it_throws_when_force_hash_variable_in_request_and_auto_convert_is_deactivated()
    {
        $request = new Request([], [], ['_hash_post' => 'FooBar']);
        $configuration = new ParamConverter([]);
        $configuration->setName('post');

        $this->shouldThrow(new \LogicException('Unable to decode parameter "post".'))->during('apply', [$request, $configuration]);
    }

    public function it_does_not_hash_when_there_is_no_hashid()
    {
        $request = new Request([], [], ['hashid' => 'not_an_hashid']);
        $configuration = new ParamConverter([]);
        $configuration->setName('');

        $this->apply($request, $configuration);
        expect($request->attributes->get('controllerArgument'))->toBe(null);
    }

    public function it_does_not_passthrough_when_argument_is_false()
    {
        $request = new Request();
        $configuration = new ParamConverter([]);
        $configuration->setName('');

        $this->apply($request, $configuration)->shouldReturn(true);
    }

    public function it_passthrough_when_argument_is_true(HashidsInterface $hashids)
    {
        $this->beConstructedWith($hashids, true, false, self::ALPHABET);

        $request = new Request();
        $configuration = new ParamConverter([]);
        $configuration->setName('');

        $this->apply($request, $configuration)->shouldReturn(false);
    }

    public function it_always_supports_method(ParamConverter $paramConverter)
    {
        $this->supports($paramConverter)->shouldReturn(true);
    }
}
