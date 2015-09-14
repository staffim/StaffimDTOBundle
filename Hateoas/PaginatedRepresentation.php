<?php

namespace Staffim\DTOBundle\Hateoas;

use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Representation\RouteAwareRepresentation;

/**
 * @Serializer\ExclusionPolicy("all")
 * @Serializer\XmlRoot("collection")
 *
 * @Hateoas\Relation(
 *      "first",
 *      href = @Hateoas\Route(
 *          "expr(object.getRoute())",
 *          parameters = "expr(object.getParameters(0))",
 *          absolute = "expr(object.isAbsolute())"
 *      )
 * )
 * @Hateoas\Relation(
 *      "last",
 *      href = @Hateoas\Route(
 *          "expr(object.getRoute())",
 *          parameters = "expr(object.getParameters(object.getCount() - 1 - ((object.getCount() - 1) % object.getLimit())))",
 *          absolute = "expr(object.isAbsolute())"
 *      ),
 *      exclusion = @Hateoas\Exclusion(
 *          excludeIf = "expr(object.getCount() === null || object.getLimit() == 0)"
 *      )
 * )
 * @Hateoas\Relation(
 *      "next",
 *      href = @Hateoas\Route(
 *          "expr(object.getRoute())",
 *          parameters = "expr(object.getParameters(object.getOffset() + object.getLimit()))",
 *          absolute = "expr(object.isAbsolute())"
 *      ),
 *      exclusion = @Hateoas\Exclusion(
 *          excludeIf = "expr((object.getCount() !== null && (object.getOffset() + object.getLimit()) >= object.getCount()) || object.getLimit() == 0)"
 *      )
 * )
 * @Hateoas\Relation(
 *      "previous",
 *      href = @Hateoas\Route(
 *          "expr(object.getRoute())",
 *          parameters = "expr(object.getParameters(object.getOffset() - object.getLimit() > 0 ? object.getOffset() - object.getLimit() : 0))",
 *          absolute = "expr(object.isAbsolute())"
 *      ),
 *      exclusion = @Hateoas\Exclusion(
 *          excludeIf = "expr(object.getOffset() == 0 || object.getLimit())"
 *      )
 * )
 */
class PaginatedRepresentation extends RouteAwareRepresentation
{
    /**
     * @var int
     */
    private $offset;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var int
     */
    private $count;

    /**
     * @var string
     */
    private $offsetParameterName;

    /**
     * @var string
     */
    private $limitParameterName;

    public function __construct(
        $inline,
        $route,
        array $parameters = [],
        $offset = 0,
        $limit = 10,
        $count = null,
        $offsetParameterName = 'offset',
        $limitParameterName = 'limit',
        $absolute = false
    ) {
        parent::__construct($inline, $route, $parameters, $absolute);

        $this->offset = $offset;
        $this->limit = $limit;
        $this->count = $count;
        $this->offsetParameterName = $offsetParameterName;
        $this->limitParameterName = $limitParameterName;
    }

    /**
     * @return int
     */
    public function getoffset()
    {
        return $this->offset;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function getParameters($offset = null, $limit = null)
    {
        $parameters = parent::getParameters();

        $parameters[$this->offsetParameterName]  = null === $offset ? $this->getOffset() : $offset;
        $parameters[$this->limitParameterName] = null === $limit ? $this->getLimit() : $limit;

        return $parameters;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @return string
     */
    public function getOffsetParameterName()
    {
        return $this->offsetParameterName;
    }

    /**
     * @return string
     */
    public function getLimitParameterName()
    {
        return $this->limitParameterName;
    }

    /**
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("_meta")
     */
    public function getMeta()
    {
        return [
            'offset' => $this->offset,
            'limit' => $this->limit,
            'count' => $this->count
        ];
    }
}
