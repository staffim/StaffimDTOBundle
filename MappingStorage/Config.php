<?php

namespace Staffim\DTOBundle\MappingStorage;

use Staffim\DTOBundle\Exception\OutOfBoundsException;

class Config
{
    private static $emptyRoot = [
        'children' => [],
        'fields' => [],
    ];

    private $config;
    private $empty;

    public function __construct()
    {
        $this->clear();
    }

    public function clear()
    {
        $this->config = self::$emptyRoot;
        $this->empty = true;
    }

    public function add(array $propertyPath, $asField = true)
    {
        if (!$propertyPath) {
            return;
        }
        $this->empty = false;
        if ($asField) {
            $value = array_pop($propertyPath);
        }

        $config = &$this->config;
        foreach ($propertyPath as $pathItem) {
            if (!array_key_exists($pathItem, $config['children'])) {
                $this->addRoot($config['children'], $pathItem);
            }
            $config = &$config['children'][$pathItem];
        }

        if ($asField) {
            $config['fields'][] = $value;
        }        
    }

    public function isEmpty()
    {
        return $this->empty;
    }

    public function hasPath(array $path, $withFields = true)
    {
        $config = $this->config;
        foreach ($path as $pathItem) {
            if (array_key_exists($pathItem, $config['children'])) {
                $config = $config['children'][$pathItem];
            } else {
                return false;
            }
        }

        return $withFields ? count($config['fields']) > 0 : true;
    }

    public function hasField(array $path, $field)
    {
        return in_array($field, $this->getFields($path));
    }

    public function getFields(array $path)
    {
        $config = $this->config;
        foreach ($path as $pathItem) {
            if (array_key_exists($pathItem, $config['children'])) {
                $config = $config['children'][$pathItem];
            } else {
                throw new OutOfBoundsException('Path "' . implode('.',  $path) . '" not found');
            }
        }

        return $config['fields'];
    }

    public function getChildrenKeys(array $path = [])
    {
        $config = $this->config;
        foreach ($path as $pathItem) {
            if (array_key_exists($pathItem, $config['children'])) {
                $config = $config['children'][$pathItem];
            } else {
                throw new OutOfBoundsException('Path "' . implode('.',  $path) . '" not found');
            }
        }

        return array_keys($config['children']);
    }

    private function addRoot(array &$config, $pathItem)
    {
        $config[$pathItem] = self::$emptyRoot;
    }
}
