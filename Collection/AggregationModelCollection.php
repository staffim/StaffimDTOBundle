<?php

namespace Staffim\DTOBundle\Collection;

use Doctrine\ODM\MongoDB\Aggregation\Builder;

class AggregationModelCollection extends ModelCollection
{
    private $commandCursor;

    public function __construct(Builder $builder, Pagination $pagination = null, Sorting $sorting = null, string $hydrationClass = null)
    {
        $this->count = (clone $builder)
            ->count('count')->execute()->current()['count'] ?: 0;

        if ($hydrationClass) {
            $builder->hydrate($hydrationClass);
        }

        $this->preparePagination($builder, $pagination);
        $this->prepareSorting($builder, $sorting);

        $this->commandCursor = $builder->execute();
    }

    /**
     * @return \Doctrine\ODM\MongoDB\Iterator\Iterator
     */
    public function getIterator()
    {
        return $this->commandCursor;
    }

    /**
     * @param Builder
     * @param \Staffim\DTOBundle\Collection\Pagination|null $sorting
     */
    protected function prepareSorting(Builder $builder, Sorting $sorting = null)
    {
        if ($sorting) {
            $builder->sort($sorting->fieldName, $sorting->order);
            $this->sorting = $sorting;
        }
    }
}
