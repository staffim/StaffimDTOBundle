<?php

namespace Staffim\DTOBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Staffim\DTOBundle\Model\ModelInterface;
use Staffim\DTOBundle\DTO\Model\DTOInterface;

class PostMapEvent extends Event
{
    /**
     * @var \Staffim\DTOBundle\Model\ModelInterface
     */
    private $model;

    /**
     * @var \Staffim\DTOBundle\DTO\Model\DTOInterface
     */
    private $dto;

    /**
     * @param \Staffim\DTOBundle\Model\ModelInterface $model
     * @param \Staffim\DTOBundle\DTO\Model\DTOInterface $dto
     */
    public function __construct(ModelInterface $model, DTOInterface $dto)
    {
        $this->model = $model;
        $this->dto = $dto;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getDTO()
    {
        return $this->dto;
    }
}
