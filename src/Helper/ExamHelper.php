<?php

/**
 * This file is part of the Pimcore Learning Management Framework
 * Docs and updates: https://github.com/spotbot2k/pimcore-learning-management-framework
 *
 *  @license GPLv3
 */

namespace LearningManagementFrameworkBundle\Helper;

use Pimcore\Model\DataObject\ExamDefinition;
use Pimcore\Model\DataObject\Fieldcollection\Data\AbstractData;
use Symfony\Component\Security\Core\Security;

class ExamHelper
{
    private $user = null;

    public function __construct(string $defaultStudentClass, Security $security)
    {
        $user = $security->getUser();
        $class = sprintf("Pimcore\Model\DataObject\%s", $defaultStudentClass);
        if ($user instanceof $class) {
            $this->user = $security->getUser();
        }
    }

    public function process(ExamDefinition $exam, array $data): array
    {
        $correctAnswers = 0;
        $incorrectAnswers = [];
        $timeTaken = time() - $data['initts'];
        $gradeAchieved = null;

        if (array_key_exists('questions', $data)) {
            foreach ($exam->getQuestions() as $idx => $question) {
                $isCorrect = $this->processQuestion($question->getType(), $question, $data['questions']['items'][$idx]['answer']);
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
            if (array_key_exists('ratio', $grade) && $ratio < $grade['ratio']->getData()) {
                continue;
            }
            if (array_key_exists('timeLimit', $grade)
                && !is_null($grade['timeLimit']->getData())
                && $timeTaken > $grade['timeLimit']->getData()
            ) {
                continue;
            }

            $gradeAchieved = $grade['title']->getData();

            break;
        }

        return [
            'correct'   => $correctAnswers,
            'incorrect' => $incorrectAnswers,
            'ratio'     => $ratio,
            'time'      => time() - $data['initts'],
            'grade'     => $gradeAchieved,
        ];
    }

    public function isSudentLoggedIn()
    {
        return is_null($this->user);
    }

    public function getStudent()
    {
        return $this->user;
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

    private function processQuestion(string $type, AbstractData $question, $submitedValue): bool
    {
        $class = sprintf("LearningManagementFrameworkBundle\Question\%sQuestion", $type);

        if (class_exists($class)) {
            return $class::processQuestion($type, $question, $submitedValue);
        }

        return false;
    }
}
