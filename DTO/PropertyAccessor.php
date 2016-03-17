<?php

namespace Staffim\DTOBundle\DTO;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;

class PropertyAccessor implements PropertyAccessorInterface
{
    public function getValue($objectOrArray, $propertyPath)
    {
        $camelize = lcfirst($propertyPath);
        $getter = 'get' . $camelize;
        $isser = 'is' . $camelize;
        if (method_exists($objectOrArray, $getter)) {
            return $objectOrArray->$getter();
        } elseif (method_exists($objectOrArray, $isser)) {
            return $objectOrArray->$isser();
        } elseif (array_key_exists($propertyPath, get_object_vars($objectOrArray))) {
            return $objectOrArray->$propertyPath;
        } else {
            throw new NoSuchPropertyException;
        }
    }

    public function setValue(&$objectOrArray, $propertyPath, $value)
    {
        $camelize = lcfirst($propertyPath);
        $setter = 'set' . $camelize;
        if (property_exists($objectOrArray, $propertyPath)) {
            $objectOrArray->$propertyPath = $value;
        } elseif (method_exists($objectOrArray, $setter)) {
            return $objectOrArray->$setter($value);
        } else {
            throw new NoSuchPropertyException;
        }
    }

    public function isReadable($objectOrArray, $propertyPath)
    {
        return true;
    }

    public function isWritable($objectOrArray, $propertyPath)
    {
        return true;
    }
}
