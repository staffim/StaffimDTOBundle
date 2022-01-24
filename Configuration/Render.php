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

    protected $groups = [];

    public function getFormat(): string
    {
        return $this->format;
    }

    public function setFormat(string $format)
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

    function getGroups(): array
    {
        return $this->groups;
    }

    function setGroups(array $groups = [])
    {
        $this->groups = $groups;
    }

    public function getAliasName(): string
    {
        return 'render';
    }

    public function allowArray(): bool
    {
        return false;
    }
}
