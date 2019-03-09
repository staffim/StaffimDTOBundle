<?php

namespace Staffim\DTOBundle\Serializer;

use Doctrine\Common\Persistence\ObjectManager;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\JsonDeserializationVisitor;
use Staffim\DTOBundle\DTO\Model\DTOInterface;

class ModelHandler implements SubscribingHandlerInterface
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $objectManager;

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $objectManager
     */
    public function setObjectManager(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public static function getSubscribingMethods()
    {
        return [
            [
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'type' => 'DTO',
                'method' => 'serializeModelToJson',
            ],
            [
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'format' => 'json',
                'type' => 'DTO',
                'method' => 'deserializeModelFromJson',
            ]
        ];
    }

    /**
     * @param \JMS\Serializer\JsonSerializationVisitor $visitor
     * @param string|\Staffim\DTOBundle\DTO\Model\DTOInterface $model
     * @param array $type
     * @return string
     */
    public function serializeModelToJson(JsonSerializationVisitor $visitor, $model, array $type)
    {
        if ($model instanceof DTOInterface) {
            return $model->id;
        } else {
            return $model;
        }
    }

    /**
     * @param \JMS\Serializer\JsonDeserializationVisitor $visitor
     * @param string $model
     * @param array $type
     * @return mixed
     */
    public function deserializeModelFromJson(JsonDeserializationVisitor $visitor, $model, array $type)
    {
        if ($model && count($type['params'])) {
            if (!$this->objectManager) {
                throw new \Staffim\DTOBundle\Exception\Exception('You should set object manager for using document auto-fetching.');
            }
            $className = $type['params'][0]['name'];
            $object = $this->objectManager->getRepository($className)->find($model);
            if (!$object) {
                throw new \Staffim\DTOBundle\Exception\ObjectNotFoundException($className, $model);
            }
        } else {
            $object = $model;
        }

        return $object;
    }
}
