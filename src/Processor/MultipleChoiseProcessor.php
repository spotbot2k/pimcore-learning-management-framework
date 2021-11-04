<?php

namespace LearningManagementFrameworkBundle\Processor;

class MultipleChoiseProcessor implements QuestionProcessorInterface
{
    public static function processQuestion(string $type, iterable $answers, $submitedValue): bool
    {
        $isCorrect = true;
        foreach ($answers as $answer) {
            if ($answer['IsCorrect']->getData() && !in_array($answer['Title']->getData(), $submitedValue)) {
                $isCorrect = false;
            }
            if (!$answer['IsCorrect']->getData() && in_array($answer['Title']->getData(), $submitedValue)) {
                $isCorrect = false;
            }
        }

        return $isCorrect;
    }
}
