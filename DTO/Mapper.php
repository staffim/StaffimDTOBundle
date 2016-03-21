<?php

namespace Staffim\DTOBundle\DTO;

use Staffim\DTOBundle\Collection\EmbeddedModelCollection;
use Staffim\DTOBundle\Exception\MappingException;
use Staffim\DTOBundle\Request\MappingConfiguratorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;

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
     * @var \Staffim\DTOBundle\Request\MappingConfiguratorInterface
     */
    private $mappingConfigurator;

    /**
     * @var \Staffim\DTOBundle\DTO\Factory
     */
    private $factory;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param \Symfony\Component\PropertyAccess\PropertyAccessorInterface $propertyAccessor
     * @param \Staffim\DTOBundle\Request\MappingConfiguratorInterface $mappingConfigurator
     * @param \Staffim\DTOBundle\DTO\Factory $factory
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        PropertyAccessorInterface $propertyAccessor,
        MappingConfiguratorInterface $mappingConfigurator,
        Factory $factory,
        EventDispatcherInterface $eventDispatcher = null
    ) {
        $this->propertyAccessor = $propertyAccessor;
        $this->mappingConfigurator = $mappingConfigurator;
        $this->factory = $factory;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Map domain model to DTO.
     *
     * @param \Staffim\DTOBundle\Model\ModelInterface $model
     * @return object $dto
     */
    public function map(ModelInterface $model, $parentPropertyName = null)
    {
        $dto = $this->factory->create($model);
        $properties = get_object_vars($dto);

        // @todo trigger pre event

        foreach ($properties as $propertyName => $property) {
            $this->updateProperty($model, $dto, $propertyName, $parentPropertyName);
        }

        if ($this->eventDispatcher) {
            $event = new PostMapEvent($model, $dto);
            $modelClassParts = explode('\\', get_class($model));
            $modelName = \Doctrine\Common\Util\Inflector::tableize(end($modelClassParts));
            $this->eventDispatcher->dispatch('dto.' . $modelName . '.post_map', $event);
        }

        return $dto;
    }

    /**
     * Map iterator of models to array with DTO.
     *
     * @param \Staffim\DTOBundle\Collection\ModelIteratorInterface $collection
     * @param string $parentPropertyName
     * @return array
     */
    public function mapCollection(ModelIteratorInterface $collection, $parentPropertyName = null)
    {
        $a = [];
        foreach ($collection as $model) {
            if (!($model instanceof ModelInterface)) {
                throw new MappingException('Class \''. get_class($model) .'\' should implement \\Staffim\\DTOBundle\\Model\\ModelInterface');
            }

            $a[] = $this->map($model, $parentPropertyName);
        }

        return $a;
    }

    private function updateProperty($model, $dto, $propertyName, $parentPropertyName = null)
    {
        $fullPropertyName = $parentPropertyName ? $parentPropertyName . '.' . $propertyName : $propertyName;

        if (!$this->mappingConfigurator->isPropertyVisible($fullPropertyName)) {
            $modelValue = null;
        } else {
            try {
                $modelValue = $this->propertyAccessor->getValue($model, $propertyName);
            } catch (NoSuchPropertyException $e) {
                $modelValue = null;
            }
        }

        $this->propertyAccessor->setValue(
            $dto,
            $propertyName,
            $this->convertValue($modelValue, $fullPropertyName)
        );
    }

    private function convertValue($modelValue, $propertyName)
    {
        if ($modelValue instanceof EmbeddedModelCollection) {
            $value = $this->mapCollection($modelValue, $propertyName);
        } elseif (is_array($modelValue) || (is_object($modelValue) && ($modelValue instanceof \Traversable))) {
            $value = [];
            foreach ($modelValue as $key => $modelValueItem) {
                $value[$key] = $this->convertValue($modelValueItem, $propertyName);
            }
        } elseif ($modelValue instanceof ModelInterface) {
            if ($this->mappingConfigurator->hasRelation($propertyName) || $modelValue instanceof EmbeddedModelInterface) {
                $value = $this->map($modelValue, $propertyName);
            } else {
                $value = $modelValue->getId();
            }
        } else {
            $value = $modelValue;
        }

        return $value;
    }
}
