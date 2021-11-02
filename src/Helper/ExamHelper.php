<?php

namespace LearningManagementFrameworkBundle\Helper;

use Pimcore\Model\DataObject\ExamDefinition;

class ExamHelper
{
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
                $buffer['answers'][] = $answer["Title"]->getData();
            }

            $output['questions'][] = $buffer;
        }

        return $output;
    }

    public function buildJson(ExamDefinition $exam)
    {
        return json_encode($this->buildArray($exam), JSON_THROW_ON_ERROR);
    }
}
