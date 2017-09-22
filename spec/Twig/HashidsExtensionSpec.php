<?php

namespace spec\Roukmoute\HashidsBundle\Twig;

use Roukmoute\HashidsBundle\Hashids;
use Roukmoute\HashidsBundle\Twig\HashidsExtension;
use PhpSpec\ObjectBehavior;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class HashidsExtensionSpec extends ObjectBehavior
{
    public function it_is_initializable(Hashids $hashids)
    {
        $this->beConstructedWith($hashids);

        $this->shouldHaveType(HashidsExtension::class);
    }

    public function it_encodes_in_twig_file()
    {
        $extension = new HashidsExtension(new Hashids());
        $twig = new Environment(
            new ArrayLoader(['template' => "{{ 1|hashids_encode }}"]),
            ['cache' => false, 'optimizations' => 0]
        );
        $twig->addExtension($extension);

        expect($twig->render('template'))->toBe('jR');
    }

    public function it_decodes_in_twig_file()
    {
        $extension = new HashidsExtension(new Hashids());
        $twig = new Environment(
            new ArrayLoader(['template' => "{{ 'jR'|hashids_decode|first }}"]),
            ['cache' => false, 'optimizations' => 0]
        );
        $twig->addExtension($extension);

        expect($twig->render('template'))->toBe('1');
    }
}
