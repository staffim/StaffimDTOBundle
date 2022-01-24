<?php

namespace Staffim\DTOBundle\Collection;

use Doctrine\ODM\MongoDB\Aggregation\Builder;

class AggregationModelCollection extends ModelCollection
{
    private $commandCursor;

    public function __construct(
        Builder $builder,
        Pagination $pagination = null,
        Sorting $sorting = null,
        string $hydrationClass = null
    ) {
        $this->initMetadata($builder);

        if ($hydrationClass) {
            $builder->hydrate($hydrationClass);
        }

        $this->preparePagination($builder, $pagination);
        $this->prepareSorting($builder, $sorting);

        $this->commandCursor = $builder->getAggregation()->getIterator();
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

    protected function initMetadata(Builder $builder)
    {
        $count = (clone $builder)
            ->hydrate('')
            ->count('count')->getAggregation()->getIterator()->current();

        $this->count = is_array($count) ? $count['count'] : 0;
    }
}
