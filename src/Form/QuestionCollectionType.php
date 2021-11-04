<?php

/**
 * This file is a part of the Pimcore Learning Management Framework
 * Docs and updates: https://github.com/spotbot2k/pimcore-learning-management-framework
 *
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace LearningManagementFrameworkBundle\Form;

use Pimcore\Model\DataObject\Fieldcollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionCollectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $collection = $event->getData();
            $form = $event->getForm();

            $form->add('items', CollectionType::class, [
                'entry_type' => MultipleChoiseType::class,
            ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Fieldcollection::class,
        ]);
    }
}
