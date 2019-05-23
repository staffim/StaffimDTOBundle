<?php

namespace Staffim\DTOBundle\DTO;

class ModelNameResolver
{
    /**
     * @param mixed $model
     * @return string
     */
    public function resolve($model)
    {
        $modelClassParts = explode('\\', get_class($model));

        return \Doctrine\Common\Inflector\Inflector::tableize(end($modelClassParts));
    }
}
