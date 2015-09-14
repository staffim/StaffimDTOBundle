<?php

namespace Staffim\DTOBundle\Collection;

use Doctrine\MongoDB\Query\Builder;

class ModelCollection implements ModelIteratorInterface
{
    /**
     * @var \Doctrine\ODM\MongoDB\Query\Query
     */
    protected $query;

    /**
     * @var int
     */
    private $count;

    /**
     * @var \Staffim\DTOBundle\Collection\Pagination
     */
    private $pagination;

    /**
     * Constructor.
     *
     * @param \Doctrine\MongoDB\Query\Builder $queryBuilder
     * @param null|\Staffim\DTOBundle\Collection\Pagination $pagination
     * @param null|\Staffim\DTOBundle\Collection\Sorting $sorting
     */
    public function __construct(Builder $queryBuilder, Pagination $pagination = null, Sorting $sorting = null)
    {
        $this->query = $queryBuilder->getQuery();
        $this->count = $this->query->count();

        if ($sorting || $pagination) {
            if ($sorting) {
                $queryBuilder->sort($sorting->fieldName, $sorting->order);
            }
            if ($pagination) {
                if ($pagination->limit) {
                    $queryBuilder->limit($pagination->limit);
                }
                if ($pagination->offset) {
                    $queryBuilder->skip($pagination->offset);
                }
                $this->pagination = $pagination;
            }
            $this->query = $queryBuilder->getQuery();
        }
    }

    /**
     * @return \Staffim\DTOBundle\Collection\Pagination
     */
    public function getPagination()
    {
        return $this->pagination;
    }

    /**
     * @return \Doctrine\MongoDB\Iterator
     */
    public function getIterator()
    {
        return $this->query->getIterator();
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

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
}
