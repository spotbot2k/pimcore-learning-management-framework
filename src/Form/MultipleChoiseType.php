<?php

namespace LearningManagementFrameworkBundle\Form;

use Pimcore\Model\DataObject\Fieldcollection\Data\MultipleChoise;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MultipleChoiseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $question = $event->getData();
            $form = $event->getForm();
            $choices = [];

            foreach ($question->getAnswer() as $answer) {
                $choices[$answer["Title"]->getData()] = $answer["Title"]->getData();
            }

            $form->add('answer', ChoiceType::class, [
                'mapped'  => false,
                'choices' => $choices,
            ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MultipleChoise::class,
        ]);
    }
}