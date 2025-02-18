<?php

declare(strict_types=1);

namespace IM\Fabric\Bundle\AndroidServicesBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('android_services');
        $rootNode = $treeBuilder->getRootNode();

        // Define the parameters that are allowed to configure your bundle.
        $rootNode
            ->children()
            ->scalarNode('package_name')->defaultValue('')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
