<?php

namespace Staffim\DTOBundle\Request;

interface MappingConfiguratorInterface
{
    /**
     * @param string $propertyName
     * @return bool
     */
    public function isPropertyVisible($propertyName);

    /**
     * @param string $propertyName
     * @return bool
     */
    public function hasRelation($propertyName);
}
