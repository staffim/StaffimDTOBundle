<?php

namespace Staffim\DTOBundle\Configuration;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationAnnotation;

/**
 * The Render class handles the @Render annotation parts.
 *
 * @Annotation
 */
class Render extends ConfigurationAnnotation
{
    protected $format = 'json';

    protected $code = 200;

    public function getFormat()
    {
        return $this->format;
    }

    public function setFormat($format)
    {
        $this->format = $format;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * Returns the annotation alias name.
     *
     * @see \Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface
     *
     * @return string
     */
    public function getAliasName()
    {
        return 'render';
    }

    public function allowArray()
    {
        return false;
    }
}
