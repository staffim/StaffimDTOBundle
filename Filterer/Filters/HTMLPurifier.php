<?php

namespace Staffim\DTOBundle\Filterer\Filters;

use HTMLPurifier as HTMLPurifierClass;

class HTMLPurifier implements FilterInterface
{
    public function apply($value)
    {
        return HTMLPurifierClass::getInstance()->purify($value);
    }
}
