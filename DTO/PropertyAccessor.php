<?php

namespace Staffim\DTOBundle\DTO;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

class PropertyAccessor implements PropertyAccessorInterface
{
    public function getValue(object|array $objectOrArray, string|PropertyPathInterface $propertyPath): mixed
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

    public function setValue(
        object|array &$objectOrArray,
        string|PropertyPathInterface $propertyPath,
        mixed $value
    ) {
        $camelize = lcfirst($propertyPath);
        $setter = 'set' . $camelize;
        if (property_exists($objectOrArray, $propertyPath)) {
            $objectOrArray->$propertyPath = $value;
        } elseif
        (method_exists($objectOrArray, $setter)) {
            return $objectOrArray->$setter($value);
        } else {
            throw new NoSuchPropertyException;
        }
    }

    public function isReadable(object|array $objectOrArray, string|PropertyPathInterface $propertyPath): bool {
        return true;
    }

    public function isWritable(object|array $objectOrArray, string|PropertyPathInterface $propertyPath): bool {
        return true;
    }
}
