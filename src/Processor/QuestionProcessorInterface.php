<?php

namespace LearningManagementFrameworkBundle\Processor;

interface QuestionProcessorInterface
{
    public static function processQuestion(string $type, iterable $answers, $submitedValue): bool;
}
