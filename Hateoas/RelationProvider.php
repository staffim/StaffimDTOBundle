<?php

namespace Staffim\DTOBundle\Hateoas;

use Staffim\DTOBundle\DTO\Model\DTOInterface;

class RelationProvider extends \Hateoas\Configuration\Provider\RelationProvider
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
    public function getRelations(string $class)
    {
        if (is_a($class, DTOInterface::class)) {
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
