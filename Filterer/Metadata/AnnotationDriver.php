<?php

namespace Staffim\DTOBundle\Filterer\Metadata;

use Doctrine\Common\Annotations\Reader as AnnotationsReader;
use Metadata\ClassMetadata;
use Metadata\Driver\DriverInterface;

class AnnotationDriver implements DriverInterface
{
    /**
     * @var \Doctrine\Common\Annotations\Reader
     */
    private $reader;

    /**
     * @param \Doctrine\Common\Filters\Reader
     */
    public function __construct(AnnotationsReader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * {@inheritdoc}
     */
    public function loadMetadataForClass(\ReflectionClass $class): ?ClassMetadata
    {
        $classMetadata = new ClassMetadata($name = $class->name);

        foreach ($class->getProperties() as $property) {
            if ($property->class !== $name) {
                continue;
            }
            if ($filters = $this->readPropertyFilters($property)) {
                $propertyMetadata = new PropertyMetadata($name, $property->getName());
                $propertyMetadata->setFilters($filters);
                $classMetadata->addPropertyMetadata($propertyMetadata);
            }
        }

        return $classMetadata;
    }

    /**
     * @param \ReflectionProperty $property
     * @return \Staffim\DTOBundle\Filterer\Annotations\FilterInterface[]
     */
    private function readPropertyFilters($property)
    {
        return array_filter($this->reader->getPropertyAnnotations($property), function ($annotation) {
            return $this->supports($annotation);
        });
    }

    /**
     * @param \Staffim\DTOBundle\Filterer\Annotations\FilterInterface $annotation
     * @return bool
     */
    public function supports($annotation)
    {
        if (class_exists(get_class($annotation))) {
            return in_array('Staffim\DTOBundle\Filterer\Annotations\FilterInterface', class_implements($annotation));
        } else {
            return false;
        }
    }
}
