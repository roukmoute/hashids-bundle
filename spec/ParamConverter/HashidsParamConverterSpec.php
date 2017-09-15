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
    public function it_is_initializable(Hashids $hashids, ManagerRegistry $registry)
    {
        $this->beConstructedWith($hashids, $registry, false);

        $this->shouldHaveType(HashidsParamConverter::class);
    }

    public function it_returns_false_when_id_cannot_be_founded(
        Hashids $hashids,
        ManagerRegistry $registry,
        Request $request,
        ParamConverter $configuration
    ) {
        $this->beConstructedWith($hashids, $registry, false);

        $configuration->getOptions()->willReturn([]);

        $this->apply($request, $configuration)->shouldReturn(false);
    }

    public function it_throws_a_LogicException_when_hashid_cannot_be_guessed(
        Hashids $hashids,
        ManagerRegistry $registry
    ) {
        $this->beConstructedWith($hashids, $registry, false);

        $request = new Request();
        $request->attributes->set('hashid', 'h45h1d5');

        $configuration = new ParamConverter(
            [
                'name' => 'user',
                'class' => 'Roukmoute\User',
                'options' => [
                    'id' => 'hashid',
                ],
            ]
        );

        $this->shouldThrow(new \LogicException('Unable to guess hashid from the request information.'))
            ->during(
                'apply',
                [$request, $configuration]
            )
        ;
    }

    public function it_throws_a_NotFoundHttpException_when_object_cannot_be_resolved(
        Hashids $hashids,
        ManagerRegistry $registry,
        ObjectManager $manager,
        ObjectRepository $repository
    ) {
        $this->beConstructedWith($hashids, $registry, false);

        $class = 'Roukmoute\User';
        $repository->find(1)->willReturn();
        $manager->getRepository($class)->willReturn($repository);
        $registry->getManagerForClass($class)->willReturn($manager);
        $hash = 'h45h1d5';
        $hashids->decode($hash)->willReturn([1]);

        $request = new Request();
        $request->attributes->set('hashid', $hash);

        $configuration = new ParamConverter(
            [
                'name' => 'user',
                'class' => $class,
                'options' => [
                    'id' => 'hashid',
                ],
            ]
        );

        $this->shouldThrow(new NotFoundHttpException('User "' . $hash . '" not found.'))
            ->during(
                'apply',
                [$request, $configuration]
            )
        ;
    }

    public function it_applies_an_id_without_autowire(
        Hashids $hashids,
        ManagerRegistry $registry,
        ObjectManager $manager,
        ObjectRepository $repository
    ) {
        $this->beConstructedWith($hashids, $registry, false);

        $class = 'Roukmoute\User';
        $repository->find(1)->willReturn(new \stdClass());
        $manager->getRepository($class)->willReturn($repository);
        $registry->getManagerForClass($class)->willReturn($manager);
        $hashids->decode('h45h1d5')->willReturn([1]);

        $request = new Request();
        $request->attributes->set('hashid', 'h45h1d5');

        $configuration = new ParamConverter(
            [
                'name' => 'user',
                'class' => $class,
                'options' => [
                    'id' => 'hashid',
                ],
            ]
        );

        $this->apply($request, $configuration)->shouldReturn(true);
    }

    public function it_applies_an_id_with_autowire(
        Hashids $hashids,
        ManagerRegistry $registry,
        ObjectManager $manager,
        ObjectRepository $repository
    ) {
        $this->beConstructedWith($hashids, $registry, true);

        $class = 'Roukmoute\User';
        $repository->find(1)->willReturn(new \stdClass());
        $manager->getRepository($class)->willReturn($repository);
        $registry->getManagerForClass($class)->willReturn($manager);
        $hashids->decode('h45h1d5')->willReturn([1]);

        $request = new Request();
        $request->attributes->set('hashid', 'h45h1d5');

        $configuration = new ParamConverter(
            [
                'name' => 'user',
                'class' => $class,
            ]
        );

        $this->apply($request, $configuration)->shouldReturn(true);
    }

    public function it_applies_an_hashid_with_autowire(
        Hashids $hashids,
        ManagerRegistry $registry,
        ObjectManager $manager,
        ObjectRepository $repository
    ) {
        $this->beConstructedWith($hashids, $registry, true);

        $class = 'Roukmoute\User';
        $repository->find('h45h1d5')->willReturn();
        $repository->find(1)->willReturn(new \stdClass());
        $manager->getRepository($class)->willReturn($repository);
        $registry->getManagerForClass($class)->willReturn($manager);
        $hashids->decode('h45h1d5')->willReturn([1]);

        $request = new Request();
        $request->attributes->set('id', 'h45h1d5');

        $configuration = new ParamConverter(
            [
                'name' => 'user',
                'class' => $class,
            ]
        );

        $this->apply($request, $configuration)->shouldReturn(true);
    }

    public function it_throws_a_LogicException_when_object_cannot_be_guessed_with_autowire(
        Hashids $hashids,
        ManagerRegistry $registry,
        ObjectManager $manager,
        ObjectRepository $repository,
        ClassMetadata $classMetadata
    ) {
        $this->beConstructedWith($hashids, $registry, true);

        $class = 'Roukmoute\User';
        $manager->getRepository($class)->willReturn($repository);
        $manager->getClassMetadata($class)->willReturn($classMetadata);
        $registry->getManagerForClass($class)->willReturn($manager);

        $request = new Request();
        $request->attributes->set('hashid', 'h45h1d5');

        $configuration = new ParamConverter(
            [
                'name' => 'user',
                'class' => $class,
            ]
        );

        $this->shouldThrow(\LogicException::class)->during('apply', [$request, $configuration]);
    }
}
