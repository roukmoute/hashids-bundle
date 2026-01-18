<?php

declare(strict_types=1);

namespace spec\Roukmoute\HashidsBundle\Twig;

use Hashids\HashidsInterface;
use PhpSpec\ObjectBehavior;
use Roukmoute\HashidsBundle\Twig\HashidsExtension;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class HashidsExtensionSpec extends ObjectBehavior
{
    public function it_is_initializable(HashidsInterface $hashids)
    {
        $this->beConstructedWith($hashids);

        $this->shouldHaveType(HashidsExtension::class);
    }

    public function it_encodes_in_twig_file(HashidsInterface $hashids)
    {
        $hashids->encode(1)->willReturn('jR');
        $extension = new HashidsExtension($hashids->getWrappedObject());
        $twig = new Environment(
            new ArrayLoader(['template' => '{{ 1|hashids_encode }}']),
            ['cache' => false, 'optimizations' => 0]
        );
        $twig->addExtension($extension);

        expect($twig->render('template'))->toBe('jR');
    }

    public function it_decodes_in_twig_file(HashidsInterface $hashids)
    {
        $hashids->decode('jR')->willReturn([1]);
        $extension = new HashidsExtension($hashids->getWrappedObject());
        $twig = new Environment(
            new ArrayLoader(['template' => '{{ \'jR\'|hashids_decode|first }}']),
            ['cache' => false, 'optimizations' => 0]
        );
        $twig->addExtension($extension);

        expect($twig->render('template'))->toBe('1');
    }
}
