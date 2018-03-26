<?php

namespace spec\Roukmoute\HashidsBundle\ParamConverter;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Roukmoute\HashidsBundle\Hashids;
use Roukmoute\HashidsBundle\ParamConverter\HashidsParamConverter;
use PhpSpec\ObjectBehavior;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HashidsParamConverterSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith(new Hashids(), false);
    }

    public function it_is_initializable(Hashids $hashids)
    {
        $this->beConstructedWith($hashids, false);

        $this->shouldHaveType(HashidsParamConverter::class);
    }

    public function it_hashes_when_hashid_is_in_request()
    {
        $request = new Request([], [], ['hashid' => '9x']);
        $configuration = new ParamConverter([]);
        $configuration->setName('controllerArgument');

        $this->apply($request, $configuration)->shouldReturn(true);
        expect($request->attributes->get('controllerArgument'))->toBe(42);
    }

    public function it_hashes_when_id_is_in_request()
    {
        $request = new Request([], [], ['id' => '9x']);
        $configuration = new ParamConverter([]);
        $configuration->setName('controllerArgument');

        $this->apply($request, $configuration)->shouldReturn(true);
        expect($request->attributes->get('controllerArgument'))->toBe(42);
    }

    public function it_hashes_when_hashid_is_in_ParamConverter_options()
    {
        $request = new Request([], [], ['slug' => '9x']);
        $configuration = new ParamConverter(['options' => ['hashid' => 'slug']]);
        $configuration->setName('controllerArgument');

        $this->apply($request, $configuration)->shouldReturn(true);
        expect($request->attributes->get('controllerArgument'))->toBe(42);
    }

    public function it_does_not_hash_when_there_is_no_hashid()
    {
        $request = new Request([], [], ['hashid' => 'not_an_hashid']);
        $configuration = new ParamConverter([]);
        $configuration->setName('controllerArgument');

        $this->apply($request, $configuration)->shouldReturn(true);
        expect($request->attributes->get('controllerArgument'))->toBe(null);
    }

    public function it_passthrough_when_argument_is_true(Hashids $hashids)
    {
        $this->beConstructedWith($hashids, true);

        $request = new Request();
        $configuration = new ParamConverter([]);
        $configuration->setName('controllerArgument');

        $this->apply($request, $configuration)->shouldReturn(false);
    }

    public function it_does_not_passthrough_when_argument_is_false(Hashids $hashids)
    {
        $this->beConstructedWith($hashids, false);

        $request = new Request();
        $configuration = new ParamConverter([]);
        $configuration->setName('controllerArgument');

        $this->apply($request, $configuration)->shouldReturn(true);
    }
}
