<?php

namespace Staffim\DTOBundle\MappingStorage;

class StaticMappingStorage extends AbstractMappingStorage
{
    /**
     * @var array
     */
    private $config = [];

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->setConfig($config);
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param array $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    protected function isValuePresent($model, $key)
    {
        return true;
    }

    protected function getValue($model, $key)
    {
        return $this->getModelConfig($model)[$key];
    }

    private function getModelConfig($model)
    {
        $config = [];

        $modelClass = get_class($model);
        if (array_key_exists($modelClass, $this->config)) {
            $config = $this->config[$modelClass];
        }
        
        return array_replace_recursive($config, ['fields' => [], 'hideFields' => [], 'relations' => []]);
    }
}
