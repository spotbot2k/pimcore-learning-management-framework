<?php

/**
 * This file is part of the Pimcore Learning Management Framework
 * Docs and updates: https://github.com/spotbot2k/pimcore-learning-management-framework
 *
 *  @license GPLv3
 */

namespace LearningManagementFrameworkBundle\Controller;

use Pimcore\Bundle\AdminBundle\Controller\AdminController as AbstractAdminController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractAdminController
{
    /**
     * @Route("/admin/lmf/students", name="lmf_admin_get_students", methods={"POST","GET"})
     */
    public function getStudentList(Request $request)
    {
        if ($this->userCanView()) {
            dd($this->getParameter('pimcore_learning_management_framework.student_name_property'));
        }
    }

    /**
     * @Route("/admin/lmf/progress", name="lmf_admin_get_progress", methods={"GET"})
     */
    public function getStudentProgress(Request $request)
    {
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
