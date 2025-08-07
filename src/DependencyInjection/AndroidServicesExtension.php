<?php

declare(strict_types=1);

namespace IM\Fabric\Bundle\AndroidServicesBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\Definition\Configuration;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class AndroidServicesExtension extends Extension
{
    /**
     * @suppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.yaml');
    }
}
