<?php

namespace LearningManagementFrameworkBundle;

use Pimcore\Logger;
use Pimcore\Extension\Bundle\Installer\AbstractInstaller;

class Installer extends AbstractInstaller
{
    public function install()
    {
        $this->installClasses();

        return true;
    }

    public function installClasses()
    {
        $sourcePath = __DIR__.'/../install/class_source';

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
