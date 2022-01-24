<?php

namespace Staffim\DTOBundle\Request\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class SortingParamConverter implements ParamConverterInterface
{
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $class = $configuration->getClass();
        $options = $this->getOptions($configuration);

        $order   = ($request->get('sort_order') && in_array($request->get('sort_order'), ['asc', 'desc']) )
            ? $request->get('sort_order')
            : $options['sort_order'];

        if (is_string($sortBy = $request->get('sort_by'))) {
            $fieldName = is_array($fieldName = json_decode(urldecode($sortBy), true)) ? $fieldName : $sortBy;
            $sort = new $class;
            $sort->fieldName = $fieldName;
            $sort->order  = $order;

            $request->attributes->set($configuration->getName(), $sort);

            return true;
        };

        return false;
    }

    public function supports(ParamConverter $configuration): bool
    {
        if (null === $configuration->getClass()) {
            return false;
        }

        $class = $configuration->getClass();

        return $class == 'Staffim\DTOBundle\Collection\Sorting';

    }

    private function getOptions(ParamConverter $configuration): array
    {
        return array_replace([
            'sort_order' => 'desc',
        ], $configuration->getOptions());
    }
}
