<?php

namespace Staffim\DTOBundle\Collection;

use Doctrine\MongoDB\Query\Builder;

class ModelCollection extends BaseModelCollection
{
    /**
     * @var \Doctrine\ODM\MongoDB\Query\Query
     */
    protected $query;

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
     * @return \Doctrine\MongoDB\Iterator
     */
    public function getIterator()
    {
        return $this->query->getIterator();
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
