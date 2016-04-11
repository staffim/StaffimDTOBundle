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
     * @param array $propertyPath
     * @return bool
     */
    public function isPropertyVisible(array $fullPropertyPath)
    {
        $fieldsToShow = $this->storage->getFieldsToShow();
        $fieldsToHide = $this->storage->getFieldsToHide();

        if (count($fieldsToShow) === 0 && count($fieldsToHide) === 0) {
            return true;
        }

        $propertyName = array_pop($fullPropertyPath);
        if ($propertyName === 'id') {
            return true;
        }

        $propertyPath = [$propertyName];

        if ($fullPropertyPath) {
            $fieldsToShow = $this->getPropertyConfig($fullPropertyPath, $fieldsToShow);
            $fieldsToHide = $this->getPropertyConfig($fullPropertyPath, $fieldsToHide);
        }

        if (count($fieldsToShow) > 0) {
            $showPropertyConfig = $this->getPropertyConfig($propertyPath, $fieldsToShow);

            return is_array($showPropertyConfig);
        }

        if (is_array($fieldsToHide)) {
            $hideConfig = $this->getPropertyConfig($propertyPath, $fieldsToHide);

            return !is_array($hideConfig) || count($hideConfig) > 0;
        }

        return false;
    }

    /**
     * @param array $propertyName
     * @return bool
     */
    public function hasRelation(array $propertyPath)
    {
        return is_array($this->getPropertyConfig($propertyPath, $this->storage->getRelations()));
    }

    /**
     * @param array $path
     * @param array $fieldsTree
     * @return array|null
     */
    private function getPropertyConfig(array $path, array $fieldsTree = [])
    {
        $propertyConfig = $fieldsTree;

        foreach ($path as $property) {
            if (!array_key_exists($property, $propertyConfig)) {
                return null;
            }

            $propertyConfig = $propertyConfig[$property];
        }

        return $propertyConfig;
    }
}
