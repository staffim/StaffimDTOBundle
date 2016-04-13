<?php

namespace spec\Staffim\DTOBundle\Request;

use PhpSpec\ObjectBehavior;
use Staffim\DTOBundle\MappingStorage\RequestMappingStorage;
use Staffim\DTOBundle\MappingStorage\Config;
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
        $showConfig = new Config;
        $showConfig->add(['some', 'field']);
        $hideConfig = new Config;
        $storage->getFieldsToShow()->willReturn($showConfig);
        $storage->getFieldsToHide()->willReturn($hideConfig);

        $this->isPropertyVisible(['id'])->shouldReturn(true);
        $this->isPropertyVisible(['some', 'field'])->shouldReturn(true);
    }

    function it_should_detect_relations_inheritance($storage)
    {
        $config = new Config;
        $config->add(['parent', 'name'], false);
        $config->add(['address'], false);
        $storage->getRelations()->willReturn($config);

        $this->hasRelation(['parent'])->shouldReturn(true);
        $this->hasRelation(['parent', 'name'])->shouldReturn(true);
        $this->hasRelation(['address'])->shouldReturn(true);
        $this->hasRelation(['someRelation'])->shouldReturn(false);
    }

    function it_should_use_separate_config_embedded_objects($storage)
    {
        $showConfig = new Config;
        $showConfig->add(['name']);
        $showConfig->add(['status']);
        $showConfig->add(['shop']);
        $hideConfig = new Config;
        $hideConfig->add(['shop', 'products']);
        $storage->getFieldsToShow()->willReturn($showConfig);
        $storage->getFieldsToHide()->willReturn($hideConfig);

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
        $showConfig = new Config;
        $hideConfig = new Config;
        $hideConfig->add(['in', 'items']);
        $storage->getFieldsToShow()->willReturn($showConfig);
        $storage->getFieldsToHide()->willReturn($hideConfig);

        $this->isPropertyVisible(['id'])->shouldReturn(true);
        $this->isPropertyVisible(['in'])->shouldReturn(true);
        $this->isPropertyVisible(['in', 'items'])->shouldReturn(false);
        $this->isPropertyVisible(['in', 'some'])->shouldReturn(true);
    }
}
