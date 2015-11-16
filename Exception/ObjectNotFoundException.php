<?php

namespace Staffim\DTOBundle\Exception;

class ObjectNotFoundException extends Exception
{
    private $class;
    private $id;

    public function __construct($class, $id)
    {
        parent::__construct('Object of type "' . $class . '" with id "' . $id . '" not found.');
        $this->class = $class;
        $this->id = $id;
    }

    function getClass()
    {
        return $this->class;
    }

    function getId()
    {
        return $this->id;
    }
}
