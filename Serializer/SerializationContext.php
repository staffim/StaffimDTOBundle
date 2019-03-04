<?php

namespace Staffim\DTOBundle\Serializer;

use JMS\Serializer\Exception\LogicException;
use JMS\Serializer\SerializationContext as BaseSerializationContext;

class SerializationContext extends BaseSerializationContext
{
    public function startVisiting($object): void
    {
        if (is_object($object)) {
            parent::startVisiting($object);
        }
    }

    public function stopVisiting($object): void
    {
        if (is_object($object)) {
            parent::stopVisiting($object);
        }
    }

    public function isVisiting($object): bool
    {
        try {
            return parent::isVisiting($object);
        } catch (LogicException $e) {
            return false;
        }
    }
}
