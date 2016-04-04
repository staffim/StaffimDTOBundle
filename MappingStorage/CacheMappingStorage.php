<?php

namespace Staffim\DTOBundle\MappingStorage;

class CacheMappingStorage extends AbstractMappingStorage
{
    /**
     * @var array
     */
    private $cache = [];

    /**
     * @inheritdoc
     */
    protected function isValuePresent($model, $key)
    {
        return array_key_exists($key, $this->cache);
    }

    /**
     * @inheritdoc
     */
    protected function getValue($model, $key)
    {
        return $this->cache[$key];
    }

    /**
     * @param mixed $model
     * @param string $key
     * @return mixed
     */
    public function getFields($model, $key)
    {
        $result = parent::getFields($model, $key);

        if (!$this->isValuePresent($model, $key)) {
            $this->cache[$key] = $result;
        }

        return $result;
    }
}
