<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
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

    private function processQuestion(string $type, iterable $answers, $submitedValue)
    {
        switch ($type) {
            case 'MultipleChoise':
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
}
