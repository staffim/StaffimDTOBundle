<?php

namespace Staffim\DTOBundle\Collection;

class ModelIterator extends \ArrayIterator implements ModelIteratorInterface
{
    /**
     * @param \Staffim\DTOBundle\Model\ModelInterface[]
     */
    public function __construct(array $models)
    {
        parent::__construct($models);
    }
}
