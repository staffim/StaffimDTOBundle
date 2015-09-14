<?php

namespace Staffim\DTOBundle\Hateoas;

use JMS\Serializer\Annotation as Serializer;

class CollectionRepresentation
{
    /**
     * @Serializer\Expose
     *
     * @var mixed
     */
    private $items;


    public function __construct($items)
    {
        if ($items instanceof \Traversable) {
            $items = iterator_to_array($items);
        }

        $this->items = $items;
    }
}
