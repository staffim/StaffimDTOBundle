<?php

namespace Staffim\DTOBundle\DTO;

use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory;

class ModelNameResolver
{
    private Inflector $inflector;

    public function getInflector(): Inflector
    {
        if (!$this->inflector) {
            $this->inflector = InflectorFactory::create()->build();
        }
        return $this->inflector;
    }

    public function resolve(object $model): string
    {
        $modelClassParts = explode('\\', get_class($model));

        return $this->getInflector()->tableize(end($modelClassParts));
    }
}
