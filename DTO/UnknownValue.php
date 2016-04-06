<?php

namespace Staffim\DTOBundle\DTO;

final class UnknownValue
{
    private static $instance;

    public static function create()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {}

    private function __clone() {}

    public function __sleep()
    {
        throw new \RuntimeException('Cannot serialize ' . get_class($this));
    }
}
