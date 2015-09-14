<?php

namespace Staffim\DTOBundle\Request\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class PaginationParamConverter implements ParamConverterInterface
{
    public function apply(Request $request, ParamConverter $configuration)
    {
        $class = $configuration->getClass();
        $options = $this->getOptions($configuration);

        $offset  = is_numeric($request->get('offset')) ? (int) $request->get('offset') : 0;
        $limit   = is_numeric($request->get('limit'))  ? (int) $request->get('limit')  : $options['limit'];
        if ($offset < 0) {
            $offset = 0;
        }
        if ($limit < $options['min_limit']) {
            $limit = $options['min_limit'];
        }
        if ($limit > $options['max_limit']) {
            $limit = $options['max_limit'];
        }
        $pagination = new $class;
        $pagination->offset = $offset;
        $pagination->limit  = $limit;

        $request->attributes->set($configuration->getName(), $pagination);

        return true;
    }

    public function supports(ParamConverter $configuration)
    {
        if (null === $configuration->getClass()) {
            return false;
        }

        $class = $configuration->getClass();

        return $class == 'Staffim\DTOBundle\Collection\Pagination';

    }

    private function getOptions(ParamConverter $configuration)
    {
        return array_replace([
            'limit' => 10,
            'min_limit' => 0,
            'max_limit' => 500
        ], $configuration->getOptions());
    }
}
