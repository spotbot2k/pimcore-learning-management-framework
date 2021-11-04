<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
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

    protected function getComposerPackageName(): string
    {
        return 'spotbot2k/pimcore-learning-management-framework';
    }

    public function getInstaller()
    {
        return $this->container->get(Installer::class);
    }
}
