<?php

namespace Staffim\DTOBundle\Request;

interface MappingConfiguratorInterface
{
    /**
     * @param array $propertyPath
     * @return bool
     */
    public function isPropertyVisible(array $propertyPath);

    /**
     * @param array $propertyPath
     * @return bool
     */
    public function hasRelation(array $propertyPath);
}
