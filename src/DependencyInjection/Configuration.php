<?php

/**
 * This file is part of the Pimcore Learning Management Framework
 * Docs and updates: https://github.com/spotbot2k/pimcore-learning-management-framework
 *
 *  @license GPLv3
 */

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
                ->scalarNode('student_class')->info('Must be a Pimcore data object')->defaultValue('Student')->end()
                ->scalarNode('student_name_property')->info('In lowercase, as it will be used in the SQL query')->defaultValue('email')->end()
                ->integerNode('attempt_reset_period')->info('Time in hours to be passed before an attempt resets')->min(0)->defaultValue('168')->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
