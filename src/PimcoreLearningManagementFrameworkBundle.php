<?php

/**
 * This file is part of the Pimcore Learning Management Framework
 * Docs and updates: https://github.com/spotbot2k/pimcore-learning-management-framework
 *
 *  @license GPLv3
 */

namespace LearningManagementFrameworkBundle;

use LearningManagementFrameworkBundle\Tools\Installer;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;

class PimcoreLearningManagementFrameworkBundle extends AbstractPimcoreBundle
{
    use PackageVersionTrait;

    public function getDescription()
    {
        return 'Provides tools to build a LMS within Pimcore installation';
    }

    /**
     * {@inheritdoc}
     */
    public function getAdminIframePath()
    {
        return '/admin/lmf/dashboard';
    }

    /**
     * {@inheritdoc}
     */
    public function getJsPaths()
    {
        return [
            '/bundles/pimcorelearningmanagementframework/js/startup.js',
        ];
    }

    protected function getComposerPackageName(): string
    {
        return 'spotbot2k/pimcore-learning-management-framework';
    }

    public function getInstaller()
    {
        return $this->container->get(Installer::class);
    }
}
