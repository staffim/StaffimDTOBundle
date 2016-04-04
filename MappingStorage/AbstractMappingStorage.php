<?php

namespace Staffim\DTOBundle\MappingStorage;

abstract class AbstractMappingStorage implements MappingStorageInterface
{
    /**
     * @var \Staffim\DTOBundle\MappingStorage\AbstractMappingStorage
     */
    protected $innerStorage;

    /**
     * @param mixed $model
     * @param string $key
     * @return bool
     */
    abstract protected function isValuePresent($model, $key);

    /**
     * @param mixed $model
     * @param string $key
     * @return bool
     */
    abstract protected function getValue($model, $key);

    /**
     * @return \Staffim\DTOBundle\MappingStorage\AbstractMappingStorage
     */
    public function getInnerStorage()
    {
        return $this->innerStorage;
    }

    /**
     * @param \Staffim\DTOBundle\MappingStorage\AbstractMappingStorage $innerStorage
     */
    public function setInnerStorage(AbstractMappingStorage $innerStorage)
    {
        $this->innerStorage = $innerStorage;
    }

    /**
     * @param mixed $model
     * return array
     */
    public function getRelations($model)
    {
        return $this->getFields($model, 'relations');
    }

    /**
     * @param mixed $model
     * @return array
     */
    public function getFieldsToShow($model)
    {
        return $this->getFields($model, 'fields');
    }

    /**
     * @param mixed $model
     * @return array
     */
    public function getFieldsToHide($model)
    {
        return $this->getFields($model, 'hideFields');
    }

    /**
     * @param mixed $model
     * @param string $key
     * @return mixed
     */
    public function getFields($model, $key)
    {
        $result = null;

        if ($this->isValuePresent($model, $key)) {
            $result = $this->getValue($model, $key);
        } elseif ($this->innerStorage) {
            $result = $this->innerStorage->getFields($model, $key);
        }

        return $result;
    }
}
