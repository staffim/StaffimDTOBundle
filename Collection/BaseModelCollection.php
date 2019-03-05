<?php

namespace Staffim\DTOBundle\Collection;

abstract class BaseModelCollection implements ModelIteratorInterface, PaginableCollectionInterface
{
    /**
     * @var int
     */
    protected $count;

    /**
     * @var \Staffim\DTOBundle\Collection\Pagination
     */
    protected $pagination;

    /**
     * @return \Staffim\DTOBundle\Collection\Pagination
     */
    public function getPagination(): ?Pagination
    {
        return $this->pagination;
    }

    /**
     * @return \Iterator
     */
    abstract public function getIterator();

    public function current()
    {
        return $this->getIterator()->current();
    }

    public function key()
    {
        return $this->getIterator()->key();
    }

    public function next()
    {
        $this->getIterator()->next();
    }

    public function rewind()
    {
        $this->getIterator()->rewind();
    }

    public function valid()
    {
        return $this->getIterator()->valid();
    }

    public function toArray()
    {
        return iterator_to_array($this->getIterator());
    }

    public function count(): int
    {
        return $this->count;
    }
}
