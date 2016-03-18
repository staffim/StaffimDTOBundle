<?php

namespace Staffim\DTOBundle\Request;

use Symfony\Component\HttpFoundation\RequestStack;

class RequestMappingConfigurator implements MappingConfiguratorInterface
{
    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * return array
     */
    public function getRelations()
    {
        return $this->getFields('relations');
    }

    /**
     * @return array
     */
    public function getFieldsToShow()
    {
        return $this->getFields('fields');
    }

    /**
     * @return array
     */
    public function getFieldsToHide()
    {
        return $this->getFields('hideFields');
    }

    /**
     * @param string $propertyName
     * @return bool
     */
    public function isPropertyVisible($propertyName)
    {
        $fieldsToShow = $this->getFieldsToShow();
        $fieldsToHide = $this->getFieldsToHide();

        if (count($fieldsToShow) === 0 && count($fieldsToHide) === 0) {
            return true;
        }

        if ($propertyName === 'id' || strpos($propertyName, '.id') === strlen($propertyName) - 3) {
            return true;
        }

        if (count($fieldsToShow) > 0) {
            foreach ($fieldsToShow as $fieldToShow) {
                if ($propertyName === $fieldToShow) {
                    return true;
                }

                if (strpos($fieldToShow, $propertyName . '.') === 0) {
                    return true;
                }
            }

            return false;
        }

        return !in_array($propertyName, $fieldsToHide);
    }

    /**
     * @param string $propertyName
     * @return bool
     */
    public function hasRelation($propertyName)
    {
        foreach ($this->getRelations() as $relation) {
            if ($propertyName === $relation) {
                return true;
            }

            if (strpos($relation, $propertyName . '.') === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $type
     * @return array
     */
    protected function getFields($type)
    {
        $value = $this->requestStack->getCurrentRequest()->get($type, []);

        return $this->extractFields($value);
    }

    /**
     * @param mixed $value
     * @return array
     */
    private function extractFields($value)
    {
        $result = [];
        if (is_array($value)) {
            $result = $value;
        } elseif (is_string($value)) {
            $result = array_map('trim', explode(',', $value));
        }

        return $result;
    }
}
