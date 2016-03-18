<?php

namespace Staffim\DTOBundle\Request;

interface MappingConfiguratorInterface
{
    /**
     * @return array
     */
    public function getRelations();

    /**
     * @return array
     */
    public function getFieldsToShow();

    /**
     * @return array
     */
    public function getFieldsToHide();

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
