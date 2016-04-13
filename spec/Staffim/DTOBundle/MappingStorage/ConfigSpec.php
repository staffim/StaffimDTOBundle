<?php

namespace spec\Staffim\DTOBundle\MappingStorage;

use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Staffim\DTOBundle\Model\ModelInterface;

class ConfigSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Staffim\DTOBundle\MappingStorage\Config');
    }

    function it_should_store_root_items()
    {
        $this->add(['a']);
        $this->hasPath([])->shouldReturn(true);
        $this->getFields([])->shouldReturn(['a']);
        $this->hasField([], 'a')->shouldReturn(true);
    }

    function it_should_store_hierarchical_items()
    {
        $this->add(['a', 'b']);
        $this->hasPath(['a'])->shouldReturn(true);
        $this->getFields(['a'])->shouldReturn(['b']);
    }

    function it_should_have_only_end_path_as_field()
    {
        $this->add(['a', 'b', 'c']);
        $this->hasPath(['a'])->shouldReturn(false);
        $this->hasPath(['a', 'b'])->shouldReturn(true);
    }

    function it_should_store_item_as_path()
    {
        $this->add(['a', 'b', 'c'], false);
        $this->hasPath(['a'], false)->shouldReturn(true);
        $this->hasPath(['a', 'b'], false)->shouldReturn(true);
        $this->hasPath(['a', 'b', 'c'], false)->shouldReturn(true);
    }
}
