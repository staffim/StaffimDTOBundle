<?php

namespace Staffim\DTOBundle\Filterer;

use Staffim\DTOBundle\Filterer\Annotations\FilterInterface;

interface AnnotationFilterFactoryInterface
{
    /**
     * @param \Staffim\DTOBundle\Filterer\Annotations\FilterInterface $annotationFilter
     * @return \Staffim\DTOBundle\Filterer\Filters\FilterInterface $filter
     */
    public function getInstance(FilterInterface $annotationFilter);

    /**
     * @param \Staffim\DTOBundle\Filterer\Filters\FilterInterface $filter
     * @param string $name
     */
    public function addFilter(Filters\FilterInterface $filter, $name);
}
