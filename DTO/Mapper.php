<?php

namespace Staffim\DTOBundle\DTO;

use Staffim\DTOBundle\Collection\EmbeddedModelCollection;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;

use Staffim\DTOBundle\Request\RelationManager;
use Staffim\DTOBundle\Collection\ModelIteratorInterface;
use Staffim\DTOBundle\Model\ModelInterface;
use Staffim\DTOBundle\Model\EmbeddedModelInterface;
use Staffim\DTOBundle\Event\PostMapEvent;

class Mapper
{
    /**
     * @var \Symfony\Component\PropertyAccess\PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * @var \Staffim\DTOBundle\Request\RelationManager
     */
    private $relationManager;

    /**
     * @var \Staffim\DTOBundle\DTO\Factory
     */
    private $factory;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $eventDispatcher;

    private $usedRelations = [];

    /**
     * @param \Symfony\Component\PropertyAccess\PropertyAccessorInterface $propertyAccessor
     * @param \Staffim\DTOBundle\Request\RelationManager $relationManager
     * @param \Staffim\DTOBundle\DTO\Factory $factory
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        PropertyAccessorInterface $propertyAccessor,
        RelationManager $relationManager,
        Factory $factory,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->propertyAccessor = $propertyAccessor;
        $this->relationManager = $relationManager;
        $this->factory = $factory;
        $this->eventDispatcher = $eventDispatcher;
    }

    private function resetRelations()
    {
        $this->usedRelations = [];
    }

    private function pushRelation($relation)
    {
        array_push($this->usedRelations, $relation);
    }

    private function popRelation($relation)
    {
        array_pop($this->usedRelations);
    }

    private function hasRelation($relation)
    {
        return $this->relationManager->hasRelation(implode('.', array_merge($this->usedRelations, [$relation])));
    }

    /**
     * Map domain model to DTO.
     *
     * @param \Staffim\DTOBundle\Model\ModelInterface $model
     * @return object $dto
     */
    public function map(ModelInterface $model)
    {
        $this->resetRelations();

        return $this->doMap($model);
    }

    private function doMap(ModelInterface $model)
    {
        $dto = $this->factory->create($model);
        $properties = get_object_vars($dto);

        // @todo trigger pre event

        foreach ($properties as $propertyName => $property) {
            $this->updateProperty($model, $dto, $propertyName);
        }

        $event = new PostMapEvent($model, $dto);
        $modelClassParts = explode('\\', get_class($model));
        $modelName = \Doctrine\Common\Util\Inflector::tableize(end($modelClassParts));
        $this->eventDispatcher->dispatch('dto.' . $modelName . '.post_map', $event);

        return $dto;
    }

    private function updateProperty($model, $dto, $propertyName)
    {
        try {
            $modelValue = $this->propertyAccessor->getValue($model, $propertyName);
        } catch (NoSuchPropertyException $e) {
            $modelValue = null;
        }

        $this->propertyAccessor->setValue(
            $dto,
            $propertyName,
            $this->convertValue($modelValue, $propertyName)
        );
    }

    /**
     * Map iterator of models to array with DTO.
     *
     * @param \Staffim\DTOBundle\Collection\ModelIteratorInterface $collection
     * @return array
     */
    public function mapCollection(ModelIteratorInterface $collection)
    {
        $this->resetRelations();

        return $this->doMapCollection($collection);
    }

    private function doMapCollection(\Iterator $iterator)
    {
        $a = [];
        foreach ($iterator as $i) {
            if (!($i instanceof ModelInterface)) {
                var_dump(get_class($model));die();
            }

            $a[] = $this->doMap($i);
        }
        return $a;

        return array_map(function ($model) {
            if (!($model instanceof ModelInterface)) {
                var_dump(get_class($model));die();
            }

            return $this->doMap($model);
        }, array_values(iterator_to_array($iterator)));
    }

    private function convertValue($modelValue, $propertyName)
    {
        if ($modelValue instanceof EmbeddedModelCollection) {
            $this->pushRelation($propertyName);
            $value = $this->doMapCollection($modelValue);
            $this->popRelation($propertyName);
        } elseif (is_array($modelValue) || (is_object($modelValue) && ($modelValue instanceof \Traversable))) {
            $value = [];
            foreach ($modelValue as $key => $modelValueItem) {
                $value[$key] = $this->convertValue($modelValueItem, $propertyName);
            }
        } elseif ($modelValue instanceof ModelInterface) {
            if ($this->hasRelation($propertyName) || $modelValue instanceof EmbeddedModelInterface) {
                $this->pushRelation($propertyName);
                $value = $this->doMap($modelValue);
                $this->popRelation($propertyName);
            } else {
                $value = $modelValue->getId();
            }
        } else {
            $value = $modelValue;
        }

        return $value;
    }
}
