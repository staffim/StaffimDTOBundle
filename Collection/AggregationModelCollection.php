<?php

namespace Staffim\DTOBundle\Collection;

use Doctrine\MongoDB\Aggregation\Builder;

class AggregationModelCollection extends ModelCollection
{
    private $commandCursor;

    public function __construct(Builder $builder, Pagination $pagination = null)
    {
        $this->count = $builder->execute()->count();

        $this->preparePagination($builder, $pagination);

        $this->commandCursor = $builder->execute();
    }

    /**
     * @return \Doctrine\MongoDB\Iterator
     */
    public function getIterator()
    {
        return $this->commandCursor;
    }
}
