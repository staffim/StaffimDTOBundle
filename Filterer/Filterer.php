<?php

namespace Staffim\DTOBundle\Filterer;

use Metadata\MetadataFactoryInterface;
use Staffim\DTOBundle\Filterer\Annotations\FilterInterface;

class Filterer
{
    /**
     * @var \Metadata\MetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * @var \Staffim\DTOBundle\Filterer\AnnotationFilterFactoryInterface
     */
    private $annotationFilterFactory;

    /**
     * @param \Metadata\MetadataFactoryInterface $metadataFactory
     * @param \Staffim\DTOBundle\Filterer\AnnotationFilterFactoryInterface $annotationFilterFactory
     */
    public function __construct(MetadataFactoryInterface $metadataFactory, AnnotationFilterFactoryInterface $annotationFilterFactory)
    {
        $this->metadataFactory = $metadataFactory;
        $this->annotationFilterFactory = $annotationFilterFactory;
    }

    /**
     * @param \Staffim\DTOBundle\DTO\Model\DTOInterface $modelObject
     * @return \Staffim\DTOBundle\Filterer\Metadata\PropertyMetadata
     */
    private function getClassPropertyMetadata($modelObject)
    {
        return $this->metadataFactory
            ->getMetadataForClass(get_class($modelObject))
            ->getRootClassMetadata()
            ->propertyMetadata;
    }

    /**
     * @param \Staffim\DTOBundle\DTO\Model\DTOInterface $modelObject
     */
    public function apply($modelObject)
    {
        if ($classFilteredProperties = $this->getClassPropertyMetadata($modelObject)) {
            foreach ($classFilteredProperties as $property) {
                $filters = $property->getFilters();
                array_walk($filters, function ($filter) use ($modelObject, $property) {
                    $this->applyFilter($modelObject, $property->reflection, $filter);
                });
            }
        }
    }

    /**
     * @param \Staffim\DTOBundle\Model\ModelInterface $object
     * @param \ReflectionProperty $property
     * @param \Staffim\DTOBundle\Filterer\Annotations\FilterInterface $filter
     */
    private function applyFilter($object, $property, FilterInterface $filter)
    {
        $filteredValue = $this->annotationFilterFactory->getInstance($filter)->apply($property->getValue($object));
        $property->setValue($object, $filteredValue);
    }
}
