<?php

namespace spec\Staffim\DTOBundle\DTO;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Staffim\DTOBundle\Request\RequestMappingConfigurator;
use Staffim\DTOBundle\DTO\Factory;
use Staffim\DTOBundle\Model\ModelInterface;
use Staffim\DTOBundle\Model\EmbeddedModelInterface;
use Staffim\DTOBundle\Collection\ModelCollection;
use Staffim\DTOBundle\Collection\EmbeddedModelCollection;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class MapperSpec extends ObjectBehavior
{
    public function let(
        PropertyAccessorInterface $propertyAccessor,
        RequestMappingConfigurator $mappingConfigurator,
        Factory $factory,
        EventDispatcherInterface $eventDispatcher
    ) {
        $mappingConfigurator->getFieldsToHide()->willReturn([]);
        $mappingConfigurator->getFieldsToShow()->willReturn([]);

        $this->beConstructedWith($propertyAccessor, $mappingConfigurator, $factory, $eventDispatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Staffim\DTOBundle\DTO\Mapper');
    }

    function it_should_convert_model($propertyAccessor, $factory, ModelInterface $model, ModelDTO $dto)
    {
        $factory->create($model)->willReturn($dto);
        $propertyAccessor->getValue($model, 'a')->willReturn('value');
        $propertyAccessor->setValue($dto, 'a', 'value')->shouldBeCalled();

        $this->map($model)->shouldReturn($dto);
    }

    function it_should_convert_empty_collection(ModelCollection $modelCollection)
    {
        $this->mapCollection($modelCollection)->shouldReturn([]);
    }

    function it_should_convert_collection($factory, ModelCollection $modelCollection, ModelInterface $model, ModelDTO $dto)
    {
        $factory->create($model)->willReturn($dto);
        $this->stubIterator($modelCollection, new \ArrayIterator([$model->getWrappedObject()]));

        $this->mapCollection($modelCollection)->shouldReturn([$dto]);
    }

    function it_should_convert_embedded_collection($factory, EmbeddedModelCollection $modelCollection, ModelInterface $model, ModelDTO $dto)
    {
        $factory->create($model)->willReturn($dto);
        $this->stubIterator($modelCollection, new \ArrayIterator([$model->getWrappedObject()]));

        $this->mapCollection($modelCollection)->shouldReturn([$dto]);
    }

    function it_should_convert_relation_like_object($propertyAccessor, $mappingConfigurator, $factory, ModelInterface $model1, ModelDTO $dto1, ModelInterface $model2, ModelDTO $dto2)
    {
        $mappingConfigurator->hasRelation('a')->willReturn(true);
        $factory->create($model1)->willReturn($dto1);
        $factory->create($model2)->willReturn($dto2);
        $propertyAccessor->getValue($model1, 'a')->willReturn($model2);
        $propertyAccessor->getValue($model2, 'a')->willReturn('value');
        $propertyAccessor->setValue($dto1, 'a', $dto2)->shouldBeCalled();
        $propertyAccessor->setValue($dto2, 'a', 'value')->shouldBeCalled();

        $this->map($model1)->shouldReturn($dto1);
    }

    function it_should_convert_relation_like_id($propertyAccessor, $mappingConfigurator, $factory, ModelInterface $model1, ModelDTO $dto1, ModelInterface $model2)
    {
        $mappingConfigurator->hasRelation('a')->willReturn(false);
        $factory->create($model1)->willReturn($dto1);
        $model2->getId()->willReturn('id');
        $propertyAccessor->getValue($model1, 'a')->willReturn($model2);
        $propertyAccessor->setValue($dto1, 'a', 'id')->shouldBeCalled();

        $this->map($model1)->shouldReturn($dto1);
    }

    function it_should_convert_collection_relation_like_ids($propertyAccessor, $mappingConfigurator, $factory, ModelInterface $model1, ModelDTO $dto1, ModelInterface $model2, ModelCollection $collection)
    {
        $mappingConfigurator->hasRelation('a')->willReturn(false);
        $factory->create($model1)->willReturn($dto1);
        $model2->getId()->willReturn('id');
        $this->stubIterator($collection, new \ArrayIterator([$model2->getWrappedObject()]));
        $propertyAccessor->getValue($model1, 'a')->willReturn($collection);
        $propertyAccessor->setValue($dto1, 'a', ['id'])->shouldBeCalled();

        $this->map($model1)->shouldReturn($dto1);
    }

    function it_should_convert_collection_relation_like_objects($propertyAccessor, $mappingConfigurator, $factory, ModelInterface $model1, ModelDTO $dto1, ModelInterface $model2, ModelDTO $dto2, ModelCollection $collection)
    {
        $mappingConfigurator->hasRelation('a')->willReturn(true);
        $factory->create($model1)->willReturn($dto1);
        $factory->create($model2)->willReturn($dto2);
        $model2->getId()->willReturn('id');
        $this->stubIterator($collection, new \ArrayIterator([$model2->getWrappedObject()]));
        $propertyAccessor->getValue($model1, 'a')->willReturn($collection);
        $propertyAccessor->getValue($model2, 'a')->willReturn('value');
        $propertyAccessor->setValue($dto1, 'a', [$dto2])->shouldBeCalled();
        $propertyAccessor->setValue($dto2, 'a', 'value')->shouldBeCalled();

        $this->map($model1)->shouldReturn($dto1);
    }

    function it_should_convert_collection_embedded_relation_like_objects($propertyAccessor, $mappingConfigurator, $factory, ModelInterface $model1, ModelDTO $dto1, EmbeddedModelInterface $model2, ModelDTO $dto2, ModelCollection $collection)
    {
        $mappingConfigurator->hasRelation('a')->willReturn(false);
        $factory->create($model1)->willReturn($dto1);
        $factory->create($model2)->willReturn($dto2);
        $model2->getId()->willReturn('id');
        $this->stubIterator($collection, new \ArrayIterator([$model2->getWrappedObject()]));
        $propertyAccessor->getValue($model1, 'a')->willReturn($collection);
        $propertyAccessor->getValue($model2, 'a')->willReturn('value');
        $propertyAccessor->setValue($dto1, 'a', [$dto2])->shouldBeCalled();
        $propertyAccessor->setValue($dto2, 'a', 'value')->shouldBeCalled();

        $this->map($model1)->shouldReturn($dto1);
    }

    function it_should_convert_embedded_collection_like_objects($propertyAccessor, $mappingConfigurator, $factory, ModelInterface $model1, ModelDTO $dto1, ModelInterface $model2, ModelDTO $dto2, EmbeddedModelCollection $collection)
    {
        $mappingConfigurator->hasRelation('a')->willReturn(false);
        $factory->create($model1)->willReturn($dto1);
        $factory->create($model2)->willReturn($dto2);
        $model2->getId()->willReturn('id');
        $this->stubIterator($collection, new \ArrayIterator([$model2->getWrappedObject()]));
        $propertyAccessor->getValue($model1, 'a')->willReturn($collection);
        $propertyAccessor->getValue($model2, 'a')->willReturn('value');
        $propertyAccessor->setValue($dto1, 'a', [$dto2])->shouldBeCalled();
        $propertyAccessor->setValue($dto2, 'a', 'value')->shouldBeCalled();

        $this->map($model1)->shouldReturn($dto1);
    }

    function it_should_trigger_events($factory, $eventDispatcher, ModelInterface $model, ModelDTO $dto)
    {
        $factory->create($model)->willReturn($dto);
        $eventDispatcher->dispatch(Argument::type('string'), Argument::type('Staffim\DTOBundle\Event\PostMapEvent'))->shouldBeCalled();
        $this->map($model);
    }

    function it_should_hide_specified_fields($propertyAccessor, $mappingConfigurator, $factory, ModelInterface $model, UserDTO $userDTO)
    {
        $mappingConfigurator->getFieldsToHide()->willReturn(['name']);
        $factory->create($model)->willReturn($userDTO);

        $propertyAccessor->getValue($model, 'id')->willReturn('Some user id');
        $propertyAccessor->getValue($model, 'name')->willReturn('Some name');
        $propertyAccessor->getValue($model, 'parent')->willReturn('John');

        $propertyAccessor->setValue($userDTO, 'id', 'Some user id')->shouldBeCalled();
        $propertyAccessor->setValue($userDTO, 'name', null)->shouldBeCalled();
        $propertyAccessor->setValue($userDTO, 'parent', 'John')->shouldBeCalled();

        $this->map($model)->shouldReturn($userDTO);

        $mappingConfigurator->getFieldsToHide()->shouldBeCalled();
    }

    function it_should_show_model_id_even_if_all_fields_hidden($propertyAccessor, $mappingConfigurator, $factory, ModelInterface $model, UserDTO $userDTO)
    {
        $mappingConfigurator->getRelations()->willReturn(['']);
        $mappingConfigurator->getFieldsToShow()->willReturn(['someNonexistentField']);
        $factory->create($model)->willReturn($userDTO);

        $propertyAccessor->getValue($model, 'id')->willReturn('Some user id');
        $propertyAccessor->getValue($model, 'name')->willReturn('Some name');
        $propertyAccessor->getValue($model, 'parent')->willReturn('John');

        $propertyAccessor->setValue($userDTO, 'id', 'Some user id')->shouldBeCalled();
        $propertyAccessor->setValue($userDTO, 'name', null)->shouldBeCalled();
        $propertyAccessor->setValue($userDTO, 'parent', null)->shouldBeCalled();

        $this->map($model)->shouldReturn($userDTO);
    }

    function it_should_hide_fields_in_embedded_documents($propertyAccessor, $mappingConfigurator, $factory, ModelInterface $user, UserDTO $userDTO, ModelInterface $parent, ParentDTO $parentDTO)
    {
        $mappingConfigurator->getFieldsToShow()->willReturn(['parent.name']);
        $mappingConfigurator->hasRelation('parent')->willReturn(true);

        $factory->create($user)->willReturn($userDTO);
        $factory->create($parent)->willReturn($parentDTO);

        $propertyAccessor->getValue($user, 'id')->willReturn('Some user id');
        $propertyAccessor->getValue($user, 'name')->willReturn('Some name');
        $propertyAccessor->getValue($user, 'parent')->willReturn($parent);

        $propertyAccessor->getValue($parent, 'id')->willReturn('Some parent id');
        $propertyAccessor->getValue($parent, 'name')->willReturn('Adam');

        $propertyAccessor->setValue($userDTO, 'id', 'Some user id')->shouldBeCalled();
        $propertyAccessor->setValue($userDTO, 'name', null)->shouldBeCalled();
        $propertyAccessor->setValue($userDTO, 'parent', $parentDTO)->shouldBeCalled();

        $propertyAccessor->setValue($parentDTO, 'id', 'Some parent id')->shouldBeCalled();
        $propertyAccessor->setValue($parentDTO, 'name', 'Adam')->shouldBeCalled();

        $this->map($user)->shouldReturn($userDTO);
    }

    private function stubIterator($modelCollection, $iterator)
    {
        $modelCollection->rewind()->will(function () use ($iterator) {
            return $iterator->rewind();
        });
        $modelCollection->valid()->will(function () use ($iterator) {
            return $iterator->valid();
        });
        $modelCollection->current()->will(function () use ($iterator) {
            return $iterator->current();
        });
        $modelCollection->key()->will(function () use ($iterator) {
            return $iterator->key();
        });
        $modelCollection->next()->will(function () use ($iterator) {
            return $iterator->next();
        });
    }
}

class ModelDTO implements \Staffim\DTOBundle\DTO\Model\DTOInterface
{
    public $a;
}

class UserDTO implements \Staffim\DTOBundle\DTO\Model\DTOInterface
{
    public $id;

    public $name;

    public $parent;
}

class ParentDTO implements \Staffim\DTOBundle\DTO\Model\DTOInterface
{
    public $id;

    public $name;
}
