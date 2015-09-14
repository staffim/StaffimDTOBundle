<?php

namespace Staffim\DTOBundle\Filterer\Annotations;

interface FilterInterface
{
    /**
     * @return string
     */
    public function filteredBy();
}
