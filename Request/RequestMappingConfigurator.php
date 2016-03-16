<?php

namespace Staffim\DTOBundle\Request;

use Symfony\Component\HttpFoundation\RequestStack;

class RequestMappingConfigurator
{
    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @DI\InjectParams({
     *   "requestStack": @DI\Inject
     * })
     *
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
        return $this->requestStack->getCurrentRequest()->get('relations', []);
    }

    /**
     * @param string $relation
     * @return bool
     */
    public function hasRelation($relation)
    {
        return in_array($relation, $this->getRelations());
    }

    public function getFieldsToShow()
    {
        return $this->getFields('fields');
    }

    public function getFieldsToHide()
    {
        return $this->getFields('hideFields');
    }

    /**
     * @param string $type
     * @return array
     */
    private function getFields($type)
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
            $result = explode(',', $value);
        }

        return $result;
    }
}
