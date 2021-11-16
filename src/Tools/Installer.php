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
use Pimcore\Model\Translation;

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
        $this->installFieldCollections();
        $this->installClasses();
        $this->installPermissions();
        $this->installDatabaseTables();
        $this->installTranslations();

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

    public function installDatabaseTables()
    {
        Db::get()->query('
        CREATE TABLE IF NOT EXISTS `plugin_lmf_student_progress` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `uuid` varchar(64) NOT NULL,
            `examId` int(3) unsigned NOT NULL,
            `studentId` int(3) unsigned NOT NULL,
            `date` bigint(20) unsigned DEFAULT CURRENT_TIMESTAMP,
            `isActive` tinyint(3) unsigned NOT NULL DEFAULT 1,
            `isPassed` tinyint(3) unsigned NOT NULL,
            `grade` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `ratio` int(3) unsigned NOT NULL DEFAULT 0,
            `time` int(5) unsigned NOT NULL DEFAULT 0,
            PRIMARY KEY (`id`),
            KEY `IDX_UUID` (`uuid`),
            KEY `IDX_STUDENTID` (`studentId`),
            KEY `IDX_EXAMID` (`examId`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');
    }

    private function installTranslations()
    {
        Translation::importTranslationsFromFile(__DIR__.'/../Resources/install/translations/export_admin_translations.csv', Translation::DOMAIN_ADMIN);
        Translation::importTranslationsFromFile(__DIR__.'/../Resources/install/translations/export_website_translations.csv', Translation::DOMAIN_DEFAULT);
    }
}
