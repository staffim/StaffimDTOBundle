<?php

namespace Staffim\DTOBundle\MappingStorage;

abstract class AbstractMappingStorage implements MappingStorageInterface
{
    /**
     * @param string $key
     * @return array
     */
    abstract protected function getRawFields($key);

    /**
     * return \Staffim\DTOBundle\MappingStorage\Config
     */
    public function getRelations()
    {
        return $this->getFields('relations', false);
    }

    /**
     * @return \Staffim\DTOBundle\MappingStorage\Config
     */
    public function getFieldsToShow()
    {
        return $this->getFields('fields');
    }

    /**
     * @return \Staffim\DTOBundle\MappingStorage\Config
     */
    public function getFieldsToHide()
    {
        return $this->getFields('hideFields');
    }

    /**
     * @param string $key
     * @return array
     */
    private function getFields($key, $asField = true)
    {
        return $this->compileFields($key, $asField);
    }

    /**
     * @param string $key
     * @param bool $expandPath
     * @return array
     */
    private function compileFields($key, $asField = true)
    {
        return $this->buildConfig($this->getRawFields($key), $asField);
    }

    /**
     * @param array $relations
     * @return array
     */
    private function buildConfig(array $data, $asField)
    {
        $config = new Config;
        foreach ($data as $item) {
            $config->add(explode('.', $item), $asField);
        }

        return $config;
    }
}
