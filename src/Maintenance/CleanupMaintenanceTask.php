<?php

namespace LearningManagementFrameworkBundle\Maintenance;

use Pimcore\Db;
use Pimcore\Maintenance\TaskInterface;

class CleanupMaintenanceTask implements TaskInterface
{
    private string $defaultStudentClass;

    public function __construct($defaultStudentClass)
    {
        $this->defaultStudentClass = $defaultStudentClass;
    }

    public function execute()
    {
        $studentClassName = sprintf("Pimcore\Model\DataObject\%s", $this->defaultStudentClass);
        $sql = sprintf("
            DELETE lmf FROM `plugin_lmf_student_progress` lmf
            LEFT JOIN  `object_store_LMF_ED` e
                ON e.oo_id = lmf.examId
            LEFT JOIN `object_store_%s` s
                ON s.oo_id = lmf.studentId
            WHERE
                e.oo_id IS NULL
                OR s.oo_id IS NULL
            " ,
            $studentClassName::classId()
        );
        Db::get()->executeQuery($sql);
    }
}
