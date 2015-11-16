<?php

namespace Staffim\DTOBundle\EventListener;

use JMS\Serializer\SerializerInterface;

use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpFoundation\Response;

use Staffim\DTOBundle\DTO\Mapper;
use Staffim\DTOBundle\Collection\ModelCollection;
use Staffim\DTOBundle\Collection\ModelIteratorInterface;
use Staffim\DTOBundle\Hateoas\CollectionRepresentation;
use Staffim\DTOBundle\Hateoas\PaginatedRepresentation;
use Staffim\DTOBundle\Model\ModelInterface;
use Staffim\DTOBundle\Serializer\SerializationContext;

class RenderListener
{
    /**
     * @var \JMS\Serializer\SerializerInterface
     */
    private $serializer;

    /**
     * @var \Staffim\DTOBundle\DTO\Mapper
     */
    private $mapper;

    /**
     * @param \JMS\Serializer\SerializerInterface $serializer
     * @param \Staffim\DTOBundle\DTO\Mapper $mapper
     */
    public function __construct(SerializerInterface $serializer, Mapper $mapper)
    {
        $this->serializer = $serializer;
        $this->mapper = $mapper;
    }

    /**
     * Renders the template and initializes a new response object with the
     * rendered template content.
     *
     * @param GetResponseForControllerResultEvent $event A GetResponseForControllerResultEvent instance
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        if (!$render = $event->getRequest()->attributes->get('_render')) {
            return;
        }
        $route = $event->getRequest()->attributes->get('_route');
        $routeParams = $event->getRequest()->attributes->get('_route_params', []);
        $pagination = $event->getRequest()->attributes->get('_');
        $data = $event->getControllerResult();

        $response = new Response();
        $response->setStatusCode($render->getCode());

        // "The 204 response MUST NOT include a message-body, and thus is always terminated by the first empty line
        // after the header fields" — http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html.
        if (204 == $render->getCode()) {
            $content = null;
        } elseif ($data !== null) {
            if ($data instanceof ModelInterface) {
                $presentationData = $this->mapper->map($data);
            } elseif ($data instanceof ModelIteratorInterface) {
                $presentationData = new CollectionRepresentation($this->mapper->mapCollection($data));
                if ($route && $data instanceof ModelCollection && $pagination = $data->getPagination()) {
                    $presentationData = new PaginatedRepresentation(
                        $presentationData,
                        $route,
                        $routeParams,
                        $pagination->offset,
                        $pagination->limit,
                        $data->getCount()
                    );
                }
            } else {
                $presentationData = $data;
            }
            $context = new SerializationContext;
            $context->setSerializeNull(true);
            if ($render->getGroups()) {
                $context->setGroups($render->getGroups());
            }
            $content = $this->serializer->serialize($presentationData, $render->getFormat(), $context);

            $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
            $response->headers->set('Content-Length', strlen($content));
            $response->setContent($content);
        }

        $event->setResponse($response);
    }
}
