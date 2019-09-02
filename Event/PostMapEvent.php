<?php

namespace Staffim\DTOBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Staffim\DTOBundle\Model\ModelInterface;
use Staffim\DTOBundle\DTO\Model\DTOInterface;

class PostMapEvent extends Event
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var \Staffim\DTOBundle\Model\ModelInterface
     */
    private $model;

    /**
     * @var \Staffim\DTOBundle\DTO\Model\DTOInterface
     */
    private $dto;

    /**
     * @param string $name
     * @param \Staffim\DTOBundle\Model\ModelInterface $model
     * @param \Staffim\DTOBundle\DTO\Model\DTOInterface $dto
     */
    public function __construct($name, ModelInterface $model, DTOInterface $dto)
    {
        $this->name = $name;
        $this->model = $model;
        $this->dto = $dto;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return \Staffim\DTOBundle\Model\ModelInterface
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return \Staffim\DTOBundle\DTO\Model\DTOInterface
     */
    public function getDTO()
    {
        return $this->dto;
    }
}
