<?php

namespace Staffim\DTOBundle\Filterer\Metadata;

use Metadata\PropertyMetadata as BasePropertyMetadata;

class PropertyMetadata extends BasePropertyMetadata
{
    /**
     * @var \Staffim\DTOBundle\Filterer\Annotations\FilterInterface[]
     */
    private $filters = [];

    /**
     * @param \Staffim\DTOBundle\Filterer\Annotations\FilterInterface[] $filters
     */
    public function setFilters(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * @return \Staffim\DTOBundle\Filterer\Annotations\FilterInterface[]
     */
    public function getFilters()
    {
        return $this->filters;
    }

    public function serialize()
    {
        return serialize([
            $this->filters,
            parent::serialize(),
        ]);
    }

    public function unserialize($str)
    {
        list(
            $this->filters,
            $parentStr
        ) = unserialize($str);

        parent::unserialize($parentStr);
    }
}
