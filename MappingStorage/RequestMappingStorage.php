<?php

namespace Staffim\DTOBundle\MappingStorage;

use Symfony\Component\HttpFoundation\RequestStack;

class RequestMappingStorage extends AbstractMappingStorage
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
     * @inheritdoc
     */
    protected function isValuePresent($model, $key)
    {
        return !!$this->requestStack->getCurrentRequest()->get($key);
    }

    /**
     * @inheritdoc
     */
    protected function getValue($model, $key)
    {
        $value = $this->requestStack->getCurrentRequest()->get($key, []);

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
