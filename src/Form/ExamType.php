<?php

namespace LearningManagementFrameworkBundle\Form;

use Pimcore\Model\DataObject\ExamDefinition;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('id', HiddenType::class);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $exam = $event->getData();
            $form = $event->getForm();

            $form->add('questions', QuestionCollectionType::class);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ExamDefinition::class,
        ]);
    }
}
