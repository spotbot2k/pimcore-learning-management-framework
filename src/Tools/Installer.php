<?php

/**
 * This file is part of the Pimcore Learning Management Framework
 * Docs and updates: https://github.com/spotbot2k/pimcore-learning-management-framework
 *
 *  @license GPLv3
 */

namespace LearningManagementFrameworkBundle\Tools;

use Pimcore\Db;
use Pimcore\Extension\Bundle\Installer\Exception\InstallationException;
use Pimcore\Extension\Bundle\Installer\SettingsStoreAwareInstaller;
use Pimcore\Logger;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Service;
use Pimcore\Model\DataObject\Fieldcollection;

class Installer extends SettingsStoreAwareInstaller
{
    private $permissionsToInstall = [
        'plugin_lmf_manage',
        'plugin_lmf_view',
    ];

    /**
     * {@inheritdoc}
     */
    public function needsReloadAfterInstall()
    {
        return true;
    }

    public function install()
    {
        $this->installClasses();
        $this->installFieldCollections();
        $this->installPermissions();
        $this->installDatabaseTables();

        parent::install();

        return true;
    }

    public function installClasses()
    {
        $sourcePath = __DIR__.'/../Resources/install/class_source';

        self::installClass('ExamDefinition', $sourcePath.'/class_ExamDefinition_export.json');
    }

    public static function installClass($classname, $filepath)
    {
        $class = ClassDefinition::getByName($classname);
        if (!$class) {
            $class = new ClassDefinition();
            $class->setName($classname);
            $class->setGroup('CustomerManagement');

            $json = file_get_contents($filepath);

            $success = Service::importClassDefinitionFromJson($class, $json);
            if (!$success) {
                Logger::err("Could not import $classname Class.");
            }
        }
    }

    public function installFieldCollections()
    {
        $sourcePath = __DIR__.'/../Resources/install/fieldcollection_source';
        $collections = [
            'MultipleChoise',
            'TextInput',
        ];

        foreach ($collections as $key) {
            self::installFieldCollection($key, $sourcePath);
        }
    }

    public static function installFieldCollection($key, $filepath)
    {
        $fieldCollection = Fieldcollection\Definition::getByKey($key);
        if (!$fieldCollection) {
            $fieldCollection = new Fieldcollection\Definition();
            $fieldCollection->setKey($key);

            $data = file_get_contents(sprintf('%s/fieldcollection_%s_export.json', $filepath, $key));
            $success = Service::importFieldCollectionFromJson($fieldCollection, $data);

            if (!$success) {
                throw new InstallationException(sprintf('Failed to import field collection "%s"', $key));
            }
        }
    }

    public function installPermissions()
    {
        foreach ($this->permissionsToInstall as $key) {
            $permission = new \Pimcore\Model\User\Permission\Definition();
            $permission->setKey($key);

            $res = new \Pimcore\Model\User\Permission\Definition\Dao();
            $res->configure();
            $res->setModel($permission);
            $res->save();
        }
    }

    /**
     * @todo: create table where the progress will be storred
     */
    public function installDatabaseTables()
    {
        // Db::get()->query();
    }
}
