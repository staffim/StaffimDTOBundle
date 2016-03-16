<?php

namespace Staffim\DTOBundle\DTO;

use Staffim\DTOBundle\Collection\EmbeddedModelCollection;
use Staffim\DTOBundle\Exception\MappingException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;

use Staffim\DTOBundle\Request\RequestMappingConfigurator;
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
     * @var \Staffim\DTOBundle\Request\RequestMappingConfigurator
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

    private $fieldsToShow = [];

    private $fieldsToHide = [];

    /**
     * @param \Symfony\Component\PropertyAccess\PropertyAccessorInterface $propertyAccessor
     * @param \Staffim\DTOBundle\Request\RequestMappingConfigurator $mappingConfigurator
     * @param \Staffim\DTOBundle\DTO\Factory $factory
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        PropertyAccessorInterface $propertyAccessor,
        RequestMappingConfigurator $mappingConfigurator,
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
        $this->resetFieldsMap();

        return $this->doMap($model);
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
        return $this->doMapCollection($collection, $parentPropertyName);
    }

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

    private function updateProperty($model, $dto, $propertyName, $parentPropertyName = null)
    {
        $fullPropertyName = $parentPropertyName ? $parentPropertyName . '.' . $propertyName : $propertyName;

        if (!$this->isPropertyAllowed($fullPropertyName)) {
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
            $this->convertValue($modelValue, $propertyName)
        );
    }

    private function isPropertyAllowed($propertyName)
    {
        if (count($this->fieldsToShow) === 0 && count($this->fieldsToHide) === 0) {
            return true;
        }

        if ($propertyName === 'id' || strpos($propertyName, '.id') === strlen($propertyName) - 3) {
            return true;
        }

        if (count($this->fieldsToShow) > 0) {
            foreach ($this->fieldsToShow as $fieldToShow) {
                if ($propertyName === $fieldToShow) {
                    return true;
                }

                if (strpos($fieldToShow, $propertyName . '.') === 0) {
                    return true;
                }
            }

            return false;
        }

        return !in_array($propertyName, $this->fieldsToHide);
    }

    private function doMapCollection(\Iterator $iterator, $parentPropertyName = null)
    {
        $a = [];
        foreach ($iterator as $model) {
            if (!($model instanceof ModelInterface)) {
                throw new MappingException('Class \''. get_class($model) .'\' should implement \\Staffim\\DTOBundle\\Model\\ModelInterface');
            }

            $a[] = $this->doMap($model, $parentPropertyName);
        }

        return $a;
    }

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
            if ($this->hasRelation($propertyName) || $modelValue instanceof EmbeddedModelInterface) {
                $value = $this->doMap($modelValue, $propertyName);
            } else {
                $value = $modelValue->getId();
            }
        } else {
            $value = $modelValue;
        }

        return $value;
    }

    private function resetFieldsMap()
    {
        $this->fieldsToHide = $this->mappingConfigurator->getFieldsToHide();
        $this->fieldsToShow = $this->mappingConfigurator->getFieldsToShow();
    }

    private function hasRelation($propertyName)
    {
        return $this->mappingConfigurator->hasRelation($propertyName);
    }
}
