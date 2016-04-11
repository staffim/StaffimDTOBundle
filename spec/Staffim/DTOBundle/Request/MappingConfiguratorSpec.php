<?php

namespace spec\Staffim\DTOBundle\Request;

use PhpSpec\ObjectBehavior;
use Staffim\DTOBundle\MappingStorage\RequestMappingStorage;
use Staffim\DTOBundle\Model\ModelInterface;

class MappingConfiguratorSpec extends ObjectBehavior
{
    function let(RequestMappingStorage $storage)
    {
        $this->beConstructedWith($storage);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Staffim\DTOBundle\Request\MappingConfigurator');
    }

    function it_should_always_allow_map_id($storage)
    {
        $storage->getFieldsToShow()->willReturn(['some' => ['field' => []]]);
        $storage->getFieldsToHide()->willReturn([]);

        $this->isPropertyVisible(['id'])->shouldReturn(true);
        $this->isPropertyVisible(['some', 'field'])->shouldReturn(true);
    }

    function it_should_detect_relations_inheritance($storage)
    {
        $storage->getRelations()->willReturn(['parent' => ['name' => []], 'address' => []]);

        $this->hasRelation(['parent'])->shouldReturn(true);
        $this->hasRelation(['parent', 'name'])->shouldReturn(true);
        $this->hasRelation(['address'])->shouldReturn(true);
        $this->hasRelation(['someRelation'])->shouldReturn(false);
    }

    function it_should_use_separate_config_embedded_objects($storage)
    {
        $storage->getFieldsToShow()->willReturn(['name' => [], 'status' => [], 'shop' => []]);
        $storage->getFieldsToHide()->willReturn(['shop' => ['products' => []]]);

        $this->isPropertyVisible(['id'])->shouldReturn(true);
        $this->isPropertyVisible(['status'])->shouldReturn(true);
        $this->isPropertyVisible(['shop'])->shouldReturn(true);
        $this->isPropertyVisible(['user'])->shouldReturn(false);
        $this->isPropertyVisible(['shop', 'products'])->shouldReturn(false);
        $this->isPropertyVisible(['shop', 'id'])->shouldReturn(true);
        $this->isPropertyVisible(['shop', 'name'])->shouldReturn(true);
    }

    function it_should_correct_hide_deep_fields($storage)
    {
        $storage->getFieldsToShow()->willReturn([]);
        $storage->getFieldsToHide()->willReturn(['in' => ['items' => []]]);

        $this->isPropertyVisible(['id'])->shouldReturn(true);
        $this->isPropertyVisible(['in'])->shouldReturn(true);
        $this->isPropertyVisible(['in', 'items'])->shouldReturn(false);
        $this->isPropertyVisible(['in', 'some'])->shouldReturn(true);
    }
}
