<?php

namespace spec\Staffim\DTOBundle\DTO;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Staffim\DTOBundle\DTO\UnknownValue;
use Staffim\DTOBundle\Request\MappingConfigurator;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Staffim\DTOBundle\Request\RequestMappingStorage;
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
        MappingConfigurator $mappingConfigurator,
        Factory $factory,
        EventDispatcherInterface $eventDispatcher
    ) {

        $mappingConfigurator->isPropertyVisible(['id'])->willReturn(true);
        $mappingConfigurator->isPropertyVisible(['a'])->willReturn(true);
        $mappingConfigurator->hasRelation(['a'])->willReturn(false);

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
        $mappingConfigurator->hasRelation(['a'])->willReturn(true);
        $mappingConfigurator->isPropertyVisible(['a'])->willReturn(true);
        $mappingConfigurator->isPropertyVisible(['a', 'a'])->willReturn(true);

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
        $mappingConfigurator->isPropertyVisible(['a'])->willReturn(true);
        $mappingConfigurator->hasRelation(['a'])->willReturn(false);

        $factory->create($model1)->willReturn($dto1);
        $model2->getId()->willReturn('id');
        $propertyAccessor->getValue($model1, 'a')->willReturn($model2);
        $propertyAccessor->setValue($dto1, 'a', 'id')->shouldBeCalled();

        $this->map($model1)->shouldReturn($dto1);
    }

    function it_should_convert_collection_relation_like_ids($propertyAccessor, $mappingConfigurator, $factory, ModelInterface $model1, ModelDTO $dto1, ModelInterface $model2, ModelCollection $collection)
    {
        $mappingConfigurator->hasRelation(['a'])->willReturn(false);
        $mappingConfigurator->isPropertyVisible(['a'])->willReturn(true);

        $factory->create($model1)->willReturn($dto1);

        $model2->getId()->willReturn('id');
        $this->stubIterator($collection, new \ArrayIterator([$model2->getWrappedObject()]));
        $propertyAccessor->getValue($model1, 'a')->willReturn($collection);
        $propertyAccessor->setValue($dto1, 'a', ['id'])->shouldBeCalled();

        $this->map($model1)->shouldReturn($dto1);
    }

    function it_should_convert_collection_relation_like_objects($propertyAccessor, $mappingConfigurator, $factory, ModelInterface $model1, ModelDTO $dto1, ModelInterface $model2, ModelDTO $dto2, ModelCollection $collection)
    {
        $mappingConfigurator->hasRelation(['a'])->willReturn(true);

        $mappingConfigurator->isPropertyVisible(['a'])->willReturn(true);
        $mappingConfigurator->isPropertyVisible(['a', 'a'])->willReturn(true);

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
        $mappingConfigurator->hasRelation(['a'])->willReturn(true);
        $mappingConfigurator->isPropertyVisible(['a'])->willReturn(true);
        $mappingConfigurator->isPropertyVisible(['a', 'a'])->willReturn(true);

        $factory->create($model1)->willReturn($dto1);
        $factory->create($model2)->willReturn($dto2);

        $this->stubIterator($collection, new \ArrayIterator([$model2->getWrappedObject()]));

        $propertyAccessor->getValue($model1, 'a')->willReturn($collection);
        $propertyAccessor->getValue($model2, 'a')->willReturn('value');
        $propertyAccessor->setValue($dto1, 'a', [$dto2])->shouldBeCalled();
        $propertyAccessor->setValue($dto2, 'a', 'value')->shouldBeCalled();

        $this->map($model1)->shouldReturn($dto1);
    }

    function it_should_convert_embedded_collection_like_objects($propertyAccessor, $mappingConfigurator, $factory, ModelInterface $model1, ModelDTO $dto1, ModelInterface $model2, ModelDTO $dto2, EmbeddedModelCollection $collection)
    {
        $mappingConfigurator->isPropertyVisible(['a'])->willReturn(true);
        $mappingConfigurator->isPropertyVisible(['a', 'a'])->willReturn(true);

        $factory->create($model1)->willReturn($dto1);
        $factory->create($model2)->willReturn($dto2);

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
        $mappingConfigurator->isPropertyVisible(['name'])->willReturn(false);
        $mappingConfigurator->isPropertyVisible(['parent'])->willReturn(true);

        $factory->create($model)->willReturn($userDTO);

        $propertyAccessor->getValue($model, 'id')->willReturn('Some user id');
        $propertyAccessor->getValue($model, 'name')->willReturn('Some name');
        $propertyAccessor->getValue($model, 'parent')->willReturn('John');

        $propertyAccessor->setValue($userDTO, 'id', 'Some user id')->shouldBeCalled();
        $propertyAccessor->setValue($userDTO, 'name', UnknownValue::create())->shouldBeCalled();
        $propertyAccessor->setValue($userDTO, 'parent', 'John')->shouldBeCalled();

        $this->map($model)->shouldReturn($userDTO);
    }

    function it_should_hide_fields_in_embedded_documents($propertyAccessor, $mappingConfigurator, $factory, ModelInterface $user, UserDTO $userDTO, ModelInterface $parent, ParentDTO $parentDTO)
    {
        $mappingConfigurator->isPropertyVisible(['id'])->willReturn(true);
        $mappingConfigurator->isPropertyVisible(['name'])->willReturn(false);
        $mappingConfigurator->isPropertyVisible(['parent'])->willReturn(true);
        $mappingConfigurator->isPropertyVisible(['parent', 'name'])->willReturn(true);
        $mappingConfigurator->isPropertyVisible(['parent', 'id'])->willReturn(true);

        $mappingConfigurator->hasRelation(['parent'])->willReturn(true);

        $factory->create($user)->willReturn($userDTO);
        $factory->create($parent)->willReturn($parentDTO);

        $user->getId()->willReturn('some_id');
        $propertyAccessor->getValue($user, 'id')->willReturn('Some user id');
        $propertyAccessor->getValue($user, 'name')->willReturn('Some name');
        $propertyAccessor->getValue($user, 'parent')->willReturn($parent);

        $propertyAccessor->getValue($parent, 'id')->willReturn('Some parent id');
        $propertyAccessor->getValue($parent, 'name')->willReturn('Adam');

        $propertyAccessor->setValue($userDTO, 'id', 'Some user id')->shouldBeCalled();
        $propertyAccessor->setValue($userDTO, 'name', UnknownValue::create())->shouldBeCalled();
        $propertyAccessor->setValue($userDTO, 'parent', $parentDTO)->shouldBeCalled();

        $propertyAccessor->setValue($parentDTO, 'id', 'Some parent id')->shouldBeCalled();
        $propertyAccessor->setValue($parentDTO, 'name', 'Adam')->shouldBeCalled();

        $this->map($user)->shouldReturn($userDTO);
    }

    function it_should_hide_fields_in_embedded_collections(
        $propertyAccessor,
        $mappingConfigurator,
        $factory,
        ModelInterface $model,
        ModelDTO $modelDto,
        ModelInterface $user,
        UserDTO $userDTO,
        ModelInterface $parent,
        ParentDTO $parentDTO,
        ModelCollection $collection
    ) {
        $mappingConfigurator->hasRelation(['a'])->willReturn(true);
        $mappingConfigurator->hasRelation(['a', 'parent'])->willReturn(true);

        $mappingConfigurator->isPropertyVisible(['name'])->willReturn(false);
        $mappingConfigurator->isPropertyVisible(['a', 'id'])->willReturn(true);
        $mappingConfigurator->isPropertyVisible(['a', 'name'])->willReturn(false);
        $mappingConfigurator->isPropertyVisible(['a','parent'])->willReturn(true);
        $mappingConfigurator->isPropertyVisible(['a', 'parent', 'id'])->willReturn(true);
        $mappingConfigurator->isPropertyVisible(['a', 'parent', 'name'])->willReturn(true);

        $factory->create($model)->willReturn($modelDto);
        $factory->create($user)->willReturn($userDTO);
        $factory->create($parent)->willReturn($parentDTO);

        //get
        $propertyAccessor->getValue($model, 'a')->willReturn($collection);
        $this->stubIterator($collection, new \ArrayIterator([$user->getWrappedObject()]));

        $propertyAccessor->getValue($user, 'id')->willReturn('abel_id');
        $propertyAccessor->getValue($user, 'name')->willReturn('Abel');
        $propertyAccessor->getValue($user, 'parent')->willReturn($parent);

        $propertyAccessor->getValue($parent, 'id')->willReturn('adam_id');
        $propertyAccessor->getValue($parent, 'name')->willReturn('Adam');
        //set
        $propertyAccessor->setValue($modelDto, 'a', [$userDTO])->shouldBeCalled();

        $propertyAccessor->setValue($userDTO, 'id', 'abel_id')->shouldBeCalled();
        $propertyAccessor->setValue($userDTO, 'name', UnknownValue::create())->shouldBeCalled();
        $propertyAccessor->setValue($userDTO, 'parent', $parentDTO)->shouldBeCalled();

        $propertyAccessor->setValue($parentDTO, 'id', 'adam_id')->shouldBeCalled();
        $propertyAccessor->setValue($parentDTO, 'name', 'Adam')->shouldBeCalled();

        //configurator calls
        $mappingConfigurator->isPropertyVisible(['a'])->shouldBeCalled();

        $this->map($model)->shouldReturn($modelDto);
    }

    function it_should_return_default_value_when_exception_was_thrown($propertyAccessor, $mappingConfigurator, $factory, ModelInterface $user, GroupDTO $groupDTO)
    {
        $mappingConfigurator->isPropertyVisible(['access'])->willReturn(true);

        $factory->create($user)->willReturn($groupDTO);

        $propertyAccessor->getValue($user, 'access')
            ->shouldBeCalled()
            ->willThrow(new \Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException());

        $propertyAccessor->setValue($groupDTO, 'access', null)->shouldNotBeCalled();

        $this->map($user)->shouldReturn($groupDTO);
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

class GroupDTO implements \Staffim\DTOBundle\DTO\Model\DTOInterface
{
    public $access = 'default';
}
