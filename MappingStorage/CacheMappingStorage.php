<?php

namespace Staffim\DTOBundle\MappingStorage;

class CacheMappingStorage implements MappingStorageInterface
{
    /**
     * @var \Staffim\DTOBundle\MappingStorage\MappingStorageInterface
     */
    private $storage;

    /**
     * @var array
     */
    private $cache = [];

    /**
     * @param \Staffim\DTOBundle\MappingStorage\MappingStorageInterface $storage
     */
    public function __construct(MappingStorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @inheritdoc
     */
    public function getRelations($model)
    {
        if (!array_key_exists('relations', $this->cache)) {
            $this->cache['relations'] = $this->storage->getRelations($model);
        }

        return $this->cache['relations'];
    }

    /**
     * @inheritdoc
     */
    public function getFieldsToShow($model)
    {
        if (!array_key_exists('fieldsToShow', $this->cache)) {
            $this->cache['fieldsToShow'] = $this->storage->getFieldsToShow($model);
        }

        return $this->cache['fieldsToShow'];
    }

    /**
     * @inheritdoc
     */
    public function getFieldsToHide($model)
    {
        if (!array_key_exists('fieldsToHide', $this->cache)) {
            $this->cache['fieldsToHide'] = $this->storage->getFieldsToHide($model);
        }

        return $this->cache['fieldsToHide'];
    }
}
