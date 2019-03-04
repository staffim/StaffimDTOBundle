<?php

namespace Staffim\DTOBundle\Serializer\Exclusion;

use JMS\Serializer\Context;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Exclusion\ExclusionStrategyInterface;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Accessor\DefaultAccessorStrategy;
use Staffim\DTOBundle\DTO\UnknownValue;

class HiddenFieldsExclusionStrategy implements ExclusionStrategyInterface
{
    /**
     * @var \JMS\Serializer\Accessor\DefaultAccessorStrategy
     */
    private $accessor;

    public function __construct()
    {
        $this->accessor = new DefaultAccessorStrategy();
    }

    public function shouldSkipClass(ClassMetadata $metadata, Context $context): bool
    {
        return false;
    }

    public function shouldSkipProperty(PropertyMetadata $property, Context $context): bool
    {
        if ($context instanceof DeserializationContext) {
            return false;
        }

        $data = $context->getObject();

        if ($data instanceof \Staffim\DTOBundle\Hateoas\CollectionRepresentation) {
            return false;
        }

        if ($property->class == 'Hateoas\Configuration\Relation') {
            return false;
        }

        return is_object($data) && $this->accessor->getValue($data, $property, $context) === UnknownValue::create();
    }
}
