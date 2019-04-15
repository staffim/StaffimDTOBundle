<?php

namespace Staffim\DTOBundle\Hateoas;

use Hateoas\Configuration\Metadata\ConfigurationExtensionInterface;
use Hateoas\Configuration\Metadata\ClassMetadataInterface;
use Hateoas\Configuration\Relation;

class DTOConfigurationExtension implements ConfigurationExtensionInterface
{
    private $relationProvider;

    public function __construct(DTORelationProvider $relationProvider)
    {
        $this->relationProvider = $relationProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function decorate(ClassMetadataInterface $classMetadata): void
    {
        $class = new \ReflectionClass($classMetadata->getName());
        if (!$class->isAbstract()) {
            $relations = $this->relationProvider->addRelations($classMetadata->getName());
            foreach ($relations as $relation) {
                $classMetadata->addRelation($relation);
            }
        }
    }
}
