<?php

namespace Staffim\DTOBundle\Request;

class CachedRequestMappingConfigurator extends RequestMappingConfigurator
{
    /**
     * @var array
     */
    private $cache = [];

    /**
     * @param string $type
     * @return array
     */
    protected function getFields($type)
    {
        if (!array_key_exists($type, $this->cache)) {
            $this->cache[$type] = parent::getFields($type);
        }

        return $this->cache[$type];
    }
}
