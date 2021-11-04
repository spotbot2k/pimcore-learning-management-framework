<?php

/**
 * This file is part of the Pimcore Learning Management Framework
 * Docs and updates: https://github.com/spotbot2k/pimcore-learning-management-framework
 *
 *  @license GPLv3
 */

namespace LearningManagementFrameworkBundle\Helper;

use Pimcore\Model\DataObject\ExamDefinition;

class ExamHelper
{
    public function process(ExamDefinition $exam, array $data): array
    {
        $correctAnswers = 0;
        $incorrectAnswers = [];
        $timeTaken = time() - $data['initts'];
        $gradeAchieved = null;

        if (array_key_exists('questions', $data)) {
            foreach ($exam->getQuestions() as $idx => $question) {
                $isCorrect = $this->processQuestion($question->getType(), $question->getAnswer(), $data['questions']['items'][$idx]['answer']);
                if ($isCorrect) {
                    $correctAnswers += 1;
                } else {
                    $incorrectAnswers[] = [
                        'idx'       => $idx,
                        'title'     => $question->getQuestion(),
                        'submitted' => $data['questions']['items'][$idx]['answer'],
                    ];
                }
            }
        }

        $ratio = $correctAnswers / $exam->getQuestions()->getCount() * 100;

        foreach ($exam->getGrades() as $grade) {
            if (array_key_exists('Ratio', $grade) && $ratio < $grade['Ratio']->getData()) {
                continue;
            }
            if (array_key_exists('TimeLimit', $grade) && $timeTaken > $grade['TimeLimit']->getData()) {
                continue;
            }
            $gradeAchieved = $grade['Title']->getData();

            break;
        }

        return [
            'correct' => $correctAnswers,
            'incorrect' => $incorrectAnswers,
            'ratio'     => $ratio,
            'time'      => time() - $data['initts'],
            'grade'     => $gradeAchieved,
        ];
    }

    public function buildArray(ExamDefinition $exam)
    {
        $output = [
            'title'       => $exam->getTitle(),
            'description' => $exam->getDescription(),
            'questions'   => [],
        ];

        foreach ($exam->getQuestions() as $question) {
            $buffer = [
                'type'        => $question->getType(),
                'question'    => $question->getQuestion(),
                'description' => $question->getDescription(),
                'answers'     => [],
            ];

            foreach ($question->getAnswer() as $answer) {
                $buffer['answers'][] = $answer['Title']->getData();
            }

            $output['questions'][] = $buffer;
        }

        return $output;
    }

    public function buildJson(ExamDefinition $exam)
    {
        return json_encode($this->buildArray($exam), JSON_THROW_ON_ERROR);
    }

    private function processQuestion(string $type, iterable $answers, $submitedValue): bool
    {
        $class = sprintf("LearningManagementFrameworkBundle\Processor\%sProcessor", $type);

        if (class_exists($class)) {
            return $class::processQuestion($type, $answers, $submitedValue);
        }

        return false;
    }
}
