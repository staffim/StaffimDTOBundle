<?php

namespace Staffim\DTOBundle\DTO;

use Staffim\DTOBundle\Collection\EmbeddedModelCollection;
use Staffim\DTOBundle\DTO\Model\DTOInterface;
use Staffim\DTOBundle\Exception\MappingException;
use Staffim\DTOBundle\Request\MappingConfigurator;
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
     * @var \Staffim\DTOBundle\Request\MappingConfigurator
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
     * @param \Staffim\DTOBundle\Request\MappingConfigurator $mappingConfigurator
     * @param \Staffim\DTOBundle\DTO\Factory $factory
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        PropertyAccessorInterface $propertyAccessor,
        MappingConfigurator $mappingConfigurator,
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
    public function map(ModelInterface $model)
    {
        return $this->doMap($model);
    }

    /**
     * Map iterator of models to array with DTO.
     *
     * @param \Staffim\DTOBundle\Collection\ModelIteratorInterface $collection
     * @return array
     */
    public function mapCollection(ModelIteratorInterface $collection)
    {
        return $this->doMapCollection($collection);
    }

    /**
     * @param \Staffim\DTOBundle\Collection\ModelIteratorInterface $collection
     * @param string $parentPropertyName
     * @return array
     * @throws \Staffim\DTOBundle\Exception\MappingException
     */
    private function doMapCollection(ModelIteratorInterface $collection, $parentPropertyName = null)
    {
        $result = [];
        foreach ($collection as $model) {
            if (!($model instanceof ModelInterface)) {
                throw new MappingException('Class \''. get_class($model) .'\' should implement \\Staffim\\DTOBundle\\Model\\ModelInterface');
            }

            $result[] = $this->doMap($model, $parentPropertyName);
        }

        return $result;
    }

    /**
     * @param \Staffim\DTOBundle\Model\ModelInterface $model
     * @param string $parentPropertyName
     * @return \Staffim\DTOBundle\DTO\Model\DTOInterface
     */
    private function doMap(ModelInterface $model, $parentPropertyName = null)
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
     * @param mixed $model
     * @param \Staffim\DTOBundle\DTO\Model\DTOInterface $dto
     * @param string $propertyName
     * @param string $parentPropertyName
     */
    private function updateProperty($model, DTOInterface $dto, $propertyName, $parentPropertyName = null)
    {
        $fullPropertyName = $parentPropertyName ? $parentPropertyName . '.' . $propertyName : $propertyName;

        if (!$this->mappingConfigurator->isPropertyVisible($fullPropertyName)) {
            $modelValue = UnknownValue::create();
        } else {
            try {
                $modelValue = $this->propertyAccessor->getValue($model, $propertyName);
            } catch (NoSuchPropertyException $e) {
                return;
            }
        }

        $this->propertyAccessor->setValue(
            $dto,
            $propertyName,
            $this->convertValue($modelValue, $fullPropertyName)
        );
    }

    /**
     * @param mixed $modelValue
     * @param string $propertyName
     * @return array|object
     */
    private function convertValue($modelValue, $propertyName)
    {
        if ($modelValue instanceof EmbeddedModelCollection) {
            $value = $this->doMapCollection($modelValue, $propertyName);
        } elseif (is_array($modelValue) || (is_object($modelValue) && ($modelValue instanceof \Traversable))) {
            $value = [];
            foreach ($modelValue as $key => $modelValueItem) {
                $value[$key] = $this->convertValue($modelValueItem, $propertyName);
            }
        } elseif ($modelValue instanceof ModelInterface) {
            if ($this->mappingConfigurator->hasRelation($propertyName) || $modelValue instanceof EmbeddedModelInterface) {
                $value = $this->doMap($modelValue, $propertyName);
            } else {
                $value = $modelValue->getId();
            }
        } else {
            $value = $modelValue;
        }

        return $value;
    }
}
