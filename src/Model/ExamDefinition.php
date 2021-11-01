<?php

namespace LearningManagementFrameworkBundle\Model;

class ExamDefinition extends \Pimcore\Model\DataObject\ExamDefinition
{
    public function toJson()
    {
        $output = [
            'title'       => $this->getTitle(),
            'description' => $this->getDescription(),
            'questions'   => [],
        ];

        foreach ($this->getQuestions() as $question) {
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
}
