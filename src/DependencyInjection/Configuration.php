<?php

namespace LearningManagementFrameworkBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('pimcore_learning_management_framework');
        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('student_class')
                ->defaultValue('Student')
            ->end()
        ;

        return $treeBuilder;
    }
}
