<?php

/**
 * This file is part of the Pimcore Learning Management Framework
 * Docs and updates: https://github.com/spotbot2k/pimcore-learning-management-framework
 *
 *  @license GPLv3
 */

namespace LearningManagementFrameworkBundle\Question;

use Pimcore\Model\DataObject\Fieldcollection\Data\AbstractData;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;

class MultipleChoiseQuestion implements QuestionInterface
{
    public static function renderQuestion(FormEvent &$event): void
    {
        $question = $event->getData();
        $form = $event->getForm();
        $choices = [];

        foreach ($question->getAnswer() as $answer) {
            $choices[$answer['title']->getData()] = $answer['title']->getData();
        }

        $form->add('answer', ChoiceType::class, [
            'mapped'    => false,
            'choices'   => $choices,
            'multiple'  => true,
            'expanded'  => true,
            'required'  => true,
            'label'     => $question->getQuestion(),
            'help'      => $event->getData()->getDescription(),
            'help_html' => true,
        ]);
    }

    public static function processQuestion(string $type, AbstractData $question, $submitedValue): bool
    {
        $isCorrect = true;
        foreach ($question->getAnswer() as $answer) {
            if ($answer['isCorrect']->getData() && !in_array($answer['title']->getData(), $submitedValue)) {
                $isCorrect = false;
            }
            if (!$answer['isCorrect']->getData() && in_array($answer['title']->getData(), $submitedValue)) {
                $isCorrect = false;
            }
        }

        return $isCorrect;
    }
}
