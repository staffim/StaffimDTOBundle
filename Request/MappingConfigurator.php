<?php

namespace Staffim\DTOBundle\Request;

use Staffim\DTOBundle\MappingStorage\MappingStorageInterface;

class MappingConfigurator implements  MappingConfiguratorInterface
{
    /**
     * @var MappingStorageInterface
     */
    private $storage;

    /**
     * @param MappingStorageInterface $storage
     */
    public function __construct(MappingStorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param array $fullPropertyPath
     * @return bool
     */
    public function isPropertyVisible(array $fullPropertyPath)
    {
        $fieldsToShow = $this->storage->getFieldsToShow();
        $fieldsToHide = $this->storage->getFieldsToHide();

        if ($fieldsToShow->isEmpty() && $fieldsToHide->isEmpty()) {
            return true;
        }

        $propertyName = array_pop($fullPropertyPath);
        if ($propertyName === 'id') {
            return true;
        }

        if ($fieldsToShow->hasPath($fullPropertyPath)) {
            return $fieldsToShow->hasField($fullPropertyPath, $propertyName);
        }

        if ($fieldsToHide->hasPath($fullPropertyPath)) {
            return !$fieldsToHide->hasField($fullPropertyPath, $propertyName);
        }

        return true;
    }

    /**
     * @param array $propertyPath
     * @return bool
     */
    public function hasRelation(array $propertyPath)
    {
        return $this->storage->getRelations()->hasPath($propertyPath, false);
    }
}
