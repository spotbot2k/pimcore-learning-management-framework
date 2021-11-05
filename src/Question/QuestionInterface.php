<?php

namespace LearningManagementFrameworkBundle\Question;

use Pimcore\Model\DataObject\Fieldcollection\Data\AbstractData;
use Symfony\Component\Form\FormEvent;

interface QuestionInterface
{
    public static function renderQuestion(FormEvent &$event): void;
    public static function processQuestion(string $type, AbstractData $question, $submitedValue): bool;
}
