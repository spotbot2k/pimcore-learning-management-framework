<?php

/**
 * This file is part of the Pimcore Learning Management Framework
 * Docs and updates: https://github.com/spotbot2k/pimcore-learning-management-framework
 *
 *  @license GPLv3
 */

namespace LearningManagementFrameworkBundle\Question;

use Pimcore\Model\DataObject\Fieldcollection\Data\AbstractData;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;

class TextInputQuestion implements QuestionInterface
{
    public static function renderQuestion(FormEvent &$event): void
    {
        $event->getForm()->add('answer', TextType::class, [
            'mapped'     => false,
            'label'      => $event->getData()->getQuestion(),
            'help'       => $event->getData()->getDescription(),
            'help_html'  => true,
            'empty_data' => null,
        ]);
    }

    public static function processQuestion(string $type, AbstractData $question, $submitedValue): bool
    {
        $correctAnswer = $question->getAnswer();
        if ($question->getLowercaseComparsion()) {
            $correctAnswer = strtolower($correctAnswer);
        }

        return $correctAnswer === $submitedValue;
    }
}
