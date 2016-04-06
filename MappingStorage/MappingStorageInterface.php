<?php

namespace Staffim\DTOBundle\MappingStorage;

interface MappingStorageInterface
{
    /**
     * return array
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
}
