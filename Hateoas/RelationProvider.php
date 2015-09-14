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
     * @param \Staffim\DTOBundle\DTO\Model\DTOInterface $object
     * @return array
     */
    public function getRelations($object)
    {
        if ($object instanceof DTOInterface) {
            $classMetadata = new \Hateoas\Configuration\Metadata\ClassMetadata(get_class($object));

            return $this->relationProvider->addRelations($object, $classMetadata);
        }

        return [];
    }
}
