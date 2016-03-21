<?php

namespace spec\Staffim\DTOBundle\Request;

use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestMappingConfiguratorSpec extends ObjectBehavior
{
    function let(RequestStack $requestStack, Request $request)
    {
        $requestStack->getCurrentRequest()->willReturn($request);
        $this->beConstructedWith($requestStack);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Staffim\DTOBundle\Request\RequestMappingConfigurator');
    }

    function it_should_always_allow_map_id($request)
    {
        $request->get('fields', [])->willReturn(['some field']);
        $request->get('hideFields', [])->willReturn([]);

        $this->isPropertyVisible('id')->shouldReturn(true);
    }

    function it_should_parse_fields_from_comma_separated_string($request)
    {
        $request->get('fields', [])->willReturn('name, age,sex');
        $request->get('hideFields', [])->willReturn([]);

        $this->isPropertyVisible('id')->shouldReturn(true);
        $this->isPropertyVisible('age')->shouldReturn(true);
        $this->isPropertyVisible('someField')->shouldReturn(false);
    }

    function it_should_detect_relations_inheritance($request)
    {
        $request->get('relations', [])->willReturn(['parent.name', 'address']);

        $this->hasRelation('parent')->shouldReturn(true);
        $this->hasRelation('parent.name')->shouldReturn(true);
        $this->hasRelation('address')->shouldReturn(true);
        $this->hasRelation('someRelation')->shouldReturn(false);
    }
}
