<?php

namespace Staffim\DTOBundle\Filterer;

use Staffim\DTOBundle\Exception\UnexpectedTypeException;
use Staffim\DTOBundle\Filterer\Annotations\FilterInterface;
use Staffim\DTOBundle\Filterer\Filters;

/**
 * @DI\Service("filterer.annotation_filter_factory")
 */
class AnnotationFilterFactory implements AnnotationFilterFactoryInterface
{
    protected $filters = [];

    /**
     * {@inheritdoc}
     */
    public function getInstance(FilterInterface $annotationFilter)
    {
        $name = $annotationFilter->filteredBy();
        if (!isset($this->filters[$name])) {
            if (!class_exists($name)) {
                throw new UnexpectedTypeException;
            }
            $this->filters[$name] = new $name();
        }

        if (!$this->filters[$name] instanceof Filters\FilterInterface) {
            throw new UnexpectedTypeException;
        }

        return $this->filters[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function addFilter(Filters\FilterInterface $filter, $name)
    {
        $this->filters[$name] = $filter;
    }
}
