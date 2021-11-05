<?php

namespace LearningManagementFrameworkBundle\Form;

use Pimcore\Model\DataObject\Fieldcollection\Data\AbstractData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $class = sprintf("LearningManagementFrameworkBundle\Question\%sQuestion", $event->getData()->getType());
            if (class_exists($class)) {
                $class::renderQuestion($event);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AbstractData::class,
        ]);
    }
}