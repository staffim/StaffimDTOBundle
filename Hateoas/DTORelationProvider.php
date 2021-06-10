<?php

namespace Staffim\DTOBundle\Hateoas;

use Hateoas\Configuration as Hateoas;
use Hateoas\Configuration\Metadata\ClassMetadataInterface;
use JMS\Serializer\Expression\CompilableExpressionEvaluatorInterface;
use Metadata\MetadataFactoryInterface;
use Staffim\DTOBundle\DTO\UnknownValue;

class DTORelationProvider
{
    /**
     * @var \Metadata\MetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * @var \JMS\Serializer\Expression\CompilableExpressionEvaluatorInterface
     */
    private $evaluator;

    /**
     * @param \Metadata\MetadataFactoryInterface $metadataFactory
     * @param \JMS\Serializer\Expression\CompilableExpressionEvaluatorInterface $evaluator
     */
    public function __construct(
        MetadataFactoryInterface $metadataFactory,
        CompilableExpressionEvaluatorInterface $evaluator
    ) {
        $this->metadataFactory = $metadataFactory;
        $this->evaluator = $evaluator;
    }

    public function addRelations($class)
    {
        $metadata = $this->metadataFactory->getMetadataForClass($class);
        $relations = [];

        foreach ($metadata->propertyMetadata as $propertyName => $propertyMetadata) {
            if ($this->isPropertyDTO($propertyMetadata->type)) {
                $value = $this->evaluator->parse('object.' . $propertyName, ['object']);
                $route = null;
                $relations[] = new Hateoas\Relation(
                    $propertyName,
                    $route,
                    new Hateoas\Embedded($value),
                    [],
                    new Hateoas\Exclusion(null, null, null, null,
                        'object.' . $propertyName . ' === null || !is_dto(object.' . $propertyName . ')')
                );
            }
        }

        return $relations;
    }

    public function _addRelations($object, ClassMetadataInterface $classMetadata)
    {
        // @todo fix hateoas metadata to include serializer metadata
        $metadata = $this->metadataFactory->getMetadataForClass($classMetadata->name);
        $relations = [];

        foreach ($metadata->propertyMetadata as $propertyName => $propertyMetadata) {
            if ($this->_isPropertyDTO($propertyMetadata->type, $object->$propertyName)) {
                $route = null;
                $relations[] = new Hateoas\Relation(
                    $propertyName,
                    $route,
                    new Hateoas\Embedded('expr(object.' . $propertyName . ')')
                );
            }
        }

        return $relations;
    }

    public static function isPropertyDTO($type)
    {
        if (!is_array($type)) {
            return false;
        }

        if ($type['name'] == 'DTO') {
            return true;
        } elseif ($type['name'] == 'array' && $type['params'] && $type['params'][0]['name'] == 'DTO') {
            return true;
        } else {
            return false;
        }
    }

    private function _isPropertyDTO($type, $value)
    {
        if ($value === UnknownValue::create()) {
            return false;
        }

        if ($type['name'] == 'DTO') {
            return is_object($value);
        } elseif ($type['name'] == 'array' && $type['params'] && $type['params'][0]['name'] == 'DTO') {
            return $value && count($value) && is_object($value[0]);
        } else {
            return false;
        }
    }
}
