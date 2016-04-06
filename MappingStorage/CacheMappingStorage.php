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
    public function getRelations()
    {
        if (!array_key_exists('relations', $this->cache)) {
            $this->cache['relations'] = $this->storage->getRelations();
        }

        return $this->cache['relations'];
    }

    /**
     * @inheritdoc
     */
    public function getFieldsToShow()
    {
        if (!array_key_exists('fieldsToShow', $this->cache)) {
            $this->cache['fieldsToShow'] = $this->storage->getFieldsToShow();
        }

        return $this->cache['fieldsToShow'];
    }

    /**
     * @inheritdoc
     */
    public function getFieldsToHide()
    {
        if (!array_key_exists('fieldsToHide', $this->cache)) {
            $this->cache['fieldsToHide'] = $this->storage->getFieldsToHide();
        }

        return $this->cache['fieldsToHide'];
    }
}
