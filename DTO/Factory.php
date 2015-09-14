<?php

namespace Staffim\DTOBundle\DTO;

class Factory
{
    private $namespace;
    private $postfix;

    public function __construct($namespace = '', $postfix = '')
    {
        $this->namespace = $namespace;
        $this->postfix = $postfix;
    }

    public function create($model)
    {
        $className = $this->getClass($model);

        return new $className;
    }

    private function getClass($model)
    {
        $modelClassParts = explode('\\', get_class($model));
        $modelClass = array_pop($modelClassParts);
        $namespace = $this->namespace ?: implode('\\', $modelClassParts);

        return '\\' . $namespace . '\\' . $modelClass . $this->postfix;
    }
}
