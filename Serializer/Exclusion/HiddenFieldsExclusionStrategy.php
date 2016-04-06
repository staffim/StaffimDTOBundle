<?php

namespace Staffim\DTOBundle\Serializer\Exclusion;

use JMS\Serializer\Context;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Exclusion\ExclusionStrategyInterface;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use Staffim\DTOBundle\DTO\UnknownValue;

class HiddenFieldsExclusionStrategy implements ExclusionStrategyInterface
{
    public function shouldSkipClass(ClassMetadata $metadata, Context $context)
    {
        return false;
    }

    public function shouldSkipProperty(PropertyMetadata $property, Context $context)
    {
        if ($context instanceof DeserializationContext) {
            return false;
        }

        $data = $context->getObject();

        if ($data instanceof \Staffim\DTOBundle\Hateoas\CollectionRepresentation) {
            return false;
        }

        return is_object($data) && $property->getValue($data) === UnknownValue::create();
    }
}
