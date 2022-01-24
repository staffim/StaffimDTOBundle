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

    public function current(): mixed
    {
        return $this->getIterator()->current();
    }

    public function key(): mixed
    {
        return $this->getIterator()->key();
    }

    public function next(): void
    {
        $this->getIterator()->next();
    }

    public function rewind(): void
    {
        $this->getIterator()->rewind();
    }

    public function valid(): bool
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
