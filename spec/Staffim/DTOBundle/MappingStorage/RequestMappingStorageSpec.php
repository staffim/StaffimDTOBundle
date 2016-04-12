<?php

namespace spec\Staffim\DTOBundle\MappingStorage;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Staffim\DTOBundle\Model\ModelInterface;

class RequestMappingStorageSpec extends ObjectBehavior
{
    function let(RequestStack $requestStack, Request $request)
    {
        $requestStack->getCurrentRequest()->willReturn($request);
        $this->beConstructedWith($requestStack);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Staffim\DTOBundle\MappingStorage\RequestMappingStorage');
    }

    function it_should_parse_fields_from_comma_separated_string($request, ModelInterface $model)
    {
        $request->get('fields', [])->willReturn('name, age,sex');
        $config = $this->getFieldsToShow($model);
        $config->shouldHaveType('Staffim\DTOBundle\MappingStorage\Config');
        $config->getFields([])->shouldReturn(['name', 'age', 'sex']);
    }

    function it_should_extract_property_path($request, ModelInterface $model)
    {
        $request->get('fields', [])->willReturn(['shop.merchandiser.name', 'shop.merchandiser.email']);
        $config = $this->getFieldsToShow($model);
        $config->getFields(['shop', 'merchandiser'])->shouldReturn(['name', 'email']);
    }
}
