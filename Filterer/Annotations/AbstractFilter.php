<?php

namespace Staffim\DTOBundle\Filterer\Annotations;

abstract class AbstractFilter implements FilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function filteredBy()
    {
        return str_replace('Annotations', 'Filters', get_class($this));
    }
}
