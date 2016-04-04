<?php

namespace Staffim\DTOBundle\MappingStorage;

interface MappingStorageInterface
{
    /**
     * @param mixed $model
     * return array
     */
    public function getRelations($model);

    /**
     * @param mixed $model
     * @return array
     */
    public function getFieldsToShow($model);

    /**
     * @param mixed $model
     * @return array
     */
    public function getFieldsToHide($model);
}
