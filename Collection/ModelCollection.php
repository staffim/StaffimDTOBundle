<?php

namespace Staffim\DTOBundle\Collection;

use Doctrine\MongoDB\Query\Builder;

class ModelCollection implements ModelIteratorInterface, \Countable
{
    /**
     * @var \Doctrine\ODM\MongoDB\Query\Query
     */
    protected $query;

    /**
     * @var int
     */
    protected $count;

    /**
     * @var \Staffim\DTOBundle\Collection\Pagination
     */
    protected $pagination;

    /**
     * Constructor.
     *
     * @param \Doctrine\MongoDB\Query\Builder $queryBuilder
     * @param null|\Staffim\DTOBundle\Collection\Pagination $pagination
     * @param null|\Staffim\DTOBundle\Collection\Sorting $sorting
     */
    public function __construct(Builder $queryBuilder, Pagination $pagination = null, Sorting $sorting = null)
    {
        $this->count = $queryBuilder->getQuery()->count();

        $this->preparePagination($queryBuilder, $pagination);

        if ($sorting) {
            $queryBuilder->sort($sorting->fieldName, $sorting->order);
        }

        $this->query = $queryBuilder->getQuery();
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
     * @deprecated
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

    public function count()
    {
        return $this->getCount();
    }

    /**
     * @param \Doctrine\MongoDB\Aggregation\Builder|\Doctrine\MongoDB\Query\Builder $builder
     * @param \Staffim\DTOBundle\Collection\Pagination|null $pagination
     */
    protected function preparePagination($builder, Pagination $pagination = null)
    {
        if (!$pagination) {
            return;
        }

        if ($pagination->offset) {
            $builder->skip($pagination->offset);
        }

        if ($pagination->limit) {
            $builder->limit($pagination->limit);
        }

        $this->pagination = $pagination;
    }
}
