<?php

namespace Staffim\DTOBundle\Collection;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class ORMModelCollection extends BaseModelCollection
{
    /**
     * @var \Doctrine\ODM\MongoDB\Query\Query
     */
    protected $query;

    protected $iterator;

    /**
     * Constructor.
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param null|\Staffim\DTOBundle\Collection\Pagination $pagination
     * @param null|\Staffim\DTOBundle\Collection\Sorting $sorting
     */
    public function __construct(QueryBuilder $queryBuilder, Pagination $pagination = null, Sorting $sorting = null)
    {

        $this->preparePagination($queryBuilder, $pagination);

        if ($sorting) {
            $queryBuilder->orderBy($sorting->fieldName, $sorting->order);
        }

        $paginator = new Paginator($queryBuilder);
        $this->count = count($paginator);

        $this->query = $paginator->getQuery();
        $this->iterator = $paginator->getIterator();
    }

    /**
     * @return \Doctrine\ORM\Internal\Hydration\IterableResult
     */
    public function getIterator()
    {
        return $this->iterator;
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param \Staffim\DTOBundle\Collection\Pagination|null $pagination
     */
    protected function preparePagination(QueryBuilder $queryBuilder, Pagination $pagination = null)
    {
        if (!$pagination) {
            return;
        }

        if ($pagination->offset) {
            $queryBuilder->setFirstResult($pagination->offset);
        }

        if ($pagination->limit) {
            $queryBuilder->setMaxResults($pagination->limit);
        }

        $this->pagination = $pagination;
    }
}
