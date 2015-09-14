<?php

namespace Staffim\DTOBundle\Request;

use Symfony\Component\HttpFoundation\RequestStack;

class RelationManager
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
}
