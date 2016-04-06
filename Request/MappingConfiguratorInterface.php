<?php

namespace Staffim\DTOBundle\Request;

interface MappingConfiguratorInterface
{
    /**
     * @param mixed $model
     * @param string $propertyName
     * @return bool
     */
    public function isPropertyVisible($model, $propertyName);

    /**
     * @param mixed $model
     * @param string $propertyName
     * @return bool
     */
    public function hasRelation($model, $propertyName);
}
