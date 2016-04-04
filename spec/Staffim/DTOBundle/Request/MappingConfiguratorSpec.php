<?php

namespace spec\Staffim\DTOBundle\Request;

use PhpSpec\ObjectBehavior;
use Staffim\DTOBundle\MappingStorage\StaticMappingStorage;
use Staffim\DTOBundle\Model\ModelInterface;

class MappingConfiguratorSpec extends ObjectBehavior
{
    function let(StaticMappingStorage $storage)
    {
        $this->beConstructedWith($storage);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Staffim\DTOBundle\Request\MappingConfigurator');
    }

    function it_should_always_allow_map_id($storage, ModelInterface $model)
    {
        $storage->getFieldsToShow($model)->willReturn(['some field']);
        $storage->getFieldsToHide($model)->willReturn([]);

        $this->isPropertyVisible($model, 'id')->shouldReturn(true);
    }

    function it_should_detect_relations_inheritance($storage, ModelInterface $model)
    {
        $storage->getRelations($model)->willReturn(['parent.name', 'address']);

        $this->hasRelation($model, 'parent')->shouldReturn(true);
        $this->hasRelation($model, 'parent.name')->shouldReturn(true);
        $this->hasRelation($model, 'address')->shouldReturn(true);
        $this->hasRelation($model, 'someRelation')->shouldReturn(false);
    }
}
