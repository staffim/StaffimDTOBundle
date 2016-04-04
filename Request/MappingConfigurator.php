<?php

namespace Staffim\DTOBundle\Request;

use Staffim\DTOBundle\MappingStorage\AbstractMappingStorage;
use Staffim\DTOBundle\Model\ModelInterface;

class MappingConfigurator
{
    /**
     * @var \Staffim\DTOBundle\MappingStorage\AbstractMappingStorage
     */
    private $storage;

    /**
     * @param \Staffim\DTOBundle\MappingStorage\AbstractMappingStorage $storage
     */
    public function __construct(AbstractMappingStorage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param mixed $model
     * @param string $propertyName
     * @return bool
     */
    public function isPropertyVisible($model, $propertyName)
    {
        $fieldsToShow = $this->storage->getFieldsToShow($model);
        $fieldsToHide = $this->storage->getFieldsToHide($model);

        if (count($fieldsToShow) === 0 && count($fieldsToHide) === 0) {
            return true;
        }

        if ($propertyName === 'id' || strpos($propertyName, '.id') === strlen($propertyName) - 3) {
            return true;
        }

        if (count($fieldsToShow) > 0) {
            foreach ($fieldsToShow as $fieldToShow) {
                if ($propertyName === $fieldToShow) {
                    return true;
                }

                if (strpos($fieldToShow, $propertyName . '.') === 0) {
                    return true;
                }
            }

            return false;
        }

        return !in_array($propertyName, $fieldsToHide);
    }

    /**
     * @param mixed $model
     * @param string $propertyName
     * @return bool
     */
    public function hasRelation($model, $propertyName)
    {
        foreach ($this->storage->getRelations($model) as $relation) {
            if ($propertyName === $relation) {
                return true;
            }

            if (strpos($relation, $propertyName . '.') === 0) {
                return true;
            }
        }

        return false;
    }
}
