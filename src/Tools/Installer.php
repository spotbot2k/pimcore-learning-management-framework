<?php

namespace LearningManagementFrameworkBundle\Tools;

use Pimcore\Logger;
use Pimcore\Extension\Bundle\Installer\SettingsStoreAwareInstaller;

class Installer extends SettingsStoreAwareInstaller
{
    public function install()
    {
        $this->installClasses();

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
        $class = \Pimcore\Model\DataObject\ClassDefinition::getByName($classname);
        if (!$class) {
            $class = new \Pimcore\Model\DataObject\ClassDefinition();
            $class->setName($classname);
            $class->setGroup('CustomerManagement');

            $json = file_get_contents($filepath);

            $success = \Pimcore\Model\DataObject\ClassDefinition\Service::importClassDefinitionFromJson($class, $json);
            if (!$success) {
                Logger::err("Could not import $classname Class.");
            }
        }
    }
}
