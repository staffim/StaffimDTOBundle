<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles(): array
    {
        return array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new Bazinga\Bundle\HateoasBundle\BazingaHateoasBundle(),
            new Staffim\DTOBundle\StaffimDTOBundle(),
        );
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__.'/config.yml');
    }
}
