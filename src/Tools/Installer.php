<?php

namespace LearningManagementFrameworkBundle\Tools;

use Pimcore\Logger;
use Pimcore\Extension\Bundle\Installer\Exception\InstallationException;
use Pimcore\Extension\Bundle\Installer\SettingsStoreAwareInstaller;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Service;
use Pimcore\Model\DataObject\Fieldcollection;

class Installer extends SettingsStoreAwareInstaller
{
    public function install()
    {
        $this->installClasses();
        $this->installFieldCollections();

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
        $collections = ['Question'];

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

            $data = file_get_contents(sprintf("%s/fieldcollection_%s_export.json", $filepath, $key));
            $success = Service::importFieldCollectionFromJson($fieldCollection, $data);

            if (!$success) {
                throw new InstallationException(sprintf('Failed to import field collection "%s"', $key));
            }
        }
    }
}
