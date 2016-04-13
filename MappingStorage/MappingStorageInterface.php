<?php

namespace Staffim\DTOBundle\MappingStorage;

interface MappingStorageInterface
{
    /**
     * return \Staffim\DTOBundle\MappingStorage\Config
     */
    public function getRelations();

    /**
     * @return \Staffim\DTOBundle\MappingStorage\Config
     */
    public function getFieldsToShow();

    /**
     * @return \Staffim\DTOBundle\MappingStorage\Config
     */
    public function getFieldsToHide();
}
