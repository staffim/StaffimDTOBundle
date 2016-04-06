<?php

namespace Staffim\DTOBundle\Request;

use Staffim\DTOBundle\MappingStorage\MappingStorageInterface;

class MappingConfigurator implements  MappingConfiguratorInterface
{
    /**
     * @var \Staffim\DTOBundle\MappingStorage\MappingStorageInterface
     */
    private $storage;

    /**
     * @param \Staffim\DTOBundle\MappingStorage\MappingStorageInterface $storage
     */
    public function __construct(MappingStorageInterface $storage)
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
            return in_array($propertyName, $fieldsToShow);
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
