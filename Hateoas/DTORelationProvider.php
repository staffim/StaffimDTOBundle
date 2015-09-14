<?php

namespace Staffim\DTOBundle\Hateoas;

use Hateoas\Configuration as Hateoas;
use Hateoas\Configuration\Metadata\ClassMetadataInterface;
use Metadata\MetadataFactoryInterface;

class DTORelationProvider
{
    /**
     * @var \Metadata\MetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * @param \Metadata\MetadataFactoryInterface $metadataFactory
     */
    public function __construct(MetadataFactoryInterface $metadataFactory)
    {
        $this->metadataFactory = $metadataFactory;
    }

    public function addRelations($object, ClassMetadataInterface $classMetadata)
    {
        // @todo fix hateoas metadata to include serializer metadata
        $metadata = $this->metadataFactory->getMetadataForClass($classMetadata->name);
        $relations = [];

        foreach ($metadata->propertyMetadata as $propertyName => $propertyMetadata) {
            if ($this->isPropertyDTO($propertyMetadata->type, $object->$propertyName)) {
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

    private function isPropertyDTO($type, $value)
    {
        if ($type['name'] == 'DTO') {
            return is_object($value);
        } elseif ($type['name'] == 'array' && $type['params'] && $type['params'][0]['name'] == 'DTO') {
            return count($value) && is_object($value[0]);
        } else {
            return false;
        }
    }
}
