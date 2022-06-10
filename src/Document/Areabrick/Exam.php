<?php

/**
 * This file is part of the Pimcore Learning Management Framework
 * Docs and updates: https://github.com/spotbot2k/pimcore-learning-management-framework
 *
 *  @license GPLv3
 */

namespace LearningManagementFrameworkBundle\Document\Areabrick;

use LearningManagementFrameworkBundle\Form\ExamType;
use LearningManagementFrameworkBundle\Helper\ExamHelper;
use Pimcore\Extension\Document\Areabrick\AbstractTemplateAreabrick;
use Pimcore\Extension\Document\Areabrick\EditableDialogBoxConfiguration;
use Pimcore\Extension\Document\Areabrick\EditableDialogBoxInterface;
use Pimcore\Model\Document\Editable;
use Pimcore\Model\Document\Editable\Area\Info;
use Pimcore\Model\Translation;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class Exam extends AbstractTemplateAreabrick implements EditableDialogBoxInterface
{
    private ExamHelper $helper;

    private TranslatorInterface $translator;

    private FormFactoryInterface $formFactory;

    public function __construct(ExamHelper $helper, TranslatorInterface $translator, FormFactoryInterface $formFactory)
    {
        $this->helper = $helper;
        $this->translator = $translator;
        $this->formFactory = $formFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Exam (LMF)';
    }

    public function action(Info $info): ?Response
    {
        $examObject = $this->getDocumentEditable($info->getDocument(), 'relation', 'examObject')->getElement();
        $canAttend = $this->helper->canAttend($examObject);
        $form = $this->formFactory->createBuilder(ExamType::class, $examObject)->getForm();

        $form->handleRequest($info->getRequest());
        if ($form->isSubmitted() && $form->isValid()) {
            $result = $this->helper->process($examObject, $info->getRequest()->get(ExamType::PREFIX));

            if ($result->isPassed) {
                $info->setParam('result', $this->helper->validateCertificate($result->hash));
            }

            $info->setParam('ratio', $result->ratio);
        }

        $info->setParam('form', $form->createView());
        $info->setParam('exam', $examObject);
        $info->setParam('canAttend', $canAttend);
        $info->setParam('student', $this->helper->getStudent());
        if (!is_null($examObject->getMaxAttempts())) {
            $info->setParam('attempts', $examObject->getMaxAttempts() - $this->helper->getAttemptsCountForCurrentUser($examObject));
        }

        return null;
    }

    public function getEditableDialogBoxConfiguration(Editable $area, ?Info $info): EditableDialogBoxConfiguration
    {
        $config = new EditableDialogBoxConfiguration();
        $config->setItems([
            'type'  => 'panel',
            'items' => [
                [
                    'type'   => 'relation',
                    'label'  => $this->translator->trans('plugin_pimcore_learning_management_framework_action_open_exam', [], Translation::DOMAIN_ADMIN),
                    'name'   => 'examObject',
                    'config' => [
                        'types'    => ['object'],
                        'subtypes' => ['object'],
                        'classes'  => ['ExamDefinition'],
                        'reload'   => true,
                        'width'    => 350,
                    ],
                ],
            ],
        ]);

        return $config;
    }

    /**
     * Disables autodiscovery of the template - we need it to enable Pimcore 6.9 support
     */
    public function getTemplate()
    {
        return '@PimcoreLearningManagementFramework/areas/exam/view.html.twig';
    }
}
