<?php

namespace spec\Roukmoute\HashidsBundle;

use PhpSpec\ObjectBehavior;
use Roukmoute\HashidsBundle\DependencyInjection\RoukmouteHashidsExtension;
use Roukmoute\HashidsBundle\Hashids;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class HashidsSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(Hashids::class);
    }

    public function it_sets_min_hashs_length()
    {
        expect($this->encode(1)->getWrappedObject())->toBe('jR');
        $this->setMinHashLength(4);
        expect($this->encode(1)->getWrappedObject())->toBe('ejRe');
        $this->setMinHashLength(15);
        expect($this->encode(1)->getWrappedObject())->toBe('4q2VolejRejNmGQ');
    }

    public function it_encodes_with_custom_hash_length()
    {
        expect($this->encode(1)->getWrappedObject())->toBe('jR');
        expect($this->encodeWithCustomHashLength(4, 1)->getWrappedObject())->toBe('ejRe');
        expect($this->encode(1)->getWrappedObject())->toBe('jR');
    }

    public function it_decodes_with_custom_hash_length()
    {
        expect($this->decode('jR')->getWrappedObject())->toBe([1]);
        expect($this->decodeWithCustomHashLength(4, 'ejRe')->getWrappedObject())->toBe([1]);
        expect($this->decode('jR')->getWrappedObject())->toBe([1]);
    }

    public function it_has_public_visibility_for_hashid_service()
    {
        $container = new ContainerBuilder(new ParameterBag([]));
        (new RoukmouteHashidsExtension())->load([[]], $container);

        expect($container->getDefinition('hashids')->isPublic())->toBe(true);
    }
}
