<?php

namespace LearningManagementFrameworkBundle\Controller;

use Pimcore\Db;
use Pimcore\Bundle\AdminBundle\Controller\AdminController as AbstractAdminController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class AdminController extends AbstractAdminController
{
    /**
     * @Route("/admin/lmf/dashboard", name="lmf_admin_dashboard", methods={"POST","GET"})
     */
    public function dashboard(Request $request, Security $security)
    {
        $user = $this->getAdminUser();

        if ($user->isAllowed('plugin_lmf_manage') || $user->isAllowed('plugin_lmf_view') ) {
            return $this->render('@PimcoreLearningManagementFramework/admin/dashboard.html.twig', [
                'user'    => $user,
                'isAdmin' => $user->isAllowed('plugin_lmf_manage'),
            ]);
        }
        
        return $this->render('@PimcoreLearningManagementFramework/admin/denied.html.twig', [
            'user' => $user,
        ]);
    }
}
