<?php

namespace MNC\DoctrineCarbonBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('mnc_doctrine_carbon');

        $rootNode
            ->children()
                ->scalarNode('locale')->end()
                ->arrayNode('properties')->defaultValue(['createdAt', 'deletedAt', 'updatedAt'])
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('excluded_entities')->defaultValue([])
                    ->prototype('scalar')->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
