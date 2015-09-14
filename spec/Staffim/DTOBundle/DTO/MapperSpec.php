<?php

namespace spec\Staffim\DTOBundle\DTO;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Staffim\DTOBundle\Request\RelationManager;
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
        RelationManager $relationManager,
        Factory $factory,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->beConstructedWith($propertyAccessor, $relationManager, $factory, $eventDispatcher);
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

    function it_should_convert_relation_like_object($propertyAccessor, $relationManager, $factory, ModelInterface $model1, ModelDTO $dto1, ModelInterface $model2, ModelDTO $dto2)
    {
        $relationManager->hasRelation('a')->willReturn(true);
        $factory->create($model1)->willReturn($dto1);
        $factory->create($model2)->willReturn($dto2);
        $propertyAccessor->getValue($model1, 'a')->willReturn($model2);
        $propertyAccessor->getValue($model2, 'a')->willReturn('value');
        $propertyAccessor->setValue($dto1, 'a', $dto2)->shouldBeCalled();
        $propertyAccessor->setValue($dto2, 'a', 'value')->shouldBeCalled();

        $this->map($model1)->shouldReturn($dto1);
    }

    function it_should_convert_relation_like_id($propertyAccessor, $relationManager, $factory, ModelInterface $model1, ModelDTO $dto1, ModelInterface $model2)
    {
        $relationManager->hasRelation('a')->willReturn(false);
        $factory->create($model1)->willReturn($dto1);
        $model2->getId()->willReturn('id');
        $propertyAccessor->getValue($model1, 'a')->willReturn($model2);
        $propertyAccessor->setValue($dto1, 'a', 'id')->shouldBeCalled();

        $this->map($model1)->shouldReturn($dto1);
    }

    function it_should_convert_collection_relation_like_ids($propertyAccessor, $relationManager, $factory, ModelInterface $model1, ModelDTO $dto1, ModelInterface $model2, ModelCollection $collection)
    {
        $relationManager->hasRelation('a')->willReturn(false);
        $factory->create($model1)->willReturn($dto1);
        $model2->getId()->willReturn('id');
        $this->stubIterator($collection, new \ArrayIterator([$model2->getWrappedObject()]));
        $propertyAccessor->getValue($model1, 'a')->willReturn($collection);
        $propertyAccessor->setValue($dto1, 'a', ['id'])->shouldBeCalled();

        $this->map($model1)->shouldReturn($dto1);
    }

    function it_should_convert_collection_relation_like_objects($propertyAccessor, $relationManager, $factory, ModelInterface $model1, ModelDTO $dto1, ModelInterface $model2, ModelDTO $dto2, ModelCollection $collection)
    {
        $relationManager->hasRelation('a')->willReturn(true);
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

    function it_should_convert_collection_embedded_relation_like_objects($propertyAccessor, $relationManager, $factory, ModelInterface $model1, ModelDTO $dto1, EmbeddedModelInterface $model2, ModelDTO $dto2, ModelCollection $collection)
    {
        $relationManager->hasRelation('a')->willReturn(false);
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

    function it_should_convert_embedded_collection_like_objects($propertyAccessor, $relationManager, $factory, ModelInterface $model1, ModelDTO $dto1, ModelInterface $model2, ModelDTO $dto2, EmbeddedModelCollection $collection)
    {
        $relationManager->hasRelation('a')->willReturn(false);
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
