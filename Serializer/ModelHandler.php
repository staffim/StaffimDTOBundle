<?php

namespace Staffim\DTOBundle\Serializer;

use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\JsonDeserializationVisitor;
use Staffim\DTOBundle\DTO\Model\DTOInterface;

class ModelHandler implements SubscribingHandlerInterface
{
    /**
     * @var \Doctrine\ODM\MongoDB\DocumentManager
     */
    private $documentManager;

    /**
     * @param \Doctrine\ODM\MongoDB\DocumentManager $documentManager
     */
    public function setDocumentManager($documentManager)
    {
        $this->documentManager = $documentManager;
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
            if (!$this->documentManager) {
                throw new \Staffim\DTOBundle\Exception\Exception('You should set document manager for using document auto-fetching.');
            }
            $className = $type['params'][0]['name'];
            $object = $this->documentManager->getRepository($className)->find($model);
            if (!$object) {
                            var_dump($className, $model);die();
                throw new \Staffim\DTOBundle\Exception\ObjectNotFoundException($className, $model);
            }
        }

        return $object;
    }
}
