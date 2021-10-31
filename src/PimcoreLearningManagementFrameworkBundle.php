<?php

namespace LearningManagementFrameworkBundle;


use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;

class PimcoreLearningManagementFrameworkBundle extends AbstractPimcoreBundle
{
    use PackageVersionTrait;

    public function getDescription()
    {
        return 'Provides tools to build a LMS within Pimcore installation';
    }

    protected function getComposerPackageName(): string
    {
        return 'spotbot2k/pimcore-learning-management-framework';
    }

    public function getInstaller()
    {
        return $this->container->get(Installer::class);
    }

    /**
     * @todo: add real check
     *
     * @return boolean
     */
    public function isInstalled()
    {
        return false;
    }

    public function canBeInstalled()
    {
        return !$this->isInstalled();
    }
}
