<?php

namespace Staffim\DTOBundle\MappingStorage;

abstract class AbstractMappingStorage implements MappingStorageInterface
{
    /**
     * @param mixed $model
     * @param string $key
     * @return array
     */
    abstract protected function getRawFields($model, $key);

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
        return $this->getFields($model, 'hideFields', false);
    }

    /**
     * @param mixed $model
     * @param string $key
     * @return array
     */
    private function getFields($model, $key, $expandPath = true)
    {
        return $this->compileFields($model, $key, $expandPath);
    }

    /**
     * @param mixed $model
     * @param string $key
     * @param bool $expandPath
     * @return array
     */
    private function compileFields($model, $key, $expandPath = true)
    {
        $rawValues = $this->getRawFields($model, $key);

        if ($expandPath) {
            $result = [];
            foreach ($rawValues as $path) {
                $path = explode('.', $path);
                $value = '';
                foreach ($path as $item) {
                    $value .= $item;
                    $result[] = $value;
                    $value .= '.';
                }

            }
            $result = array_values(array_unique($result));
        } else {
            $result = $rawValues;
        }

        return $result;
    }
}
