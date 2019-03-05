<?php

namespace Staffim\DTOBundle\Collection;

class EmbeddedModelCollection extends ModelCollection
{
    /**
     * @var \Staffim\DTOBundle\Collection\ModelCollection
     */
    private $modelCollection;

    /**
     * @param \Staffim\DTOBundle\Collection\ModelCollection $modelCollection
     */
    public function __construct(ModelCollection $modelCollection)
    {
        $this->modelCollection = $modelCollection;
    }

    /**
     * @return \Doctrine\MongoDB\Iterator
     */
    public function getIterator()
    {
        return $this->modelCollection->getIterator();
    }

    /**
     * @return \Staffim\DTOBundle\Collection\Pagination
     */
    public function getPagination(): ?Pagination
    {
        return $this->modelCollection->getPagination();
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->modelCollection->getCount();
    }
}
