<?php

namespace Staffim\DTOBundle\Hateoas;

use Staffim\DTOBundle\DTO\Model\DTOInterface;
use Hateoas\Configuration\RelationProvider as Configuration;
use Hateoas\Configuration\Provider\RelationProviderInterface;

class RelationProvider implements RelationProviderInterface
{
    /**
     * @var \Staffim\DTOBundle\Hateoas\DTORelationProvider
     */
    private $relationProvider;

    /**
     * @param \Staffim\DTOBundle\Hateoas\DTORelationProvider $relationProvider
     */
    public function __construct(DTORelationProvider $relationProvider)
    {
        $this->relationProvider = $relationProvider;
    }

    /**
     * @param string $class
     * @return array
     */
    public function getRelations(Configuration $configuration, string $class): array
    {
        if (in_array(DTOInterface::class, class_implements($class))) {
            return $this->relationProvider->addRelations($class);
        }

        return [];
    }

    /**
     * @param \Staffim\DTOBundle\DTO\Model\DTOInterface $object
     * @return array
     */
    public function _getRelations($object)
    {
        if ($object instanceof DTOInterface) {
            $classMetadata = new \Hateoas\Configuration\Metadata\ClassMetadata(get_class($object));

            return $this->relationProvider->addRelations($object, $classMetadata);
        }

        return [];
    }
}
