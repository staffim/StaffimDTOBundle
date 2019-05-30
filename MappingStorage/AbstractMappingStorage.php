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
     * return Config
     */
    public function getRelations()
    {
        return $this->getFields('relations', false);
    }

    /**
     * @return Config
     */
    public function getFieldsToShow()
    {
        return $this->getFields('fields');
    }

    /**
     * @return Config
     */
    public function getFieldsToHide()
    {
        return $this->getFields('hideFields');
    }

    /**
     * @param $key
     * @param bool $asField
     * @return Config
     */
    private function getFields($key, $asField = true)
    {
        return $this->compileFields($key, $asField);
    }

    /**
     * @param $key
     * @param bool $asField
     * @return Config
     */
    private function compileFields($key, $asField = true)
    {
        return $this->buildConfig($this->getRawFields($key), $asField);
    }

    /**
     * @param array $data
     * @param $asField
     * @return Config
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
