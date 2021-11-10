<?php

/**
 * This file is part of the Pimcore Learning Management Framework
 * Docs and updates: https://github.com/spotbot2k/pimcore-learning-management-framework
 *
 *  @license GPLv3
 */

namespace LearningManagementFrameworkBundle\Controller;

use Pimcore\Db;
use Pimcore\Bundle\AdminBundle\Controller\AdminController as AbstractAdminController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractAdminController
{
    /**
     * @Route("/admin/lmf/students", name="lmf_admin_get_students", methods={"POST","GET"})
     */
    public function getStudentList()
    {
        if ($this->userCanView()) {
            $studentClassName = sprintf("Pimcore\Model\DataObject\%s", $this->getParameter('pimcore_learning_management_framework.student_class'));
            $studentNameProperty = $this->getParameter('pimcore_learning_management_framework.student_name_property');
            $sql = sprintf("
                SELECT DISTINCT(%s) text, s.oo_id id, 'true' as leaf
                    FROM `plugin_lmf_student_progress` lmf
                JOIN `object_store_%s` s ON s.oo_id = lmf.studentId
            ", $studentNameProperty, $studentClassName::classId());

            $result = Db::get()->fetchAll($sql);

            return new JsonResponse($result);
        }

        return new JsonResponse();
    }

    /**
     * @Route("/admin/lmf/student/{id}", name="lmf_admin_get_student_progress", methods={"POST","GET"})
     */
    public function getStudentProgress(int $id)
    {
        if ($this->userCanView()) {
            $sql = "
                SELECT
                    lmf.`examId` examId,
                    e.title,
                    COUNT(lmf.id) attempts,
                    DATE_FORMAT(lmf.date, '%d.%m.%Y') lastAttempt,
                    MAX(lmf.`isPassed`) passed,
                    MIN(lmf.time) bestTime,
                    MAX(lmf.ratio) bestRatio,
                    (SELECT grade FROM `plugin_lmf_student_progress` WHERE `grade` IS NOT NULL ORDER BY `date` LIMIT 1) latestGrade
                FROM `plugin_lmf_student_progress` lmf
                LEFT JOIN `object_store_LMF_ED` e
                    ON e.oo_id = lmf.examId
                WHERE `studentId` = ?
                ORDER BY date
            ";

            $result = Db::get()->fetchAll($sql, [ $id ]);

            return new JsonResponse($result);
        }

        return new JsonResponse();
    }

    protected function userCanView()
    {
        $user = $this->getAdminUser();
        if ($user->isAllowed('plugin_lmf_manage') || $user->isAllowed('plugin_lmf_view')) {
            return true;
        }

        return false;
    }
}
