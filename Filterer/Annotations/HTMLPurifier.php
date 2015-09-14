<?php

namespace Staffim\DTOBundle\Filterer\Annotations;

use Doctrine\Common\Annotations\Annotation as Annotation;

/**
 * @Annotation
 */
class HTMLPurifier extends AbstractFilter
{
    /**
     * {@inheritdoc}
     */
    public function filteredBy()
    {
        return 'html_purifier';
    }
}
