<?php

namespace Staffim\DTOBundle\Request\ParamConverter;

use JMS\Serializer\SerializerInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;

use Staffim\DTOBundle\Serializer\Exclusion\HiddenFieldsExclusionStrategy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Rs\Json\Patch;

use Staffim\DTOBundle\Serializer\SerializationContext;
use Staffim\DTOBundle\Model\ModelInterface;
use Staffim\DTOBundle\Exception\ConstraintViolationException;
use Staffim\DTOBundle\Exception\Exception;
use Staffim\DTOBundle\Filterer\Filterer;

class DTOParamConverter implements ParamConverterInterface
{
    /**
     * @var \JMS\SerializerBundle\Serializer\SerializerInterface
     */
    protected $serializer;

    /**
     * @var \Staffim\DTOBundle\Serializer\SerializationContext
     */
    protected $serializationContext;

    /**
     * @var \Symfony\Component\Validator\Validator
     */
    protected $validator;

    /**
     * @var \Staffim\DTOBundle\DTO\Mapper
     */
    protected $mapper;

    /**
     * @var \Staffim\DTOBundle\Filterer\Filterer
     */
    private $filterer;

    /**
     * @param \JMS\SerializerBundle\Serializer\SerializerInterface $serializer
     * @param \Symfony\Component\Validator\Validator\ValidatorInterface $validator
     * @param \Staffim\DTOBundle\Filterer\Filterer $filterer
     */
    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator, Filterer $filterer = null)
    {
        $this->validator = $validator;
        $this->serializer = $serializer;
        $this->filterer = $filterer;
        $this->serializationContext = new SerializationContext;
        $this->serializationContext->setSerializeNull(true);
        $this->serializationContext->addExclusionStrategy(new HiddenFieldsExclusionStrategy());
    }

    /**
     * @param \Staffim\DTOBundle\DTO\Mapper $mapper
     */
    public function setMapper(\Staffim\DTOBundle\DTO\Mapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * @param \Staffim\DTOBundle\Filterer\Filterer $filterer
     */
    public function setFilterer(Filterer $filterer = null)
    {
        $this->filterer = $filterer;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter $configuration
     * @return bool
     * @throws \Exception
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $class = $configuration->getClass();
        $options = $this->getOptions($configuration);

        switch ($request->getMethod()) {
            case 'GET':
                $data = $this->buildModelFromGetRequest($request, $configuration, $options);
                break;
            case 'PATCH':
                $data = $this->buildModelFromPatchRequest($request, $configuration, $options);
                break;
            case 'POST':
                $data = $this->buildModelFromPostRequest($request, $configuration, $options);
                break;
            case 'DELETE':
                $data = $this->buildModelFromDeleteRequest($request, $configuration, $options);
                break;
            default:
                $data = $request->getContent();
        }

        $object = null;
        if (!empty($data) || false === $configuration->isOptional()) {
            $object = $this->serializer->deserialize($data, $class, $options['format']);
        }

        if (null === $object && false === $configuration->isOptional()) {
            throw new NotFoundHttpException(sprintf('%s object not found.', $class));
        }

        if (null !== $object) {
            if ($this->filterer) {
                $this->filterer->apply($object);
            }

            $constraintViolations = $this->validator->validate($object);
            if ($constraintViolations->count()) {
                throw new ConstraintViolationException($constraintViolations);
            }
        }

        $request->attributes->set($configuration->getName(), $object);

        return true;
    }

    /**
     * @param \Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter $configuration
     * @return bool
     */
    public function supports(ParamConverter $configuration)
    {
        if (null === $configuration->getClass()) {
            return false;
        }

        $class = $configuration->getClass();
        if (class_exists($class)) {
            return in_array('Staffim\DTOBundle\DTO\Model\DTOInterface', class_implements($class));
        } else {
            return false;
        }
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter $configuration
     * @param array $options
     * @return null|string
     */
    protected function buildModelFromGetRequest(Request $request, ParamConverter $configuration, array $options)
    {
        $data = null;
        if (is_string($request->get('q'))) {
            $data = urldecode($request->get('q'));
        }

        return $data;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter $configuration
     * @param array $options
     * @return string
     * @throws \Patch\FailedTestException
     * @throws \Staffim\DTOBundle\Exception\Exception
     */
    protected function buildModelFromPatchRequest(Request $request, ParamConverter $configuration, array $options)
    {
        $model = $this->getModelFromRequest($request, $options);
        if (!$model) {
            throw new Exception('No model in request.');
        }

        $operations = json_decode($request->getContent(), true);
        if (!$operations) {
            throw new Exception('Patch operations is missing');
        }

        return $this->applyPatch($model, $request->getContent());
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter $configuration
     * @param array $options
     * @return resource|string
     */
    protected function buildModelFromPostRequest(Request $request, ParamConverter $configuration, array $options)
    {
        return $request->getContent();
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter $configuration
     * @param array $options
     * @return null|string
     */
    protected function buildModelFromDeleteRequest(Request $request, ParamConverter $configuration, array $options)
    {
        return $request->getContent();
    }

    /**
     * @param \Staffim\DTOBundle\Model\ModelInterface $model
     * @param string $patchOperations
     * @return string
     * @throws \Rs\Json\Patch\FailedTestException
     */
    protected function applyPatch(ModelInterface $model, $patchOperations)
    {
        $document = $this->serializer->serialize($this->mapper->map($model), 'json', $this->serializationContext);
        $patch = new Patch($document, $patchOperations);

        return $patch->apply();
    }

    /**
     * @param \Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter $configuration
     * @return array
     */
    protected function getOptions(ParamConverter $configuration)
    {
        return array_replace([
            'format' => 'json',
            'model' => null,
        ], $configuration->getOptions());
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param array $options
     * @return null|\Staffim\DTOBundle\Model\ModelInterface
     * @throws \Staffim\DTOBundle\Exception\Exception
     */
    protected function getModelFromRequest(Request $request, array $options)
    {
        $model = null;
        if ($options['model']) {
            $model = $request->attributes->get($options['model']);
        } else {
            foreach ($request->attributes->all() as $attribute) {
                if ($attribute instanceof ModelInterface) {
                    if (!$model) {
                        $model = $attribute;
                    } else {
                        throw new Exception('Multiple models in request.');
                    }
                }
            }
        }

        return $model;
    }
}
